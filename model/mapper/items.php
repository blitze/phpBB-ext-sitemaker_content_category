<?php
/**
 *
 * @package sitemaker
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\model\mapper;

use blitze\sitemaker\model\base_mapper;
use blitze\category\services\tree\nestedset;

class items extends base_mapper
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \blitze\sitemaker\services\forum\data */
	protected $forum;

	/** @var \blitze\category\services\tree\nestedset */
	protected $tree;

	/** @var string */
	protected $items_table;

	/** @var string */
	protected $data_table;

	/** @var string */
	protected $entity_class = 'blitze\category\model\entity\item';

	/** @var string */
	protected $entity_pkey = 'cat_id';

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface				$db					Database object
	 * @param \blitze\sitemaker\model\base_collection		$collection			Entity collection
	 * @param \blitze\category\model\mapper_factory			$mapper_factory		Mapper factory object
	 * @param string										$items_table		Category Items table
	 * @param \phpbb\config\config							$config				Config object
	 * @param \blitze\sitemaker\services\forum\data			$forum				Forum Data object
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \blitze\sitemaker\model\base_collection $collection, \blitze\category\model\mapper_factory $mapper_factory, $items_table, \phpbb\config\config $config, \blitze\sitemaker\services\forum\data $forum)
	{
		parent::__construct($db, $collection, $mapper_factory, $items_table);

		$this->config = $config;
		$this->forum = $forum;
		$this->items_table = $items_table;
		$this->data_table = $mapper_factory->mapper_tables['data'];
		$this->tree = new nestedset(
			$db,
			new \phpbb\lock\db('blitze.category.table_lock.category_items_table', $this->config, $db),
			$this->entity_table
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function load(array $condition = array())
	{
		$sql_where = join(' AND ', $this->get_sql_condition($condition));
		$row = $this->tree
			->set_sql_where($sql_where)
			->get_item_info();

		if ($row)
		{
			return $this->create_entity($row);
		}
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function find(array $condition = array())
	{
		$sql_where = join(' AND ', $this->get_sql_condition($condition));
		$tree_data = $this->tree
			->set_sql_where($sql_where)
			->get_all_tree_data();

		$this->collection->clear();
		foreach ($tree_data as $id => $row)
		{
			$this->collection[$id] = $this->create_entity($row);
		}

		return $this->collection;
	}

	/**
	 * @param int $group_id
	 * @return array
	 */
	public function find_all($group_id = 0)
	{
		$sql_custom = array(
			'SELECT'	=> array('c.*, count(d.topic_id) as items_count'),
			'FROM'		=> array(
				$this->items_table	=> 'c',
				$this->data_table	=> 'd'
			),
			'WHERE'		=> 'c.cat_id = d.cat_id
				AND t.topic_id = d.topic_id' .
				(($group_id) ? ' AND c.group_id = ' . (int) $group_id : ''),
			'GROUP_BY'	=> 'd.cat_id',
			'ORDER_BY'	=> 'c.group_id, c.left_id ASC'
		);

		$sql_array = $this->forum->query(false, false)
			->fetch_custom($sql_custom, array('SELECT'))
			->build(true, true, false)
			->get_sql_array();
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);

		$this->collection->clear();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->collection[$id] = $this->create_entity($row);
		}
		$this->db->sql_freeresult($result);

		return $this->collection;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(\blitze\sitemaker\model\entity_interface $entity)
	{
		/** @type \blitze\category\model\entity\item $entity */
		$sql_data = $entity->to_db();

		$this->tree->set_sql_where($this->get_sql_where($entity->get_group_id()));

		if ($entity->get_cat_id())
		{
			$item = $this->tree->update_item($entity->get_cat_id(), $sql_data);
		}
		else
		{
			$item = $this->tree->insert($sql_data);
		}

		return $this->create_entity($item);
	}

	/**
	 * Add multiple items via string depicting hierarchical structure
	 *
	 * @param int $group_id
	 * @param int $parent_id
	 * @param $string
	 * @return \blitze\category\model\collections\items
	 */
	public function add_items($group_id, $parent_id, $string)
	{
		$items = $this->tree->string_to_nestedset($string, array('cat_name' => ''), array('group_id' => $group_id));

		$new_item_ids = array();
		if (sizeof($items))
		{
			$branch = $this->prep_items_for_storage($items);

			$new_item_ids = $this->tree->set_sql_where($this->get_sql_where($group_id))
				->add_branch($branch, $parent_id);
		}

		return $this->find(array('cat_id', '=', $new_item_ids));
	}

	/**
	 * Update entire tree saving parent-child relationships in a single go
	 *
	 * @param int $group_id
	 * @param array $items
	 * @return array
	 */
	public function update_items($group_id, array $items)
	{
		return $this->tree->set_sql_where($this->get_sql_where($group_id))
			->update_tree($items);
	}

	/**
	 * {@inheritdoc}
	 */
	public function create_entity(array $row)
	{
		return new $this->entity_class($row);
	}

	/**
	 * @param array $items
	 * @return array
	 */
	protected function prep_items_for_storage(array $items)
	{
		$branch = array();
		foreach ($items as $key => $row)
		{
			$entity = $this->create_entity($row);
			$branch[$key] = array_merge($entity->to_db(), array(
				'cat_id' => $key,
			));
		}

		return $branch;
	}

	/**
	 * @param int $group_id
	 * @return string
	 */
	protected function get_sql_where($group_id)
	{
		return '%sgroup_id = ' . (int) $group_id;
	}
}

<?php
/**
 *
 * @package blitze
 * @copyright (c) 2018 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\services;

/**
 * @package phpBB Primetime Categories
 */
class categories
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \blitze\category\model\mapper_factory */
	protected $mapper_factory;

	/** @var string */
	protected $categories_table;

	/** @var string */
	protected $categories_data_table;

	/**
	 * Construct
	 *
	 * @param \phpbb\db\driver\driver_interface			$db						Database connection
	 * @param \blitze\category\model\mapper_factory		$mapper_factory		Mapper factory object
	 * @param string									$categories_table		Categories Table
	 * @param string									$categories_data_table	Categories Data Table
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \blitze\category\model\mapper_factory $mapper_factory, $categories_table, $categories_data_table)
	{
		$this->db = $db;
		$this->mapper_factory = $mapper_factory;
		$this->categories_table = $categories_table;
		$this->categories_data_table = $categories_data_table;
	}

	/**
	 * @return array
	 */
	public function get_groups()
	{
		$collection = $this->mapper_factory->create('groups')->find();

		$options = array();
		foreach ($collection as $entity)
		{
			$options[$entity->get_group_id()] = $entity->get_group_name();
		}

		return $options;
	}

	/**
	 * @param int $group_id
	 * @return array
	 */
	public function get_group_items($group_id = 0)
	{
		$item_mapper = $this->mapper_factory->create('items');
		$collection = $item_mapper->find_all($group_id);

		$data = array();
		foreach ($collection as $entity)
		{
			$row = $entity->to_array();
			$data[$row['group_id']][$row['cat_id']] = $row;
		}

		return $group_id ? (isset($data[$group_id]) ? $data[$group_id] : array()) : $data;
	}

	/**
	 * @param array $topic_ids
	 * @return array
	 */
	public function get_topic_categories(array $topic_ids)
	{
		$result = $this->db->sql_query($this->db->sql_build_query('SELECT', array(
			'SELECT'	=> 'd.cat_id, d.topic_id, d.field, c.cat_name',
			'FROM'		=> array(
				$this->categories_data_table	=> 'd',
				$this->categories_table			=> 'c',
			),
			'WHERE'		=> 'c.cat_id = d.cat_id
				AND ' . $this->db->sql_in_set('d.topic_id', array_map('intval', $topic_ids))
		)));

		$db_fields = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$db_fields[$row['topic_id']][$row['field']][$row['cat_id']] = $row['cat_name'];
		}
		$this->db->sql_freeresult($result);

		return $db_fields;
	}

	/**
	 * @param int $topic_id
	 * @param string $field
	 * @param array $sql_ary
	 * @return void
	 */
	public function set_topic_categories($topic_id, $field, array $sql_ary)
	{
		$this->delete_topic_categories($topic_id, $field);

		if (sizeof($sql_ary))
		{
			$this->db->sql_multi_insert($this->categories_data_table, $sql_ary);
		}
	}

	/**
	 * @param int $topic_id
	 * @param string $field
	 * @return void
	 */
	public function delete_topic_categories($topic_id, $field = '')
	{
		$sql_where = array(
			(($field) ? "field = '" . $this->db->sql_escape($field) . "'" : ''),
			'topic_id = ' . (int) $topic_id,
		);

		$this->db->sql_query('DELETE FROM ' . $this->categories_data_table . ' WHERE ' . join(' AND ', array_filter($sql_where)));
	}
}

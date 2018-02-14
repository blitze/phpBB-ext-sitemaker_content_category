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
	/** @var \blitze\category\model\mapper_factory */
	protected $mapper_factory;

	/**
	 * Construct
	 *
	 * @param \blitze\category\model\mapper_factory		$mapper_factory		Mapper factory object
	 */
	public function __construct(\blitze\category\model\mapper_factory $mapper_factory)
	{
		$this->mapper_factory = $mapper_factory;
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
	public function get_items($group_id = 0)
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
}

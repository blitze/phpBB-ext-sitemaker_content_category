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

class groups extends base_mapper
{
	/** @var string */
	protected $entity_class = 'blitze\category\model\entity\group';

	/** @var string */
	protected $entity_pkey = 'group_id';

	/**
	 * {@inheritdoc}
	 */
	public function load(array $condition = array())
	{
		/** @type \blitze\category\model\entity\group|null $entity */
		$entity = parent::load($condition);

		if ($entity)
		{
			$items_mapper = $this->mapper_factory->create('items');

			/** @type \blitze\category\model\collections\items $collection */
			$collection = $items_mapper->find(array('group_id', '=', $entity->get_group_id()));
			$entity->set_items($collection);
		}

		return $entity;
	}

	/**
	 * @param array|\blitze\category\model\entity\group $condition
	 */
	public function delete($condition)
	{
		parent::delete($condition);

		// delete category items associated with this category group
		if ($condition instanceof $this->entity_class)
		{
			$items_mapper = $this->mapper_factory->create('items');
			$items_mapper->delete(array('group_id', '=', $condition->get_group_id()));
		}
	}
}

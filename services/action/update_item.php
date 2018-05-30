<?php
/**
 *
 * @package sitemaker
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\services\action;

class update_item extends base_action
{
	/**
	 * {@inheritdoc}
	 */
	public function execute()
	{
		$cat_id = $this->request->variable('cat_id', 0);
		$field = $this->request->variable('field', 'cat_icon');
		$allowed_fields = array(
			'cat_icon'		=> $this->request->variable('cat_icon', ''),
			'cat_name'		=> $this->request->variable('cat_name', '', true),
		);

		$item_mapper = $this->mapper_factory->create('items');
		$entity = $this->get_entity($cat_id, $item_mapper);

		if (isset($allowed_fields[$field]))
		{
			$mutator = 'set_' . $field;
			$entity->$mutator($allowed_fields[$field]);
			$item_mapper->save($entity);
		}

		return $entity->to_array();
	}

	/**
	 * @param int $cat_id
	 * @param \blitze\category\model\mapper\items $item_mapper
	 * @return \blitze\category\model\entity\item
	 * @throws \blitze\sitemaker\exception\out_of_bounds
	 */
	protected function get_entity($cat_id, \blitze\category\model\mapper\items $item_mapper)
	{
		if (($entity = $item_mapper->load(array('cat_id', '=', $cat_id))) === null)
		{
			throw new \blitze\sitemaker\exception\out_of_bounds('cat_id');
		}

		return $entity;
	}
}

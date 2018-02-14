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
	 * @throws \blitze\sitemaker\exception\out_of_bounds
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

		if (($entity = $item_mapper->load(array('cat_id', '=', $cat_id))) === null)
		{
			throw new \blitze\sitemaker\exception\out_of_bounds('cat_id');
		}

		if (isset($allowed_fields[$field]))
		{
			$mutator = 'set_' . $field;
			$entity->$mutator($allowed_fields[$field]);
			$item_mapper->save($entity);
		}

		return $entity->to_array();
	}
}

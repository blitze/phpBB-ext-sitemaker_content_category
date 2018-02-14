<?php
/**
 *
 * @package sitemaker
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\services\action;

class save_tree extends base_action
{
	/**
	 * {@inheritdoc}
	 * @throws \blitze\sitemaker\exception\out_of_bounds
	 */
	public function execute()
	{
		$group_id = $this->request->variable('group', 0);
		$raw_tree = $this->request->variable('tree', array(0 => array('' => 0)));

		/** @type \blitze\category\model\mapper\items $item_mapper */
		$item_mapper = $this->mapper_factory->create('items');
		$group_mapper = $this->mapper_factory->create('groups');

		if ($group_mapper->load(array('group_id', '=', $group_id)) === null)
		{
			throw new \blitze\sitemaker\exception\out_of_bounds('group_id');
		}

		$tree = $this->prepare_tree($raw_tree);

		return $item_mapper->update_items($group_id, $tree);
	}

	/**
	 * @param array $raw_tree
	 * @return array
	 */
	protected function prepare_tree(array $raw_tree)
	{
		$tree = array();
		$raw_tree = array_values($raw_tree);

		for ($i = 0, $size = sizeof($raw_tree); $i < $size; $i++)
		{
			$item_id = (int) $raw_tree[$i]['item_id'];
			$parent_id = (int) $raw_tree[$i]['parent_id'];

			if ($item_id)
			{
				$tree[$item_id] = array(
					'cat_id'	=> $item_id,
					'parent_id' => $parent_id,
				);
			}
		}

		return $tree;
	}
}

<?php
/**
 *
 * @package sitemaker
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\services\action;

class edit_group extends base_action
{
	/**
	 * {@inheritdoc}
	 * @throws \blitze\sitemaker\exception\out_of_bounds
	 */
	public function execute()
	{
		$group_id = $this->request->variable('group', 0);
		$group_name = $this->request->variable('title', '', true);

		$group_mapper = $this->mapper_factory->create('groups');

		/** @type \blitze\sitemaker\model\entity\group $entity */
		if (($entity = $group_mapper->load(array('group_id', '=', $group_id))) === null)
		{
			throw new \blitze\sitemaker\exception\out_of_bounds('group_id');
		}

		$entity->set_group_name($group_name);
		$entity = $group_mapper->save($entity);

		return array(
			'id'	=> $entity->get_group_id(),
			'name'	=> $entity->get_group_name(),
		);
	}
}

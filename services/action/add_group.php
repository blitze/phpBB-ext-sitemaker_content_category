<?php
/**
 *
 * @package sitemaker
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\services\action;

class add_group extends base_action
{
	/**
	 * {@inheritdoc}
	 */
	public function execute()
	{
		$group_mapper = $this->mapper_factory->create('groups');

		/** @type \blitze\category\model\entity\group $entity */
		$entity = $group_mapper->create_entity(array(
			'group_name' => $this->translator->lang('GROUP') . '-' . mt_rand(1000, 9999),
		));

		$entity = $group_mapper->save($entity);

		return array(
			'id'	=> $entity->get_group_id(),
			'title'	=> $entity->get_group_name(),
		);
	}
}

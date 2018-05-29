<?php
/**
 *
 * @package sitemaker
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\acp;

use blitze\sitemaker\services\menus\nestedset;

/**
* @package acp
*/
class category_module
{
	/** @var \phpbb\controller\helper */
	protected $controller_helper;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \blitze\sitemaker\services\icon_picker */
	protected $icon;

	/** @var \blitze\sitemaker\model\mapper_factory */
	protected $mapper_factory;

	/** @var \blitze\sitemaker\services\util */
	protected $util;

	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var string phpEx */
	protected $php_ext;

	/** @var string */
	public $tpl_name;

	/** @var string */
	public $page_title;

	/** @var string */
	public $u_action;

	/**
	 * menu_module constructor.
	 */
	public function __construct()
	{
		global $phpbb_container, $request, $template, $phpbb_root_path, $phpEx;

		$this->request = $request;
		$this->template = $template;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $phpEx;

		$this->controller_helper = $phpbb_container->get('controller.helper');
		$this->mapper_factory = $phpbb_container->get('blitze.category.mapper.factory');
		$this->icon = $phpbb_container->get('blitze.sitemaker.icon_picker');
		$this->util = $phpbb_container->get('blitze.sitemaker.util');
	}

	/**
	 * @return void
	 */
	public function main()
	{
		$group_id = $this->request->variable('group', 0);

		$this->set_group_options($group_id);
		$this->load_assets();

		$this->template->assign_vars(array(
			'S_GROUPS'		=> true,
			'GROUP_ID'		=> $group_id,
			'ICON_PICKER'	=> $this->icon->picker(),
			'T_PATH'		=> $this->phpbb_root_path,
			'UA_GROUP_ID'	=> $group_id,
			'UA_AJAX_URL'   => $this->controller_helper->route('blitze_category_admin', array(), true, '') . '/',
		));

		$this->tpl_name = 'acp_category';
		$this->page_title = 'CATEGORIES';
	}

	/**
	 * @param int $current_group_id
	 * @return void
	 */
	protected function set_group_options(&$current_group_id)
	{
		$group_mapper = $this->mapper_factory->create('groups');

		// Get all category groups
		$collection = $group_mapper->find();

		$entity = (isset($collection[$group_id])) ? $collection[$group_id] : $collection->current();
		$current_group_id = $entity->get_group_id();

		foreach ($collection as $entity)
		{
			$group_id = $entity->get_group_id();
			$this->template->assign_block_vars('group', array(
				'ID'		=> $group_id,
				'NAME'		=> $entity->get_group_name(),
				'S_ACTIVE'	=> ($current_group_id == $group_id) ? true : false,
			));
		}
	}

	/**
	 * @return void
	 */
	protected function load_assets()
	{
		nestedset::load_scripts($this->util);

		$this->util->add_assets(array(
			'js'	=> array('@blitze_category/assets/admin.min.js'),
			'css'	=> array('@blitze_category/assets/admin.min.css'),
		));
	}
}

<?php

/**
 *
 * @package sitemaker
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\blocks;

/**
 * Categories Block
 */
class categories extends \blitze\sitemaker\services\blocks\driver\block
{
	/** @var \phpbb\language\language */
	protected $translator;

	/** @var \blitze\category\services\categories */
	protected $categories;

	/** @var \blitze\category\model\mapper_factory */
	protected $mapper_factory;

	/** @var \blitze\category\services\tree\display */
	protected $tree;

	/** @var string */
	protected $data_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\language\language					$translator			Language object
	 * @param \blitze\category\services\categories		$categories			Categories object
	 * @param \blitze\category\services\tree\display	$tree				Category tree display object
	 * @param string									$data_table			Categories Data Table
	 */
	public function __construct(\phpbb\language\language $translator, \blitze\category\services\categories $categories, \blitze\category\services\tree\display $tree, $data_table)
	{
		$this->translator = $translator;
		$this->categories = $categories;
		$this->tree = $tree;
		$this->data_table = $data_table;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_config(array $settings)
	{
		$category_groups = $this->categories->get_groups();
		$depth_options = $this->get_depth_options();

		return array(
			'legend1' => 'SETTINGS',
			'group_id' => array('lang' => 'CATEGORY_GROUP', 'validate' => 'int', 'type' => 'select', 'options' => $category_groups, 'default' => key($category_groups)),
			'max_depth' => array('lang' => 'CATEGORY_MAX_DEPTH', 'validate' => 'int', 'type' => 'select', 'options' => $depth_options, 'default' => 3),
			'show_count' => array('lang' => 'CATEGORY_SHOW_COUNT', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => false, 'default' => 1),
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function display(array $bdata, $editing = false)
	{
		$this->translator->add_lang('common', 'blitze/category');

		$title = 'CATEGORIES';
		$group_id = $bdata['settings']['group_id'];

		$data = array('items' => $this->categories->get_items($group_id));

		if (!sizeof($data)) {
			return array(
				'title' => $title,
				'content' => $this->get_message($group_id, $editing),
				'status' => (int)!$editing,
			);
		}

		$this->ptemplate->assign_var('SHOW_COUNT', $bdata['settings']['show_count']);
		$this->tree->display_navlist($data, $this->ptemplate, 'tree');

		return array(
			'title' => $title,
			'content' => $this->ptemplate->render_view('blitze/category', 'blocks/categories.html', 'categories_block'),
		);
	}

	/**
	 * @param int $group_id
	 * @param bool $editing
	 * @return string
	 */
	protected function get_message($group_id, $editing)
	{
		$msg_key = '';
		if ($editing) {
			$msg_key = $this->translator->lang(($group_id) ? 'CATEGORY_GROUP_NO_ITEMS' : 'SELECT_CATEGORY_GROUP');
		}

		return $msg_key;
	}

	/**
	 * @return array
	 */
	protected function get_depth_options()
	{
		$options = array();
		for ($i = 3; $i < 10; $i++) {
			$options[$i] = $i;
		}

		return $options;
	}
}

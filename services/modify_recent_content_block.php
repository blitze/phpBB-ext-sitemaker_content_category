<?php
/**
 *
 * @package sitemaker
 * @copyright (c) 2018 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\services;

class modify_recent_content_block extends \blitze\content\blocks\recent
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \blitze\category\services\categories */
	protected $categories;

	/** @var \blitze\category\services\tree\display */
	protected $tree;

	/** @var string */
	protected $data_table;

	/** @var int */
	protected $cat_id;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\db							$config				Config object
	 * @param \phpbb\language\language					$language			Language Object
	 * @param \blitze\content\services\types			$content_types		Content types object
	 * @param \blitze\content\services\fields			$fields				Content fields object
	 * @param \blitze\sitemaker\services\date_range		$date_range			Date Range Object
	 * @param \blitze\sitemaker\services\forum\data		$forum				Forum Data object
	 * @param \\phpbb\controller\helper					$helper				Controller helper class
	 * @param \blitze\category\services\categories		$categories			Categories object
	 * @param \blitze\category\services\tree\display	$tree				Category tree display object
	 * @param string									$data_table			Categories Data Table
	 */
	public function __construct(\phpbb\config\db $config, \phpbb\language\language $language, \blitze\content\services\types $content_types, \blitze\content\services\fields $fields, \blitze\sitemaker\services\date_range $date_range, \blitze\sitemaker\services\forum\data $forum, \phpbb\controller\helper $helper, \blitze\category\services\categories $categories, \blitze\category\services\tree\display $tree, $data_table)
	{
		parent::__construct($config, $language, $content_types, $fields, $date_range, $forum);

		$this->helper = $helper;
		$this->categories = $categories;
		$this->tree = $tree;
		$this->data_table = $data_table;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_config(array $settings)
	{
		$array = parent::get_config($settings);
		$options = $this->categories->get_items();

		// insert category config key after fields key
		$config = array();
		foreach ($array as $k => $value)
		{
			$config[$k] = $value;
			if ($k === 'fields')
			{
				$config['category'] = array('lang' => 'CATEGORY', 'validate' => 'string', 'type' => 'select:1', 'object' => $this, 'method' => 'select_category', 'options' => $options, 'default' => '');
			}
		}
		return $config;
	}

	/**
	 * {@inheritdoc}
	 */
	public function display(array $bdata, $edit_mode = false)
	{
		list($group_id, $this->cat_id) = preg_split('/-/', $bdata['settings']['category']);

		$block = parent::display($bdata, $edit_mode);

		if ($group_id && $this->cat_id)
		{
			$block['title'] = $this->overwrite_block_title($group_id, $block['title']);
		}

		return $block;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function build_query($forum_id)
	{
		parent::build_query($forum_id);

		if ($this->cat_id)
		{
			$this->forum->fetch_custom(array(
				'FROM'	=> array(
					$this->data_table	=> 'd',
				),
				'WHERE'	=> array(
					't.topic_id = d.topic_id',
					'd.cat_id = ' . (int) $this->cat_id,
				),
			));
		}
	}

	/**
	 * @param int $group_id
	 * @param string $block_title
	 * @return string
	 */
	protected function overwrite_block_title($group_id, $block_title)
	{
		$items = $this->categories->get_items($group_id);
		if (isset($items[$this->cat_id]))
		{
			$cat_name = $items[$this->cat_id]['cat_name'];
			$url = $this->helper->route('blitze_content_filter', array(
				'filter_type'	=> 'category',
				'filter_value'	=> urlencode($cat_name),
			));

			$block_title = '<a href="' . $url . '">' . $cat_name . '</a>';
		}
		unset($items);

		return $block_title;
	}

	/**
	 * @param array $content_types
	 * @param string $type
	 * @return string
	 */
	public function select_category(array $categories, $type)
	{
		$groups = $this->categories->get_groups();

		$html = '<option value="">' . $this->language->lang('ALL') . '</option>';
		foreach ($categories as $group_id => $items)
		{
			$grp_name = $groups[$group_id];
			$html .= '<optgroup label="' . $grp_name . '">';
			foreach ($items as $cat_id => $row)
			{
				$value = $row['group_id'] . '-' . $cat_id;
				$selected = ($type == $value) ? ' selected="selected"' : '';
				$html .= '<option value="' . $value . '"' . $selected . '">' . $row['cat_name'] . '</option>';
			}
			$html .= '</optgroup>';
		}

		return $html;
	}
}

<?php
/**
 *
 * @package blitze
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\services\form\field;

class category extends \blitze\content\services\form\field\choice
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \blitze\category\services\categories */
	protected $categories;

	/** @var \blitze\category\services\tree\display */
	protected $tree;

	/** @var string */
	protected $data_table;

	/** @var array */
	protected $cats = array();

	/**
	 * Constructor
	 *
	 * @param \phpbb\language\language                  $language       Language object
	 * @param \phpbb\request\request_interface			$request		Request object
	 * @param \blitze\sitemaker\services\template		$ptemplate		Sitemaker template object
	 * @param \phpbb\db\driver\driver_interface			$db				Database connection
	 * @param \phpbb\controller\helper					$helper			Controller helper class
	 * @param \blitze\category\services\categories		$categories		Categories object
	 * @param \blitze\category\services\tree\display	$tree			Categories tree object
	 * @param string									$data_table		Categories Data Table
	 */
	public function __construct(\phpbb\language\language $language, \phpbb\request\request_interface $request, \blitze\sitemaker\services\template $ptemplate, \phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \blitze\category\services\categories $categories, \blitze\category\services\tree\display $tree, $data_table)
	{
		parent::__construct($language, $request, $ptemplate);

		$this->db = $db;
		$this->helper = $helper;
		$this->categories = $categories;
		$this->tree = $tree;
		$this->data_table = $data_table;
	}

	/**
	 * @inheritdoc
	 */
	public function display_field(array $data, array $topic_data, $view_mode)
	{
		$list = array();
		$callable = 'get_category_url';

		// previewing...?
		if ($this->request->is_set('preview'))
		{
			$callable = 'get_preview_url';
			$field_value = array_filter(explode("<br>\n", $data['field_value']));
			$data['field_value'] = array_intersect_key($this->cats, array_flip($field_value));
		}

		$categories = (array) $data['field_value'];
		foreach ($categories as $category)
		{
			$u_cat = $this->$callable($category, $data['content_type']);
			$list[] = '<a href="' . $u_cat . '">' . $category . '</a>';
		}

		return (sizeof($list)) ? join($this->language->lang('COMMA_SEPARATOR'), $list) : '';
	}

	/**
	 * @inheritdoc
	 */
	public function show_form_field($name, array &$data, $forum_id = 0, $topic_id = 0)
	{
		$grp_items = $this->get_group_items($data['field_props']['group_id']);

		if ($data['field_props']['dropdown'])
		{
			$data['field_type'] = 'select';
			$data['field_props']['options']	= $this->tree->display_options($grp_items, 'cat_name', array(), 'data');
		}
		else
		{
			$data['field_type'] = ($data['field_props']['multi_select']) ? 'checkbox' : 'radio';
			$data['field_props']['options'] = $this->cats;
			$data['field_props']['vertical'] = true;
		}

		return parent::show_form_field($name, $data);
	}

	/**
	 * @inheritdoc
	 */
	public function save_field($field_value, array $field_data, array $topic_data)
	{
		$field = $field_data['field_name'];
		$topic_id = (int) $topic_data['topic_id'];
		$categories = array_filter((array) $field_value);

		$sql_ary = array();
		foreach ($categories as $cat_id)
		{
			$sql_ary[] = array(
				'cat_id'		=> (int) $cat_id,
				'topic_id'		=> $topic_id,
				'topic_time'	=> $topic_data['topic_time'],
				'field'			=> $field,
			);
		}

		$this->categories->set_topic_categories($topic_id, $field, $sql_ary);
	}

	/**
	 * @param int $group_id
	 * @return array
	 */
	protected function get_group_items($group_id)
	{
		$grp_items	= $this->tree->get_tree_data(0, 0, array(
			'WHERE'	=> array('i.group_id = ' . (int) $group_id)
		));

		foreach ($grp_items as $row)
		{
			$this->cats[$row['cat_id']] = $row['cat_name'];
		}

		return $grp_items;
	}

	/**
	 * @param string $category
	 * @param string $content_type
	 * @return string
	 */
	protected function get_category_url($category, $content_type)
	{
		return $this->helper->route('blitze_content_type_filter', array(
			'type'			=> $content_type,
			'filter_type'	=> 'category',
			'filter_value'	=> urlencode($category),
		));
	}

	/**
	 * @return string
	 */
	protected function get_preview_url()
	{
		return '#preview';
	}

	/**
	 * @inheritdoc
	 */
	public function get_default_props()
	{
		return array(
			'group_id'		=> 0,
			'dropdown'		=> true,
			'multi_select'	=> false,
			'show_icons'	=> true,
			'is_db_field'	=> true,
		);
	}

	/**
	 * @inheritdoc
	 */
	public function get_name()
	{
		return 'category';
	}

	/**
	 * @inheritdoc
	 */
	public function get_langname()
	{
		return 'CATEGORY';
	}
}

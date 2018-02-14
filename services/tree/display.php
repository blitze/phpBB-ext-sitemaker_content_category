<?php
/**
 *
 * @package blitze
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\services\tree;

/**
 * Manage nested sets
 * @package phpBB Primetime Categories
 */
class display extends \blitze\sitemaker\services\menus\display
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string */
	protected $column_item_id = 'cat_id';

	/** @var bool */
	protected $show_count = false;

	/**
	 * Construct
	 *
	 * @param \phpbb\db\driver\driver_interface			$db             	Database connection
	 * @param \\phpbb\controller\helper					$helper				Controller helper class
	 * @param \phpbb\user								$user				User Object
	 * @param string									$items_table		Table name
	 * @param string									$pk					Primary key
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\controller\helper $helper, \phpbb\user $user, $items_table, $pk)
	{
		parent::__construct($db, $user, $items_table, $pk);

		$this->helper = $helper;
	}

	/**
	 * @param array $data
	 * @return bool
	 */
	protected function set_current_item(array $data)
	{
	}

	/**
	 * @param array $row
	 * @return string
	 */
	protected function get_full_url(array $row)
	{
		return $this->helper->route('blitze_content_filter', array(
			'filter_type'	=> 'category',
			'filter_value'	=> urlencode($row['cat_name']),
		));
	}
}

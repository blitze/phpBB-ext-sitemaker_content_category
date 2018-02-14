<?php
/**
 *
 * @package sitemaker
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\model\entity;

use blitze\sitemaker\model\base_entity;

/**
 * @method string get_cat_name()
 * @method object set_cat_icon($cat_icon)
 * @method string get_cat_icon()
 * @method object set_group_id($group_id)
 * @method integer get_group_id()
 * @method object set_parent_id($parent_id)
 * @method integer get_parent_id()
 * @method object set_left_id($left_id)
 * @method boolean get_left_id()
 * @method object set_right_id($right_id)
 * @method boolean get_right_id()
 * @method object set_depth($depth)
 * @method boolean get_depth()
 * @method object item_parents($item_parents)
 * @method integer get_item_parents()
 */
final class item extends base_entity
{
	/** @var integer */
	protected $cat_id;

	/** @var string */
	protected $cat_name = '';

	/** @var string */
	protected $cat_icon = '';

	/** @var integer */
	protected $group_id;

	/** @var integer */
	protected $parent_id = 0;

	/** @var integer */
	protected $left_id = 0;

	/** @var integer */
	protected $right_id = 0;

	/** @var integer */
	protected $depth = 0;

	/** @var string */
	protected $item_parents = '';

	/** @var integer */
	protected $items_count = 0;

	/** @var array */
	protected $required_fields = array('group_id');

	/** @var array */
	protected $db_fields = array(
		'cat_name',
		'cat_icon',
		'group_id',
		'parent_id',
		'left_id',
		'right_id',
		'depth',
		'item_parents',
	);

	/**
	 * Set category ID
	 */
	public function set_cat_id($cat_id)
	{
		if (!$this->cat_id)
		{
			$this->cat_id = (int) $cat_id;
		}
		return $this;
	}

	/**
	 * Set category name
	 */
	public function set_cat_name($cat_name)
	{
		$this->cat_name = ucwords(trim($cat_name));
		return $this;
	}

	/**
	 * @param string $icon
	 * @return $this
	 */
	public function set_cat_icon($icon)
	{
		$this->cat_icon = ($icon) ? trim($icon) . ' ' : '';
		return $this;
	}
}

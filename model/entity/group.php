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
 * @method integer get_group_id()
 * @method string get_group_name()
 * @method object set_items(\blitze\category\model\collections\items $items)
 * @method \blitze\category\model\collections\items get_items()
 */
final class group extends base_entity
{
	/** @var integer */
	protected $group_id;

	/** @var string */
	protected $group_name;

	/** @var \blitze\category\model\collections\items */
	protected $items = array();

	/** @var array */
	protected $required_fields = array('group_name');

	/** @var array */
	protected $db_fields = array(
		'group_name',
	);

	/**
	 * Set group ID
	 * @param int $group_id
	 * @return $this
	 */
	public function set_group_id($group_id)
	{
		if (!$this->group_id)
		{
			$this->group_id = (int) $group_id;
		}
		return $this;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function set_group_name($name)
	{
		$this->group_name = ucwords(trim($name));
		return $this;
	}
}

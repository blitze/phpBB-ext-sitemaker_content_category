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
 * @method object set_cat_id($cat_id)
 * @method int get_cat_id()
 * @method object set_item_id($item_id)
 * @method int get_item_id()
 */
final class data extends base_entity
{
	/** @var integer */
	protected $cat_id;

	/** @var integer */
	protected $item_id;

	/** @var array */
	protected $required_fields = array('cat_id', 'item_id');

	/** @var array */
	protected $db_fields = array(
		'cat_id',
		'item_id',
	);
}

<?php
/**
 *
 * @package sitemaker
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\model\collections;

use blitze\sitemaker\model\base_collection;

class items extends base_collection
{
	protected $entity_class = 'blitze\category\model\entity\item';
}

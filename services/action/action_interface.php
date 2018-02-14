<?php
/**
 *
 * @package sitemaker
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\services\action;

interface action_interface
{
	/**
	 * Execute the action
	 *
	 * @return array
	 */
	public function execute();
}

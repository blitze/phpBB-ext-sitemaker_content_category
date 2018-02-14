<?php
/**
 *
 * @package blitze
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\acp;

class category_info
{
	function module()
	{
		return array(
			'filename'	=> '\blitze\category\acp\category_module',
			'title'		=> 'ACP_CATEGORY_MANAGEMENT',
			'parent'	=> 'ACP_MOD_MANAGEMENT',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'category'		=> array('title' => 'CATEGORIES', 'auth' => 'ext_blitze/category', 'before' => 'ACP_MENU', 'cat' => array('ACP_CATEGORY')),
			),
		);
	}
}

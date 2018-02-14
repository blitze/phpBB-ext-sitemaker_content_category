<?php
/**
*
* @package phpBB Primetime [English]
* @copyright (c) 2012 Daniel A. (blitze)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'CATEGORY'					=> 'Category',
	'CATEGORY_GROUP'			=> 'Category Group',
	'CATEGORY_FIELD_TYPE'		=> 'Display field as',
	'CATEGORY_FIELD_DROPDOWN'	=> 'Dropdown',
	'CATEGORY_FIELD_FLAT'		=> 'Flat list',
));

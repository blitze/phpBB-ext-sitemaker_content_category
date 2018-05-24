<?php
/**
*
* @package phpBB Sitemaker [English]
* @copyright (c) 2017 Daniel A. (blitze)
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
	'CATEGORY_ALL'				=> 'All',
	'CATEGORY_MAX_DEPTH'		=> 'Max. Depth',
	'CATEGORY_SHOW_COUNT'		=> 'Show count?',
	'CATEGORY_GROUP'			=> 'Category Group',
	'CATEGORY_GROUP_NO_ITEMS'	=> 'The selected category group has no items',

	'FORM_FIELD_CATEGORY'		=> 'Category',

	'BLITZE_CATEGORY_BLOCK_CATEGORIES'	=> 'Categories',
));

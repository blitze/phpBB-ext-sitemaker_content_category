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
	'CATEGORIES'	=> 'Categories',
));

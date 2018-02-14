<?php
/**
 *
 * @package blitze
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\migrations\v30x;

class install_acp_module extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\blitze\content\migrations\v30x\m1_initial_schema',
			'\blitze\category\migrations\converter\convert_data',
		);
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_SITEMAKER',
				array(
					'module_basename'	=> '\blitze\category\acp\category_module',
					'modes'				=> array('category'),
				),
			)),
		);
	}
}

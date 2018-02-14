<?php
/**
 *
 * @package blitze
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\migrations\converter;

class convert_tables extends \phpbb\db\migration\migration
{
	/**
	 * Skip this migration if the categories table does not exist
	 *
	 * @return bool True to skip this migration, false to run it
	 * @access public
	 */
	public function effectively_installed()
	{
		return !$this->db_tools->sql_table_exists($this->table_prefix . 'categories');
	}

	/**
	 * @inheritdoc
	*/
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'categories'			=> array(
					'depth'		=> array('UINT', 0),
				)
			),
			'drop_columns'	=> array(
				$this->table_prefix . 'categories_data'		=> array(
					'module',
				)
			),
			'drop_tables'	=> array(
				$this->table_prefix . 'categories_config',
			),
		);
	}
}

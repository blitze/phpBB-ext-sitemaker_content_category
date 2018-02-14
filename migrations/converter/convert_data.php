<?php
/**
 *
 * @package blitze
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\migrations\converter;

class convert_data extends \phpbb\db\migration\migration
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
	static public function depends_on()
	{
		return array(
			'\blitze\category\migrations\converter\convert_tables',
		);
	}

	/**
	 * @inheritdoc
	*/
	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'set_category_depth'))),
			array('custom', array(array($this, 'rename_categories_table'))),
			array('custom', array(array($this, 'rename_categories_data_table'))),
		);
	}

	/**
	 * @inheritdoc
	 */
	public function set_category_depth()
	{
		$sql = 'SELECT cc.cat_id, (COUNT(cp.cat_id) - 1) AS depth
			FROM ' . $this->table_prefix . 'categories cc, ' . $this->table_prefix . 'categories cp
			WHERE cc.left_id BETWEEN cp.left_id AND cp.right_id
			GROUP BY cc.cat_id
			ORDER BY cc.left_id';
		$result = $this->db->sql_query($sql);

		$data = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->db->sql_query('UPDATE ' . $this->table_prefix . 'categories SET depth = ' . (int) $row['depth'] . ' WHERE cat_id = ' . (int) $row['cat_id']);
		}
		$this->db->sql_freeresult($result);
	}

	/**
	 * @inheritdoc
	 */
	public function rename_categories_table()
	{
		$this->rename_table($this->table_prefix . 'categories', $this->table_prefix . 'pt_categories');
	}

	/**
	 * @inheritdoc
	 */
	public function rename_categories_data_table()
	{
		$this->rename_table($this->table_prefix . 'categories_data', $this->table_prefix . 'pt_categories_data');
	}

	/**
	 * @inheritdoc
	*/
	public function rename_table($old_name, $new_name)
	{
		switch ($this->db->get_sql_layer())
		{
			// SQL Server dbms support this syntax
			case 'mssql':
			case 'mssql_odbc':
			case 'mssqlnative':
				$sql = "EXEC sp_rename '$old_name', '$new_name'";
			break;
			// All other dbms support this syntax
			default:
				$sql = "ALTER TABLE $old_name RENAME TO $new_name";
			break;
		}
		$this->db->sql_query($sql);
	}
}

<?php
/**
 *
 * @package blitze
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\migrations\v30x;

class initial_schema extends \phpbb\db\migration\migration
{
	/**
	 * @inheritdoc
	 */
	static public function depends_on()
	{
		return array(
			'\blitze\content\migrations\v30x\m1_initial_schema',
			'\blitze\category\migrations\converter\convert_data',
		);
	}

	/**
	 * @inheritdoc
	 */
	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'sm_category_grps' => array(
					'COLUMNS'		=> array(
						'group_id'		=> array('UINT', null, 'auto_increment'),
						'group_name'	=> array('VCHAR:55', ''),
					),
					'PRIMARY_KEY'	=> 'group_id',
					'KEYS'			=> array(
						'group_name'	=> array('UNIQUE', 'group_name'),
						'group_id'		=> array('INDEX', 'group_id'),
					),
				),

				$this->table_prefix . 'sm_categories' => array(
					'COLUMNS'        => array(
						'cat_id'			=> array('UINT', null, 'auto_increment'),
						'cat_name'			=> array('VCHAR:55', ''),
						'cat_icon'			=> array('VCHAR', ''),
						'group_id'			=> array('UINT', 0),
						'parent_id'			=> array('UINT', 0),
						'left_id'			=> array('UINT', 0),
						'right_id'			=> array('UINT', 0),
						'depth'				=> array('UINT', 0),
						'item_parents'		=> array('MTEXT', ''),
					),
					'PRIMARY_KEY'	=> 'cat_id',
					'KEYS'			=> array(
						'cat_id'			=> array('INDEX', 'cat_id'),
					),
				),

				$this->table_prefix . 'sm_categories_data' => array(
					'COLUMNS'        => array(
						'cat_id'		=> array('UINT', 0),
						'topic_id'		=> array('UINT', 0),
						'field'			=> array('VCHAR:125', ''),
					),
					'KEYS'			=> array(
						'topic_id'		=> array('INDEX', 'topic_id'),
					),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'	=> array(
				$this->table_prefix . 'sm_category_grps',
				$this->table_prefix . 'sm_categories',
				$this->table_prefix . 'sm_categories_data',
			),
		);
	}
}

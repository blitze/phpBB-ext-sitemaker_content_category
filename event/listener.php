<?php
/**
 *
 * @package blitze
 * @copyright (c) 2013 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $translator;

	/** @var \blitze\category\services\categories */
	protected $categories;

	/** @var string */
	protected $categories_table;

	/** @var string */
	protected $categories_data_table;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface			$db						Database connection
	 * @param \phpbb\language\language					$translator				Language object
	 * @param \blitze\category\services\categories		$categories				Categories object
	 * @param string									$categories_table		Categories Table
	 * @param string									$categories_data_table	Categories Data Table
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\language\language $translator, \blitze\category\services\categories $categories, $categories_table, $categories_data_table)
	{
		$this->db = $db;
		$this->translator = $translator;
		$this->categories = $categories;
		$this->categories_table = $categories_table;
		$this->categories_data_table = $categories_data_table;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return array(
			'blitze.content.field_controller.modify_data'	=> 'get_category_groups',
			'blitze.content.acp_modify_field_data'			=> 'get_category_groups',
			'blitze.content.fields.set_values'				=> 'set_topics_categories',
			'blitze.content.builder.set_field_values'		=> 'set_form_field_values',
			'blitze.content.view.filter'					=> 'filter_by_category',
			'core.delete_post_after'						=> 'cleanup',
		);
	}

	/**
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function get_category_groups(\phpbb\event\data $event)
	{
		$field_data = (array) $event['field_data'];

		// Get all category groups
		$groups = $this->categories->get_groups();

		$field_data['category_groups'] = array();
		foreach ($groups as $group_id => $group_name)
		{
			$field_data['category_groups'][] = array(
				'id'	=> $group_id,
				'name'	=> $group_name,
			);
		}

		$event['field_data'] = $field_data;
	}

	/**
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function set_topics_categories(\phpbb\event\data $event)
	{
		$category_fields = array_keys((array) $event['view_mode_fields'], 'category');
		$db_fields = (array) $event['db_fields'];

		if (sizeof($category_fields) && sizeof($db_fields))
		{
			$categories = $this->categories->get_topic_categories(array_keys($db_fields));

			$this->set_uncategorized_items($db_fields, $category_fields, $categories);

			$event['db_fields'] = array_replace_recursive($db_fields, $categories);
		}
	}

	/**
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function set_form_field_values(\phpbb\event\data $event)
	{
		/** @var \blitze\content\model\entity\type $entity */
		$entity = $event['entity'];
		$category_fields = array_keys($entity->get_field_types(), 'category');

		if (sizeof($category_fields))
		{
			$categories = $this->categories->get_topic_categories(array($event['topic_id']));
			$categories = (array) array_shift($categories);

			$fields_data = (array) $event['fields_data'];
			foreach ($categories as $field => $value)
			{
				$fields_data[$field]['field_value'] = array_keys($value);
			}

			$event['fields_data'] = $fields_data;
		}
	}

	/**
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function filter_by_category(\phpbb\event\data $event)
	{
		if (isset($event['filters']['category']))
		{
			$sql_array = (array) $event['sql_array'];

			$sql_array = array_merge_recursive($sql_array, array(
				'FROM'	=> array(
					$this->categories_data_table	=> 'cats_data',
					$this->categories_table			=> 'cats',
				),
				'WHERE'	=> array(
					't.topic_id = cats_data.topic_id',
					'cats_data.cat_id = cats.cat_id',
					$this->db->sql_in_set('cats.cat_name', array_map('urldecode', $event['filters']['category'])),
				),
			));

			$event['sql_array'] = $sql_array;
		}
	}

	/**
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function cleanup(\phpbb\event\data $event)
	{
		if ($event['post_mode'] === 'delete_topic' && !$event['is_soft'])
		{
			 $this->categories->delete_topic_categories((int) $event['topic_id']);
		}
	}

	/**
	 * @param array $db_fields
	 * @param array $category_fields
	 * @param array $categories
	 * @return void
	 */
	protected function set_uncategorized_items(array $db_fields, array $category_fields, array &$categories)
	{
		$uncategorized = array_keys(array_diff_key($db_fields, array_filter($categories)));

		foreach ($uncategorized as $topic_id)
		{
			foreach ($category_fields as $field)
			{
				$categories[$topic_id][$field] = $this->translator->lang('UNCATEGORIZED');
			}
		}
	}
}

<?php
/**
 *
 * @package sitemaker
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\model;

use blitze\sitemaker\model\mapper_factory_interface;

class mapper_factory implements mapper_factory_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \blitze\sitemaker\services\forum\data */
	protected $forum;

	/** @var array */
	public $mapper_tables;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config						$config		Config object
	 * @param \phpbb\db\driver\driver_interface			$db			Database object
	 * @param \blitze\sitemaker\services\forum\data		$forum		Sitemaker forum data
	 * @param array										$tables		Tables for data mapping
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \blitze\sitemaker\services\forum\data $forum, array $tables)
	{
		$this->config = $config;
		$this->db = $db;
		$this->forum = $forum;
		$this->mapper_tables = array_shift($tables);
	}

	/**
	 * {@inheritdoc}
	 */
	public function create($type)
	{
		$mapper_class = 'blitze\\category\\model\\mapper\\' . $type;
		$collection = 'blitze\\category\\model\\collections\\' . $type;

		return new $mapper_class($this->db, new $collection, $this, $this->mapper_tables[$type], $this->config, $this->forum);
	}
}

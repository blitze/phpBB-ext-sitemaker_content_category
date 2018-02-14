<?php
/**
 *
 * @package sitemaker
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\services;

class action_handler
{
	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\language\language */
	protected $translator;

	/** @var \blitze\category\model\mapper_factory */
	protected $mapper_factory;

	/**
	 * Constructor
	 *
	 * @param \phpbb\cache\driver\driver_interface			$cache					Cache object
	 * @param \phpbb\request\request_interface				$request				Request object
	 * @param \phpbb\language\language						$translator				Language object
	 * @param \blitze\category\model\mapper_factory			$mapper_factory			Mapper factory object
	 */
	public function __construct(\phpbb\cache\driver\driver_interface $cache, \phpbb\request\request_interface $request, \phpbb\language\language $translator, \blitze\category\model\mapper_factory $mapper_factory)
	{
		$this->cache = $cache;
		$this->request = $request;
		$this->translator = $translator;
		$this->mapper_factory = $mapper_factory;
	}

	/**
	 * @param string $action
	 * @return \blitze\category\services\action\action_interface
	 * @throws \blitze\sitemaker\exception\unexpected_value
	 */
	public function create($action)
	{
		$action_class = 'blitze\\category\\services\\action\\' . $action;

		if (!class_exists($action_class))
		{
			throw new \blitze\sitemaker\exception\unexpected_value(array($action, 'INVALID_ACTION'));
		}

		return new $action_class($this->request, $this->translator, $this->mapper_factory);
	}

	/**
	 * Clear cache after every action
	 */
	public function clear_cache()
	{
		$this->cache->destroy('sitemaker_content_categories');
	}
}

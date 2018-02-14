<?php
/**
 *
 * @package sitemaker
 * @copyright (c) 2017 Daniel A. (blitze)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace blitze\category\services\action;

abstract class base_action implements action_interface
{
	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\language\language */
	protected $translator;

	/** @var \blitze\sitemaker\model\mapper_factory */
	protected $mapper_factory;

	/**
	 * Constructor
	 *
	 * @param \phpbb\request\request_interface				$request				Request object
	 * @param \phpbb\language\language						$translator				Language object
	 * @param \blitze\category\model\mapper_factory			$mapper_factory			Mapper factory object
	 */
	public function __construct(\phpbb\request\request_interface $request, \phpbb\language\language $translator, \blitze\category\model\mapper_factory $mapper_factory)
	{
		$this->request = $request;
		$this->translator = $translator;
		$this->mapper_factory = $mapper_factory;
	}

	/**
	 * @param \blitze\category\model\collections\items $collection
	 * @return array
	 */
	protected function get_items(\blitze\category\model\collections\items $collection)
	{
		$items = array();
		foreach ($collection as $item)
		{
			$items[] = $item->to_array();
		}

		return array(
			'items' => $items,
		);
	}
}

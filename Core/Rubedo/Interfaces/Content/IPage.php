<?php
/**
 * Rubedo
 *
 * LICENSE
 *
 * yet to be written
 *
 * @category Rubedo
 * @package Rubedo
 * @copyright Copyright (c) 2012-2012 WebTales (http://www.webtales.fr)
 * @license yet to be written
 * @version $Id$
 */
namespace Rubedo\Interfaces\Content;

/**
 * Page service
 *
 *
 * @author jbourdin
 * @category Rubedo
 * @package Rubedo
 */
Interface IPage {

	/**
	 * Return page infos based on its ID
	 *
	 * @param string|int $pageId requested URL
	 * @return array
	 */
	public function getPageInfo($pageId);

}
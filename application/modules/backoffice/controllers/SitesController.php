<?php
/**
 * Rubedo -- ECM solution
 * Copyright (c) 2012, WebTales (http://www.webtales.fr/).
 * All rights reserved.
 * licensing@webtales.fr
 *
 * Open Source License
 * ------------------------------------------------------------------------------------------
 * Rubedo is licensed under the terms of the Open Source GPL 3.0 license. 
 *
 * @category   Rubedo
 * @package    Rubedo
 * @copyright  Copyright (c) 2012-2012 WebTales (http://www.webtales.fr)
 * @license    http://www.gnu.org/licenses/gpl.html Open Source GPL 3.0 license
 */

require_once('DataAccessController.php');
 
/**
 * Controller providing CRUD API for the sitesController JSON
 *
 * Receveive Ajax Calls for read & write from the UI to the Mongo DB
 *
 *
 * @author jbourdin
 * @category Rubedo
 * @package Rubedo
 *
 */
class Backoffice_SitesController extends Backoffice_DataAccessController
{
    public function init(){
		parent::init();
		
		// init the data access service
		$this -> _dataService = Rubedo\Services\Manager::getService('Sites');
	}
	
	public function deleteAction() {
		$pages = Rubedo\Services\Manager::getService('Pages');
		$masks = Rubedo\Services\Manager::getService('Masks');
		
		$data = $this->getRequest()->getParam('data');

        if (!is_null($data)) {
            $data = Zend_Json::decode($data);
            if (is_array($data)) {
				$siteId = $data['id'];
				$resultPages = $pages->customDelete(array('site' => $siteId));
				$resultMasks = $masks->customDelete(array('site' => $siteId));
				
				if($resultPages['ok'] == 1 && $resultMasks['ok'] == 1){
					$returnArray = $this->_dataService->destroy($data, true);
				} else {
					$returnArray = array('success' => false, "msg" => 'Error during the deletion of masks and pages');
				}
            } else {
                $returnArray = array('success' => false, "msg" => 'Not an array');
            }

        } else {
            $returnArray = array('success' => false, "msg" => 'Invalid Data');
        }
        if (!$returnArray['success']) {
            $this->getResponse()->setHttpResponseCode(500);
        }
        $this->_returnJson($returnArray);
	}
	

}
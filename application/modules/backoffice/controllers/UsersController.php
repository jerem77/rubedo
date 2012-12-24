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

require_once ('DataAccessController.php');

/**
 * Controller providing CRUD API for the users JSON
 *
 * Receveive Ajax Calls for read & write from the UI to the Mongo DB
 *
 *
 * @author jbourdin
 * @category Rubedo
 * @package Rubedo
 *
 */
class Backoffice_UsersController extends Backoffice_DataAccessController {

	
	public function init(){
		parent::init();
		
		// init the data access service
		$this -> _dataService = Rubedo\Services\Manager::getService('Users');
	}

	public function changePasswordAction(){
		$hashService = \Rubedo\Services\Manager::getService('Hash');
		
		$password = $this -> getRequest() -> getParam('password');
		$id = $this -> getRequest() -> getParam('id');
		$version = $this -> getRequest() -> getParam('version');
		
		
		if (!empty($password) && !empty($id) && !empty($version)) {
			
			
			$result = $this->_dataService->changePassword($password,$version,$id);
			
			if($result == true){
				$message['success'] = true;
			} else{
				$message['success'] = false;
			}
			
			return $this->_helper->json($message);
		} else {
			$returnArray = array('success' => false, "msg" => 'No Data');
		}
		
		if (!$returnArray['success']) {
			$this -> getResponse() -> setHttpResponseCode(500);
		}
		
		return $this->_helper->json($returnArray);
	}

	

}

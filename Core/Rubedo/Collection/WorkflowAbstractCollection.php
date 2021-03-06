<?php
/**
 * Rubedo -- ECM solution
 * Copyright (c) 2013, WebTales (http://www.webtales.fr/).
 * All rights reserved.
 * licensing@webtales.fr
 *
 * Open Source License
 * ------------------------------------------------------------------------------------------
 * Rubedo is licensed under the terms of the Open Source GPL 3.0 license. 
 *
 * @category   Rubedo
 * @package    Rubedo
 * @copyright  Copyright (c) 2012-2013 WebTales (http://www.webtales.fr)
 * @license    http://www.gnu.org/licenses/gpl.html Open Source GPL 3.0 license
 */
namespace Rubedo\Collection;

use Rubedo\Interfaces\Collection\IWorkflowAbstractCollection, Rubedo\Services\Manager;


//require_once APPLICATION_PATH.'/../Core/Rubedo/Interfaces/Collection/IWorkflowAbstractCollection.php';

/**
 * Class implementing the API to MongoDB
 *
 * @author jbourdin
 * @category Rubedo
 * @package Rubedo
 */
abstract class WorkflowAbstractCollection extends AbstractCollection implements IWorkflowAbstractCollection
{
    

    protected function _init() {
        // init the data access service
        $this->_dataService = Manager::getService('MongoWorkflowDataAccess');
        $this->_dataService->init($this->_collectionName);
    }
	
	/**
     * Update an objet in the current collection
     *
     * @see \Rubedo\Interfaces\IDataAccess::update
     * @param array $obj data object
     * @param bool $options should we wait for a server response
     * @return array
     */
	public function update(array $obj, $options = array('safe'=>true), $live = true){
		
	    if($live === true){
			$this->_dataService->setLive();
		} else {
			$this->_dataService->setWorkspace();
		}
		
		$previousVersion = $this->findById($obj['id'],$live,false);
		$previousStatus = $previousVersion['status'];
		
		$returnArray = parent::update($obj, $options);
		if($returnArray['success']){
		    if(!$live){
		        $transitionResult = $this->_transitionEvent($returnArray['data'], $previousStatus);
		        if($transitionResult){
		            $returnArray = $transitionResult;
		        }
		    }
		} else {
			$returnArray = array('success' => false, 'msg' => 'failed to update');
		}
		
		return $returnArray;
	}
	
	/**
     * Create an objet in the current collection
     *
     * @see \Rubedo\Interfaces\IDataAccess::create
     * @param array $obj data object
     * @param bool $options should we wait for a server response
     * @return array
     */
    public function create(array $obj, $options = array('safe'=>true), $live = false) {
    	if($live === true){
    		throw new \Rubedo\Exceptions\Access('Can\'t create directly in live');
		}

		$this->_dataService->setWorkspace();
		
		
        $returnArray = parent::create($obj, $options);
		
		if($returnArray['success']){
			if($returnArray['data']['status'] === 'published'){
				$result = $this->publish($returnArray['data']['id']);
				
				if(!$result['success']){
					$returnArray['success'] = false;
					$returnArray['msg'] = "failed to publish the content";
					unset($returnArray['data']);
				}
			}
		} else {
			$returnArray = array('success' => false, 'msg' => 'failed to update');
		}
		
		return $returnArray;
    }
	
	/**
     * Find an item given by its literral ID
     * @param string $contentId
     * @return array
     */
    public function findById($contentId, $live = true,$raw = true) {
        if($live === true){
			$this->_dataService->setLive();
		} else {
			$this->_dataService->setWorkspace();
		}
		
        $obj = $this->_dataService->findById($contentId,$raw);
        if ($obj) {
            $obj = $this->_addReadableProperty($obj);
        }
		return $obj;
    }
    
    /* (non-PHPdoc)
     * @see \Rubedo\Collection\AbstractCollection::getList()
     */
    public function getList($filters = null, $sort = null, $start = null, $limit = null, $live = true) {
    	if($live === true){
			$this->_dataService->setLive();
		} else {
			$this->_dataService->setWorkspace();
		}
        $returnArray = parent::getList($filters, $sort, $start, $limit);
		
		return $returnArray;
    }
	
	/**
     * Find child of a node tree
     * @param string $parentId id of the parent node
     * @param array $filters array of data filters (mongo syntax)
     * @param array $sort  array of data sorts (mongo syntax)
     * @return array children array
     */
    public function readChild($parentId, $filters = null, $sort = null, $live = true) {
        if($live === true){
			$this->_dataService->setLive();
		} else {
			$this->_dataService->setWorkspace();
		}
		
        $returnArray = parent::readChild($parentId, $filters, $sort);
		
		return $returnArray;
    }
	
	public function publish($objectId) {
		return $this->_dataService->publish($objectId);
	}
	
	protected function _transitionEvent($obj,$previousStatus){
	       if($obj['status'] === 'published'){
	           $returnArray = array();
				$result = $this->publish($obj['id']);
				
				if(!$result['success']){
					$returnArray['success'] = false;
					$returnArray['msg'] = "failed to publish the content";
					unset($returnArray['data']);
				}
				
			}elseif($previousStatus!='pending' && $obj['status']=="pending"){
                $currentUser = Manager::getService('CurrentUser')->getCurrentUserSummary();
                $obj['lastPendingUser'] = $currentUser;
                $currentTime = Manager::getService('CurrentTime')->getCurrentTime();
                $obj['lastPendingTime'] = $currentTime;
			    $returnArray = parent::update($obj);
			    $this->_notify($obj,'pending');
			    
			}else{
			  $returnArray = null;
			}
			
			if($previousStatus == 'pending' && $obj['status'] == 'refused'){
			    $this->_notify($obj,'refused');
			}
			if($previousStatus == 'pending' && $obj['status'] == 'published'){
			    $this->_notify($obj,'published');
			}
			
			
			    
			return $returnArray;
	}
	
	protected function _notify($obj,$notificationType){
	    return Manager::getService('Notification')->notify($obj,$notificationType);
	}
	
	

}

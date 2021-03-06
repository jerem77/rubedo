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
require_once ('DataAccessController.php');

Use Rubedo\Services\Manager;

/**
 * Controller providing CRUD API for the Groups JSON
 *
 * Receveive Ajax Calls for read & write from the UI to the Mongo DB
 *
 *
 * @author jbourdin
 * @category Rubedo
 * @package Rubedo
 *         
 */
class Backoffice_DamController extends Backoffice_DataAccessController
{

    /**
     * Array with the read only actions
     */
    protected $_readOnlyAction = array(
        'index',
        'find-one',
        'read-child',
        'tree',
        'clear-orphan-terms',
        'model',
        'get-original-file',
        'get-thumbnail'
    );

    public function init ()
    {
        parent::init();
        
        // init the data access service
        $this->_dataService = Rubedo\Services\Manager::getService('Dam');
    }
    
    /*
     * (non-PHPdoc) @see Backoffice_DataAccessController::indexAction()
     */
    public function indexAction ()
    {
        // merge filter and tFilter
        $jsonFilter = $this->getParam('filter', Zend_Json::encode(array()));
        $jsonTFilter = $this->getParam('tFilter', Zend_Json::encode(array()));
        $filterArray = Zend_Json::decode($jsonFilter);
        $tFilterArray = Zend_Json::decode($jsonTFilter);
        $globalFilterArray = array_merge($tFilterArray, $filterArray);
        
        // call standard method with merge array
        $this->getRequest()->setParam('filter', Zend_Json::encode($globalFilterArray));
        parent::indexAction();
    }

    public function getThumbnailAction ()
    {
        $mediaId = $this->getParam('id', null);
        if (! $mediaId) {
            throw new \Rubedo\Exceptions\User('no id given');
        }
        $media = $this->_dataService->findById($mediaId);
        if (! $media) {
            throw new \Rubedo\Exceptions\NotFound('no media found');
        }
        $mediaType = Manager::getService('DamTypes')->findById($media['typeId']);
        if (! $mediaType) {
            throw new \Rubedo\Exceptions\Server('unknown media type');
        }
        if ($mediaType['mainFileType'] == 'Image') {
            $this->_forward('get-thumbnail', 'image', 'default', array(
                'file-id' => $media['originalFileId']
            ));
        } else {
            $this->_forward('get-thumbnail', 'file', 'default', array(
                'file-id' => $media['originalFileId']
            ));
        }
    }

    public function getOriginalFileAction ()
    {
        $mediaId = $this->getParam('id', null);
        if (! $mediaId) {
            throw new \Rubedo\Exceptions\User('no id given');
        }
        $media = $this->_dataService->findById($mediaId);
        if (! $media) {
            throw new \Rubedo\Exceptions\NotFound('no media found');
        }
        $mediaType = Manager::getService('DamTypes')->findById($media['typeId']);
        if (! $mediaType) {
            throw new \Rubedo\Exceptions\Server('unknown media type');
        }
        if ($mediaType['mainFileType'] == 'Image') {
            $this->_forward('index', 'image', 'default', array(
                'file-id' => $media['originalFileId']
            ));
        } else {
            $this->_forward('index', 'file', 'default', array(
                'file-id' => $media['originalFileId']
            ));
        }
    }

    public function createAction ()
    {
        $typeId = $this->getParam('typeId');
        if (! $typeId) {
            throw new \Rubedo\Exceptions\User('no type ID Given');
        }
        $damType = Manager::getService('DamTypes')->findById($typeId);
        if (! $damType) {
            throw new \Rubedo\Exceptions\Server('unknown type');
        }
        $obj['typeId'] = $damType['id'];
        $obj['mainFileType'] = $damType['mainFileType'];
        
        $title = $this->getParam('title');
        if (! $title) {
            throw new \Rubedo\Exceptions\User('missing title');
        }
        $obj['title'] = $title;
        $obj['fields']['title'] = $title;
        $obj['taxonomy'] = Zend_Json::decode($this->getParam('taxonomy', Zend_Json::encode(array())));
		
		$workspace = $this->getParam('writeWorkspace');
		if(!is_null($workspace) && $workspace != ""){
			$obj['writeWorkspace'] = $workspace;
			$obj['fields']['writeWorkspace'] = $workspace;
		}
		
		$targets = Zend_Json::decode($this->getRequest()->getParam('targetArray'));
		if(is_array($targets) && count($targets) > 0){
			$obj['target'] = $targets;
			$obj['fields']['target'] = $targets;
		}
        
        $fields = $damType['fields'];
        
        foreach ($fields as $field) {
            if ($field['cType'] == 'Ext.form.field.File') {
                continue;
            }
            $fieldConfig = $field['config'];
            $name = $fieldConfig['name'];
            $obj['fields'][$name] = $this->getParam($name);
            if (! $fieldConfig['allowBlank'] && ! $obj['fields'][$name]) {
                throw new \Rubedo\Exceptions\User('required field missing :' . $name);
            }
        }
        
        foreach ($fields as $field) {
            if ($field['cType'] !== 'Ext.form.field.File') {
                continue;
            }
            $fieldConfig = $field['config'];
            $name = $fieldConfig['name'];
            
			$uploadResult = $this->_uploadFile($name, $damType['mainFileType']);
			if(!is_array($uploadResult)){
				$obj['fields'][$name] = $uploadResult;
			} else {
				return $this->_returnJson($uploadResult);
			}
			
            if (! $fieldConfig['allowBlank'] && ! $obj['fields'][$name]) {
                throw new \Rubedo\Exceptions\User('required field missing :' . $name);
            }
        }
        
		$uploadResult = $this->_uploadFile('originalFileId', $damType['mainFileType']);
		if(!is_array($uploadResult)){
        	$obj['originalFileId'] = $uploadResult;
		} else {
			return $this->_returnJson($uploadResult);
		}
        
        $obj['Content-Type'] = $this->mimeType;
        
        if (! $obj['originalFileId']) {
            $this->getResponse()->setHttpResponseCode(500);
            return $this->_returnJson(array(
                'success' => false,
                'msg' => 'no main file uploaded'
            ));
        }

        $returnArray = $this->_dataService->create($obj);
        
        if (! $returnArray['success']) {
            $this->getResponse()->setHttpResponseCode(500);
        }
        // disable layout and set content type
        $this->getHelper('Layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
        
        $returnValue = Zend_Json::encode($returnArray);
        if ($this->_prettyJson) {
            $returnValue = Zend_Json::prettyPrint($returnValue);
        }
        $this->getResponse()->setBody($returnValue);
    }

    protected function _uploadFile ($name, $fileType)
    {
        $adapter = new Zend_File_Transfer_Adapter_Http();
        
        if (! $adapter->receive($name)) {
            return null;
        }
        
        $filesArray = $adapter->getFileInfo();
        
        $fileInfos = $filesArray[$name];
        
        $finfo = new finfo(FILEINFO_MIME);
        
        $mimeType = mime_content_type($fileInfos['tmp_name']);
        
        if ($name == 'originalFileId') {
            $this->mimeType = $mimeType;
        }
        
        $fileService = Manager::getService('Files');
        
        $fileObj = array(
            'serverFilename' => $fileInfos['tmp_name'],
            'text' => $fileInfos['name'],
            'filename' => $fileInfos['name'],
            'Content-Type' => isset($mimeType) ? $mimeType : $fileInfos['type'],
            'mainFileType' => $fileType
        );
        $result = $fileService->create($fileObj);
        if (! $result['success']) {
            return $result;
        }
        return $result['data']['id'];
    }
}

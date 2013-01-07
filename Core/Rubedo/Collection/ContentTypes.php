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
namespace Rubedo\Collection;

use Rubedo\Interfaces\Collection\IContentTypes;

/**
 * Service to handle ContentTypes
 *
 * @author jbourdin
 * @category Rubedo
 * @package Rubedo
 */
class ContentTypes extends AbstractCollection implements IContentTypes
{

    protected $_model = array(
        'type' => array(
            'domain' => 'name',
            'required' => true
        ),
        'dependant' => array(
            'domain' => 'bool',
            'required' => true
        ),
        'dependantTypes' => array(
            'domain' => 'list',
            'required' => true,
            'items' => array(
                'domain' => 'id',
                'required' => false
            )
        ),
        'fields' => array(
            'domain' => 'list',
            'required' => true,
            'items' => array(
                'domain' => 'array',
                'required' => false,
                'items' => array(
                    'cType' => array(
                        'domain' => 'string',
                        'required' => true
                    ),
                    'config' => array(
                        "name" => array(
                            'domain' => 'string',
                            'required' => true
                        ),
                        "fieldLabel" => array(
                            'domain' => 'string',
                            'required' => true
                        ),
                        "allowBlank" => array(
                            'domain' => 'bool',
                            'required' => false
                        ),
                        "localizable" => array(
                            'domain' => 'bool',
                            'required' => false
                        ),
                        "searchable" => array(
                            'domain' => 'bool',
                            'required' => false
                        ),
                        "multivalued" => array(
                            'domain' => 'bool',
                            'required' => false
                        ),
                        "tooltip" => array(
                            'domain' => 'string',
                            'required' => false
                        ),
                        "labelSeparator" => array(
                            'domain' => 'string',
                            'required' => false
                        )
                    )
                )
            )
        ),
        'vocabularies' => array(
            'domain' => 'list',
            'required' => true,
            'items' => array(
                'domain' => 'id',
                'required' => false
            )
        )
    );

    public function __construct ()
    {
        $this->_collectionName = 'ContentTypes';
        parent::__construct();
    }
    
    /*
     * (non-PHPdoc) @see \Rubedo\Collection\AbstractCollection::create()
     */
    public function create (array $obj, $options = array('safe'=>true), $live = true)
    {
        $returnArray = parent::create($obj, $options, $live);
        
        if ($returnArray["success"]) {
            $this->_indexContentType($returnArray['data']);
        }
        
        return $returnArray;
    }
    
    /*
     * (non-PHPdoc) @see \Rubedo\Collection\AbstractCollection::update()
     */
    public function update (array $obj, $options = array('safe'=>true), $live = true)
    {
        $returnArray = parent::update($obj, $options, $live);
        
        if ($returnArray["success"]) {
            $this->_indexContentType($returnArray['data']);
        }
        
        return $returnArray;
    }
    
    /*
     * (non-PHPdoc) @see \Rubedo\Collection\AbstractCollection::destroy()
     */
    public function destroy (array $obj, $options = array('safe'=>true))
    {
        $returnArray = parent::destroy($obj, $options);
        if ($returnArray["success"]) {
            $this->_unIndexContentType($obj);
        }
        return $returnArray;
    }

    /**
     * Push the content type to Elastic Search
     *
     * @param array $obj            
     */
    protected function _indexContentType ($obj)
    {
        $ElasticDataIndexService = \Rubedo\Services\Manager::getService('ElasticDataIndex');
        $ElasticDataIndexService->init();
        $ElasticDataIndexService->indexContentType($obj['id'], $obj, TRUE);
    }

    /**
     * Remove the content type from Indexed Search
     *
     * @param array $obj            
     */
    protected function _unIndexContentType ($obj)
    {
        $ElasticDataIndexService = \Rubedo\Services\Manager::getService('ElasticDataIndex');
        $ElasticDataIndexService->init();
        $ElasticDataIndexService->deleteContentType($obj['id'], TRUE);
    }

    /**
     * Find an item given by its name (find only one if many)
     *
     * @param string $name            
     * @return array
     */
    public function findByName ($name)
    {
        return $this->_dataService->findOne(array(
            'type' => $name
        ));
    }
}

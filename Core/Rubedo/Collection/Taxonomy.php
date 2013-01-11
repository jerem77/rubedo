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

use Rubedo\Interfaces\Collection\ITaxonomy;
use Rubedo\Services\Manager;

/**
 * Service to handle Taxonomy
 *
 * @author jbourdin
 * @category Rubedo
 * @package Rubedo
 */
class Taxonomy extends AbstractCollection implements ITaxonomy
{

    /**
     * a virtual taxonomy which reflects sites & pages trees
     *
     * @var array
     */
    protected $_virtualNavigationVocabulary = array(
        'id' => 'navigation',
        'name' => 'Navigation',
        'multiSelect' => true
    );

    public function __construct ()
    {
        $this->_collectionName = 'Taxonomy';
        parent::__construct();
    }

    /**
     * (non-PHPdoc) @see \Rubedo\Collection\AbstractCollection::getList()
     */
    public function getList ($filters = null, $sort = null, $start = null, $limit = null)
    {
        $list = parent::getList($filters, $sort, $start, $limit);
        $list['data'] = array_merge(array(
            $this->_virtualNavigationVocabulary
        ), $list['data']);
        $list['count'] ++;
        return $list;
    }

    /**
     * Find an item given by its name (find only one if many)
     *
     * @param string $name            
     * @return array
     */
    public function findByName ($name)
    {
        if ($name == 'navigation') {
            return $this->_virtualNavigationVocabulary;
        }
        return $this->_dataService->findOne(array(
            'name' => $name
        ));
    }

    public function destroy (array $obj, $options = array('safe'=>true))
    {
        if ($obj['id'] == 'navigation') {
            throw new \Exception('can\'t destroy navigation');
        }
        $childDelete = Manager::getService('TaxonomyTerms')->deleteByVocabularyId($obj['id']);
        if ($childDelete["ok"] == 1) {
            return parent::destroy($obj, $options);
        } else {
            return $childDelete;
        }
    }

    /**
     * (non-PHPdoc) @see \Rubedo\Collection\AbstractCollection::count()
     */
    public function count ($filters = null)
    {
        return parent::count($filters) + 1;
    }

    /**
     * (non-PHPdoc) @see \Rubedo\Collection\AbstractCollection::create()
     */
    public function create (array $obj, $options = array('safe'=>true,))
    {
        if ($obj['name'] == 'navigation') {
            throw new \Exception('can\'t create a navigation vocabulary');
        }
        return parent::create($obj, $options);
    }

    /**
     * (non-PHPdoc) @see \Rubedo\Collection\AbstractCollection::findById()
     */
    public function findById ($contentId)
    {
        if ($contentId == 'navigation') {
            return $this->_virtualNavigationVocabulary;
        } else {
            return parent::findById($contentId);
        }
    }

    /**
     * (non-PHPdoc) @see \Rubedo\Collection\AbstractCollection::update()
     */
    public function update (array $obj, $options = array('safe'=>true,))
    {
        if ($obj['id'] == 'navigation') {
            throw new \Exception('can\'t update navigation vocabulary');
        }
        if ($obj['name'] == 'navigation') {
            throw new \Exception('can\'t create a navigation vocabulary');
        }
        return parent::update($obj, $options);
    }
}

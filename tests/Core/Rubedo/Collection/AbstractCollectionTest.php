<?php
/**
 * Rubedo
 *
 * LICENSE
 *
 * yet to be written
 *
 * @category Rubedo-Test
 * @package Rubedo-Test
 * @copyright Copyright (c) 2012-2012 WebTales (http://www.webtales.fr)
 * @license yet to be written
 * @version $Id$
 */

Use Rubedo\Collection\AbstractCollection;

require_once('Rubedo/Interfaces/Collection/IAbstractCollection.php');
require_once('Rubedo/Collection/AbstractCollection.php');

class testCollection extends AbstractCollection {
    public function __construct() {
        $this->_collectionName = 'test';
        parent::__construct();
    }

}

/**
 * Test suite of the collection service :
 * @author jbourdin
 * @category Rubedo-Test
 * @package Rubedo-Test
 */
class AbstractCollectionTest extends PHPUnit_Framework_TestCase {
	/**
     * clear the DB of the previous test data
     */
    public function tearDown() {
        Rubedo\Services\Manager::resetMocks();
    }

    /**
     * init the Zend Application for tests
     */
    public function setUp() {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
		$this->bootstrap->bootstrap();
        $this->_mockDataAccessService = $this->getMock('Rubedo\\Mongo\\DataAccess');
        Rubedo\Services\Manager::setMockService('MongoDataAccess', $this->_mockDataAccessService);

        parent::setUp();
    }
	
	/**
	 * Test if getList call the read method only one time
	 */
	public function testNormalGetList(){
		$this->_mockDataAccessService->expects($this->once())->method('read');
		
		$collection = new testCollection();
		$collection->getList();
	}
	
	/**
	 * Test if getList method call addFilter when a filter is given in parameter
	 */
	public function testGetListWithFilter(){
		$this->_mockDataAccessService->expects($this->once())->method('read');
		$this->_mockDataAccessService->expects($this->once())->method('addFilter');
		
		$filter = array(array("property" => "test", "value" => "test"));
		
		$collection = new testCollection();
		$collection->getList($filter);
	}
	
	/**
	 * Test if getList method call addSort when a sort is given in parameter
	 */
	public function testGetListWithSort(){
		$this->_mockDataAccessService->expects($this->once())->method('read');
		$this->_mockDataAccessService->expects($this->once())->method('addSort');
		
		$sort = array(array("property" => "test", "direction" => "test"));
		
		$collection = new testCollection();
		$collection->getList(NULL, $sort);
	}
	
	/**
	 * Test if getList method call addFilter and addSort when a filter and a sort are given in parameters
	 */
	public function testGetListWithFilterAndSort(){
		$this->_mockDataAccessService->expects($this->once())->method('read');
		$this->_mockDataAccessService->expects($this->once())->method('addFilter');
		$this->_mockDataAccessService->expects($this->once())->method('addSort');
		
		$filter = array(array("property" => "test", "value" => "test"));
		$sort = array(array("property" => "test", "direction" => "test"));
		
		$collection = new testCollection();
		$collection->getList($filter, $sort);
	}
	
	/**
	 * Test if findById method call the findById method only one time
	 */
	public function testNormalFindById(){
		$this->_mockDataAccessService->expects($this->once())->method('findById');
		
		$id = 'test';
		
		$collection = new testCollection();
		$collection->findById($id);
	}
	
	/**
	 * Test if create method call the create method only one time
	 */
	public function testNormalCreate(){
		$this->_mockDataAccessService->expects($this->once())->method('create');
		
		$obj = array('key' => 'value');
		
		$collection = new testCollection();
		$collection->create($obj, true);
	}
	
	/**
	 * Test if update method call the update method only one time
	 */
	public function testNormalUpdate(){
		$this->_mockDataAccessService->expects($this->once())->method('update');
		
		$obj = array('key' => 'value');
		
		$collection = new testCollection();
		$collection->update($obj, true);
	}
	
	/**
	 * Test if destroy method call the destroy method only one time
	 */
	public function testNormalDestroy(){
		$this->_mockDataAccessService->expects($this->once())->method('destroy');
		
		$obj = array('key' => 'value');
		
		$collection = new testCollection();
		$collection->destroy($obj, true);
	}
	
	/**
	 * Test if readChild method call the readChild method only one time
	 */
	public function testNormalReadChild(){
		$this->_mockDataAccessService->expects($this->once())->method('readChild');
		
		$parentId = '123456798';
		
		$collection = new testCollection();
		$collection->readChild($parentId);
	}
	
	/**
	 * Test if readChild method call addFilter method when a filter is given in parameter
	 */
	public function testReadChildWithFilter(){
		$this->_mockDataAccessService->expects($this->once())->method('readChild');
		$this->_mockDataAccessService->expects($this->once())->method('addFilter');
		
		$parentId = '123456798';
		$filter = array(array("property" => "test", "value" => "test"));
		
		$collection = new testCollection();
		$collection->readChild($parentId, $filter);
	}
	
	/**
	 * Test if readChild method call addSort method when a sort is given in parameter
	 */
	public function testReadChildWithSort(){
		$this->_mockDataAccessService->expects($this->once())->method('readChild');
		$this->_mockDataAccessService->expects($this->once())->method('addSort');
		
		$parentId = '123456798';
		$sort = array(array("property" => "test", "direction" => "test"));
		
		$collection = new testCollection();
		$collection->readChild($parentId, NULL, $sort);
	}
	
	/**
	 * Test if readChild method call addFilter and addSort methods when a filter and a sort are given in parameters
	 */
	public function testReadChildWithFilterAndSort(){
		$this->_mockDataAccessService->expects($this->once())->method('readChild');
		$this->_mockDataAccessService->expects($this->once())->method('addFilter');
		$this->_mockDataAccessService->expects($this->once())->method('addSort');
		
		$parentId = '123456798';
		$filter = array(array("property" => "test", "value" => "test"));
		$sort = array(array("property" => "test", "direction" => "test"));
		
		$collection = new testCollection();
		$collection->readChild($parentId, $filter, $sort);
	}
}
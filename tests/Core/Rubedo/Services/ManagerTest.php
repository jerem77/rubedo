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

/**
 * Mock Service Interface for unit test
 */
interface ITestService
{
    public function fakeMethod();

}

/**
 * Mock Valid Service Class for testing purpose
 */
class TestService implements ITestService
{
    public function fakeMethod()
    {
        return 42;
    }

}

/**
 * Tests suite for the service manager
 *
 *
 * @author jbourdin
 * @category Rubedo-Test
 * @package Rubedo-Test
 */
class ManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Init
     */
    public function setUp()
    {
        testBootstrap();
        parent::setUp();
    }

    /**
     * Cleaning
     */
    public function tearDown()
    {
        Rubedo\Services\Manager::setOptions(array());
        Rubedo\Interfaces\config::clearInterfaces();
        parent::tearDown();
    }

    /**
     * Test if setOptions correctly set an array of options
     */
    public function testConformOptions()
    {
        $options = array('fakeService' => array('fakeOptions1' => true, 'fakeOptions2' => 'value2'));
        \Rubedo\Services\Manager::setOptions($options);
        $this->assertAttributeEquals($options, '_servicesOptions', '\\Rubedo\\Services\\Manager');
        $this->assertEquals($options, \Rubedo\Services\Manager::getOptions());
    }

    /**
     * Test if setOptions correctly throw exception if params isn't an array
     *
     * @expectedException \Rubedo\Exceptions\ServiceManager
     */
    public function testMalformedOptions()
    {
        $options = 'nonArrayInput';
        Rubedo\Services\Manager::setOptions($options);
    }

    /**
     * Normal getService Result
     */
    public function testValidGetService()
    {
        $options = array('TestService' => array('class' => 'TestService'));
        Rubedo\Services\Manager::setOptions($options);
        Rubedo\Interfaces\config::addInterface('TestService', 'ITestService');

        $service = \Rubedo\Services\Manager::getService('TestService');
        $this->assertInstanceOf('\\Rubedo\\Services\\Proxy', $service);
        $this->assertAttributeInstanceOf('TestService', '_object', $service);
    }

    /**
     * GetService Exception if called without a string param
     *
     * @expectedException \Rubedo\Exceptions\ServiceManager
     */
    public function testNonStringGetService()
    {

        $service = \Rubedo\Services\Manager::getService(666);
    }

    /**
     *GetService Exception if called with an unknown serviceName
     *
     * @expectedException \Rubedo\Exceptions\ServiceManager
     */
    public function testNonDeclaredServiceGetService()
    {

        $service = \Rubedo\Services\Manager::getService('TestService');
    }

    /**
     * GetService Exception if called without an undeclared interface
     *
     * @expectedException \Rubedo\Exceptions\ServiceManager
     */
    public function testNonDeclaredInterfaceGetService()
    {
        $classname = $this->getMockClass('TestService');
        $options = array('TestService' => array('class' => $classname));
        \Rubedo\Services\Manager::setOptions($options);
        $service = \Rubedo\Services\Manager::getService('TestService');
    }

    /**
     * GetService Exception if the service class do not implement the service class
     *
     * @expectedException \Rubedo\Exceptions\ServiceManager
     */
    public function testDontImplementdInterfaceGetService()
    {

        $options = array('TestService' => array('class' => 'stdClass'));
        Rubedo\Services\Manager::setOptions($options);
        Rubedo\Interfaces\config::addInterface('TestServiceNoInterface', 'ITestService');

        $service = \Rubedo\Services\Manager::getService('TestService');
    }

}

<?php
/**
 * @license See the file LICENSE for copying permission.
 */

namespace Soflomo\Purifier\Test;

use HTMLPurifier;
use HTMLPurifier_Config;
use PHPUnit_Framework_TestCase as TestCase;
use Soflomo\Purifier;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Application;
use Zend\ServiceManager\ServiceManager;

class ModuleIntegrationTest extends TestCase
{
    /**
     * @var array
     */
    protected $appConfig;

    protected function setUp()
    {
        $this->serviceManager = new ServiceManager();
        $this->appConfig      = [
            'modules' => [
                'Soflomo\Purifier',
                'Zend\Router',
                'Zend\Filter',
                'Zend\InputFilter',
            ],
            'module_listener_options' => [],
        ];
    }

    public function testCanLoadModule()
    {
        $app           = Application::init($this->appConfig);
        $loadedModules = $app->getServiceManager()->get('ModuleManager')->getLoadedModules();
        $this->assertArrayHasKey('Soflomo\Purifier', $loadedModules);
        $this->assertInstanceOf(Purifier\Module::class, $loadedModules['Soflomo\Purifier']);
    }

    public function testServicesAreRegistered()
    {
        $app            = Application::init($this->appConfig);
        $serviceManager = $app->getServiceManager();

        $this->assertTrue($serviceManager->has(HTMLPurifier_Config::class));
        $this->assertTrue($serviceManager->has(HTMLPurifier::class));

        $htmlPurifierConfig = $serviceManager->get(HTMLPurifier_Config::class);
        $this->assertInstanceOf(HTMLPurifier_Config::class, $htmlPurifierConfig);

        $htmlPurifier = $serviceManager->get(HTMLPurifier::class);
        $this->assertInstanceOf(HTMLPurifier::class, $htmlPurifier);
    }

    public function testFilterIsRegistered()
    {
        $app           = Application::init($this->appConfig);
        $filterManager = $app->getServiceManager()->get('FilterManager');

        $this->assertTrue($filterManager->has(Purifier\PurifierFilter::class));
        $this->assertTrue($filterManager->has('htmlpurifier'));

        $purifierFilter = $filterManager->get(Purifier\PurifierFilter::class);

        $this->assertInstanceOf(Purifier\PurifierFilter::class, $purifierFilter);
        $this->assertEquals($purifierFilter, $filterManager->get('htmlpurifier'));
    }

    public function testViewHelperIsRegistered()
    {
        $app               = Application::init($this->appConfig);
        $viewHelperManager = $app->getServiceManager()->get('ViewHelperManager');

        $this->assertTrue($viewHelperManager->has(Purifier\PurifierViewHelper::class));
        $this->assertTrue($viewHelperManager->has('htmlPurifier'));

        $purifierViewHelper = $viewHelperManager->get(Purifier\PurifierViewHelper::class);

        $this->assertInstanceOf(Purifier\PurifierViewHelper::class, $purifierViewHelper);
        $this->assertEquals($purifierViewHelper, $viewHelperManager->get('htmlPurifier'));
    }

    public function testFilterConfigCanBeInitializedByZendInputFilterFactory()
    {
        $app         = Application::init($this->appConfig);
        $inputFilter = new InputFilter();
        $app->getServiceManager()->get('InputFilterManager')->populateFactory($inputFilter, null);

        $config = [ 'HTML.AllowedElements' => 'a' ];

        $inputFilter->add([
            'name'    => 'test',
            'filters' => [
                [
                    'name'    => 'htmlpurifier',
                    'options' => [
                        'config' => $config,
                    ],
                ],
            ],
        ]);

        /** @var Purifier\PurifierFilter $filter */
        $filter = $inputFilter->get('test')->getFilterChain()->getFilters()->top();

        $this->assertInstanceOf(Purifier\PurifierFilter::class, $filter);
        $this->assertEquals($config, $filter->getConfig());
    }
}

<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE for copying permission.
 * ************************************************
 */

namespace Soflomo\Purifier\Test\Factory;

use PHPUnit_Framework_TestCase as TestCase;
use Soflomo\Purifier\Factory\HtmlPurifierFactory;
use VirtualFileSystem\FileSystem;
use Zend\ServiceManager\ServiceManager;

class HtmlPurifierFactoryTest extends TestCase
{
    /**
     * @var HtmlPurifierFactory
     */
    protected $factory;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    protected function setUp()
    {
        $this->factory        = new HtmlPurifierFactory();
        $this->serviceManager = new ServiceManager();
    }

    public function testStandaloneFileInclusion()
    {
        $this->setConfigService(array(
            'soflomo_purifier' => array(
                'standalone' => true,
                'standalone_path' => './tests/_files/standalone_mock.php',
            )
        ));

        $this->factory->createService($this->serviceManager);

        $this->assertTrue(class_exists('StandaloneMock', false));
    }

    public function testFactoryThrowsExceptionIfStandaloneFileNotFound()
    {
        $this->setConfigService(array(
            'soflomo_purifier' => array(
                'standalone' => true,
                'standalone_path' => 'bogus',
            )
        ));

        $this->setExpectedException('RuntimeException', 'Could not find standalone purifier file');
        $this->factory->createService($this->serviceManager);
    }

    public function testFactoryCanSetDefinitions()
    {
        $validAttributes= array('foo','bar','baz','bat');

        $this->setConfigService(array(
            'soflomo_purifier' => array(
                'standalone' => false,
                'config' => array(
                    'HTML.DefinitionID' => 'custom definitions',
                    'Cache.DefinitionImpl' => null,
                ),
                'definitions' => array(
                    'HTML' => array(
                        'addAttribute' => array('a', 'foo', new \HTMLPurifier_AttrDef_Enum($validAttributes)),
                    ),
                ),
            )
        ));

        /** @var \HTMLPurifier $purifier */
        $purifier = $this->factory->createService($this->serviceManager);

        /** @var \HTMLPurifier_HTMLDefinition $definition */
        $definition = $purifier->config->getDefinition('HTML');
        $this->assertInstanceOf('HTMLPurifier_HTMLDefinition', $definition);

        /** @var \HTMLPurifier_ElementDef $elementDefinition */
        $elementDefinition = $definition->info['a'];
        $this->assertInstanceOf('HTMLPurifier_ElementDef', $elementDefinition);

        /** @var \HTMLPurifier_AttrDef_Enum $attributeDefinition */
        $attributeDefinition = $elementDefinition->attr['foo'];
        $this->assertInstanceOf('HTMLPurifier_AttrDef_Enum', $attributeDefinition);

        foreach($validAttributes as $value) {
            $this->assertArrayHasKey($value, $attributeDefinition->valid_values);
        }
    }

    public function testDefinitionCache()
    {
        $fileSystem = new FileSystem();
        $cacheDir   = $fileSystem->path('cache');
        mkdir($cacheDir);

        $this->setConfigService(array(
            'soflomo_purifier' => array(
                'standalone' => false,
                'config' => array(
                    'HTML.DefinitionID' => 'custom definitions',
                    'Cache.SerializerPath' => $cacheDir
                ),
                'definitions' => array(
                    'HTML' => array(
                        'addAttribute' => array('a', 'foo', new \HTMLPurifier_AttrDef_Enum(array('asd'))),
                    ),
                ),
            )
        ));

        // create the purifier and get the definition a first time to warm up the cache
        $purifier = $this->factory->createService($this->serviceManager);
        $purifier->config->getDefinition('HTML');

        $this->assertTrue(is_dir($cacheDir . '/HTML'));

        $cacheFilesNum = 0;
        $cacheDirHandle = opendir($cacheDir);
        while(readdir($cacheDirHandle) !== false) {
            $cacheFilesNum++;
        }
        $this->assertGreaterThan(0, $cacheFilesNum);

        // now repeat leaving out the definition config
        $this->serviceManager = new ServiceManager();
        $this->setConfigService(array(
            'soflomo_purifier' => array(
                'standalone' => false,
                'config' => array(
                    'HTML.DefinitionID' => 'custom definitions',
                    'Cache.SerializerPath' => $cacheDir
                ),
            )
        ));

        $purifier = $this->factory->createService($this->serviceManager);

        /** @var \HTMLPurifier_HTMLDefinition $definition */
        $definition = $purifier->config->getDefinition('HTML');
        $this->assertInstanceOf('HTMLPurifier_HTMLDefinition', $definition);

        /** @var \HTMLPurifier_ElementDef $elementDefinition */
        $elementDefinition = $definition->info['a'];
        $this->assertInstanceOf('HTMLPurifier_ElementDef', $elementDefinition);

        /** @var \HTMLPurifier_AttrDef_Enum $attributeDefinition */
        $attributeDefinition = $elementDefinition->attr['foo'];
        $this->assertInstanceOf('HTMLPurifier_AttrDef_Enum', $attributeDefinition);
    }

    protected function setConfigService($array)
    {
        $this->serviceManager->setService('config', $array);
    }
}

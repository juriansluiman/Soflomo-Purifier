<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE for copying permission.
 * ************************************************
 */

namespace Soflomo\Purifier\Test\Factory;

use HTMLPurifier_AttrDef_Enum;
use HTMLPurifier_Config;
use HTMLPurifier_ElementDef;
use HTMLPurifier_HTMLDefinition;
use PHPUnit_Framework_TestCase as TestCase;
use Soflomo\Purifier\Factory\HtmlPurifierConfigFactory;
use VirtualFileSystem\FileSystem;
use Zend\ServiceManager\ServiceManager;

class HtmlPurifierConfigFactoryTest extends TestCase
{
    /**
     * @var HtmlPurifierConfigFactory
     */
    protected $factory;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    protected function setUp()
    {
        $this->factory        = new HtmlPurifierConfigFactory();
        $this->serviceManager = new ServiceManager();
    }

    public function testStandaloneFileInclusion()
    {
        $this->setConfigService([
            'soflomo_purifier' => [
                'standalone' => true,
                'standalone_path' => './tests/_files/standalone_mock.php',
            ]
        ]);

        $this->factory->__invoke($this->serviceManager);

        $this->assertTrue(class_exists('StandaloneMock', false));
    }

    public function testFactoryThrowsExceptionIfStandaloneFileNotFound()
    {
        $this->setConfigService([
            'soflomo_purifier' => [
                'standalone' => true,
                'standalone_path' => 'bogus',
            ]
        ]);

        $this->setExpectedException('RuntimeException', 'Could not find standalone purifier file');
        $this->factory->__invoke($this->serviceManager);
    }

    public function testFactoryCanSetDefinitions()
    {
        $validAttributes= ['foo','bar','baz','bat'];

        $this->setConfigService([
            'soflomo_purifier' => [
                'standalone' => false,
                'config' => [
                    'HTML.DefinitionID' => 'custom definitions',
                    'Cache.DefinitionImpl' => null,
                    'definitions' => [
                        'HTML' => [
                            'addAttribute' => ['a', 'foo', new HTMLPurifier_AttrDef_Enum($validAttributes)],
                        ],
                    ],
                ],
            ]
        ]);

        /** @var HTMLPurifier_Config $purifier */
        $purifierConfig = $this->factory->__invoke($this->serviceManager);

        /** @var HTMLPurifier_HTMLDefinition $definition */
        $definition = $purifierConfig->getDefinition('HTML');
        $this->assertInstanceOf('HTMLPurifier_HTMLDefinition', $definition);

        /** @var HTMLPurifier_ElementDef $elementDefinition */
        $elementDefinition = $definition->info['a'];
        $this->assertInstanceOf('HTMLPurifier_ElementDef', $elementDefinition);

        /** @var HTMLPurifier_AttrDef_Enum $attributeDefinition */
        $attributeDefinition = $elementDefinition->attr['foo'];
        $this->assertInstanceOf('HTMLPurifier_AttrDef_Enum', $attributeDefinition);

        foreach ($validAttributes as $value) {
            $this->assertArrayHasKey($value, $attributeDefinition->valid_values);
        }
    }

    public function testDefinitionCache()
    {
        $fileSystem = new FileSystem();
        $cacheDir   = $fileSystem->path('cache');
        mkdir($cacheDir);

        $this->setConfigService([
            'soflomo_purifier' => [
                'standalone' => false,
                'config' => [
                    'HTML.DefinitionID' => 'custom definitions',
                    'Cache.SerializerPath' => $cacheDir,
                    'definitions' => [
                        'HTML' => [
                            'addAttribute' => ['a', 'foo', new HTMLPurifier_AttrDef_Enum(['asd'])],
                        ],
                    ],
                ],
            ]
        ]);

        // create the purifier config and get the definition a first time to warm up the cache
        $purifierConfig = $this->factory->__invoke($this->serviceManager);
        $purifierConfig->getDefinition('HTML');

        $this->assertTrue(is_dir($cacheDir . '/HTML'));

        $cacheFilesNum = 0;
        $cacheDirHandle = opendir($cacheDir);
        while (readdir($cacheDirHandle) !== false) {
            $cacheFilesNum++;
        }
        $this->assertGreaterThan(0, $cacheFilesNum);

        // now repeat leaving out the definition config
        $this->serviceManager = new ServiceManager();
        $this->setConfigService([
            'soflomo_purifier' => [
                'standalone' => false,
                'config' => [
                    'HTML.DefinitionID' => 'custom definitions',
                    'Cache.SerializerPath' => $cacheDir
                ],
            ]
        ]);

        $purifierConfig = $this->factory->__invoke($this->serviceManager);

        /** @var HTMLPurifier_HTMLDefinition $definition */
        $definition = $purifierConfig->getDefinition('HTML');
        $this->assertInstanceOf('HTMLPurifier_HTMLDefinition', $definition);

        /** @var HTMLPurifier_ElementDef $elementDefinition */
        $elementDefinition = $definition->info['a'];
        $this->assertInstanceOf('HTMLPurifier_ElementDef', $elementDefinition);

        /** @var HTMLPurifier_AttrDef_Enum $attributeDefinition */
        $attributeDefinition = $elementDefinition->attr['foo'];
        $this->assertInstanceOf('HTMLPurifier_AttrDef_Enum', $attributeDefinition);
    }

    protected function setConfigService($array)
    {
        $this->serviceManager->setService('config', $array);
    }
}

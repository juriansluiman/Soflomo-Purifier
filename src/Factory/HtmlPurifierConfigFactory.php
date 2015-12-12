<?php
/**
 * @author  Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace Soflomo\Purifier\Factory;

use HTMLPurifier_Config;
use RuntimeException;
use Zend\ServiceManager\ServiceLocatorInterface;

class HtmlPurifierConfigFactory
{
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        $configService = $serviceLocator->get('config');
        $moduleConfig  = isset($configService['soflomo_purifier']) ? $configService['soflomo_purifier'] : [];

        if ($moduleConfig['standalone']) {
            if (! file_exists($moduleConfig['standalone_path'])) {
                throw new RuntimeException('Could not find standalone purifier file');
            }

            include_once $moduleConfig['standalone_path'];
        }

        $config = isset($moduleConfig['config']) ? $moduleConfig['config'] : [ ];
        $purifierConfig = self::createConfig($config);

        return $purifierConfig;
    }

    /**
     * @param array $config
     *
     * @return HTMLPurifier_Config
     * @throws \HTMLPurifier_Exception
     */
    public static function createConfig(array $config)
    {
        $purifierConfig = HTMLPurifier_Config::createDefault();

        if (isset($config['definitions'])) {
            $definitions  = $config['definitions'];
            unset($config['definitions']);
        } else {
            $definitions = [];
        }

        foreach ($config as $key => $value) {
            $purifierConfig->set($key, $value);
        }

        foreach ($definitions as $type => $methods) {
            $definition = $purifierConfig->getDefinition($type, true, true);

            if (! $definition) {
                // definition is cached, skip iteration
                continue;
            }

            foreach ($methods as $method => $args) {
                call_user_func_array([ $definition, $method ], $args);
            }
        }

        return $purifierConfig;
    }
}

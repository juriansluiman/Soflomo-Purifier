<?php
/**
 * @license See the file LICENSE for copying permission.
 */

namespace Soflomo\Purifier\Factory;

use HTMLPurifier_Config;
use HTMLPurifier_Exception;
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

        $config      = isset($moduleConfig['config']) ? $moduleConfig['config'] : [ ];
        $definitions = isset($moduleConfig['definitions']) ? $moduleConfig['definitions'] : [ ];

        $purifierConfig = self::createConfig($config, $definitions);

        return $purifierConfig;
    }

    /**
     * @param array $config
     * @param array $definitions
     *
     * @throws HTMLPurifier_Exception
     *
     * @return HTMLPurifier_Config
     */
    public static function createConfig(array $config, array $definitions = [])
    {
        $purifierConfig = HTMLPurifier_Config::create($config);

        foreach ($definitions as $type => $methods) {
            $definition = $purifierConfig->getDefinition($type, true, true);

            if (! $definition) {
                // definition is cached, skip iteration
                continue;
            }

            foreach ($methods as $method => $invocations) {
                $invocations = self::convertSingleInvocationToArray($invocations);
                foreach ($invocations as $args) {
                    call_user_func_array([ $definition, $method ], $args);
                }
            }
        }

        return $purifierConfig;
    }

    /**
     * @param array $invocations
     *
     * @return array[]
     */
    private static function convertSingleInvocationToArray(array $invocations)
    {
        if (count($invocations) !== 3) {
            return $invocations;
        }

        $allArgumentsAreArray = array_reduce($invocations, function ($carry, $value) {
            return is_array($value) && $carry;
        }, true);

        if ($allArgumentsAreArray) {
            return $invocations;
        }

        return [ $invocations ];
    }
}

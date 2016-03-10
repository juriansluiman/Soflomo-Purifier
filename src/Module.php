<?php
/**
 * @license See the file LICENSE for copying permission.
 */

namespace Soflomo\Purifier;

use Zend\ModuleManager\Feature;

class Module implements Feature\ConfigProviderInterface
{
    const VERSION = '1.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}

<?php
/**
 * @license See the file LICENSE for copying permission.
 */

namespace Soflomo\Purifier\Factory;

use Interop\Container\ContainerInterface;
use Soflomo\Purifier\PurifierViewHelper;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurifierViewHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $htmlPurifier = $container->get('HTMLPurifier');

        return new PurifierViewHelper($htmlPurifier);
    }
}

<?php
/**
 * @license See the file LICENSE for copying permission.
 */

namespace Soflomo\Purifier\Factory;

use Interop\Container\ContainerInterface; 
use Soflomo\Purifier\PurifierFilter;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurifierFilterFactory
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $htmlPurifier = $container->get('HTMLPurifier');

        return new PurifierFilter($htmlPurifier, $options);
    }
}

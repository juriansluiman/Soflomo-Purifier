<?php
/**
 * @license See the file LICENSE for copying permission.
 */

namespace Soflomo\Purifier\Factory;

use Soflomo\Purifier\PurifierFilter;
use Zend\Filter\FilterPluginManager;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\MutableCreationOptionsTrait;

class PurifierFilterFactory implements MutableCreationOptionsInterface
{
    use MutableCreationOptionsTrait;

    public function __invoke(FilterPluginManager $filterPluginManager)
    {
        $htmlPurifier = $filterPluginManager->getServiceLocator()->get('HTMLPurifier');

        return new PurifierFilter($htmlPurifier, $this->getCreationOptions());
    }
}

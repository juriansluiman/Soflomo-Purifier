<?php
/**
 * @author  Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace Soflomo\Purifier\Factory;

use Soflomo\Purifier\PurifierFilter;
use Zend\Filter\FilterPluginManager;

class PurifierFilterFactory
{
    public function __invoke(FilterPluginManager $filterPluginManager)
    {
        $htmlPurifier = $filterPluginManager->getServiceLocator()->get('HTMLPurifier');

        return new PurifierFilter($htmlPurifier);
    }
}

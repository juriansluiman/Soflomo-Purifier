<?php
/**
 * @license See the file LICENSE for copying permission.
 */

namespace Soflomo\Purifier\Factory;

use HTMLPurifier;
use HTMLPurifier_Config;
use Zend\ServiceManager\ServiceLocatorInterface;

class HtmlPurifierFactory
{
    /**
     * {@inheritdocs}
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        $purifierConfig = $serviceLocator->get(HTMLPurifier_Config::class);
        $purifier = new HTMLPurifier($purifierConfig);

        return $purifier;
    }
}

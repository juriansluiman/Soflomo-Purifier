<?php
/**
 * @license See the file LICENSE for copying permission.
 */

namespace Soflomo\Purifier\Factory;

use Soflomo\Purifier\PurifierViewHelper;
use Zend\View\HelperPluginManager;

class PurifierViewHelperFactory
{
    public function __invoke(HelperPluginManager $helperPluginManager)
    {
        $htmlPurifier = $helperPluginManager->getServiceLocator()->get('HTMLPurifier');

        return new PurifierViewHelper($htmlPurifier);
    }
}

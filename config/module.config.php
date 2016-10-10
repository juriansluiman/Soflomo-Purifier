<?php
/**
 * @license See the file LICENSE for copying permission.
 */

namespace Soflomo\Purifier;

use HTMLPurifier;
use HTMLPurifier_Config;

return [
    'soflomo_purifier' => [
        'standalone' => false,
        'standalone_path' => 'vendor/ezyang/htmlpurifier/library/HTMLPurifier.standalone.php',
    ],

    'service_manager' => [
        'factories' => [
            HTMLPurifier::class => Factory\HtmlPurifierFactory::class,
            HTMLPurifier_Config::class => Factory\HtmlPurifierConfigFactory::class,
        ],
    ],

    'filters' => [
        'factories' => [
            PurifierFilter::class => Factory\PurifierFilterFactory::class,
        ],
        'aliases' => [
            PurifierFilter::ALIAS => PurifierFilter::class,
        ],
    ],

    'view_helpers' => [
        'factories' => [
            PurifierViewHelper::class => Factory\PurifierViewHelperFactory::class,
        ],
        'aliases' => [
            PurifierViewHelper::ALIAS => PurifierViewHelper::class,
        ],
    ],
];

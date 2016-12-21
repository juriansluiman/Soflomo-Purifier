<?php
/**
 * @license See the file LICENSE for copying permission.
 */

namespace Soflomo\Purifier;

use HTMLPurifier;
use Soflomo\Purifier\Factory\HtmlPurifierConfigFactory;
use Traversable;
use Zend\Filter\AbstractFilter;
use Zend\Filter\FilterInterface;

class PurifierFilter extends AbstractFilter implements FilterInterface
{
    const ALIAS = 'htmlpurifier';

    /**
     * @var HTMLPurifier
     */
    protected $purifier;

    /**
     * @var array
     */
    protected $options = [
        'config'      => [],
        'definitions' => [],
    ];

    /**
     * PurifierFilter constructor.
     *
     * @param HTMLPurifier      $purifier
     * @param array|Traversable $options
     */
    public function __construct(HTMLPurifier $purifier, $options = [])
    {
        $this->purifier = $purifier;

        if (! empty($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * {@inheritdocs}
     */
    public function filter($value)
    {
        $configArray = $this->getConfig();

        if (! empty($configArray)) {
            $config = HtmlPurifierConfigFactory::createConfig($configArray, $this->getDefinitions());

            return $this->purifier->purify($value, $config);
        }

        return $this->purifier->purify($value);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->options['config'];
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->options['config'] = $config;
    }

    /**
     * @return array
     */
    public function getDefinitions()
    {
        return $this->options['definitions'];
    }

    /**
     * @param array $definitions
     */
    public function setDefinitions(array $definitions)
    {
        $this->options['definitions'] = $definitions;
    }
}

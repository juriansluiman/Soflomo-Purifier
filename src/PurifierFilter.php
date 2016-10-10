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
        'purifier_config' => [],
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

        if (!empty($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * {@inheritdocs}
     */
    public function filter($value)
    {
        $configArray = $this->getPurifierConfig();

        if (empty($configArray)) {
            return $this->purifier->purify($value);
        }

        $purifierConfig = HtmlPurifierConfigFactory::createConfig($configArray);

        return $this->purifier->purify($value, $purifierConfig);
    }

    /**
     * @return array
     */
    public function getPurifierConfig()
    {
        return $this->options['purifier_config'];
    }

    /**
     * @param array $config
     */
    public function setPurifierConfig(array $config)
    {
        $this->options['purifier_config'] = $config;
    }
}

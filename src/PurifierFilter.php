<?php

namespace Soflomo\Purifier;

use HTMLPurifier;
use Zend\Filter\FilterInterface;

class PurifierFilter implements FilterInterface
{
    const ALIAS = 'htmlpurifier';

    /**
     * @var HTMLPurifier
     */
    protected $purifier;

    public function __construct(HTMLPurifier $purifier)
    {
        $this->purifier = $purifier;
    }

    /**
     * {@inheritdocs}
     */
    public function filter($value)
    {
        return $this->purifier->purify($value);
    }
}

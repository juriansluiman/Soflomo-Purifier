<?php
/**
 * @license See the file LICENSE for copying permission.
 */

namespace Soflomo\Purifier;

use HTMLPurifier;
use Zend\View\Helper\AbstractHelper;

class PurifierViewHelper extends AbstractHelper
{
    const ALIAS = 'htmlPurifier';

    /**
     * @var HTMLPurifier
     */
    protected $purifier;

    public function __construct(HTMLPurifier $purifier)
    {
        $this->purifier = $purifier;
    }

    public function __invoke($html = null)
    {
        if (null === $html) {
            return $this;
        }

        return $this->purify($html);
    }

    public function purify($html)
    {
        return $this->purifier->purify($html);
    }
}

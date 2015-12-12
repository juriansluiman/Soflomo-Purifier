<?php
/**
 * @author  Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE for copying permission.
 * ************************************************
 */

namespace Soflomo\Purifier\Test;

use HTMLPurifier;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Soflomo\Purifier\Factory\PurifierFilterFactory;
use Soflomo\Purifier\PurifierFilter;
use Zend\InputFilter\Factory;
use Zend\ServiceManager\ServiceManager;

class PurifierFilterTest extends TestCase
{
    /**
     * @var HTMLPurifier|MockObject
     */
    private $htmlPurifier;

    /**
     * @var PurifierFilter
     */
    private $filter;

    public function setUp()
    {
        $this->htmlPurifier = new HTMLPurifier();
        $this->filter = new PurifierFilter($this->htmlPurifier);
    }

    public function testFilterWithCustomConfig()
    {
        $value = '<p><a>fo<script>alert();</script>obar</a></p>';

        $this->assertSame('<p><a>foobar</a></p>', $this->filter->filter($value));

        $this->filter->setPurifierConfig([
            'HTML.AllowedElements' => 'a'
        ]);

        $this->assertSame('<a>foobar</a>', $this->filter->filter($value));
    }

    public function testOptionsCanBeInitializedWithConstructor()
    {
        $options = [ 'purifier_config' => [ 'HTML.AllowedElements' => 'a' ] ];
        $filter = new PurifierFilter($this->htmlPurifier, $options);
        $this->assertEquals($options, $filter->getOptions());
    }
}

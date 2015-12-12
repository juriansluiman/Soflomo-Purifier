# Soflomo\Purifier

Soflomo\Purifier is the [HTMLPurifier](http://htmlpurifier.org/) integration for Zend Framework 2.

It provides a `Zend\Filter\FilterInterface` implementation so you can use HTMLPurifier within your `Zend\InputFilter` classes.
Furthermore, a view helper is provided to help purifying html on the fly in view scripts.


## Installation

You can install `Soflomo\Purifier` through Composer
```shell
$: composer require soflomo/purifier
```

To enable the module in your ZF2 application, add an entry `Soflomo\Purifier` to the list of enabled modules in `config/application.config.php`.


## Usage

In your input filter configuration, use the `htmlpurifier` as `name` in your filter spec.

An example `Form`:

```php
class MyForm extends Zend\Form\Form implements Zend\InputFilter\InputFilterProviderInterface
{
    public function init()
    {
        $this->add([
            'name'    => 'text',
            'options' => [
                'label' => 'Text'
            ],
            'attributes' => [
                'type' => 'textarea',
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'text'  => [
                'required' => true,
                'filters'  => [
                    [ 'name' => 'stringtrim' ],
                    [ 'name' => 'htmlpurifier' ],
                ],
            ],
        ];
    }
}
```

or an `InputFilter`:

```php
class MyInputFilter extends Zend\InputFilter\InputFilter
{
    public function init()
    {
        $this->add([
            'name'     => 'text',
            'required' => true,
            'filters'  => [
                [ 'name' => 'stringtrim' ],
                [ 'name' => 'htmlpurifier' ],
            ],
        ]);
    }
}
```

Alternatively, you can use the FQCN `Soflomo\Purifier\PurifierFilter` in place of the `htmlpurifier` alias.

If your pulling the consumers from their respective plugin managers, this should work out-of-the-box.
If not, please read [how to inject the filter plugin manager](#injecting-the-filtermanager).

If for some reason you want to use the filter in your view templates, you also have a view helper available.
Please be aware that HTMLPurifier is not a very fast library and as such, filtering on every request can be a significant performance bottleneck. Be advised to use a caching mechanism to cache the output of the filtered html. The view helper is available under the key `htmlPurifier`:

```php
<?php echo $this->htmlPurifier()->purify($foo->getText()) ?>
```

And there is a shorthand available too:

```php
<?php echo $this->htmlPurifier($foo->getText()) ?>
```


### Configuring HTMLPurifier

HTMLPurifier use the class `HTMLPurifier_Config` to configure its rules. Most configuration rules are based on a key/value pair: `$config->set('HTML.Doctype', 'HTML 4.01 Transitional')`. This API is exposed by **Soflomo\Purifier** as an associative array into the ZF2 configuration, so you can customize the default `HTMLPurifier_Config` instance like this:

```php
return [
    'soflomo_purifier' => [
        'config' => [
            'HTML.Doctype' => 'HTML 4.01 Transitional'
        ],
    ],
];
```

The module configuration also accepts a `definitions` array to add custom definitions to the purifier, as [documented here](http://htmlpurifier.org/docs/enduser-customize.html).

For example:

```php
return [
    'soflomo_purifier' => [
        'config' => [
            'HTML.DefinitionID' => 'my custom definitions',
        ],
        'definitions' => [
            'HTML' => [
                'addAttribute' => [
                    'a', 'target', 'Enum#_blank,_self,_target,_top'
                ],
            ],
        ],
    ],
];
```

This will add a `HTMLPurifier_AttrDef_Enum` definition for the `target` attribute of the `a` element.
Note that an arbitrary value for the `HTML.DefinitionID` config key is required to correctly load the definition.


### Injecting the FilterManager

If you instantiate your forms or your input filters manually with the `new` keyword rather than pulling them from their respective plugin managers (i.e. `FormElementManager` and `InputFilterManager`), the `FilterManager` is not injected automatically into their factories, and these will resort to using a default one.

As such, you get a `ServiceNotFoundException: "Zend\Filter\FilterPluginManager::get was unable to fetch or create an instance for htmlpurifier"`. This means the filter plugin manager was lazily instantiated, and does not know about the `htmlpurifier` plugin.

You can hack your way through this by grabbing the filter plugin manager from the Service Manager and injecting it manually via setters:

```php
$filters = $serviceManager->get('FilterManager');

$form = new MyForm();
$form->getFormFactory()->getInputFilterFactory()->getDefaultFilterChain()->setPluginManager($filters);

// same goes for input filters
$inputFilter = new MyInputFilter();
$inputFilter->getFactory()->getDefaultFilterChain()->setPluginManager($filters);
```

It is however strongly advised to pull forms and input filters from their respective plugin managers, and use the `init()` method (which will be invoked after all the factories are injected) when applicable.


### Performance optimization

HTMLPurifier is not the fastest library. It uses a large number of classes and files so autoloading can be cumbersome.

Luckily, you can [create a standalone version](http://htmlpurifier.org/live/INSTALL) of the HTMLPurifier class, where a single file contains most of the classes.

The script in `vendor/bin/purifier-generate-standalone` generates this file for you. The standalone file is created inside `vendor/ezyang/htmlpurifier/library` so make sure you can write in that directory.

**Soflomo\Purifier** helps you using this standalone version with the configuration option `soflomo_purifier.standalone`.

For example, you could add this in your `config/autoload/local.php`:

```php
return [
    'soflomo_purifier' => [
        'standalone' => true,
    ],
];
```

If you want to place the standalone file somewhere else, you can set its path too:

```php
return [
    'soflomo_purifier' => [
        'standalone'      => true,
        'standalone_path' => 'path/to/HTMLPurifier.standalone.php',
    ],
];
```

**Note:** The standalone generator script requires HTMLPurifier to be installed either with version `<4.7.0` or with Composer `--prefer-source` flag, because since that version the maintenance tools were removed from the archive (see [htmlpurifier #65](https://github.com/ezyang/htmlpurifier/pull/65)).

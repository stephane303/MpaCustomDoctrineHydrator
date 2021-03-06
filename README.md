[![Build Status](https://travis-ci.org/mpalourdio/MpaCustomDoctrineHydrator.png?branch=master)](https://travis-ci.org/mpalourdio/MpaCustomDoctrineHydrator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mpalourdio/MpaCustomDoctrineHydrator/badges/quality-score.png?s=2c109f8b765d059d4b33cb1f6195eae07b2fdb1c)](https://scrutinizer-ci.com/g/mpalourdio/MpaCustomDoctrineHydrator/)
[![Code Coverage](https://scrutinizer-ci.com/g/mpalourdio/MpaCustomDoctrineHydrator/badges/coverage.png?s=b249873714b3c85f08dfcd9306bd4c6b9cb19ba0)](https://scrutinizer-ci.com/g/mpalourdio/MpaCustomDoctrineHydrator/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/58b40eee-e087-4489-b169-71434b8c2879/mini.png)](https://insight.sensiolabs.com/projects/58b40eee-e087-4489-b169-71434b8c2879)
[![PHP 5.5+][ico-engine]][lang]
[![MIT Licensed][ico-license]][license]

MpaCustomDoctrineHydrator
=========================

Module that helps you deal with date/datetime/time for DoctrineORMModule & ZF2 : filtering, hydration, Locale etc.
Extends and replace the ZF2 Date Element, ZF2 DateTime Element, ZF2 Time Element to make them compliant 'out-of-the-box' with doctrine hydration.

Provides an extension of the DoctrineORMModule ```AnnotationBuilder``` and a factory for more ease. The ```ElementAnnotationsListener``` is overridden too in order to better suit needs regarding filtering and validation.

The filters and the elements can be used as standalone. Using the provided elements via the ```FormElementManager``` adds automatic conversion formats for date/date and time/time strings to ```DateTime```.
Automatic filtering and validation are provided regarding the date format (Y-m-d, Y-m-d H:i:s, H:i:s, etc.) that depends of the ```Locale```. A placeholder is added to your form element too when rendered.

The hydrator service adds a strategy to every date column in your entity for extraction and hydration.

Requirements
============
PHP 5.5+ - Only Composer installation supported


Installation
============
Run the command below to install via Composer

```shell
composer require mpalourdio/mpa-custom-doctrine-hydrator
```

Add "MpaCustomDoctrineHydrator" to your **modules list** in **application.config.php**


Configuration
=============
Copy **mpacustomdoctrinehydrator.config.global.php.dist** in your **autoload folder** and rename it by removing the .dist
extension.

Add your own date / time formats (if needed) that are compliant with php ```DateTime```

see http://www.php.net/manual/fr/datetime.createfromformat.php

Usage (the easy and lazy way)
=============================

Create your forms with the provided annotation builder.

```php
$builder       = new \MpaCustomDoctrineHydrator\Form\Annotation\AnnotationBuilder($this->entityManager, $this->formElementManager);
$form = $builder->createForm('Application\Entity\Myentity');
```

Or with the factory

```php
$form = $this->sm->get('annotationbuilder')->createForm('Application\Entity\Myentity');
```

Then, hydrate your form

```php
$hydrator = $this->sm->get('hydrator')->setEntity('Application\Entity\Myentity');
$form->setHydrator($hydrator);
```

You're done! Date/Date & Time/ Time colums will be hydrated/extracted, filtered and validated automatically, without providing anything else in your entities.
Your form elements will be rendered with a placeholder.


Usage (the hard and decoupled way)
==================================

```php
$hydrator = $this->sm->get('hydrator')->setEntity('Application\Entity\Myentity');
$form->setHydrator($hydrator);
```
In your forms classes, when not using the ```FormElementManager``` :
```php

$this->add(
            [
                'name'       => 'mydate',
                'type'       => 'MpaCustomDoctrineHydrator\Form\Element\Date',
                'attributes' => [
                    'id'    => 'mydate',
                ],
                'options'    => [
                    'label'  => 'My date',
                    'format' => 'd/m/Y' // format needed
                ],
            ]
        );
```

If you pull your forms from the ```FEM```, just grab the element as a ```'Date'``` or ```'Zend\Form\Element\Date'```. The format option is not needed here, config will be pulled from service config.

```php
$this->add(
            [
                'name'       => 'mydate',
                'type'       => 'Date',
                'attributes' => [
                    'id'    => 'mydate',
                ],
                'options'    => [
                    'label'  => 'My date',
                ],
            ]
        );
```

You can too use the filter as standalone on other form elements with custom formats, if needed. For this, use the filter FQCN.

If you use the filter shortname (```DateToDateTime ```), the config will be pulled from the service config (ie. The options array will be ignored).

```php
public function getInputFilterSpecification()
{
        $filters = [
            'otherdate' => [
                'filters' => [
                    [
                        'name' => 'MpaCustomDoctrineHydrator\Filter\DateToDateTime',
                        'options' => [
                            'format' => 'd/m/Y' ('date_format' key is also accepted)
                        ]
                    ],
                ],
            ],
        ];
        return $filters;
}
```

or simply

```php
public function getInputFilterSpecification()
{
        $filters = [
            'otherdate' => [
                'filters' => [
                    [
                        'name' => 'DateToDateTime',
                    ], // no options needed here, would be ignored anyway
                ],
            ],
        ];
        return $filters;
}
```

/!\ If you don't create your fieldsets/forms via the ```FormElementManager```, you must manually inject the SL so the ```Date``` element can fetch the configuration
```php
$this->getFormFactory()->getFormElementManager()->setServiceLocator($this->sm);
```

/!\ Tip : To use the ```'DateToDateTime'``` filter short name in a form grabbed without the ```FEM```, you must do the following :
```php
$plugins = $this->sm ->get('FilterManager');
$chain   = new FilterChain;
$chain->setPluginManager($plugins);
$myForm->getFormFactory()->getInputFilterFactory()->setDefaultFilterChain($chain);
```

You can use the provided strategy as standalone with your hydrators too. **Date Time and Time handling work the same way as the example above**, with only few changes, like the 'format' keys names.

[ico-engine]: http://img.shields.io/badge/php-5.5+-8892BF.svg
[lang]: http://php.net
[ico-license]: http://img.shields.io/packagist/l/adlawson/veval.svg
[license]: LICENSE

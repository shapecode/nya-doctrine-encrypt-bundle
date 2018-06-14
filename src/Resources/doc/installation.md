# Installation

1. Download DoctrineEncryptBundle using composer
2. Enable the database encryption bundle
3. Configure the database encryption bundle

### Requirements

 - PHP ~5.6|~7.0
 - [symfony/framework-bundle](https://packagist.org/packages/symfony/framework-bundle) ~2.8|~3.0|~4.0

### Step 1: Download DoctrineEncryptBundle using composer

DoctrineEncryptBundle should be installed using [Composer](http://getcomposer.org/):

``` bash
composer require shapecode/nya-doctrine-encrypt-bundle
```

Composer will install the bundle to your project's `vendor` directory.

### Step 2: Enable the bundle

Enable the bundle in the Symfony2 kernel by adding it in your /app/AppKernel.php file:

``` php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Shapecode\NYADoctrineEncryptBundle\ShapecodeNYADoctrineEncryptBundle(),
    );
}
```

### Step 3: Set your configuration

All configuration value's are optional.
On the following page you can find the configuration information.

#### [Configuration](https://github.com/shapecode/nya-doctrine-encrypt-bundle/blob/master/Resources/doc/configuration.md)

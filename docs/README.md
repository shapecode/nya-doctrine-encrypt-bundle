# Shapecode - Not Yet Another Doctrine Encrypt Bundle

[![paypal](https://img.shields.io/badge/Donate-Paypal-blue.svg)](http://paypal.me/nloges)

[![PHP Version](https://img.shields.io/packagist/php-v/shapecode/nya-doctrine-encrypt-bundle.svg)](https://packagist.org/packages/shapecode/nya-doctrine-encrypt-bundle)
[![Latest Stable Version](https://img.shields.io/packagist/v/shapecode/nya-doctrine-encrypt-bundle.svg?label=stable)](https://packagist.org/packages/shapecode/nya-doctrine-encrypt-bundle)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/shapecode/nya-doctrine-encrypt-bundle.svg?label=unstable)](https://packagist.org/packages/shapecode/nya-doctrine-encrypt-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/shapecode/nya-doctrine-encrypt-bundle.svg)](https://packagist.org/packages/shapecode/nya-doctrine-encrypt-bundle)
[![Monthly Downloads](https://img.shields.io/packagist/dm/shapecode/nya-doctrine-encrypt-bundle.svg)](https://packagist.org/packages/shapecode/nya-doctrine-encrypt-bundle)
[![Daily Downloads](https://img.shields.io/packagist/dd/shapecode/nya-doctrine-encrypt-bundle.svg)](https://packagist.org/packages/shapecode/nya-doctrine-encrypt-bundle)
[![License](https://img.shields.io/packagist/l/shapecode/nya-doctrine-encrypt-bundle.svg)](https://packagist.org/packages/shapecode/nya-doctrine-encrypt-bundle)

This is an fork from the original bundle created by michaeldegroot which can be found here:
[michaeldegroot/DoctrineEncryptBundle](https://github.com/michaeldegroot/DoctrineEncryptBundle)

### Using [Halite](https://github.com/paragonie/halite)

*You will need to require Halite yourself*

`composer require paragonie/halite`

```yml
// config.yml
shapecode_doctrine_encrypt:
    encryptor: halite
```

### Documentation

* [Installation](https://github.com/shapecode/nya-doctrine-encrypt-bundle/blob/master/docs/installation.md)
* [Requirements](https://github.com/shapecode/nya-doctrine-encrypt-bundle/blob/master/docs/installation.md#requirements)
* [Configuration](https://github.com/shapecode/nya-doctrine-encrypt-bundle/blob/master/docs/configuration.md)
* [Usage](https://github.com/shapecode/nya-doctrine-encrypt-bundle/blob/master/docs/usage.md)
* [Console commands](https://github.com/shapecode/nya-doctrine-encrypt-bundle/blob/master/docs/commands.md)
* [Custom encryption class](https://github.com/shapecode/nya-doctrine-encrypt-bundle/blob/master/docs/custom_encryptor.md)

# Configuration Reference

There are only 2 paramaters in the configuration of the Doctrine encryption bundle.
This parameters are also optional.

* **encryptor** - Encryptor for encrypting data
    * Encryptor, [your own encryptor class](https://github.com/shapecode/nya-doctrine-encrypt-bundle/blob/master/docs/custom_encryptor.md) will override encryptor paramater
    * Default: defuse

## yaml

``` yaml
shapecode_doctrine_encrypt:
    encryptor: defuse # or halite
    secret_directory_path: '%kernel.project_dir%' #Path where to store the keyfiles
```

## Important!

If you want to use Halite, make sure to require it!

``` bash
composer require paragonie/halite ^4.3
```

## Usage

Read how to use the database encryption bundle in your project.
#### [Usage](https://github.com/shapecode/nya-doctrine-encrypt-bundle/blob/master/docs/usage.md)

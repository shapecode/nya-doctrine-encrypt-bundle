# Shapecode - Not Yet Another Doctrine Ecrypt Bundle

This is an fork from the original bundle created by michaeldegroot which can be found here:
[michaeldegroot/DoctrineEncryptBundle](https://github.com/michaeldegroot/DoctrineEncryptBundle)

### Using [Defuse](https://github.com/defuse/php-encryption)

*All deps are already installed with this package*

```yml
// config.yml
shapecode_doctrine_encrypt:
    encryptor: defuse
```

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

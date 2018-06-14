# shapecode/nya-doctrine-encrypt-bundle

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

* [Installation](Resources/doc/installation.md)
* [Requirements](Resources/doc/installation.md#requirements)
* [Configuration](Resources/doc/configuration.md)
* [Usage](Resources/doc/usage.md)
* [Console commands](Resources/doc/commands.md)
* [Custom encryption class](Resources/doc/custom_encryptor.md)

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

* [Installation](docs/installation.md)
* [Requirements](docs/installation.md#requirements)
* [Configuration](docs/configuration.md)
* [Usage](docs/usage.md)
* [Console commands](docs/commands.md)
* [Custom encryption class](docs/custom_encryptor.md)

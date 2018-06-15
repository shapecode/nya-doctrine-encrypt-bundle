# Commands

To make your life a little easier we created some commands that you can use for encrypting and decrypting your current database.

## 1) Get status

You can use the comment `doctrine:encrypt:status` to get the current database and encryption information.

```
$ php bin/console doctrine:encrypt:status
```

The result will look like this:

```
Show entities with encrypted properies
=====================================
                                                                                                                        
 Found 1 entities.                                                                                                      

 ----------------------- ---------------------------------------------------------------------------------
  Class Name             Properties                                                                         
 ----------------------- ---------------------------------------------------------------------------------
  BankSepaMandate        accountNumber, bankCodeNumber, internationalBankAccountNumber, bankIdentifierCode  
 ----------------------- ------------------------------------------------------------- ------------------- 
```

## 2) Encrypt current database

You can use the comment `doctrine:encrypt:database [encryptor]` to encrypt the current database.

* Optional parameter [encryptor]
    * An encryptor provided by the bundle (defuse or halite) or your own [encryption class](https://github.com/shapecode/nya-doctrine-encrypt-bundle/blob/master/docs/custom_encryptor.md).
    * Default: Your encryptor set in the configuration file or the default encryption class when not set in the configuration file

```
$ php bin/console doctrine:encrypt:database
```

## 3) Decrypt current database

You can use the comment `doctrine:decrypt:database` to decrypt the current database.

```
$ php bin/console doctrine:decrypt:database
```

## Custom encryption class

You may want to use your own encryption class learn how here:

#### [Custom encryption class](https://github.com/shapecode/nya-doctrine-encrypt-bundle/blob/master/docs/custom_encryptor.md)

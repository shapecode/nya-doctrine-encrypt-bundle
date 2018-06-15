# Configuration Reference

All available configuration options are listed below.

``` yaml
shapecode_doctrine_encrypt:
    #  If you want, you can use your own Encryptor. Encryptor must implements EncryptorInterface interface
    #  Default: defuse
    encryptor: defuse
    
    # Path where to store the keyfiles
    # Default: '%kernel.project_dir%'
    secret_directory_path: '%kernel.project_dir%'   
```


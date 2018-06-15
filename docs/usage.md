# Usage

### Entity

``` php
namespace Acme\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

// importing @Encrypted annotation
use Shapecode\NYADoctrineEncryptBundle\Configuration\Encrypted;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User {

    ..

    /**
     * @ORM\Column(type="string", name="email")
     * @Encrypted
     * @var int
     */
    private $email;

    ..

}
```

It is as simple as that, the field will now be encrypted the first time the users entity gets edited.
We keep an <ENC> prefix to check if data is encrypted or not so, unencrypted data will still work even if the field is encrypted.

## Console commands

There are some console commands that can help you encrypt your existing database or change encryption methods.
Read more about the database encryption commands provided with this bundle.

#### [Console commands](https://github.com/shapecode/nya-doctrine-encrypt-bundle/blob/master/docs/commands.md)

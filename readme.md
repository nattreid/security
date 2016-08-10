# Rozšíření Security pro Nette Framework
Databázove ACL, uživatelé, authentizace a autorizace

## Nastavení
Nastavení v **config.neon**
```neon
extensions:
    securityExt: NAttreid\Security\DI\SecurityExtension
```

dostupné nastavení
```neon
securityExt:
    authenticator:
        front: App\FrontAuthenticator
```

A přidat do orm model trackingu. V příkladu je extension orm pod nazvem **orm**
```neon
orm:
    add:
        - NAttreid\Security\Model\Orm
```

## Authenticator
```php
class FrontAuthenticator implements \Nette\Security\IAuthenticator {

    /**
     * Performs an authentication.
     * @return Identity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials) {
        // php code
    }

}
```
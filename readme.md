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
    namespace: 'user'
    authenticator:
        front: App\FrontAuthenticator
```

A přidat do orm model. V příkladu je extension orm pod nazvem **orm**
```neon
orm:
    add:
        - NAttreid\Security\Model\Orm
```

## Authenticator
```php
class FrontAuthenticator implements \NAttreid\Security\Authenticator\IAuthenticator {

    /**
     * Performs an authentication.
     * @return Identity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials) {
        // php code
    }

    /**
	 * Vrati data pokud je treba ja aktualizovat
	 * @param int $userId
	 * @return Identity|null
	 * @throws AuthenticationException
	 */
    public function getRefreshIdentity($userId) {
        // php code
    }
}
```
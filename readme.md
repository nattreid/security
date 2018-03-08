# Rozšíření Security pro Nette Framework
Databázové ACL, uživatelé, authentizace a autorizace

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

A přidat do orm model. V příkladu je extension orm pod názvem **orm**
```neon
orm:
    add:
        - NAttreid\Security\Model\Orm
```

## Authenticator
```php
class FrontAuthenticator implements \NAttreid\Security\Authenticator\IAuthenticator {

    public function authenticate(array $credentials): Identity {
        // php code
    }

    public function getIdentity(int $userId): Identity {
        // php code
    }
}
```

## TryUser
Komponenta pro dočasnou změnu identity uživatele

V BasePresenteru přidejte komponentu
```php
    /** @inject */
    public $tryUserFactory;
    
    protected function startup()
    {
        parent::startup();
        $this['tryUser']->init();
    }
    
    protected function createComponentTryUser()
    {
        $control = $this->tryUserFactory->create(":Link:Nekam:");
        $control->permission = 'nazev.prav.pro.komponentu';
        return $control;
    }
```
<?php

namespace NAttreid\Security\Authenticator;

use Nextras\Orm\Model\Model,
    NAttreid\Security\Model\Orm,
    Nette\Security\AuthenticationException,
    Nette\Security\Passwords,
    Nette\Security\Identity,
    NAttreid\Security\Model\User;

/**
 * Hlavni autentizace
 *
 * @author Attreid <attreid@gmail.com>
 */
class MainAuthenticator implements \Nette\Security\IAuthenticator {

    use \Nette\SmartObject;

    /** @var Orm */
    private $orm;

    public function __construct(Model $orm) {
        $this->orm = $orm;
    }

    /**
     * Performs an authentication.
     * @return Identity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials) {
        list($username, $password) = $credentials;

        $user = $this->orm->users->getByUsername($username);

        if (!$user) {
            throw new AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
        } elseif (!Passwords::verify($password, $user->password)) {
            throw new AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
        } elseif (!$user->active) {
            throw new AuthenticationException('Account is deactivated.', self::NOT_APPROVED);
        } elseif (Passwords::needsRehash($user->password)) {
            $user->setPassword($password);
            $this->orm->persistAndFlush($user);
        }
        $this->orm->users->setValid($user->id);

        $arr = $user->toArray(User::TO_ARRAY_RELATIONSHIP_AS_ID);
        unset($arr['password']);

        $roles = $user->getRoles();
        return new Identity($user->id, $roles, $arr);
    }

}

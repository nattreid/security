<?php

namespace NAttreid\Security\Model;

use Nextras\Orm\Relationships\ManyHasMany,
    Nette\Security\Passwords,
    NAttreid\Security\Model\AclRole,
    Nextras\Dbal\UniqueConstraintViolationException,
    Nette\Utils\Strings,
    Nette\Utils\Validators,
    Nette\InvalidArgumentException;

/**
 * User
 * 
 * @property int $id {primary}
 * @property boolean $active {default TRUE}
 * @property string $username
 * @property string $firstName
 * @property string $surname
 * @property string $email
 * @property string $password
 * @property string $fullName {virtual}
 * @property ManyHasMany|AclRole[] $roles {m:n AclRole::$users, isMain=true}
 * 
 * @author Attreid <attreid@gmail.com>
 */
class User extends \Nextras\Orm\Entity\Entity {

    /**
     * Ulozi heslo
     * @param string $newPassword
     * @param string $oldPassword
     * @throws AuthenticationException
     */
    public function setPassword($newPassword, $oldPassword = NULL) {
        if ($oldPassword != NULL) {
            if (!Passwords::verify($oldPassword, $this->password)) {
                throw new AuthenticationException('The password is incorrect.');
            }
        }
        $this->password = Passwords::hash($newPassword);
    }

    /**
     * Ulozi uzivatelske jmeno
     * @param string $username
     * @throws UniqueConstraintViolationException
     * @throws InvalidArgumentException
     */
    public function setUsername($username) {
        if (Strings::match($username, '/[^A-Za-z0-9_]/')) {
            throw new InvalidArgumentException('Username contains invalid characters');
        }

        /* @var $repository UsersRepository */
        $repository = $this->getRepository();
        $user = $repository->getByUsername($username);
        if ($user !== NULL && $user !== $this) {
            throw new UniqueConstraintViolationException("Username '$username' exists");
        }
        $this->username = $username;
    }

    /**
     * ulozi email
     * @param string $email
     * @throws UniqueConstraintViolationException
     * @throws InvalidArgumentException
     */
    public function setEmail($email) {
        if (!Validators::isEmail($email)) {
            throw new \Nette\InvalidArgumentException('Value is not valid email');
        }

        /* @var $repository UsersRepository */
        $repository = $this->getRepository();
        $user = $repository->getByEmail($email);
        if ($user !== NULL && $user !== $this) {
            throw new UniqueConstraintViolationException("Email '$email' exists");
        }
        $this->email = $email;
    }

    /**
     * Vrati cele jmeno
     * @return string
     */
    public function getterFullName() {
        return $this->firstName . ' ' . $this->surname;
    }

    /**
     * Vrati jmena roli
     * @return array
     */
    public function getRoles() {
        $result = [];
        $roles = $this->roles->get();
        /* @var $role AclRole */
        foreach ($roles as $role) {
            $result[] = $role->name;
        }
        return $result;
    }

    /**
     * Vrati nazvy roli
     * @return array
     */
    public function getRoleTitles() {
        $result = [];
        $roles = $this->roles->get();
        /* @var $role AclRole */
        foreach ($roles as $role) {
            $result[] = $role->title;
        }
        return $result;
    }

}

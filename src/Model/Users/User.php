<?php

namespace NAttreid\Security\Model;

use NAttreid\Utils\PhoneNumber;
use Nette\InvalidArgumentException;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;
use Nette\Utils\Strings;
use Nette\Utils\Validators;
use Nextras\Dbal\UniqueConstraintViolationException;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\ManyHasMany;

/**
 * User
 *
 * @property int $id {primary}
 * @property boolean $active {default true}
 * @property string $username
 * @property string|null $firstName
 * @property string|null $surname
 * @property string $email
 * @property string|null $phone
 * @property string|null $language
 * @property string $password
 * @property string $fullName {virtual}
 * @property ManyHasMany|AclRole[] $roles {m:n AclRole::$users, isMain=true}
 *
 * @author Attreid <attreid@gmail.com>
 */
class User extends Entity
{

	/**
	 * Ulozi heslo
	 * @param string $newPassword
	 * @param string $oldPassword
	 * @throws AuthenticationException
	 */
	public function setPassword($newPassword, $oldPassword = null)
	{
		if ($oldPassword != null) {
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
	public function setUsername($username)
	{
		if (Strings::match($username, '/[^A-Za-z0-9_]/')) {
			throw new InvalidArgumentException('Username contains invalid characters');
		}

		/* @var $repository UsersRepository */
		$repository = $this->getRepository();
		$user = $repository->getByUsername($username);
		if ($user !== null && $user !== $this) {
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
	public function setEmail($email)
	{
		if (!Validators::isEmail($email)) {
			throw new InvalidArgumentException('Value is not valid email');
		}

		/* @var $repository UsersRepository */
		$repository = $this->getRepository();
		$user = $repository->getByEmail($email);
		if ($user !== null && $user !== $this) {
			throw new UniqueConstraintViolationException("Email '$email' exists");
		}
		$this->email = $email;
	}

	/**
	 * @param string|null $phone
	 */
	public function setPhone($phone)
	{
		if ($phone !== null && !PhoneNumber::validatePhone($phone)) {
			throw new InvalidArgumentException('Value is not valid phone');
		}
		$this->phone = $phone;
	}

	/**
	 * Vrati cele jmeno
	 * @return string
	 */
	protected function getterFullName()
	{
		return $this->firstName . ' ' . $this->surname;
	}

	/**
	 * Vrati jmena roli
	 * @return array
	 */
	public function getRoles()
	{
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
	public function getRoleTitles()
	{
		$result = [];
		$roles = $this->roles->get();
		/* @var $role AclRole */
		foreach ($roles as $role) {
			$result[] = $role->title;
		}
		return $result;
	}

}

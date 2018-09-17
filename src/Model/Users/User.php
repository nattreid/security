<?php

declare(strict_types=1);

namespace NAttreid\Security\Model\Users;

use NAttreid\Security\Model\AclRoles\AclRole;
use NAttreid\Utils\PhoneNumber;
use Nette\InvalidArgumentException;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use Nette\Utils\Strings;
use Nette\Utils\Validators;
use Nextras\Dbal\UniqueConstraintViolationException;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Entity\ToArrayConverter;
use Nextras\Orm\Relationships\ManyHasMany;

/**
 * User
 *
 * @property int $id {primary}
 * @property bool $active {default true}
 * @property string $username
 * @property string|null $firstName
 * @property string|null $surname
 * @property string $email
 * @property string|null $phone
 * @property string|null $language
 * @property string $password
 * @property string $fullName {virtual}
 * @property ManyHasMany|AclRole[] $roles {m:m AclRole::$users, isMain=true}
 *
 * @property array $roleTitles {virtual}
 * @property array $roleConstants {virtual}
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
	public function setPassword(string $newPassword, string $oldPassword = null): void
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
	public function setUsername(string $username): void
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
	public function setEmail(string $email): void
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
	 * @param mixed|null $phone
	 */
	public function setPhone($phone): void
	{
		if ($phone !== null && !PhoneNumber::validatePhone((string) $phone)) {
			throw new InvalidArgumentException('Value is not valid phone');
		}
		$this->phone = $phone;
	}

	/**
	 * Vrati cele jmeno
	 * @return string
	 */
	protected function getterFullName(): string
	{
		return $this->firstName . ' ' . $this->surname;
	}

	/**
	 * @return Identity
	 */
	public function getIdentity(): Identity
	{
		$arr = $this->toArray(ToArrayConverter::RELATIONSHIP_AS_ID);
		unset($arr['password']);

		return new Identity($this->id, $this->roleConstants, $arr);
	}

	/**
	 * Vrati jmena roli
	 * @return array
	 */
	public function getterRoleConstants(): array
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
	protected function getterRoleTitles(): array
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

<?php

namespace NAttreid\Security\Authenticator;

use NAttreid\Security\Model\Orm;
use NAttreid\Security\Model\User;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Nette\Security\Passwords;
use Nette\SmartObject;
use Nextras\Orm\Model\Model;

/**
 * Hlavni autentizace
 *
 * @author Attreid <attreid@gmail.com>
 */
class UserAuthenticator implements IAuthenticator
{
	use SmartObject;

	/** @var Orm */
	private $orm;

	public function __construct(Model $orm)
	{
		$this->orm = $orm;
		$this->orm->users->onFlush[] = function ($persisted, $removed) {
			foreach ($persisted as $user) {
				/* @var $user User */
				$this->orm->users->invalidateIdentity($user->id);
			}
			foreach ($removed as $user) {
				/* @var $user User */
				$this->orm->users->invalidateIdentity($user->id);
			}
		};
	}

	/**
	 * Performs an authentication.
	 * @param array $credentials
	 * @return Identity
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
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

		$arr = $user->toArray($user::TO_ARRAY_RELATIONSHIP_AS_ID);
		unset($arr['password']);

		$roles = $user->getRoles();
		return new Identity($user->id, $roles, $arr);
	}

	/**
	 * Vrati data pokud je treba ja aktualizovat
	 * @param int $userId
	 * @return Identity|null
	 * @throws AuthenticationException
	 */
	public function getRefreshIdentity($userId)
	{
		$user = $this->orm->users->getRefreshUserData($userId);
		if ($user) {
			$roles = $user->getRoles();

			$arr = $user->toArray($user::TO_ARRAY_RELATIONSHIP_AS_ID);
			unset($arr['password']);

			return new Identity($user->id, $roles, $arr);
		}
		return null;
	}
}
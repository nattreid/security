<?php

declare(strict_types=1);

namespace NAttreid\Security\Authenticator;

use NAttreid\Security\Model\Orm;
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
	}

	/**
	 * Performs an authentication.
	 * @param array $credentials
	 * @return Identity
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials): Identity
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
		}

		$user->logged();

		return new Identity($user->id);
	}

	/**
	 * Vrati data
	 * @param int $userId
	 * @return Identity
	 * @throws AuthenticationException
	 */
	public function getIdentity(int $userId): Identity
	{
		$user = $this->orm->users->getById($userId);
		if (!$user) {
			throw new AuthenticationException('User does not exist');
		} elseif (!$user->active) {
			throw new AuthenticationException('User is inactive');
		}
		return $user->getIdentity();
	}
}

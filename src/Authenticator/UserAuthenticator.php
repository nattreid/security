<?php

declare(strict_types=1);

namespace NAttreid\Security\Authenticator;

use NAttreid\Security\Model\Orm;
use Nette\Security\AuthenticationException;
use Nette\Security\IdentityHandler;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;
use Nette\SmartObject;
use Nextras\Orm\Model\Model;
use Nette\Security\Authenticator;

/**
 * Hlavni autentizace
 *
 * @author Attreid <attreid@gmail.com>
 */
class UserAuthenticator implements Authenticator, IdentityHandler
{
	use SmartObject;

	/** @var Orm */
	private $orm;

	/** @var Passwords */
	private $passwords;

	public function __construct(Model $orm, Passwords $passwords)
	{
		$this->orm = $orm;
		$this->passwords = $passwords;
	}

	/**
	 * Performs an authentication.
	 * @param string $username
	 * @param string $password
	 * @return SimpleIdentity
	 * @throws AuthenticationException
	 */
	function authenticate(string $username, string $password): IIdentity
	{
		$user = $this->orm->users->getByUsername($username);

		if (!$user) {
			throw new AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		} elseif (!$this->passwords->verify($password, $user->password)) {
			throw new AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		} elseif (!$user->active) {
			throw new AuthenticationException('Account is deactivated.', self::NOT_APPROVED);
		} elseif ($this->passwords->needsRehash($user->password)) {
			$user->setPassword($password);
			$this->orm->persistAndFlush($user);
		}

		return new SimpleIdentity($user->id);
	}

	/**
	 * Vrati data
	 * @param int $userId
	 * @return SimpleIdentity
	 * @throws AuthenticationException
	 */
	public function getIdentity(int $userId): SimpleIdentity
	{
		$user = $this->orm->users->getById($userId);
		if (!$user) {
			throw new AuthenticationException('User does not exist');
		} elseif (!$user->active) {
			throw new AuthenticationException('User is inactive');
		}
		return $user->getIdentity();
	}

	function sleepIdentity(IIdentity $identity): IIdentity
	{
		return $identity;
	}

	function wakeupIdentity(IIdentity $identity): ?IIdentity
	{
		$user = $this->orm->users->getById($identity->getId());
		if (!$user) {
			throw new AuthenticationException('User does not exist');
		} elseif (!$user->active) {
			throw new AuthenticationException('User is inactive');
		}
		return $user->getIdentity();
	}
}

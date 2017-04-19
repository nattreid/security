<?php

declare(strict_types=1);

namespace NAttreid\Security\Authenticator;

use Exception;
use Nette\Http\UserStorage;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\IUserStorage;
use Nette\SmartObject;
use UnexpectedValueException;

/**
 * Prihlaseni
 *
 * @author Attreid <attreid@gmail.com>
 */
class Authenticator implements IAuthenticator
{
	use SmartObject;

	/** @var IAuthenticator[] */
	private $authenticators = [];

	/** @var string[] */
	private $mapper = [];

	/** @var UserStorage */
	private $userStorage;

	public function __construct(IUserStorage $userStorage)
	{
		$this->userStorage = $userStorage;
	}

	/**
	 * Vrati overovac
	 * @return IAuthenticator
	 * @throws UnexpectedValueException
	 */
	private function getAuthenticator(): IAuthenticator
	{
		$ns = $this->userStorage->getNamespace();
		if (isset($this->mapper[$ns])) {
			$ns = $this->mapper[$ns];
		}
		if (!isset($this->authenticators[$ns])) {
			throw new UnexpectedValueException('Namespace is not registered');
		}
		return $this->authenticators[$ns];
	}

	/**
	 * Nastavi mapovani
	 * @param string $src
	 * @param string $dest
	 */
	public function addMapping(string $src, string $dest): void
	{
		$this->mapper[$src] = $dest;
	}

	/**
	 * Prida authenticator
	 * @param string $namespace
	 * @param IAuthenticator $authenticator
	 */
	public function add(string $namespace, IAuthenticator $authenticator): void
	{
		$this->authenticators[$namespace] = $authenticator;
	}

	/**
	 * Overeni
	 * @param array $credentials
	 * @return IIdentity
	 * @throws Exception
	 */
	public function authenticate(array $credentials): IIdentity
	{
		return $this->getAuthenticator()->authenticate($credentials);
	}

	/**
	 * Vrati data pokud je treba ja aktualizovat
	 * @param int $userId
	 * @return Identity|null
	 * @throws AuthenticationException
	 */
	public function getRefreshIdentity(int $userId): ?Identity
	{
		return $this->getAuthenticator()->getRefreshIdentity($userId);
	}
}

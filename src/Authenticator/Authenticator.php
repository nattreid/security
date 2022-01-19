<?php

declare(strict_types=1);

namespace NAttreid\Security\Authenticator;

use Nette\Security\Authenticator as NAuthenticator;
use Nette\Security\IdentityHandler;
use Nette\Security\IIdentity;
use Nette\Security\UserStorage;
use Nette\SmartObject;
use UnexpectedValueException;

/**
 * Prihlaseni
 *
 * @author Attreid <attreid@gmail.com>
 */
class Authenticator implements NAuthenticator, IdentityHandler
{
	use SmartObject;

	/** @var NAuthenticator[]| */
	private $authenticators = [];

	/** @var string[] */
	private $mapper = [];

	/** @var UserStorage */
	private $userStorage;

	public function __construct(UserStorage $userStorage)
	{
		$this->userStorage = $userStorage;
	}

	/**
	 * Vrati overovac
	 * @return NAuthenticator
	 * @throws UnexpectedValueException
	 */
	private function getAuthenticator(): NAuthenticator
	{
		$ns = $this->userStorage->getNamespace();
		if (isset($this->mapper[$ns])) {
			$ns = $this->mapper[$ns];
		}
		if (!isset($this->authenticators[$ns])) {
			throw new UnexpectedValueException("Namespace '$ns' is not registered");
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
	 * @param NAuthenticator $authenticator
	 */
	public function add(string $namespace, NAuthenticator $authenticator): void
	{
		$this->authenticators[$namespace] = $authenticator;
	}

	public function authenticate(string $user, string $password): IIdentity
	{
		return $this->getAuthenticator()->authenticate($user, $password);
	}

	function sleepIdentity(IIdentity $identity): IIdentity
	{
		$authenticator = $this->getAuthenticator();
		if ($authenticator instanceof IdentityHandler) {
			return $authenticator->sleepIdentity($identity);
		}
		return $identity;
	}

	function wakeupIdentity(IIdentity $identity): ?IIdentity
	{
		$authenticator = $this->getAuthenticator();
		if ($authenticator instanceof IdentityHandler) {
			return $authenticator->wakeupIdentity($identity);
		}
		return $identity;
	}
}

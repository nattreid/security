<?php

namespace NAttreid\Security;

use Nette\Http\UserStorage;
use Nette\Security\IAuthenticator;
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

	private $authenticators = [];

	/** @var UserStorage */
	private $userStorage;

	public function __construct(UserStorage $userStorage)
	{
		$this->userStorage = $userStorage;
	}

	/**
	 * Vrati overovac
	 * @return IAuthenticator
	 * @throws UnexpectedValueException
	 */
	private function getAuthenticator()
	{
		$ns = $this->userStorage->getNamespace();
		if (empty($ns)) {
			throw new UnexpectedValueException('Namespace is not set');
		}
		if (!isset($this->authenticators[$ns])) {
			throw new UnexpectedValueException('Namespace is not registered');
		}
		return $this->authenticators[$ns];
	}

	/**
	 * Prida overovac
	 * @param string $namespace
	 * @param IAuthenticator $authenticator
	 */
	public function add($namespace, IAuthenticator $authenticator)
	{
		$this->authenticators[$namespace] = $authenticator;
	}

	/**
	 * Overeni
	 * @param array $credentials
	 * @return \Nette\Security\IIdentity
	 * @throws \Exception
	 */
	public function authenticate(array $credentials)
	{
		return $this->getAuthenticator()->authenticate($credentials);
	}

}

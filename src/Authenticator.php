<?php

namespace NAttreid\Security;

use Nette\Security\IAuthenticator;
use Nette\Security\IUserStorage;

/**
 * Prihlaseni
 *
 * @author Attreid <attreid@gmail.com>
 */
class Authenticator implements IAuthenticator
{

	use \Nette\SmartObject;

	private $authenticators = [];

	/** @var IUserStorage */
	private $userStorage;

	public function __construct(IUserStorage $userStorage)
	{
		$this->userStorage = $userStorage;
	}

	/**
	 * Vrati overovac
	 * @return IAuthenticator
	 * @throws \UnexpectedValueException
	 */
	private function getAuthenticator()
	{
		$ns = $this->userStorage->getNamespace();
		if (empty($ns)) {
			throw new \UnexpectedValueException('Namespace is not set');
		}
		if (!isset($this->authenticators[$ns])) {
			throw new \UnexpectedValueException('Namespace is not registered');
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

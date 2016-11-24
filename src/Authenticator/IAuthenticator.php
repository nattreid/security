<?php

namespace NAttreid\Security\Authenticator;

use Nette\Security\AuthenticationException;
use Nette\Security\Identity;

interface IAuthenticator extends \Nette\Security\IAuthenticator
{

	/**
	 * Vrati data pokud je treba ja aktualizovat
	 * @param int $userId
	 * @return Identity|null
	 * @throws AuthenticationException
	 */
	public function getRefreshIdentity($userId);
}
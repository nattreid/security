<?php

declare(strict_types=1);

namespace NAttreid\Security\Authenticator;

use Nette\Security\AuthenticationException;
use Nette\Security\Identity;

interface IAuthenticator extends \Nette\Security\IAuthenticator
{

	/**
	 * Vrati data
	 * @param int $userId
	 * @return Identity
	 * @throws AuthenticationException
	 */
	public function getIdentity(int $userId): Identity;
}
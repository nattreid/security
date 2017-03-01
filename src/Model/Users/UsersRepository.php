<?php

declare(strict_types = 1);

namespace NAttreid\Security\Model\Users;

use NAttreid\Orm\Repository;
use Nextras\Orm\Entity\IEntity;

/**
 * Users Repository
 *
 * @method User|null getRefreshUserData($userId) Vrati data pokud je treba ja aktualizovat
 * @method User getById($primaryValue)
 *
 * @author Attreid <attreid@gmail.com>
 */
class UsersRepository extends Repository
{
	protected function init()
	{
		$this->onFlush[] = function ($persisted, $removed) {
			foreach ($persisted as $user) {
				/* @var $user User */
				$this->invalidateIdentity($user->id);
			}
			foreach ($removed as $user) {
				/* @var $user User */
				$this->invalidateIdentity($user->id);
			}
		};
	}

	/** @var UsersMapper */
	protected $mapper;

	public static function getEntityClassNames()
	{
		return [User::class];
	}

	/**
	 * Prida identitu jako validni
	 * @param int $userId
	 */
	public function setValid(int $userId)
	{
		$this->mapper->setValid($userId);
	}

	/**
	 * Vrati uzivatele podle jmena
	 * @param string $username
	 * @return User|null
	 */
	public function getByUsername(string $username)
	{
		return $this->getBy(['username' => $username]);
	}

	/**
	 * Vrati uzivatele podle hash ID
	 * @param string $hash
	 * @return IEntity|User|null
	 */
	public function getByHashId(string $hash)
	{
		return $this->mapper->getByHash('id', $hash);
	}

	/**
	 * Vrati uzivatele podle emailu
	 * @param string $email
	 * @return User|IEntity
	 */
	public function getByEmail($email)
	{
		return $this->getBy(['email' => $email]);
	}

	/**
	 * Invaliduje identitu
	 * @param int $userId
	 */
	public function invalidateIdentity(int $userId)
	{
		return $this->mapper->invalidateIdentity($userId);
	}

}

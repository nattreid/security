<?php

namespace NAttreid\Security\Model;
use NAttreid\Orm\Repository;

/**
 * Users Repository
 *
 * @method User getRefreshUser($userId) Vrati data pokud je treba ja aktualizovat
 *
 * @author Attreid <attreid@gmail.com>
 */
class UsersRepository extends Repository
{

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
	public function setValid($userId)
	{
		$this->mapper->setValid($userId);
	}

	/**
	 * Vrati uzivatele podle jmena
	 * @param string $username
	 * @return User
	 */
	public function getByUsername($username)
	{
		return $this->findBy(['username' => $username])->fetch();
	}

	/**
	 * Vrati uzivatele podle hash ID
	 * @param string $hash
	 * @return User
	 */
	public function getByHashId($hash)
	{
		return $this->mapper->getByHash('id', $hash);
	}

	/**
	 * Vrati uzivatele podle emailu
	 * @param string $email
	 * @return User
	 */
	public function getByEmail($email)
	{
		return $this->findBy(['email' => $email])->fetch();
	}

	/**
	 * Invaliduje identitu
	 * @param int $userId
	 */
	public function invalidateIdentity($userId)
	{
		return $this->mapper->invalidateIdentity($userId);
	}

}

<?php

declare(strict_types=1);

namespace NAttreid\Security\Model\Users;

use NAttreid\Orm\Repository;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;

/**
 * Users Repository
 *
 * @method User getById($primaryValue)
 *
 * @author Attreid <attreid@gmail.com>
 */
class UsersRepository extends Repository
{
	/** @var UsersMapper */
	protected $mapper;

	public static function getEntityClassNames(): array
	{
		return [User::class];
	}

	/**
	 * Vrati uzivatele podle jmena
	 * @param string $username
	 * @return User|null
	 */
	public function getByUsername(string $username): ?User
	{
		return $this->getBy(['username' => $username]);
	}

	/**
	 * Vrati pole [id => username] serazene podle [username]
	 * @return array
	 */
	public function fetchPairsByUsername(): array
	{
		return $this->findAll()->resetOrderBy()->orderBy('username')->fetchPairs('id', 'username');
	}

	/**
	 * Vrati pole [id => fullname] serazene podle [surname]
	 * @return array
	 */
	public function fetchPairsByFullName(): array
	{
		return $this->findAll()->resetOrderBy()->orderBy('surname')->fetchPairs('id', 'fullName');
	}

	/**
	 * Vrati uzivatele podle hash ID
	 * @param string $hash
	 * @return IEntity|User|null
	 */
	public function getByHashId(?string $hash): ?User
	{
		return $this->mapper->getByHash('id', $hash);
	}

	/**
	 * Vrati uzivatele podle emailu
	 * @param string $email
	 * @return User|IEntity
	 */
	public function getByEmail($email): ?User
	{
		return $this->getBy(['email' => $email]);
	}
}

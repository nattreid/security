<?php

namespace NAttreid\Security\Model\Users;

use NAttreid\Orm\Structure\Table;
use NAttreid\Security\Model\AclRoles\AclRolesMapper;
use NAttreid\Security\Model\Mapper;
use Nette\Caching\Cache;
use Nette\Security\AuthenticationException;

/**
 * Users Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class UsersMapper extends Mapper
{

	private $tag = 'user';
	private $key = 'user_identity';

	protected function createTable(Table $table)
	{
		$table->addPrimaryKey('id')
			->int()
			->setAutoIncrement();
		$table->addColumn('active')
			->boolean()
			->setDefault(1);
		$table->addColumn('username')
			->varChar(50)
			->setUnique();
		$table->addColumn('firstName')
			->varChar()
			->setDefault(null);
		$table->addColumn('surname')
			->varChar()
			->setDefault(null);
		$table->addColumn('email')
			->varChar(100)
			->setUnique();
		$table->addColumn('phone')
			->varChar(20)
			->setDefault(null);
		$table->addColumn('language')
			->varChar(5)
			->setDefault(null);
		$table->addColumn('password')
			->varChar();

		$relationTable = $table->createRelationTable(AclRolesMapper::class);
		$relationTable->addForeignKey('userId', $table);
		$relationTable->addForeignKey('roleId', AclRolesMapper::class);
		$relationTable->setPrimaryKey('userId', 'roleId');
	}

	/**
	 * Vrati data pokud je treba ja aktualizovat
	 * @param int $userId
	 * @return User|null
	 * @throws AuthenticationException
	 */
	public function getRefreshUserData($userId)
	{
		$acceptedUsers = $this->cache->load($this->key);

		if (!isset($acceptedUsers[$userId])) {
			/* @var $user User */
			$user = $this->getRepository()->getById($userId);
			if (!$user) {
				throw new AuthenticationException('User does not exist');
			} elseif (!$user->active) {
				throw new AuthenticationException('User is inactive');
			} else {
				$acceptedUsers[$userId] = true;
				$this->cache->save($this->key, $acceptedUsers, [
					Cache::TAGS => [$this->tag]
				]);
				return $user;
			}
		}
		return null;
	}

	/**
	 * Invaliduje identitu
	 * @param int $userId
	 */
	public function invalidateIdentity($userId)
	{
		$acceptedUsers = $this->cache->load($this->key);

		unset($acceptedUsers[$userId]);

		$this->cache->save($this->key, $acceptedUsers, [
			Cache::TAGS => [$this->tag]
		]);
	}

	/**
	 * Prida identitu jako validni
	 * @param int $userId
	 */
	public function setValid($userId)
	{
		$acceptedUsers = $this->cache->load($this->key);

		$acceptedUsers[$userId] = true;

		$this->cache->save($this->key, $acceptedUsers, [
			Cache::TAGS => [$this->tag]
		]);
	}

}

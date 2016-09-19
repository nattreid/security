<?php

namespace NAttreid\Security\Model;

use NAttreid\Orm\Structure\Table;
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
			->varChar();
		$table->addColumn('surname')
			->varChar();
		$table->addColumn('email')
			->varChar(100)
			->setUnique();
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
	 * @return User
	 * @throws AuthenticationException
	 */
	public function getRefreshUser($userId)
	{
		$acceptedUsers = $this->cache->load($this->key);

		if (!isset($acceptedUsers[$userId])) {
			/* @var $user User */
			$user = $this->getRepository()->getById($userId);
			if ($user->active) {
				$acceptedUsers[$userId] = TRUE;
				$this->cache->save($this->key, $acceptedUsers, [
					Cache::TAGS => [$this->tag]
				]);
				return $user;
			} else {
				throw new AuthenticationException('User is inactive');
			}
		}
		return NULL;
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

		$acceptedUsers[$userId] = TRUE;

		$this->cache->save($this->key, $acceptedUsers, [
			Cache::TAGS => [$this->tag]
		]);
	}

}

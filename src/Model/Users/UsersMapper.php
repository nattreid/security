<?php

declare(strict_types=1);

namespace NAttreid\Security\Model\Users;

use NAttreid\Orm\Structure\Table;
use NAttreid\Security\Model\AclRoles\AclRolesMapper;
use NAttreid\Security\Model\Mapper;
use Nette\Security\AuthenticationException;

/**
 * Users Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class UsersMapper extends Mapper
{

	protected function createTable(Table $table): void
	{
		$table->addPrimaryKey('id')
			->int()
			->setAutoIncrement();
		$table->addColumn('active')
			->bool()
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
	public function getData(int $userId): ?User
	{
		/* @var $user User */
		$user = $this->getRepository()->getById($userId);
		if (!$user) {
			throw new AuthenticationException('User does not exist');
		} elseif (!$user->active) {
			throw new AuthenticationException('User is inactive');
		}
		return $user;
	}
}

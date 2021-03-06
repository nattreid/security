<?php

declare(strict_types=1);

namespace NAttreid\Security\Model\AclRoles;

use NAttreid\Orm\Structure\Table;
use NAttreid\Security\Model\Mapper;

/**
 * Acl Roles Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclRolesMapper extends Mapper
{

	const
		GUEST = 'guest',
		USER = 'user',
		EDITOR = 'editor',
		ADMIN = 'admin',
		SUPERADMIN = 'superadmin';

	protected function createTable(Table $table): void
	{
		$table->addPrimaryKey('id')
			->int()
			->setAutoIncrement();
		$table->addColumn('name')
			->varChar(50)
			->setUnique();
		$table->addForeignKey('parentId', $table, null)
			->setDefault(null);
		$table->addColumn('position')
			->int()
			->setDefault(null)
			->setKey();

		$this->onCreateTable[] = function () {
			$this->insert([
				[
					'id' => 1,
					'name' => self::GUEST,
					'parentId' => null,
					'position' => 1
				], [
					'id' => 2,
					'name' => self::USER,
					'parentId' => 1,
					'position' => 3
				], [
					'id' => 3,
					'name' => self::EDITOR,
					'parentId' => 2,
					'position' => 4
				], [
					'id' => 4,
					'name' => self::ADMIN,
					'parentId' => 3,
					'position' => 5
				], [
					'id' => 5,
					'name' => self::SUPERADMIN,
					'parentId' => null,
					'position' => 2
				]
			]);
		};
	}
}

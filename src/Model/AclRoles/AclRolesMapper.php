<?php

namespace NAttreid\Security\Model;

use NAttreid\Orm\Structure\Table;

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

	protected function createTable(Table $table)
	{
		$table->setDefaultDataFile(__DIR__ . '/roles.sql');

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
	}
}

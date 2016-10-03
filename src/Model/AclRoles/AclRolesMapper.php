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

	protected function loadDefaultData()
	{
		$this->insert([
			'parentId' => null,
			'name' => self::GUEST,
			'position' => 1
		]);
		$this->insert([
			'parentId' => 1,
			'name' => self::USER,
			'position' => 3
		]);
		$this->insert([
			'parentId' => 2,
			'name' => self::EDITOR,
			'position' => 4
		]);
		$this->insert([
			'parentId' => 3,
			'name' => self::ADMIN,
			'position' => 5
		]);
		$this->insert([
			'parentId' => null,
			'name' => self::SUPERADMIN,
			'position' => 2
		]);
	}

}

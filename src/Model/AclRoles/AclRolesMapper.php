<?php

namespace NAttreid\Security\Model;

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

	protected function createTable(\NAttreid\Orm\Structure\Table $table)
	{
		$table->addPrimaryKey('id')
			->int()
			->setAutoIncrement();
		$table->addColumn('name')
			->varChar(50)
			->setUnique();
		$table->addForeignKey('parentId', $table, NULL)
			->setDefault(NULL);
		$table->addColumn('position')
			->int()
			->setDefault(NULL)
			->setKey();
	}

	protected function loadDefaultData()
	{
		$this->insert([
			'parentId' => NULL,
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
			'parentId' => NULL,
			'name' => self::SUPERADMIN,
			'position' => 2
		]);
	}

}

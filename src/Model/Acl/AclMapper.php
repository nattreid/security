<?php

declare(strict_types=1);

namespace NAttreid\Security\Model\Acl;

use NAttreid\Orm\Structure\Table;
use NAttreid\Security\Model\AclResources\AclResourcesMapper;
use NAttreid\Security\Model\AclRoles\AclRolesMapper;
use NAttreid\Security\Model\Mapper;

/**
 * Acl Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclMapper extends Mapper
{

	protected function createTable(Table $table): void
	{
		$table->addPrimaryKey('id')
			->int()
			->setAutoIncrement();
		$table->addForeignKey('roleId', AclRolesMapper::class);
		$table->addForeignKey('resourceId', AclResourcesMapper::class);
		$table->addColumn('privilege')
			->varChar(20)
			->setKey();
		$table->addColumn('allowed')
			->bool()
			->setDefault(1)
			->setKey();
		$table->addUnique('roleId', 'resourceId', 'privilege');
	}

}

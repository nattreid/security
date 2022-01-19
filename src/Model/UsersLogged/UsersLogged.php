<?php

declare(strict_types=1);

namespace NAttreid\Security\Model\UsersLogged;

use NAttreid\Orm\Structure\Table;
use NAttreid\Security\Model\Mapper;
use NAttreid\Security\Model\Users\UsersMapper;

/**
 * UsersLogged
 *
 * @author Attreid <attreid@gmail.com>
 */
class UsersLogged extends Mapper
{

	protected function createTable(Table $table): void
	{
		$table->addPrimaryKey('id')
			->int()
			->setAutoIncrement();
		$table->addColumn('inserted')
			->timestamp();
		$table->addForeignKey('userId', UsersMapper::class);
	}
}
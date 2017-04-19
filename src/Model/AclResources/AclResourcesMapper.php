<?php

declare(strict_types=1);

namespace NAttreid\Security\Model\AclResources;

use NAttreid\Orm\Structure\Table;
use NAttreid\Security\Model\Acl\Acl;
use NAttreid\Security\Model\Mapper;
use NAttreid\Security\Model\Orm;

/**
 * Acl Resources Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclResourcesMapper extends Mapper
{

	protected function createTable(Table $table): void
	{
		$table->addPrimaryKey('id')
			->int()
			->setAutoIncrement();
		$table->addColumn('resource')
			->varChar(150)
			->setUnique();
		$table->addColumn('name')
			->varChar(150);
	}

	/**
	 * Smazani nepouzitych zdroju (pro prehlednost)
	 */
	public function deleteUnused(): void
	{
		/* @var $orm Orm */
		$orm = $this->getRepository()->getModel();
		$resources = [];
		$rules = $orm->acl->findAll();
		foreach ($rules as $rule) {
			/* @var $rule Acl */
			$resources[] = $rule->resource->id;
		}
		if (empty($resources)) {
			$this->connection->query('DELETE FROM %table', $this->getTableName());
		} else {
			$this->connection->query('DELETE FROM %table WHERE [id] NOT IN %i[]', $this->getTableName(), $resources);
		}
	}
}

<?php

namespace NAttreid\Security\Model;

use NAttreid\Orm\Structure\Table;

/**
 * Acl Resources Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclResourcesMapper extends Mapper
{

	protected function createTable(Table $table)
	{
		$table->addPrimaryKey('id')
			->int()
			->setAutoIncrement();
		$table->addColumn('name')
			->varChar(150)
			->setUnique();
	}

	/**
	 * Smazani nepouzitych zdroju (pro prehlednost)
	 */
	public function deleteUnused()
	{
		/* @var $orm \NAttreid\Security\Model\Orm */
		$orm = $this->getRepository()->getModel();
		$resources = [];
		$rules = $orm->acl->findAll();
		foreach ($rules as $rule) {
			/* @var $rule Acl */
			$resources[] = $rule->resource->id;
		}
		if (!empty($resources)) {
			$this->connection->query('DELETE FROM %table WHERE [id] NOT IN %i[]', $this->getTableName(), $resources);
		}
	}

}

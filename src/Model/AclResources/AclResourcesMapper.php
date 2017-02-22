<?php

namespace NAttreid\Security\Model\AclResources;

use NAttreid\Orm\Structure\Table;
use NAttreid\Security\Model\Acl\Acl;
use NAttreid\Security\Model\Mapper;
use NAttreid\Security\Model\Orm;
use Nette\Caching\Cache;

/**
 * Acl Resources Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclResourcesMapper extends Mapper
{
	private $tag = 'netta/pages';

	protected function createTable(Table $table)
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
	 * Smaze cache
	 */
	public function cleanCache()
	{
		$this->cache->clean([
			Cache::TAGS => [$this->tag]
		]);
	}

	/**
	 * Smazani nepouzitych zdroju (pro prehlednost)
	 */
	public function deleteUnused()
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

	/**
	 * @param $role
	 * @return ResourceItem[]
	 */
	public function getResources($role)
	{
		$key = 'resourceList-' . $role;
		$rows = $this->cache->load($key);
		if ($rows === null) {
			$rows = $this->cache->save($key, function () use ($role) {
				$result = [];
				$resources = $this->getRepository()->findAll()->orderBy('resource');
				foreach ($resources as $resource) {
					/* @var $resource AclResource */
					$list = explode('.', $resource->resource);
					end($list);
					$last = key($list);
					/* @var $current ResourceItem */
					$current = null;

					foreach ($list as $key => $row) {
						if ($current === null) {
							if (isset($result[$row])) {
								$current = $result[$row];
							} else {
								if ($key === $last) {
									$item = new ResourceItem($resource, $role);
								} else {
									$item = new ResourceItem($row, $role);
								}
								$current = $result[$row] = $item;
							}
						} else {
							if (isset($current->items[$row])) {
								$current = $current->items[$row];
							} else {
								if ($key === $last) {
									$item = new ResourceItem($resource, $role, $current);
								} else {
									$item = new ResourceItem($row, $role, $current);
								}
								$current = $current->addItem($row, $item);
							}
						}
					}
				}
				return $result;
			}, [
				Cache::TAGS => [$this->tag]
			]);
		}
		return $rows;
	}
}

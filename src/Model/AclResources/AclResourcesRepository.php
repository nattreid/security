<?php

namespace NAttreid\Security\Model;

use NAttreid\Orm\Repository;
use Nette\InvalidArgumentException;

/**
 * Acl Resources Repository
 *
 * @method AclResource getById($primaryValue)
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclResourcesRepository extends Repository
{

	/** @var AclResourcesMapper */
	protected $mapper;

	public static function getEntityClassNames()
	{
		return [AclResource::class];
	}

	/**
	 * Smazani nepouzitych zdroju (pro prehlednost)
	 */
	public function deleteUnused()
	{
		$this->mapper->deleteUnused();
	}

	/**
	 * @param $resource
	 * @return AclResource
	 */
	public function getByResource($resource)
	{
		return $this->getBy(['resource' => $resource]);
	}

	/**
	 * @param $role
	 * @param null $parent
	 * @return ResourceItem[]
	 */
	public function getResources($role, $parent = null)
	{
		$result = $this->mapper->getResources($role);
		if ($parent !== null) {
			$list = explode('.', $parent);
			foreach ($list as $name) {
				if (!isset($result[$name])) {
					throw new InvalidArgumentException;
				} else {
					$result = $result[$name]->items;
				}
			}
		}
		return $result;
	}

	/**
	 * Vrati pole [id, resource] serazene podle [resource]
	 * @return array
	 */
	public function fetchPairsByResource()
	{
		return $this->findAll()->orderBy('resource')->fetchPairs('id', 'resource');
	}
}

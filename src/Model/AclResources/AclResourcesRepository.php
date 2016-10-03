<?php

namespace NAttreid\Security\Model;

use NAttreid\Orm\Repository;

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
}

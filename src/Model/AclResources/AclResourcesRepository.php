<?php

namespace NAttreid\Security\Model;

use NAttreid\Orm\Repository;
use Nextras\Orm\Entity\IEntity;

/**
 * Acl Resources Repository
 *
 * @method AclResource|IEntity getById($primaryValue)
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

}

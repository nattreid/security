<?php

namespace NAttreid\Security\Model;

/**
 * Acl Resources Repository
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclResourcesRepository extends \NAttreid\Orm\Repository
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

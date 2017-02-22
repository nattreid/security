<?php

namespace NAttreid\Security\Model\Acl;

use NAttreid\Orm\Repository;

/**
 * Acl Repository
 *
 * @method Acl getById($primaryValue)
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclRepository extends Repository
{

	public static function getEntityClassNames()
	{
		return [Acl::class];
	}

	/**
	 * @param $resource
	 * @param $role
	 * @param string $privilege
	 * @return Acl|null
	 */
	public function getPermission($resource, $role, $privilege = Acl::PRIVILEGE_VIEW)
	{
		return $this->getBy([
			'this->resource->resource' => $resource,
			'this->role->name' => $role,
			'privilege' => $privilege
		]);
	}
}

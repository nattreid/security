<?php

declare(strict_types=1);

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

	public static function getEntityClassNames(): array
	{
		return [Acl::class];
	}

	/**
	 * @param string $resource
	 * @param string $role
	 * @param string $privilege
	 * @return Acl|null
	 */
	public function getPermission(string $resource, string $role, string $privilege = Acl::PRIVILEGE_VIEW): ?Acl
	{
		return $this->getBy([
			'this->resource->resource' => $resource,
			'this->role->name' => $role,
			'privilege' => $privilege
		]);
	}
}

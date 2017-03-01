<?php

declare(strict_types = 1);

namespace NAttreid\Security\Model\AclResources;

use NAttreid\Security\Model\Acl\Acl;
use NAttreid\Security\Model\Orm;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * Acl Resource
 *
 * @property int $id {primary}
 * @property string $resource
 * @property string|null $name
 * @property OneHasMany|Acl[] $permissions {1:m Acl::$resource}
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclResource extends Entity
{
	protected function getterName($value): string
	{
		return empty($value) ? $this->resource : $value;
	}

	/**
	 * @param string $role
	 * @param string $privilege
	 * @return bool
	 */
	public function isAllowed(string $role, string $privilege = Acl::PRIVILEGE_VIEW): bool
	{
		/* @var $orm Orm */
		$orm = $this->getModel();
		$permission = $orm->acl->getPermission($this->resource, $role, $privilege);

		if ($permission) {
			return $permission->allowed;
		}
		return false;
	}
}
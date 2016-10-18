<?php

namespace NAttreid\Security\Model;

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
	protected function getterName($value)
	{
		return empty($value) ? $this->resource : $value;
	}

	/**
	 * @param $role
	 * @param string $privilege
	 * @return bool
	 */
	public function isAllowed($role, $privilege = Acl::PRIVILEGE_VIEW)
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
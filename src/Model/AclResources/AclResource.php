<?php

namespace NAttreid\Security\Model;

use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * Acl Resource
 *
 * @property int $id {primary}
 * @property string $resource
 * @property AclResource|null $parent {m:1 AclResource::$children}
 * @property OneHasMany|AclResource[] $children {1:m AclResource::$parent}
 * @property string|null $name
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclResource extends Entity
{
	public function getterName()
	{
		return $this->name = empty($this->name) ? $this->resource : $this->name;
	}
}
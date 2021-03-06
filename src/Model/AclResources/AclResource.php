<?php

declare(strict_types=1);

namespace NAttreid\Security\Model\AclResources;

use NAttreid\Security\Model\Acl\Acl;
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
}
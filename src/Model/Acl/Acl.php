<?php

declare(strict_types = 1);

namespace NAttreid\Security\Model\Acl;

use NAttreid\Security\Model\AclResources\AclResource;
use NAttreid\Security\Model\AclRoles\AclRole;
use Nextras\Orm\Entity\Entity;

/**
 * Acl
 *
 * @property int $id {primary}
 * @property AclRole $role {m:1 AclRole, oneSided=true}
 * @property AclResource $resource {m:1 AclResource::$permissions}
 * @property string $privilege {enum self::PRIVILEGE_*}
 * @property bool $allowed {default true}
 *
 * @author Attreid <attreid@gmail.com>
 */
class Acl extends Entity
{
	const
		PRIVILEGE_VIEW = 'view',
		PRIVILEGE_EDIT = 'edit';
}

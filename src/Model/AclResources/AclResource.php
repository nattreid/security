<?php

namespace NAttreid\Security\Model;

use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * Acl Resource
 *
 * @property int $id {primary}
 * @property string $resource
 * @property AclResource $parent {m:1 Resource::$children}
 * @property OneHasMany|AclResource[] $children {1:m Resource::$$parent}
 * @property string $name
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclResource extends Entity
{

}

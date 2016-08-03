<?php

namespace NAttreid\Security\Model;

/**
 * Acl
 * 
 * @property int $id {primary}
 * @property AclRole $role {m:1 AclRole, oneSided=true}
 * @property AclResource $resource {m:1 AclResource, oneSided=true}
 * @property string $privilege {enum self::PRIVILEGE_*}
 * @property boolean $allowed {default TRUE}
 * 
 * @author Attreid <attreid@gmail.com>
 */
class Acl extends \Nextras\Orm\Entity\Entity {

    const
            PRIVILEGE_VIEW = 'view',
            PRIVILEGE_EDIT = 'edit';

}

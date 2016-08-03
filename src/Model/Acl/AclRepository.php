<?php

namespace NAttreid\Security\Model;

/**
 * Acl Repository
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclRepository extends \NAttreid\Orm\Repository {

    public static function getEntityClassNames() {
        return [Acl::class];
    }

}

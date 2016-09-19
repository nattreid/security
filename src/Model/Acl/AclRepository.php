<?php

namespace NAttreid\Security\Model;

use NAttreid\Orm\Repository;

/**
 * Acl Repository
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclRepository extends Repository
{

	public static function getEntityClassNames()
	{
		return [Acl::class];
	}

}

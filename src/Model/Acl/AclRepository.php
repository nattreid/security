<?php

namespace NAttreid\Security\Model;

use NAttreid\Orm\Repository;
use Nextras\Orm\Entity\IEntity;

/**
 * Acl Repository
 *
 * @method Acl getById($primaryValue)
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

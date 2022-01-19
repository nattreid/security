<?php

declare(strict_types=1);

namespace NAttreid\Security\Model\UsersLogged;

use NAttreid\Orm\Repository;

/**
 * UsersLogged Repository
 *
 * @author Attreid <attreid@gmail.com>
 */
class UsersLoggedRepository extends Repository
{
	public static function getEntityClassNames(): array
	{
		return [UserLogged::class];
	}
}
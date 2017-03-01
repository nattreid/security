<?php

declare(strict_types = 1);

namespace NAttreid\Security\Model;

use NAttreid\Security\Model\Acl\AclRepository;
use NAttreid\Security\Model\AclResources\AclResourcesRepository;
use NAttreid\Security\Model\AclRoles\AclRolesRepository;
use NAttreid\Security\Model\Users\UsersRepository;
use Nextras\Orm\Model\Model;

/**
 * @property-read AclRepository $acl
 * @property-read AclResourcesRepository $aclResources
 * @property-read AclRolesRepository $aclRoles
 * @property-read UsersRepository $users
 *
 * @author Attreid <attreid@gmail.com>
 */
class Orm extends Model
{

}

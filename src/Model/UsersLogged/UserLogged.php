<?php

declare(strict_types=1);

namespace NAttreid\Security\Model\UsersLogged;

use DateTimeImmutable;
use NAttreid\Security\Model\Users\User;
use Nextras\Orm\Entity\Entity;

/**
 * UserLogged
 *
 * @property int $id {primary}
 * @property DateTimeImmutable $inserted
 * @property User $user {m:1 User::$logged}
 *
 * @author Attreid <attreid@gmail.com>
 */
class UserLogged extends Entity
{

}
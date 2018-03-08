<?php

declare(strict_types=1);

namespace NAttreid\Security\Model\AclRoles;

use NAttreid\Orm\Repository;
use Nextras\Orm\Collection\ICollection;

/**
 * Acl Roles Repository
 *
 * @method AclRole getById($primaryValue)
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclRolesRepository extends Repository
{

	public static function getEntityClassNames(): array
	{
		return [AclRole::class];
	}

	/**
	 * Vrati roli podle jmena
	 * @param string $name
	 * @return AclRole|null
	 */
	public function getByName(string $name): ?AclRole
	{
		return $this->getBy(['name' => $name]);
	}

	/**
	 * Vrati serazene role pro acl
	 * @return ICollection|AclRole[]
	 */
	public function findSorted(): ICollection
	{
		return $this->findAll()->orderBy('position');
	}

	/**
	 * Vrati pole [id, name] serazene podle [id]
	 * @return array
	 */
	public function fetchPairs(): array
	{
		return $this->findAll()->orderBy('id')->fetchPairs('id', 'title');
	}

}

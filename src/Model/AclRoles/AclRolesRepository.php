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
	 * @param bool $viewSuperadmin
	 * @return array
	 */
	public function fetchPairs(bool $viewSuperadmin = false): array
	{
		return $this->findRoles($viewSuperadmin)->orderBy('id')->fetchPairs('id', 'title');
	}

	/**
	 * @param bool $viewSuperadmin
	 * @return ICollection|AclRole[]
	 */
	public function findRoles(bool $viewSuperadmin = false): ICollection
	{
		if ($viewSuperadmin) {
			return $this->findAll();
		} else {
			return $this->findBy([
				'name!=' => AclRolesMapper::SUPERADMIN
			]);
		}
	}

}

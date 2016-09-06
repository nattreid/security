<?php

namespace NAttreid\Security\Model;

use Nextras\Orm\Collection\ICollection;

/**
 * Acl Roles Repository
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclRolesRepository extends \NAttreid\Orm\Repository
{

	/** @var AclRolesMapper */
	protected $mapper;

	public static function getEntityClassNames()
	{
		return [AclRole::class];
	}

	/**
	 * Vrati roli podle jmena
	 * @param string $name
	 * @return AclRole
	 */
	public function getByName($name)
	{
		return $this->findBy(['name' => $name])->fetch();
	}

	/**
	 * Vrati serazene role pro acl
	 * @return ICollection|AclRole[]
	 */
	public function findSorted()
	{
		return $this->findAll()->orderBy('position');
	}

	/**
	 * Vrati pole [id, name] serazene podle [name]
	 * @return array
	 */
	public function fetchPairs()
	{
		return $this->findAll()->orderBy('id')->fetchPairs('id', 'title');
	}

}

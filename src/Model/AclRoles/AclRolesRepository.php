<?php

namespace NAttreid\Security\Model;

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
		return $this->getBy(['name' => $name]);
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
	 * Vrati pole [id, name] serazene podle [id]
	 * @return array
	 */
	public function fetchPairs()
	{
		return $this->findAll()->orderBy('id')->fetchPairs('id', 'title');
	}

}

<?php

namespace NAttreid\Security\Model\AclResources;

use Kdyby\Translation\ITranslator;
use NAttreid\Orm\Repository;
use Nette\InvalidArgumentException;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Mapper\IMapper;
use Nextras\Orm\Repository\IDependencyProvider;

/**
 * Acl Resources Repository
 *
 * @method AclResource getById($primaryValue)
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclResourcesRepository extends Repository
{

	/** @var AclResourcesMapper */
	protected $mapper;

	/** @var ITranslator */
	private $translator;


	public function __construct(IMapper $mapper, IDependencyProvider $dependencyProvider = null, ITranslator $translator)
	{
		parent::__construct($mapper, $dependencyProvider);
		$this->translator = $translator;
	}

	public static function getEntityClassNames()
	{
		return [AclResource::class];
	}

	/**
	 * Smazani nepouzitych zdroju (pro prehlednost)
	 */
	public function deleteUnused()
	{
		$this->mapper->deleteUnused();
	}

	/**
	 * Smaze cache
	 */
	public function cleanCache()
	{
		$this->mapper->cleanCache();
	}

	/**
	 * @param $resource
	 * @return AclResource
	 */
	public function getByResource($resource)
	{
		return $this->getBy(['resource' => $resource]);
	}

	/**
	 * @param string $role
	 * @param string $parent
	 * @return ResourceItem[]
	 */
	public function getResources($role, $parent = null)
	{
		$result = $this->mapper->getResources($role);
		if ($parent !== null) {
			$list = explode('.', $parent);
			foreach ($list as $name) {
				if (!isset($result[$name])) {
					throw new InvalidArgumentException;
				} else {
					$result = $result[$name]->items;
				}
			}
		}
		return $result;
	}

	/**
	 * @param string $role
	 * @param string $resource
	 * @return ResourceItem
	 */
	public function getResource($role, $resource)
	{
		$result = null;
		$resources = $this->mapper->getResources($role);
		$list = explode('.', $resource);
		foreach ($list as $name) {
			if (!isset($resources[$name])) {
				throw new InvalidArgumentException;
			} else {
				$result = $resources[$name];
				$resources = $result->items;
			}
		}
		return $result;
	}

	/**
	 * @return ICollection|AclResource[]
	 */
	public function findByResource()
	{
		return $this->findAll()->orderBy('resource');
	}

	/**
	 * Vrati pole [id, translatedName (resource) ] serazene podle [resource]
	 * @return array
	 */
	public function fetchPairsByResourceName()
	{
		$result = [];
		$rows = $this->findByResource();
		foreach ($rows as $row) {
			$result[$row->id] = $this->translator !== null ?
				$this->translator->translate($row->name) . ' (' . $row->resource . ')'
				: $row->resource;
		}
		return $result;
	}
}

<?php

declare(strict_types=1);

namespace NAttreid\Security\Model\AclResources;

use Kdyby\Translation\ITranslator;
use NAttreid\Orm\Repository;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Mapper\IMapper;
use Nextras\Orm\Repository\IDependencyProvider;

/**
 * Acl Resources Repository
 *
 * @method AclResource getById($primaryValue)
 * @method void deleteUnused() Smazani nepouzitych zdroju (pro prehlednost)
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclResourcesRepository extends Repository
{
	/** @var ITranslator */
	private $translator;


	public function __construct(IMapper $mapper, IDependencyProvider $dependencyProvider = null, ITranslator $translator)
	{
		parent::__construct($mapper, $dependencyProvider);
		$this->translator = $translator;
	}

	public static function getEntityClassNames(): array
	{
		return [AclResource::class];
	}

	/**
	 * @param string $resource
	 * @return AclResource
	 */
	public function getByResource(string $resource): ?AclResource
	{
		return $this->getBy(['resource' => $resource]);
	}

	/**
	 * @return ICollection|AclResource[]
	 */
	public function findByResource(): ICollection
	{
		return $this->findAll()->orderBy('resource');
	}

	/**
	 * Vrati pole [id, translatedName (resource) ] serazene podle [resource]
	 * @return array
	 */
	public function fetchPairsByResourceName(): array
	{
		$result = [];
		$rows = $this->findByResource();
		foreach ($rows as $row) {
			$result[$row->id] = $row->resource . ($this->translator ? ' - ( ' . $this->translator->translate($row->name) . ' )' : '');
		}
		return $result;
	}
}

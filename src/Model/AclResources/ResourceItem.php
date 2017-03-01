<?php
declare(strict_types = 1);

namespace NAttreid\Security\Model\AclResources;

use Nette\NotSupportedException;
use Nette\SmartObject;

/**
 * Class ResourceItem
 *
 * @property-read string $resource
 * @property-read string $name
 * @property-read ResourceItem[] $items
 * @property-read string $id
 * @property-read bool $allowed
 *
 * @author Attreid <attreid@gmail.com>
 */
class ResourceItem implements \ArrayAccess
{
	use SmartObject;

	/** @var string */
	private $resource;

	/** @var string */
	private $name;

	/** @var int */
	private $id;

	/** @var ResourceItem[] */
	private $items;

	/** @var bool */
	private $_allowed = false;

	/** @var ResourceItem|null */
	private $parent;

	/**
	 * ResourceItem constructor.
	 * @param AclResource|string $data
	 * @param string $role
	 * @param ResourceItem|null $parent
	 */
	public function __construct($data, string $role, self $parent = null)
	{
		if ($data instanceof AclResource) {
			$this->name = $data->name;
			$this->id = $this->resource = $data->resource;
			$this->_allowed = $data->isAllowed($role);
		} else {
			$this->id = ($parent !== null ? $parent->id . '.' : '') . $data;
			$this->name = $this->id . '.title';
		}
		$this->parent = $parent;
		$this->setParentPermission($this->allowed);
	}

	private function setParentPermission($permission)
	{
		if ($this->parent) {
			if (!$this->parent->resource) {
				$this->parent->_allowed &= $permission;
			}
			$this->parent->setParentPermission($permission);
		}
	}

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * @return bool
	 */
	public function isAllowed(): bool
	{
		return $this->_allowed;
	}

	/**
	 * @return string
	 */
	protected function getResource(): string
	{
		return $this->resource;
	}

	/**
	 * @return string
	 */
	protected function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return ResourceItem[]
	 */
	protected function getItems(): array
	{
		return $this->items;
	}

	/**
	 * @return bool
	 */
	public function hasChildren(): bool
	{
		return count($this->items) > 0;
	}

	/**
	 * @param string $name
	 * @param ResourceItem $item
	 * @return ResourceItem
	 */
	public function addItem(string $name, ResourceItem $item): ResourceItem
	{
		return $this->items[$name] = $item;
	}


	/**
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return bool true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to bool if non-bool was returned.
	 * @since 5.0.0
	 */
	public function offsetExists($offset)
	{
		throw new NotSupportedException;
	}

	/**
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet($offset)
	{
		return $this->$offset;
	}

	/**
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet($offset, $value)
	{
		throw new NotSupportedException;
	}

	/**
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset($offset)
	{
		throw new NotSupportedException;
	}
}
<?php

namespace NAttreid\Security\Model;

use Nextras\Orm\Relationships\OneHasMany,
    Nextras\Orm\Relationships\ManyHasMany,
    Nextras\Dbal\UniqueConstraintViolationException,
    Nette\InvalidArgumentException,
    Nette\Utils\Strings;

/**
 * Acl Role
 * 
 * @property int $id {primary}
 * @property string $name
 * @property AclRole|NULL $parent {m:1 AclRole::$children}
 * @property OneHasMany|AclRole[] $children {1:m AclRole::$parent, orderBy=position}
 * @property int $position {default 0}
 * @property string $title {virtual}
 * @property ManyHasMany|User[] $users {m:n User::$roles}
 * 
 * @author Attreid <attreid@gmail.com>
 */
class AclRole extends \Nextras\Orm\Entity\Entity {

    protected function onBeforePersist() {
        if ($this->parent) {
            $this->position = $this->parent->position + 1;
        }
    }

    /**
     * Ulozi jmeno
     * @param string $name
     * @throws UniqueConstraintViolationException
     * @throws InvalidArgumentException
     */
    public function setName($name) {
        if ($name === '') {
            throw new InvalidArgumentException;
        }
        if (Strings::match($name, '/[^A-Za-z0-9_]/')) {
            throw new InvalidArgumentException('Name contains invalid characters');
        }

        /* @var $repository AclRolesRepository */
        $repository = $this->getRepository();
        $role = $repository->getByName($name);
        if ($role !== NULL && $role !== $this) {
            throw new UniqueConstraintViolationException("Role '$name' exists");
        }
        $this->name = $name;
    }

    /**
     * Vrati nazev role
     * @return string
     */
    public function getterTitle() {
        return $this->name;
    }

    /**
     * Ulozi nazev role
     * @param string $title
     * @return string
     */
    public function setterTitle($title) {
        return $title;
    }

}

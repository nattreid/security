<?php

namespace NAttreid\Security;

use Nette\Caching\Cache,
    Nextras\Orm\Model\Model,
    NAttreid\Security\Model\Orm,
    NAttreid\Security\Model\AclRolesMapper,
    NAttreid\AppManager\AppManager,
    NAttreid\Security\Model\AclRole,
    NAttreid\Security\Model\AclResource,
    NAttreid\Security\Model\Acl,
    Nette\Security\Permission;

/**
 * Vytvoreni pravidel acl
 * 
 * @author Attreid <attreid@gmail.com>
 */
class AuthorizatorFactory {

    private $tag = 'ACL/cache';

    /** @var Cache */
    private $cache;

    /** @var Orm */
    private $orm;

    public function __construct(\Nette\Caching\IStorage $cacheStorage, Model $orm, AppManager $app = NULL) {
        $this->cache = new Cache($cacheStorage, 'nattreid-security-acl');
        $this->orm = $orm;
        if ($app !== NULL) {
            $app->onInvalidateCache[] = [$this, 'cleanCache'];
        }
        $this->orm->aclResources->onFlush[] = $this->orm->acl->onFlush[] = $this->orm->aclRoles->onFlush[] = function($persisted, $removed) {
            if (!empty($persisted) || !empty($removed)) {
                $this->cleanCache();
            }
        };
    }

    /**
     * Smaze cache
     */
    public function cleanCache() {
        $this->cache->clean([
            Cache::TAGS => [$this->tag]
        ]);
    }

    /**
     * Vytvoreni pravidel
     * @return \Nette\Security\IAuthorizator
     */
    public function create() {
        $key = 'AuthorizatorCache';
        $result = $this->cache->load($key);
        if ($result === NULL) {
            $result = $this->cache->save($key, function() {
                $permission = new Permission;

                $this->createRoles($permission);
                $this->createResource($permission);
                $this->createRules($permission);

                // povoleni vsech prav pro superadmina
                $permission->allow(AclRolesMapper::SUPERADMIN);

                return $permission;
            }, [
                Cache::TAGS => [$this->tag]
            ]);
        }
        return $result;
    }

    /**
     * Vytvoreni roli
     * @param Permission $permission
     */
    private function createRoles(Permission $permission) {
        /* @var $role AclRole */
        foreach ($this->orm->aclRoles->findSorted() as $role) {
            $parent = null;
            if ($role->parent) {
                $parent = $role->parent->name;
            }
            $permission->addRole($role->name, $parent);
        }
    }

    /**
     * Vytvoreni zdroju
     * @param Permission $permission
     */
    private function createResource(Permission $permission) {
        /* @var $resource AclResource */
        foreach ($this->orm->aclResources->findAll() as $resource) {
            $permission->addResource($resource->name);
        }
    }

    /**
     * Vytvoreni pravidel
     * @param Permission $permission
     */
    private function createRules(Permission $permission) {
        /* @var $rule Acl */
        foreach ($this->orm->acl->findAll() as $rule) {
            if ($rule->allowed) {
                $permission->allow($rule->role->name, $rule->resource->name, $rule->privilege);
            } else {
                $permission->denny($rule->role->name, $rule->resource->name, $rule->privilege);
            }
        }
    }

}

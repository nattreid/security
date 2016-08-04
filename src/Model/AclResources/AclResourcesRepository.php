<?php

namespace NAttreid\Security\Model;

/**
 * Acl Resources Repository
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclResourcesRepository extends \NAttreid\Orm\Repository {

    public static function getEntityClassNames() {
        return [AclResource::class];
    }

    /**
     * Smazani nepouzitych zdroju (pro prehlednost)
     */
    public function deleteUnused() {
        /* @var $orm \NAttreid\Security\Model\Orm */
        $orm = $this->getModel();
        $resources = $orm->acl->findAll();
        foreach ($resources as $resource) {
            $orm->remove($resource);
        }
        $orm->flush();
    }

}

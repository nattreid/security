<?php

namespace NAttreid\Security\Model;

/**
 * Acl Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclMapper extends Mapper {

    protected function createTable(\NAttreid\Orm\Structure\Table $table) {
        $table->addPrimaryKey('id')
                ->int()
                ->setAutoIncrement();
        $table->addForeignKey('roleId', AclRolesMapper::class);
        $table->addForeignKey('resourceId', AclResourcesMapper::class);
        $table->addColumn('privilege')
                ->varChar(20)
                ->setKey();
        $table->addColumn('allowed')
                ->boolean()
                ->setDefault(1)
                ->setKey();
        $table->setUnique('roleId', 'resourceId', 'privilege');
    }

}

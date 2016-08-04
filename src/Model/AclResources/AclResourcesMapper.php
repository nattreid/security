<?php

namespace NAttreid\Security\Model;

/**
 * Acl Resources Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
class AclResourcesMapper extends Mapper {

    protected function createTable(\NAttreid\Orm\Structure\Table $table) {
        $table->addPrimaryKey('id')
                ->int()
                ->setAutoIncrement();
        $table->addColumn('name')
                ->varChar(150)
                ->setUnique();
    }

}

<?php

namespace NAttreid\Security\Model;

/**
 * Mapper
 *
 * @author Attreid <attreid@gmail.com>
 */
abstract class Mapper extends \NAttreid\Orm\Mapper
{

	public function getTablePrefix()
	{
		return '_';
	}

}

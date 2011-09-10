<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Security;

use Nella\Security\Authorizator;

class AuthorizatorTest extends \Nella\Testing\TestCase
{
	public function testParseAnnotation()
	{
		$this->assertEquals(
			array(Authorizator::ROLE => "admin", Authorizator::RESOURCE => "foo", Authorizator::PRIVILEGE => "bar"),
			Authorizator::parseAnnotations('NellaTests\Security\Authorizator\Foo', 'bar1'),
			"::parseAnnotations allowed"
		);

		$this->assertEquals(
			array(Authorizator::ROLE => "admin", Authorizator::RESOURCE => "foo", Authorizator::PRIVILEGE => "bar"),
			Authorizator::parseAnnotations('NellaTests\Security\Authorizator\Foo', 'bar2'),
			"::parseAnnotations role, resource, privilege"
		);

		$this->assertEquals(
			array(Authorizator::ROLE => NULL, Authorizator::RESOURCE => NULL, Authorizator::PRIVILEGE => NULL),
			Authorizator::parseAnnotations('NellaTests\Security\Authorizator\Foo', 'bar3'),
			"::parseAnnotations none"
		);
	}
}

namespace NellaTests\Security\Authorizator;

class Foo
{
	/**
	 * @allowed(role=admin,resource=foo,privilege=bar)
	 */
	public function bar1() { }

	/**
	 * @role(admin)
	 * @resource(foo)
	 * @privilege(bar)
	 */
	public function bar2() { }

	public function bar3() { }
}

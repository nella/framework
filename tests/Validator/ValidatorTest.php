<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests\Validator;

require_once __DIR__ . "/../bootstrap.php";

class ValidatorTest extends \Nella\Testing\TestCase
{
	/** @var ValidatorMock */
	private $validator;

	public function setup()
	{
		$this->validator = new ValidatorMock;
	}

	public function testGetClassMetadataFactory()
	{
		$this->assertInstanceOf('Nella\Validator\IClassMetadataFactory', $this->validator->getClassMetadataFactory(),
			"->getClassMetadataFactory() instance of IClassMetadataFactory");
		$this->assertInstanceOf('Nella\Validator\IClassMetadataFactory', $this->validator->classMetadataFactory,
			"->classMetadataFactory instance of IClassMetadataFactory");
	}

	public function testAddValidator()
	{
		$this->validator->addValidator('test', function() {}, "Foo");
		$validator = $this->validator->getValidator('test');
		$this->assertInstanceOf('Closure', $validator[0], "is added valid callback");
		$this->assertEquals("Foo", $validator[1], "is added valid message");
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddValidatorEmptyIdException()
	{
		$this->validator->addValidator(NULL, NULL, NULL);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddValidatorInvalidIdException()
	{
		$this->validator->addValidator('...', NULL, NULL);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddValidatorInvalidCallbackException()
	{
		$this->validator->addValidator('test', NULL, NULL);
	}

	public function testValidate()
	{
		$errors = $this->validator->validate(new Validator\Bar);
		$this->assertFalse(isset($errors['foo']), "foo property ok");
	}

	public function testValidateNullables()
	{
		$errors = $this->validator->validate(new Validator\Baz);
		$this->assertFalse((bool) count($errors), "all property valid");
	}

	/******************************************************** validators *******************************************************/

	public function dataValidateUrlValid()
	{
		return array(
			array("http://domain.com"),
			array("http://domain.com/"),
			array("http://domain.com/l3tt3rsAndNumb3rs"),
			array("http://domain.com/has-dash/"),
			array("http://has-dash.a.domain.com"),
			array("https://domain.com"),
			array("https://domain.com/"),
			array("https://domain.com/l3tt3rsAndNumb3rs"),
			array("https://domain.com/has-dash/"),
			array("https://has-dash.a.domain.com"),
			array("http://127.0.0.1/"),
			//array("ftp://domain.com"),
			//array("ftp://domain.com/"),
			//array("ftp://domain.com/l3tt3rsAndNumb3rs"),
			//array("ftp://domain.com/has-dash/"),
			//array("ftp://has-dash.a.domain.com"),
			array("/l3tt3rsAndNumb3rs"),
			array("/has-dash/"),
			array("/l3tt3rsAndNumb3rs/has-dash.pdf"),
		);
	}

	/**
	 * @dataProvider dataValidateUrlValid
	 */
	public function testValidateUrlValid($url)
	{
		$this->assertTrue(ValidatorMock::validateUrl($url), "::url '$url'");
	}

	public function dataValidateUrlInvalid()
	{
		return array(
			array("/"),
			array(""),
			//array("l3tt3rsAndNumb3rs"),
			//array("l3tt3rsAndNumb3rs/has-dash.pdf"),
		);
	}

	/**
	 * @dataProvider dataValidateUrlInvalid
	 */
	public function testValidateUrlInvalid($url)
	{
		$this->assertFalse(ValidatorMock::validateUrl($url), "::url '$url'");
	}

	public function dataValidateEmailValid()
	{
		return array(
			array("l3tt3rsAndNumb3rs@domain.com"),
			array("has-dash@domain.com"),
			array("hasApostrophe.o'leary@domain.org"),
			array("uncommonTLD@domain.museum"),
			array("uncommonTLD@domain.travel"),
			array("uncommonTLD@domain.mobi"),
			array("countryCodeTLD@domain.uk"),
			array("countryCodeTLD@domain.rw"),
			array("lettersInDomain@911.com"),
			array("underscore_inLocal@domain.net"),
			array("subdomain@sub.domain.com"),
			array("local@dash-inDomain.com"),
			array("dot.inLocal@foo.com"),
			array("a@singleLetterLocal.org"),
			array("singleLetterDomain@x.org"),
			array("&*=?^+{}'~@validCharsInLocal.net"),
			array("foor@bar.newTLD"),
		);
	}

	/**
	 * @dataProvider dataValidateEmailValid
	 */
	public function testValidateEmailValid($email)
	{
		$this->assertTrue(ValidatorMock::validateEmail($email), "::email '$email'");
	}

	public function dataValidateEmailInvalid()
	{
		return array(
			array("missingDomain@.com"),
			array("@missingLocal.org"),
			array("missingatSign.net"),
			array("missingDot@com"),
			array("two@@signs.com"),
			array("colonButNoPort@127.0.0.1:"),
			array(""),
			array(".localStartsWithDot@domain.com"),
			array("localEndsWithDot.@domain.com"),
			array("two..consecutiveDots@domain.com"),
			array("domainStartsWithDash@-domain.com"),
			array("domainEndsWithDash@domain-.com"),
			array("missingTLD@domain."),
			array("! \"#\$%(),/;<>[]`|@invalidCharsInLocal.org"),
			array("invalidCharsInDomain@! \"#\$%(),/;<>_[]`|.org"),
			array("local@SecondLevelDomainNamesAreInvalidIfTheyAreLongerThan64Charactersss.org"),
		);
	}

	/**
	 * @dataProvider dataValidateEmailInvalid
	 */
	public function testValidateEmailInvalid($email)
	{
		$this->assertFalse(ValidatorMock::validateEmail($email), "::email '$email'");
	}

	public function dataValidateTypeValid()
	{
		return array(
			array("test", 'string'),
			array("test", 'string'),
			array(1, 'int'),
			array(1.1, 'float'),
			array(1.1, 'double'),
			array(FALSE, 'bool'),
			array(NULL, 'null'),
			array("test", 'scalar'),
			array(new \stdClass, 'object'),
		);
	}

	/**
	 * @dataProvider dataValidateTypeValid
	 */
	public function testValidateTypeValid($data, $type)
	{
		$this->assertTrue(ValidatorMock::validateType($data, $type), "validate valid $type");
	}

	public function dataValidateTypeInvalid()
	{
		return array(
			array(NULL, 'string'),
			array(NULL, 'int'),
			array(NULL, 'float'),
			array(NULL, 'double'),
			array(NULL, 'bool'),
			array("test", 'null'),
			array(NULL, 'scalar'),
			array(NULL, 'object'),
		);
	}

	/**
	 * @dataProvider dataValidateTypeInvalid
	 */
	public function testValidateTypeInvalid($data, $type)
	{
		$this->assertFalse(ValidatorMock::validateType($data, $type), "validate valid $type");
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testValidateTypeInvalidTypeException()
	{
		ValidatorMock::validateType(NULL, NULL);
	}

	public function testValidateInstance()
	{
		$this->assertTrue(ValidatorMock::validateInstance(new Validator\Foo, 'NellaTests\Validator\Validator\Foo'), "validate valid Foo instance");
		$this->assertFalse(ValidatorMock::validateInstance(NULL, 'NellaTests\Validator\Foo'), "validate invalid Foo instance");
	}

	public function testValidateNotNull()
	{
		$this->assertTrue(ValidatorMock::validateNotNull(new \stdClass), "validate valid not null");
		$this->assertFalse(ValidatorMock::validateNotNull(NULL), "validate invalid not null");
		$this->assertFalse(ValidatorMock::validateNotNull(""), "validate invalid not null - empty string");
	}

	public function testValidateMin()
	{
		$this->assertTrue(ValidatorMock::validateMin(10, 9), "validate valid min 9");
		$this->assertFalse(ValidatorMock::validateMin(1, 9), "validate invalid min 9");
	}

	public function testValidateMax()
	{
		$this->assertTrue(ValidatorMock::validateMax(1, 9), "validate valid max 9");
		$this->assertFalse(ValidatorMock::validateMax(10, 9), "validate invalid max 9");
	}

	public function testValidateMinLength()
	{
		$this->assertTrue(ValidatorMock::validateMinLength("Lorem ipsum dolor", 9), "validate valid min length 9");
		$this->assertFalse(ValidatorMock::validateMinLength("foo", 9), "validate invalid min length 9");
	}

	public function testValidateMaxLength()
	{
		$this->assertTrue(ValidatorMock::validateMaxLength("foo", 9), "validate valid max length 9");
		$this->assertFalse(ValidatorMock::validateMaxLength("Lorem ipsum dolor", 9), "validate invalid max length 9");
	}

	public function testValidateRegexp()
	{
		$this->assertTrue(ValidatorMock::validateRegexp("1", "~[0-9]~i"), "validate valid regexp");
		$this->assertFalse(ValidatorMock::validateRegexp("Lorem ipsum dolor", "~[0-9]~i"), "validate invalid regexp");
	}
}

namespace NellaTests\Validator\Validator;

class Foo
{
	/**
	 * @validate(url,minlength=20)
	 * @var mixed
	 */
	private $foo;
}

class Bar extends Foo
{
	/**
	 * @var mixed
	 */
	private $bar;
}

class Baz
{
	/**
	 * @validate(nullable,url)
	 * @var string
	 */
	private $foo;
}

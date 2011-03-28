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

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
	/** @var ValidatorMock */
	private $validator;
	
	public function setUp()
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
		$errors = $this->validator->validate(new Bar);
		$this->assertTrue(isset($errors['foo']), "foo property failed");
		$this->assertEquals(2, count($errors['foo']), "foo property 2 errors");
	}
	
	public function testValidateNullables()
	{
		$errors = $this->validator->validate(new Baz);
		$this->assertFalse((bool) count($errors), "all property valid");
	}
	
	/***************************************************************** validators ***************************************************************/
	
	public function testValidateUrl()
	{
		$this->assertTrue(ValidatorMock::validateUrl("http://domain.com"), "::url http://domain.com");
		$this->assertTrue(ValidatorMock::validateUrl("http://domain.com/"), "::url http://domain.com/");
		$this->assertTrue(ValidatorMock::validateUrl("http://domain.com/l3tt3rsAndNumb3rs"), "::url http://domain.com/l3tt3rsAndNumb3rs");
		$this->assertTrue(ValidatorMock::validateUrl("http://domain.com/has-dash/"), "::url http://domain.com/has-dash/");
		$this->assertTrue(ValidatorMock::validateUrl("http://has-dash.a.domain.com"), "::url http://has-dash.a.domain.com");
		$this->assertTrue(ValidatorMock::validateUrl("https://domain.com"), "::url https://domain.com");
		$this->assertTrue(ValidatorMock::validateUrl("https://domain.com/"), "::url https://domain.com/");
		$this->assertTrue(ValidatorMock::validateUrl("https://domain.com/l3tt3rsAndNumb3rs"), "::url https://domain.com/l3tt3rsAndNumb3rs");
		$this->assertTrue(ValidatorMock::validateUrl("https://domain.com/has-dash/"), "::url https://domain.com/has-dash/");
		$this->assertTrue(ValidatorMock::validateUrl("https://has-dash.a.domain.com"), "::url https://has-dash.a.domain.com");
		$this->assertTrue(ValidatorMock::validateUrl("http://127.0.0.1/"), "::url http://127.0.0.1/");
		/*$this->assertTrue(ValidatorMock::validateUrl("ftp://domain.com"), "::url ftp://domain.com");
		$this->assertTrue(ValidatorMock::validateUrl("ftp://domain.com/"), "::url ftp://domain.com/");
		$this->assertTrue(ValidatorMock::validateUrl("ftp://domain.com/l3tt3rsAndNumb3rs"), "::url ftp://domain.com/l3tt3rsAndNumb3rs");
		$this->assertTrue(ValidatorMock::validateUrl("ftp://domain.com/has-dash/"), "::url ftp://domain.com/has-dash/");
		$this->assertTrue(ValidatorMock::validateUrl("ftp://has-dash.a.domain.com"), "::url ftp://has-dash.a.domain.com");*/
		$this->assertTrue(ValidatorMock::validateUrl("/l3tt3rsAndNumb3rs"), "::url /l3tt3rsAndNumb3rs");
		$this->assertTrue(ValidatorMock::validateUrl("/has-dash/"), "::url /has-dash/");
		$this->assertTrue(ValidatorMock::validateUrl("/l3tt3rsAndNumb3rs/has-dash.pdf"), "::url /l3tt3rsAndNumb3rs/has-dash.pdf");

		$this->assertFalse(ValidatorMock::validateUrl("/"), "::url /");
		$this->assertFalse(ValidatorMock::validateUrl(""), "::url ''");
		/*$this->assertFalse(ValidatorMock::validateUrl("l3tt3rsAndNumb3rs"), "::url l3tt3rsAndNumb3rs");
		$this->assertFalse(ValidatorMock::validateUrl("l3tt3rsAndNumb3rs/has-dash.pdf"), "::url l3tt3rsAndNumb3rs/has-dash.pdf");*/
	}

	public function testValidateEmail()
	{
		$this->assertTrue(ValidatorMock::validateEmail("l3tt3rsAndNumb3rs@domain.com"), "::email l3tt3rsAndNumb3rs@domain.com");
		$this->assertTrue(ValidatorMock::validateEmail("has-dash@domain.com"), "::email has-dash@domain.com");
		$this->assertTrue(ValidatorMock::validateEmail("hasApostrophe.o'leary@domain.org"), "::email hasApostrophe.o'leary@domain.org");
		$this->assertTrue(ValidatorMock::validateEmail("uncommonTLD@domain.museum"), "::email uncommonTLD@domain.museum");
		$this->assertTrue(ValidatorMock::validateEmail("uncommonTLD@domain.travel"), "::email uncommonTLD@domain.travel");
		$this->assertTrue(ValidatorMock::validateEmail("uncommonTLD@domain.mobi"), "::email uncommonTLD@domain.mobi");
		$this->assertTrue(ValidatorMock::validateEmail("countryCodeTLD@domain.uk"), "::email countryCodeTLD@domain.uk");
		$this->assertTrue(ValidatorMock::validateEmail("countryCodeTLD@domain.rw"), "::email countryCodeTLD@domain.rw");
		$this->assertTrue(ValidatorMock::validateEmail("lettersInDomain@911.com"), "::email lettersInDomain@911.com");
		$this->assertTrue(ValidatorMock::validateEmail("underscore_inLocal@domain.net"), "::email underscore_inLocal@domain.net");
		$this->assertTrue(ValidatorMock::validateEmail("subdomain@sub.domain.com"), "::email subdomain@sub.domain.com");
		$this->assertTrue(ValidatorMock::validateEmail("local@dash-inDomain.com"), "::email local@dash-inDomain.com");
		$this->assertTrue(ValidatorMock::validateEmail("dot.inLocal@foo.com"), "::email dot.inLocal@foo.com");
		$this->assertTrue(ValidatorMock::validateEmail("a@singleLetterLocal.org"), "::email a@singleLetterLocal.org");
		$this->assertTrue(ValidatorMock::validateEmail("singleLetterDomain@x.org"), "::email singleLetterDomain@x.org");
		$this->assertTrue(ValidatorMock::validateEmail("&*=?^+{}'~@validCharsInLocal.net"), "::email &*=?^+{}'~@validCharsInLocal.net");
		$this->assertTrue(ValidatorMock::validateEmail("foor@bar.newTLD"), "::email foor@bar.newTLD");

		$this->assertFalse(ValidatorMock::validateEmail("missingDomain@.com"), "::email missingDomain@.com");
		$this->assertFalse(ValidatorMock::validateEmail("@missingLocal.org"), "::email @missingLocal.org");
		$this->assertFalse(ValidatorMock::validateEmail("missingatSign.net"), "::email missingatSign.net");
		$this->assertFalse(ValidatorMock::validateEmail("missingDot@com"), "::email missingDot@com");
		$this->assertFalse(ValidatorMock::validateEmail("two@@signs.com"), "::email two@@signs.com");
		$this->assertFalse(ValidatorMock::validateEmail("colonButNoPort@127.0.0.1:"), "::email colonButNoPort@127.0.0.1:");
		$this->assertFalse(ValidatorMock::validateEmail(""), "::email ''");
		$this->assertFalse(ValidatorMock::validateEmail(".localStartsWithDot@domain.com"), "::email .localStartsWithDot@domain.com");
		$this->assertFalse(ValidatorMock::validateEmail("localEndsWithDot.@domain.com"), "::email localEndsWithDot.@domain.com");
		$this->assertFalse(ValidatorMock::validateEmail("two..consecutiveDots@domain.com"), "::email two..consecutiveDots@domain.com");
		$this->assertFalse(ValidatorMock::validateEmail("domainStartsWithDash@-domain.com"), "::email domainStartsWithDash@-domain.com");
		$this->assertFalse(ValidatorMock::validateEmail("domainEndsWithDash@domain-.com"), "::email domainEndsWithDash@domain-.com");
		$this->assertFalse(ValidatorMock::validateEmail("missingTLD@domain."), "::email missingTLD@domain.");
		$this->assertFalse(ValidatorMock::validateEmail("! \"#\$%(),/;<>[]`|@invalidCharsInLocal.org"), "::email ! \"#\$%(),/;<>[]`|@invalidCharsInLocal.org");
		$this->assertFalse(ValidatorMock::validateEmail("invalidCharsInDomain@! \"#\$%(),/;<>_[]`|.org"), "::email invalidCharsInDomain@! \"#\$%(),/;<>_[]`|.org");
		$this->assertFalse(ValidatorMock::validateEmail("local@SecondLevelDomainNamesAreInvalidIfTheyAreLongerThan64Charactersss.org"), "::email local@SecondLevelDomainNamesAreInvalidIfTheyAreLongerThan64Charactersss.org");
	}
	
	public function testValidateType()
	{
		$this->assertTrue(ValidatorMock::validateType("test", 'string'), "validate valid string");
		$this->assertTrue(ValidatorMock::validateType(1, 'int'), "validate valid int");
		$this->assertTrue(ValidatorMock::validateType(1.1, 'float'), "validate valid float");
		$this->assertTrue(ValidatorMock::validateType(1.1, 'double'), "validate valid double");
		$this->assertTrue(ValidatorMock::validateType(FALSE, 'bool'), "validate valid bool");
		$this->assertTrue(ValidatorMock::validateType(NULL, 'null'), "validate valid null");
		$this->assertTrue(ValidatorMock::validateType("test", 'scalar'), "validate valid scalar");
		$this->assertTrue(ValidatorMock::validateType(new Foo, 'object'), "validate valid object");
		
		$this->assertFalse(ValidatorMock::validateType(NULL, 'string'), "validate invalid string");
		$this->assertFalse(ValidatorMock::validateType(NULL, 'int'), "validate invalid int");
		$this->assertFalse(ValidatorMock::validateType(NULL, 'float'), "validate invalid float");
		$this->assertFalse(ValidatorMock::validateType(NULL, 'double'), "validate invalid double");
		$this->assertFalse(ValidatorMock::validateType(NULL, 'bool'), "validate invalid bool");
		$this->assertFalse(ValidatorMock::validateType("test", 'null'), "validate invalid null");
		$this->assertFalse(ValidatorMock::validateType(NULL, 'scalar'), "validate invalid scalar");
		$this->assertFalse(ValidatorMock::validateType(NULL, 'object'), "validate invalid object");
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
		$this->assertTrue(ValidatorMock::validateInstance(new Foo, 'NellaTests\Validator\Foo'), "validate valid Foo instance");
		$this->assertFalse(ValidatorMock::validateInstance(NULL, 'NellaTests\Validator\Foo'), "validate invalid Foo instance");
	}
	
	public function testValidateNotNull()
	{
		$this->assertTrue(ValidatorMock::validateNotNull(new Foo), "validate valid not null");
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

class Baz
{
	/**
	 * @validate(nullable,url)
	 * @var string
	 */
	private $foo;
}
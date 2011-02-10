<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Tools;

require_once __DIR__ . "/../bootstrap.php";

use Nella\Tools\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
	public function testUrl()
	{
		$this->assertTrue(Validator::url("http://domain.com"), "::url http://domain.com");
		$this->assertTrue(Validator::url("http://domain.com/"), "::url http://domain.com/");
		$this->assertTrue(Validator::url("http://domain.com/l3tt3rsAndNumb3rs"), "::url http://domain.com/l3tt3rsAndNumb3rs");
		$this->assertTrue(Validator::url("http://domain.com/has-dash/"), "::url http://domain.com/has-dash/");
		$this->assertTrue(Validator::url("http://has-dash.a.domain.com"), "::url http://has-dash.a.domain.com");
		$this->assertTrue(Validator::url("https://domain.com"), "::url https://domain.com");
		$this->assertTrue(Validator::url("https://domain.com/"), "::url https://domain.com/");
		$this->assertTrue(Validator::url("https://domain.com/l3tt3rsAndNumb3rs"), "::url https://domain.com/l3tt3rsAndNumb3rs");
		$this->assertTrue(Validator::url("https://domain.com/has-dash/"), "::url https://domain.com/has-dash/");
		$this->assertTrue(Validator::url("https://has-dash.a.domain.com"), "::url https://has-dash.a.domain.com");
		$this->assertTrue(Validator::url("http://127.0.0.1/"), "::url http://127.0.0.1/");
		/*$this->assertTrue(Validator::url("ftp://domain.com"), "::url ftp://domain.com");
		$this->assertTrue(Validator::url("ftp://domain.com/"), "::url ftp://domain.com/");
		$this->assertTrue(Validator::url("ftp://domain.com/l3tt3rsAndNumb3rs"), "::url ftp://domain.com/l3tt3rsAndNumb3rs");
		$this->assertTrue(Validator::url("ftp://domain.com/has-dash/"), "::url ftp://domain.com/has-dash/");
		$this->assertTrue(Validator::url("ftp://has-dash.a.domain.com"), "::url ftp://has-dash.a.domain.com");*/
		$this->assertTrue(Validator::url("/l3tt3rsAndNumb3rs"), "::url /l3tt3rsAndNumb3rs");
		$this->assertTrue(Validator::url("/has-dash/"), "::url /has-dash/");
		$this->assertTrue(Validator::url("/l3tt3rsAndNumb3rs/has-dash.pdf"), "::url /l3tt3rsAndNumb3rs/has-dash.pdf");

		$this->assertFalse(Validator::url("/"), "::url /");
		$this->assertFalse(Validator::url(""), "::url ''");
		/*$this->assertFalse(Validator::url("l3tt3rsAndNumb3rs"), "::url l3tt3rsAndNumb3rs");
		$this->assertFalse(Validator::url("l3tt3rsAndNumb3rs/has-dash.pdf"), "::url l3tt3rsAndNumb3rs/has-dash.pdf");*/
	}

	public function testEmail()
	{
		$this->assertTrue(Validator::email("l3tt3rsAndNumb3rs@domain.com"), "::email l3tt3rsAndNumb3rs@domain.com");
		$this->assertTrue(Validator::email("has-dash@domain.com"), "::email has-dash@domain.com");
		$this->assertTrue(Validator::email("hasApostrophe.o'leary@domain.org"), "::email hasApostrophe.o'leary@domain.org");
		$this->assertTrue(Validator::email("uncommonTLD@domain.museum"), "::email uncommonTLD@domain.museum");
		$this->assertTrue(Validator::email("uncommonTLD@domain.travel"), "::email uncommonTLD@domain.travel");
		$this->assertTrue(Validator::email("uncommonTLD@domain.mobi"), "::email uncommonTLD@domain.mobi");
		$this->assertTrue(Validator::email("countryCodeTLD@domain.uk"), "::email countryCodeTLD@domain.uk");
		$this->assertTrue(Validator::email("countryCodeTLD@domain.rw"), "::email countryCodeTLD@domain.rw");
		$this->assertTrue(Validator::email("lettersInDomain@911.com"), "::email lettersInDomain@911.com");
		$this->assertTrue(Validator::email("underscore_inLocal@domain.net"), "::email underscore_inLocal@domain.net");
		$this->assertTrue(Validator::email("subdomain@sub.domain.com"), "::email subdomain@sub.domain.com");
		$this->assertTrue(Validator::email("local@dash-inDomain.com"), "::email local@dash-inDomain.com");
		$this->assertTrue(Validator::email("dot.inLocal@foo.com"), "::email dot.inLocal@foo.com");
		$this->assertTrue(Validator::email("a@singleLetterLocal.org"), "::email a@singleLetterLocal.org");
		$this->assertTrue(Validator::email("singleLetterDomain@x.org"), "::email singleLetterDomain@x.org");
		$this->assertTrue(Validator::email("&*=?^+{}'~@validCharsInLocal.net"), "::email &*=?^+{}'~@validCharsInLocal.net");
		$this->assertTrue(Validator::email("foor@bar.newTLD"), "::email foor@bar.newTLD");

		$this->assertFalse(Validator::email("missingDomain@.com"), "::email missingDomain@.com");
		$this->assertFalse(Validator::email("@missingLocal.org"), "::email @missingLocal.org");
		$this->assertFalse(Validator::email("missingatSign.net"), "::email missingatSign.net");
		$this->assertFalse(Validator::email("missingDot@com"), "::email missingDot@com");
		$this->assertFalse(Validator::email("two@@signs.com"), "::email two@@signs.com");
		$this->assertFalse(Validator::email("colonButNoPort@127.0.0.1:"), "::email colonButNoPort@127.0.0.1:");
		$this->assertFalse(Validator::email(""), "::email ''");
		$this->assertFalse(Validator::email(".localStartsWithDot@domain.com"), "::email .localStartsWithDot@domain.com");
		$this->assertFalse(Validator::email("localEndsWithDot.@domain.com"), "::email localEndsWithDot.@domain.com");
		$this->assertFalse(Validator::email("two..consecutiveDots@domain.com"), "::email two..consecutiveDots@domain.com");
		$this->assertFalse(Validator::email("domainStartsWithDash@-domain.com"), "::email domainStartsWithDash@-domain.com");
		$this->assertFalse(Validator::email("domainEndsWithDash@domain-.com"), "::email domainEndsWithDash@domain-.com");
		$this->assertFalse(Validator::email("missingTLD@domain."), "::email missingTLD@domain.");
		$this->assertFalse(Validator::email("! \"#\$%(),/;<>[]`|@invalidCharsInLocal.org"), "::email ! \"#\$%(),/;<>[]`|@invalidCharsInLocal.org");
		$this->assertFalse(Validator::email("invalidCharsInDomain@! \"#\$%(),/;<>_[]`|.org"), "::email invalidCharsInDomain@! \"#\$%(),/;<>_[]`|.org");
		$this->assertFalse(Validator::email("local@SecondLevelDomainNamesAreInvalidIfTheyAreLongerThan64Charactersss.org"), "::email local@SecondLevelDomainNamesAreInvalidIfTheyAreLongerThan64Charactersss.org");
	}
}
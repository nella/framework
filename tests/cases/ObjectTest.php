<?php
/**
 * This file is part of the Nella Framework (http://nellafw.org).
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * For the full copyright and license information, please view the file LICENSE.txt that was distributed with this source code.
 */

namespace NellaTests;

class ObjectTest extends \Nella\Testing\TestCase
{
	/** @var Duplicate */
	private $duplicate;
	/** @var Cls1 */
	private $obj1;
	/** @var Cls2 */
	private $obj2;
	/** @var Cls3 */
	private $obj3;
	/** @var Cls4 */
	private $obj4;
	
	public function setup()
	{
		$this->duplicate = new Duplicate;
		$this->obj1 = new Cls1;
		$this->obj2 = new Cls2;
		$this->obj3 = new Cls3;
		$this->obj4 = new Cls4;
	}
	
	/**
	 * @expectedException Nette\InvalidStateException
	 */
	public function testDuplicate()
	{
		$this->duplicate->prop;
	}
	
	public function testCls1GetterAndSetter()
	{
		$this->assertTrue(isset($this->obj1->var1), 'Cls1::$var1 - isset');
		
		$this->assertEquals('val1', $this->obj1->var1, 'Cls1::$var1 - get');
		$this->assertEquals('val1', $this->obj1->getVar1(), 'Cls1::$var1 - getter');

		$this->obj1->var1 = 'test';
		$this->assertEquals('test', $this->obj1->getterVar1(), 'Cls1::$var1 - set');
		
		$this->obj1->setVar1('test2');
		$this->assertEquals('test2', $this->obj1->getterVar1(), 'Cls1::$var1 - setter');
	}
	
	public function testCls1EmptyString1()
	{
		$this->obj1->var1 = '';
		$this->assertNull($this->obj1->getterVar1(), 'Cls1::$var1 - set');
	}
	
	public function testCls1EmptyString2()
	{	
		$this->obj1->setVar1('');
		$this->assertNull($this->obj1->getterVar1(), 'Cls1::$var1 - setter');
	}
	
	public function testCls1Getter()
	{
		$this->assertTrue(isset($this->obj1->var2), 'Cls1::$var2 - isset');
		
		$this->assertEquals('val2', $this->obj1->var2, 'Cls1::$var2 - get');
		$this->assertEquals('val2', $this->obj1->getVar2(), 'Cls1::$var2 - getter');
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls1GetterFailSet()
	{
		$this->obj1->var2 = 'test';
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls1GetterFailSetter()
	{
		$this->obj1->setVar2('test');
	}
	
	public function testCls1Setter()
	{
		$this->assertFalse(isset($this->obj1->var3), 'Cls1::$var3 - isset');
		
		$this->obj1->var3 = 'test';
		$this->assertEquals('test', $this->obj1->getterVar3(), 'Cls1::$var3 - set');
		
		$this->obj1->setVar3('test2');
		$this->assertEquals('test2', $this->obj1->getterVar3(), 'Cls1::$var3 - setter');
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls1SetterFailGet()
	{
		$test = $this->obj1->var3;
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls1SetterFailGetter()
	{
		$test = $this->obj1->getVar3();
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls1FailGet()
	{
		$test = $this->obj1->var4;
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls1FailGetter()
	{
		$test = $this->obj1->getVar4();
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls1FailSet()
	{
		$this->obj1->var4 = 'test';
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls1FailSetter()
	{
		$this->obj1->setVar4('test');
	}
	
	public function testCls2GetterAndSetter()
	{
		$this->assertTrue(isset($this->obj2->var1), 'Cls2::$var1 - isset');
		
		$this->assertEquals('val1', $this->obj2->var1, 'Cls2::$var1 - get');
		$this->assertEquals('val1', $this->obj2->getVar1(), 'Cls2::$var1 - getter');

		$this->obj2->var1 = 'test';
		$this->assertEquals('test', $this->obj2->getterVar1(), 'Cls2::$var1 - set');
		
		$this->obj2->setVar1('test2');
		$this->assertEquals('test2', $this->obj2->getterVar1(), 'Cls2::$var1 - setter');
	}
	
	public function testCls2Getter()
	{
		$this->assertTrue(isset($this->obj2->var2), 'Cls2::$var2 - isset');
		
		$this->assertEquals('val2', $this->obj2->var2, 'Cls2::$var2 - get');
		$this->assertEquals('val2', $this->obj2->getVar2(), 'Cls2::$var2 - getter');
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls2GetterFailSet()
	{
		$this->obj2->var2 = 'test';
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls2GetterFailSetter()
	{
		$this->obj2->setVar2('test');
	}
	
	public function testCls2Setter()
	{
		$this->assertFalse(isset($this->obj2->var3), 'Cls2::$var3 - isset');
		
		$this->obj2->var3 = 'test';
		$this->assertEquals('test', $this->obj2->getterVar3(), 'Cls2::$var3 - set');
		
		$this->obj2->setVar3('test2');
		$this->assertEquals('test2', $this->obj2->getterVar3(), 'Cls2::$var3 - setter');
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls2SetterFailGet()
	{
		$test = $this->obj2->var3;
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls2SetterFailGetter()
	{
		$test = $this->obj2->getVar3();
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls2FailGet()
	{
		$test = $this->obj2->var4;
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls2FailGetter()
	{
		$test = $this->obj2->getVar4();
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls2FailSet()
	{
		$this->obj2->var4 = 'test';
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls2FailSetter()
	{
		$this->obj2->setVar4('test');
	}
	
	public function testCls3GetterAndSetter()
	{
		$this->assertTrue(isset($this->obj3->var1), 'Cls3::$var1 - isset');
		
		$this->assertEquals('test', $this->obj3->var1, 'Cls3::$var1 - get');
		$this->assertEquals('test', $this->obj3->getVar1(), 'Cls3::$var1 - getter');

		$this->obj3->var1 = 'x';
		$this->assertEquals('xtest', $this->obj3->getterVar1(), 'Cls3::$var1 - set');
		
		$this->obj3->setVar1('x2');
		$this->assertEquals('x2test', $this->obj3->getterVar1(), 'Cls3::$var1 - setter');
	}
	
	public function testCls3Getter()
	{
		$this->assertTrue(isset($this->obj3->var2), 'Cls3::$var2 - isset');
		
		$this->assertEquals('test', $this->obj3->var2, 'Cls3::$var2 - get');
		$this->assertEquals('test', $this->obj3->getVar2(), 'Cls3::$var2 - getter');
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls3GetterFailSet()
	{
		$this->obj3->var2 = 'test';
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls3GetterFailSetter()
	{
		$this->obj3->setVar2('test');
	}
	
	public function testCls3Setter()
	{
		$this->assertFalse(isset($this->obj3->var3), 'Cls3::$var3 - isset');
		
		$this->obj3->var3 = 'x';
		$this->assertEquals('xtest', $this->obj3->getterVar3(), 'Cls3::$var3 - set');
		
		$this->obj3->setVar3('x2');
		$this->assertEquals('x2test', $this->obj3->getterVar3(), 'Cls3::$var3 - setter');
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls3SetterFailGet()
	{
		$test = $this->obj3->var3;
	}
	
	/**
	 * @expectedException Nette\MemberAccessException
	 */
	public function testCls3SetterFailGetter()
	{
		$test = $this->obj3->getVar3();
	}
	
	public function testCls3FailGet()
	{
		$this->assertEquals('test', $this->obj3->var4, 'Cls3::$var4 - get');
	}
	
	public function testCls3FailGetter()
	{
		$this->assertEquals('test', $this->obj3->getVar4(), 'Cls3::$var4 - getter');
	}
	
	public function testCls3FailSet()
	{
		$this->obj3->var5 = 'x';
		$this->assertEquals('xtest', $this->obj3->getterVar5());
	}
	
	public function testCls3FailSetter()
	{
		$this->obj3->setVar5('x');
		$this->assertEquals('xtest', $this->obj3->getterVar5());
	}
	
	public function testCls4ClassGetter()
	{
		$this->assertTrue(isset($this->obj4->var1), 'Cls4::$var1 - isset');
		
		$this->assertInstanceOf('stdClass', $this->obj4->var1, 'Cls4::$var1 - get');
		$this->assertInstanceOf('stdClass', $this->obj4->getVar1(), 'Cls4::$var1 - getter');
	}
	
	public function testCls4ClassSetter()
	{
		$obj = new \stdClass;
		$this->obj4->var1 = $obj;
		$this->assertSame($obj, $this->obj4->getterVar1(), 'Cls4::$var1 - set');
		
		$obj = new \stdClass;
		$this->obj4->setVar1($obj);
		$this->assertSame($obj, $this->obj4->getterVar1(), 'Cls4::$var1 - setter');
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ClassSetterFailed1()
	{
		$this->obj4->var1 = new Cls1;
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ClassSetterFailed2()
	{	
		$this->obj4->setVar1(new Cls1);
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ClassSetterNull1()
	{
		$this->obj4->var1 = NULL;
	}

	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ClassSetterNull2()
	{
		$this->obj4->setVar1(NULL);
	}
	
	public function testCls4ClassOrNullGetter()
	{
		$this->assertTrue(isset($this->obj4->var2), 'Cls4::$var2 - isset');
		
		$this->assertInstanceOf('stdClass', $this->obj4->var2, 'Cls4::$var2 - get');
		$this->assertInstanceOf('stdClass', $this->obj4->getVar2(), 'Cls4::$var2 - getter');
	}
	
	public function testCls4ClassOrNullSetter()
	{
		$obj = new \stdClass;
		$this->obj4->var2 = $obj;
		$this->assertSame($obj, $this->obj4->getterVar2(), 'Cls4::$var2 - set');
		
		$obj = new \stdClass;
		$this->obj4->setVar2($obj);
		$this->assertSame($obj, $this->obj4->getterVar2(), 'Cls4::$var2 - setter');
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ClassOrNullSetterFailed1()
	{
		$this->obj4->var2 = new Cls1;
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ClassOrNullSetterFailed2()
	{	
		$this->obj4->setVar2(new Cls1);
	}

	public function testCls4ClassOrNullSetterNull()
	{
		$this->obj4->var2 = NULL;
		$this->assertNull($this->obj4->getterVar2(), 'Cls4::$var2 - set');
		$this->obj4->__construct();
		
		$this->obj4->setVar2(NULL);
		$this->assertNull($this->obj4->getterVar2(), 'Cls4::$var2 - setter');
	}
	
	public function testCls4ArrayGetter()
	{
		$this->assertTrue(isset($this->obj4->var3), 'Cls4::$var3 - isset');
		
		$this->assertInternalType('array', $this->obj4->var3, 'Cls4::$var3 - get');
		$this->assertInternalType('array', $this->obj4->getVar3(), 'Cls4::$var3 - getter');
	}
	
	public function testCls4ArraySetter()
	{
		$this->obj4->var3 = array('foo');
		$this->assertEquals(array('foo'), $this->obj4->getterVar3(), 'Cls4::$var3 - set');
		
		$this->obj4->setVar3(array('bar'));
		$this->assertEquals(array('bar'), $this->obj4->getterVar3(), 'Cls4::$var3 - setter');
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ArraySetterFailed1()
	{
		$this->obj4->var3 = 'test';
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ArraySetterFailed2()
	{	
		$this->obj4->setVar3('test');
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ArraySetterNull1()
	{
		$this->obj4->var3 = NULL;
	}

	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ArraySetterNull2()
	{
		$this->obj4->setVar3(NULL);
	}
	
	public function testCls4ArrayOrNullGetter()
	{
		$this->assertTrue(isset($this->obj4->var4), 'Cls4::$var4 - isset');
		
		$this->assertInternalType('array', $this->obj4->var4, 'Cls4::$var4 - get');
		$this->assertInternalType('array', $this->obj4->getVar4(), 'Cls4::$var4 - getter');
	}
	
	public function testCls4ArrayOrNullSetter()
	{
		$this->obj4->var4 = array('foo');
		$this->assertEquals(array('foo'), $this->obj4->getterVar4(), 'Cls4::$var4 - set');
		
		$this->obj4->setVar4(array('bar'));
		$this->assertEquals(array('bar'), $this->obj4->getterVar4(), 'Cls4::$var4 - setter');
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ArrayOrNullSetterFailed1()
	{
		$this->obj4->var4 = 'test';
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ArrayOrNullSetterFailed2()
	{	
		$this->obj4->setVar4('test');
	}
	
	public function testCls4ArrayOrNullSetterNull()
	{
		$this->obj4->var4 = NULL;
		$this->assertNull($this->obj4->getterVar4(), 'Cls4::$var4 - set');
		$this->obj4->__construct();
		
		$this->obj4->setVar4(NULL);
		$this->assertNull($this->obj4->getterVar4(), 'Cls4::$var4 - setter');
	}
	
	
	
	
	
	public function testCls4ArrayOfObjectGetter()
	{
		$this->assertTrue(isset($this->obj4->var5), 'Cls4::$var5 - isset');
		
		$this->assertInternalType('array', $this->obj4->var5, 'Cls4::$var5 - get');
		$this->assertInternalType('array', $this->obj4->getVar5(), 'Cls4::$var5 - getter');
	}
	
	public function testCls4ArrayOfObjectSetter()
	{
		$data = array(new \stdClass);
		$this->obj4->var5 = $data;
		$this->assertSame($data, $this->obj4->getterVar5(), 'Cls4::$var5 - set');
		
		$data = array(new \stdClass);
		$this->obj4->setVar5($data);
		$this->assertSame($data, $this->obj4->getterVar5(), 'Cls4::$var5 - setter');
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ArrayOfObjectSetterFailed1()
	{
		$this->obj4->var5 = 'test';
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ArrayOfObjectSetterFailed2()
	{	
		$this->obj4->setVar5('test');
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ArrayOfObjectSetterNull1()
	{
		$this->obj4->var5 = NULL;
	}

	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ArrayOfObjectSetterNull2()
	{
		$this->obj4->setVar5(NULL);
	}
	
	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ArrayOfObjectSetterObj1()
	{
		$this->obj4->var5 = new \stdClass;
	}

	/**
	 * @expectedException Nette\FatalErrorException
	 */
	public function testCls4ArrayOfObjectSetterObj2()
	{
		$this->obj4->setVar5(new \stdClass);
	}
}

/**
 * @property-read string $prop
 * @property-write string $prop
 */
class Duplicate extends \Nella\Object { }

/**
 * @property string $var1
 * @property-read string $var2
 * @property-write string $var3
 */
class Cls1 extends \Nella\Object
{
	private $var1;
	private $var2;
	private $var3;
	
	public function __construct()
	{
		$this->var1 = 'val1';
		$this->var2 = 'val2';
		$this->var3 = 'val3';
	}
	
	public function getterVar1()
	{
		return $this->var1;
	}
	
	public function getterVar3()
	{
		return $this->var3;
	}
}

/**
 * @property bool|NULL $var1
 * @property-read int|NULL $var2
 * @property-write mixed $var3
 */
class Cls2 extends \Nella\Object
{
	protected $var1;
	protected $var2;
	protected $var3;
	
	public function __construct()
	{
		$this->var1 = 'val1';
		$this->var2 = 'val2';
		$this->var3 = 'val3';
	}
	
	public function getterVar1()
	{
		return $this->var1;
	}
	
	public function getterVar3()
	{
		return $this->var3;
	}
}

/**
 * @property string $var1
 * @property-read string $var2
 * @property-write string $var3
 */
class Cls3 extends \Nella\Object
{
	private $var1;
	private $var2;
	private $var3;
	private $var5;
	
	public function __construct()
	{
		$this->var1 = 'val1';
		$this->var2 = 'val2';
		$this->var3 = 'val3';
		$this->var5 = 'val5';
	}
	
	public function getVar1()
	{
		return 'test';
	}
	
	public function setVar1($value)
	{
		$this->var1 = $value.'test';
		return $this;
	}
	
	public function getterVar1()
	{
		return $this->var1;
	}
	
	public function getVar2()
	{
		return 'test';
	}
	
	public function setVar3($value)
	{
		$this->var3 = $value.'test';
		return $this;
	}
	
	public function getterVar3()
	{
		return $this->var3;
	}
	
	public function getVar4()
	{
		return 'test';
	}
	
	public function setVar5($value)
	{
		$this->var5 = $value.'test';
		return $this;
	}
	
	public function getterVar5()
	{
		return $this->var5;
	}
}

/**
 * @property \stdClass $var1
 * @property \stdClass|NULL $var2
 * @property array $var3
 * @property array|NULL $var4
 * @property \stdClass[]|array $var5
 */
class Cls4 extends \Nella\Object
{
	private $var1;
	private $var2;
	private $var3;
	private $var4;
	private $var5;
	
	public function __construct()
	{
		$this->var1 = new \stdClass;
		$this->var2 = new \stdClass;
		$this->var3 = array();
		$this->var4 = array();
		$this->var5 = array(new \stdClass, new \stdClass);
	}
	
	public function getterVar1()
	{
		return $this->var1;
	}
	
	public function getterVar2()
	{
		return $this->var2;
	}
	
	public function getterVar3()
	{
		return $this->var3;
	}
	
	public function getterVar4()
	{
		return $this->var4;
	}
	
	public function getterVar5()
	{
		return $this->var5;
	}
}
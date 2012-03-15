<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Templating;

class TemplateFilesFormatterTest extends \Nella\Testing\TestCase
{
	/** @var \Nella\Templating\TemplateFilesFormatter */
	private $formatter;

	public function setup()
	{
		parent::setup();
		$this->formatter = new \Nella\Templating\TemplateFilesFormatter;
		$this->formatter->useModuleSuffix = FALSE;
		$this->formatter->addDir(__DIR__)->addDir(__DIR__ . "/Nella");
	}

	public function testInstance()
	{
		$this->assertInstanceOf('Nella\Templating\ITemplateFilesFormatter', $this->formatter, 'is instance "Nella\Templating\ITemplateFilesFormatter"');
	}

	public function dataFormatLayoutFiles()
	{
		return array(
			array(
				"Foo", "bar",
				array(
					__DIR__ . "/templates/Foo/@bar.latte",
					__DIR__ . "/templates/Foo.@bar.latte",
					__DIR__ . "/templates/@bar.latte",
					__DIR__ . "/Nella/templates/Foo/@bar.latte",
					__DIR__ . "/Nella/templates/Foo.@bar.latte",
					__DIR__ . "/Nella/templates/@bar.latte",
				),
			),
			array(
				"Foo:Bar", "layout",
				array(
					__DIR__ . "/Foo/templates/Bar/@layout.latte",
					__DIR__ . "/Foo/templates/Bar.@layout.latte",
					__DIR__ . "/Foo/templates/@layout.latte",
					__DIR__ . "/templates/Foo/Bar/@layout.latte",
					__DIR__ . "/templates/Foo/Bar.@layout.latte",
					__DIR__ . "/templates/Foo/@layout.latte",
					__DIR__ . "/templates/@layout.latte",
					__DIR__ . "/Nella/Foo/templates/Bar/@layout.latte",
					__DIR__ . "/Nella/Foo/templates/Bar.@layout.latte",
					__DIR__ . "/Nella/Foo/templates/@layout.latte",
					__DIR__ . "/Nella/templates/Foo/Bar/@layout.latte",
					__DIR__ . "/Nella/templates/Foo/Bar.@layout.latte",
					__DIR__ . "/Nella/templates/Foo/@layout.latte",
					__DIR__ . "/Nella/templates/@layout.latte",
				),
			),
		);
	}

	/**
	 * @dataProvider dataFormatLayoutFiles
	 */
	public function testFormatLayoutTemplateFiles($name, $layout, $expected)
	{
		$this->assertEquals($expected, $this->formatter->formatLayoutTemplateFiles($name, $layout));
	}

	public function dataFormatFiles()
	{
		return array(
			array(
				"Foo", "bar",
				array(
					__DIR__ . "/templates/Foo/bar.latte",
					__DIR__ . "/templates/Foo.bar.latte",
					__DIR__ . "/templates/Foo/@global.latte",
					__DIR__ . "/templates/@global.latte",
					__DIR__ . "/Nella/templates/Foo/bar.latte",
					__DIR__ . "/Nella/templates/Foo.bar.latte",
					__DIR__ . "/Nella/templates/Foo/@global.latte",
					__DIR__ . "/Nella/templates/@global.latte",
				),
			),
			array(
				"Foo:Bar", "baz",
				array(
					__DIR__ . "/Foo/templates/Bar/baz.latte",
					__DIR__ . "/Foo/templates/Bar.baz.latte",
					__DIR__ . "/Foo/templates/Bar/@global.latte",
					__DIR__ . "/Foo/templates/@global.latte",
					__DIR__ . "/templates/Foo/Bar/baz.latte",
					__DIR__ . "/templates/Foo/Bar.baz.latte",
					__DIR__ . "/templates/Foo/Bar/@global.latte",
					__DIR__ . "/templates/Foo/@global.latte",
					__DIR__ . "/templates/@global.latte",
					__DIR__ . "/Nella/Foo/templates/Bar/baz.latte",
					__DIR__ . "/Nella/Foo/templates/Bar.baz.latte",
					__DIR__ . "/Nella/Foo/templates/Bar/@global.latte",
					__DIR__ . "/Nella/Foo/templates/@global.latte",
					__DIR__ . "/Nella/templates/Foo/Bar/baz.latte",
					__DIR__ . "/Nella/templates/Foo/Bar.baz.latte",
					__DIR__ . "/Nella/templates/Foo/Bar/@global.latte",
					__DIR__ . "/Nella/templates/Foo/@global.latte",
					__DIR__ . "/Nella/templates/@global.latte",
				),
			),
		);
	}

	/**
	 * @dataProvider dataFormatFiles
	 */
	public function testFormatTemplateFiles($name, $layout, $expected)
	{
		//$this->markTestSkipped();
		$this->assertEquals($expected, $this->formatter->formatTemplateFiles($name, $layout));
	}
}
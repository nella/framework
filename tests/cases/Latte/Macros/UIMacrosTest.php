<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2012 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.com
 */

namespace NellaTests\Latte\Macros;

use Nette\Latte\Compiler,
	Nette\Latte\Parser,
	Nette\Latte\MacroNode;

class UIMacrosTest extends \Nella\Testing\TestCase
{
	/** @var \Nette\Latte\Compiler */
	private $compiler;
	/** @var \Nette\Latte\Parser */
	private $parser;

	protected function setup()
	{
		$this->compiler = new Compiler;
		$this->compiler->setContext(Compiler::CONTENT_HTML);
		$this->parser = new Parser;
		$this->parser->setContext(Parser::CONTEXT_TEXT);
		\Nella\Latte\Macros\UIMacros::install($this->compiler);
		\Nette\Diagnostics\Debugger::$maxLen = 4096;
	}

	public function dataPhref()
	{
		return array(
			array(':Homepage:default', '":Homepage:default"'),
			array(':Foo: show, 13', "\":Foo:\", array('show', 13)"),
			array('Foo:Bar:baz show => detail, id => 13', "\"Foo:Bar:baz\", array('show' => 'detail', 'id' => 13)"),
		);
	}

	/**
	 * @dataProvider dataPhref
	 */
	public function testPhref($input, $output)
	{
		$data = '<a n:phref="'.$input.'">Link</a>';
		$expected = '<a<?php  ?> href="<?php echo htmlSpecialChars($_presenter->link('.$output.')) ?>"<?php  ?>>Link</a>';

		$tokens = $this->parser->parse($data);
		$actual = $this->compiler->compile($tokens);
		$this->assertEquals($expected, $actual, $input);
	}
}
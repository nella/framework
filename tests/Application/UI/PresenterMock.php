<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nella-project.org
 */

namespace NellaTests\Application\UI;

class PresenterMock extends \Nella\Application\UI\Presenter
{
	public function createComponentMock($name)
	{
		return $this->createComponent($name);
	}
}

namespace Nella;

class MyPresenter extends Application\UI\Presenter { }

namespace Nella\Foo;

class MyPresenter extends \Nella\Application\UI\Presenter { }

namespace App;

class FooPresenter extends \Nella\Application\UI\Presenter { }

abstract class BarPresenter extends \Nella\Application\UI\Presenter { }

class BazPresenter { }

namespace App\Bar;

class FooPresenter extends \Nella\Application\UI\Presenter { }
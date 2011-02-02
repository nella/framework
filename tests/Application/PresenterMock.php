<?php
/**
 * This file is part of the Nella Framework.
 *
 * Copyright (c) 2006, 2011 Patrik Votoček (http://patrik.votocek.cz)
 *
 * This source file is subject to the GNU Lesser General Public License. For more information please see http://nellacms.com
 */

namespace NellaTests\Application;

class PresenterMock extends \Nella\Application\Presenter { }

namespace Nella;

class MyPresenter extends \Nella\Application\Presenter { }

namespace Nella\Foo;

class MyPresenter extends \Nella\Application\Presenter { }

namespace App;

class FooPresenter extends \Nella\Application\Presenter { }

abstract class BarPresenter extends \Nella\Application\Presenter { }

class BazPresenter { }

namespace App\Bar;

class FooPresenter extends \Nella\Application\Presenter { }
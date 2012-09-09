[Nette Framework](http://nette.org) - Console Extension
=======================================================

Library for easy integration [Symfony 2 Console](http://symfony.com/doc/2.0/components/console.html)
to _Nette Framework_.

Requirements
------------

- PHP 5.3.2 or later
- Nette Framework 2.0.0 or later
- Symfony Console 2.0.0 or later


Installation
------------

Add `"nella/console": "*"` to *composer.json and run `composer update`.
Edit your *bootstrap.php* and add `Nella\Console\Config\Extension::register($configurator);`
before `$configurator->createContainer()`.


-----

For more info please follow [documentaion](http://doc.nellafw.org/en/console).

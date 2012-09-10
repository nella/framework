[Nette Framework](http://nette.org) - Localization Extension
============================================================

Library for easy application localization to _Nette Framework_.

Requirements
------------

- PHP 5.3.2 or later
- Nette Framework 2.0.0 or later


Installation
------------

Add `"nella/localization": "*"` to *composer.json and run `composer update`.
Edit your *bootstrap.php* and add `Nella\Localization\Config\Extension::register($configurator);`
before `$configurator->createContainer()`.


-----

For more info please follow [documentaion](http://doc.nellafw.org/en/localization).

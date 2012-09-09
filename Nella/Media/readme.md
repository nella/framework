[Nette Framework](http://nette.org) - Media Extension
=====================================================

Library for easy integration media (images, files) to _Nette Framework_.

Requirements
------------

- PHP 5.3.2 or later
- Nette Framework 2.0.0 or later

Suggest
-------
- Doctrine ORM 2.3.0rc or later


Installation
------------

Add `"nella/media": "*"` to *composer.json and run `composer update`.
Edit your *bootstrap.php* and add `Nella\Media\Config\Extension::register($configurator);`
before `$configurator->createContainer()`.


-----

For more info please follow [documentaion](http://doc.nellafw.org/en/media).

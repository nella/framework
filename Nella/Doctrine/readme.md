[Nette Framework](http://nette.org) - Doctrine 2 Extension
==========================================================

Library for easy integration [Doctrine 2 ORM](http://www.doctrine-project.org/projects/orm.html)
to _Nette Framework_.

Requirements
------------

- PHP 5.3.2 or later
- Nette Framework 2.0.0 or later
- Doctrine ORM 2.3.0rc or later

Suggest
-------
- Nella Console Extension 2.0.0beta or later
- Doctrine Migrations master
- Nella Gedmo Extension 2.0.0beta or later


Installation
------------

Add `"nella/doctrine": "*"` to *composer.json and run `composer update`.
Edit your *bootstrap.php* and add `Nella\Doctrine\Config\Extension::register($configurator);`
before `$configurator->createContainer()`.

If you want to use Doctrine Migrations add
`Nella\Doctrine\Config\MigrationsExtension::register($configurator);` before
`$configurator->createContainer()`.


-----

For more info please follow [documentaion](http://doc.nellafw.org/en/doctrine).

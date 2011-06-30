#!/bin/bash

##########
# Config #
##########

VENDOR_VERSION_NETTE="2.0dev"
VENDOR_VERSION_DOCTRINE="2.0.6"
VENDOR_VERSION_DOCTRINE_MIGRATIONS="master"
VENDOR_VERSION_SYMFONY_CONSOLE="2.0.0RC1"

VENDOR_URL_NETTE="http://files.nette.org/NetteFramework-$VENDOR_VERSION_NETTE-PHP5.3.zip"
VENDOR_URL_DOCTRINE="http://www.doctrine-project.org/downloads/DoctrineORM-$VENDOR_VERSION_DOCTRINE-full.tar.gz"
VENDOR_URL_DOCTRINE_MIGRATIONS="https://github.com/Vrtak-CZ/migrations/tarball/$VENDOR_VERSION_DOCTRINE_MIGRATIONS"
VENDOR_URL_SYMFONY_CONSOLE="http://pear.symfony.com/get/Console-$VENDOR_VERSION_SYMFONY_CONSOLE.tgz"

##################
# Clean old data #
##################

rm -rf "vendors"
mkdir "vendors"

############
# Download #
############

wget --no-check-certificate $VENDOR_URL_NETTE -O "vendors/_Nette.zip"
wget --no-check-certificate $VENDOR_URL_DOCTRINE -O "vendors/_Doctrine.tar.gz"
wget --no-check-certificate $VENDOR_URL_DOCTRINE_MIGRATIONS -O "vendors/_DoctrineMigrations.tar.gz"
wget --no-check-certificate $VENDOR_URL_SYMFONY_CONSOLE -O "vendors/_SymfonyConsole.tar.gz"

###########
# Extract #
###########

# Nette
7z x "vendors/_Nette.zip" -o"vendors/_Nette";
rm "vendors/_Nette.zip"
mv "vendors/_Nette/NetteFramework-$VENDOR_VERSION_NETTE-PHP5.3/Nette" "vendors/Nette"
mv "vendors/_Nette/NetteFramework-$VENDOR_VERSION_NETTE-PHP5.3/license.txt" "vendors/Nette/license.txt"
rm -rf "vendors/_Nette"

# Doctrine
mkdir "vendors/_Doctrine"
tar xzvf "vendors/_Doctrine.tar.gz" -C "vendors/_Doctrine"
rm "vendors/_Doctrine.tar.gz"
mkdir "vendors/Doctrine"
mv "vendors/_Doctrine/doctrine-orm/Doctrine/Common" "vendors/Doctrine/Common"
mv "vendors/_Doctrine/doctrine-orm/Doctrine/DBAL" "vendors/Doctrine/DBAL"
mv "vendors/_Doctrine/doctrine-orm/Doctrine/ORM" "vendors/Doctrine/ORM"
mv "vendors/_Doctrine/doctrine-orm/LICENSE" "vendors/Doctrine/license.txt"
rm -rf "vendors/_Doctrine"

# Doctrine Migrations
mkdir "vendors/_DoctrineMigrations"
tar xzvf "vendors/_DoctrineMigrations.tar.gz" -C "vendors/_DoctrineMigrations"
rm "vendors/_DoctrineMigrations.tar.gz"
TMP_MIGRATIONS=`ls "vendors/_DoctrineMigrations" | grep migrations`
mv "vendors/_DoctrineMigrations/$TMP_MIGRATIONS/lib/Doctrine/DBAL/Migrations" "vendors/Doctrine/DBAL/Migrations"
mv "vendors/_DoctrineMigrations/$TMP_MIGRATIONS/LICENSE" "vendors/Doctrine/license-migrations.txt"
rm -rf "vendors/_DoctrineMigrations"

# Symfony Console
mkdir "vendors/_SymfonyConsole"
tar xzvf "vendors/_SymfonyConsole.tar.gz" -C "vendors/_SymfonyConsole"
rm "vendors/_SymfonyConsole.tar.gz"
TMP_CONSOLE=`ls "vendors/_SymfonyConsole" | grep Console`
mv "vendors/_SymfonyConsole/$TMP_CONSOLE/Symfony" "vendors/Symfony"
rm -rf "vendors/_SymfonyConsole"

#!/bin/sh

##########
# Config #
##########

BUILD_TOOLS_REPO="git://github.com/nella/framework-build-tools.git"

########################
# Download build tools #
########################

if [ -d "build" ]
then
	rm -rf build
fi
git clone $BUILD_TOOLS_REPO build
cd build && git submodule update --init && cd ..

####################
# Move build files #
####################

mv build/build.xml ./
mv build/phpunit.xml ./
mv build/build.sh ./

################
# Init vendors #
################

if [ -f "composer.lock" ]
then
	rm composer.lock
fi
if [ -f "composer.phar" ]
then
	rm composer.phar
fi
if [ -d "vendor" ]
then
	rm -rf vendor
fi
curl -s http://getcomposer.org/installer | php
php composer.phar install --dev


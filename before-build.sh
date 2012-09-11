#!/bin/sh

##########
# Config #
##########

BUILD_TOOLS_REPO="git://github.com/nella/framework-build-tools.git"
BUILD_TOOLS_DIR="build-tools"

########################
# Download build tools #
########################

if [ -d "$BUILD_TOOLS_DIR" ]
then
	rm -rf $BUILD_TOOLS_DIR
fi

git clone $BUILD_TOOLS_REPO $BUILD_TOOLS_DIR

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
php composer.phar install


#!/bin/sh

##########
# Config #
##########

BUILD_TOOLS_REPO="git://github.com/nella/framework-build-tools.git"

########################
# Download build tools #
########################

git clone $BUILD_TOOLS_REPO build

####################
# Move build files #
####################

mv build/build.xml ./
mv build/phpunit.xml ./
mv build/build.sh ./

################
# Init vendors #
################

sh vendors.sh


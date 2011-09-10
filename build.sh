#!/bin/bash

##########
# Config #
##########

PACKAGE_VERSION_NAME="2.0dev"

################
# Init vendors #
################

sh vendors.sh

###################################################################################
# GIT commit build (hash, date) - history.txt / version.txt / Nella/Framework.php #
###################################################################################

WCREV=`git log -n 1 --pretty="%h"`
WCDATE=`git log -n 1 --pretty="%cd" --date=short`

echo `git log -n 500 --pretty="%cd (%h): %s" --date-order --date=short > HISTORY.txt`
echo `git log -n 1 --pretty="Nella Framework 2.0-dev (revision %h released on %cd)" --date=short > VERSION.txt`

sed -i "s/\$WCREV\$ /$WCREV /g" Nella/Framework.php
sed -i "s/\$WCDATE\$'/$WCDATE'/g" Nella/Framework.php

#################
# Build sandbox #
#################

git clone git://github.com/nella/sandbox.git sandbox
cp -r vendors/* sandbox/libs/
cp -r Nella/* sandbox/libs/Nella
echo "Disallow: /" > sandbox/libs/Nette/netterobots.txt
echo "Disallow: /" > sandbox/libs/Nella/netterobots.txt
echo "Disallow: /" > sandbox/libs/Doctrine/netterobots.txt

##########################################
# GIT remove .gitignore .gitmodules .git #
##########################################

find . -name ".git*" -print0 | xargs -0 rm -rf

##########
# Apigen #
##########

APIGEN_DIR="build-tools/Apigen"

git clone git://github.com/nella/build-tools.git build-tools
php "$APIGEN_DIR/apigen.php" -s "Nella" -s "vendors" -d "API-reference" --config "$APIGEN_DIR/config.neon"

#########
# Clean #
#########

rm -rf "vendors"
#rm -rf "build-tools"

############
# Packages #
############

# Prepare
PACKAGE_NAME="NellaFramework-$PACKAGE_VERSION_NAME"
mkdir "$PACKAGE_NAME"
mv "client-side" "$PACKAGE_NAME/"
mv "Nella" "$PACKAGE_NAME/"
mv "sandbox" "$PACKAGE_NAME/"
mv "LICENSE.txt" "$PACKAGE_NAME/"
mv "VERSION.txt" "$PACKAGE_NAME/"
mv "HISTORY.txt" "$PACKAGE_NAME/"
mv "README.txt" "$PACKAGE_NAME/"
mv "API-reference" "$PACKAGE_NAME/"
mv "tests" "$PACKAGE_NAME/"

# tar.gz
tar cvzf "$PACKAGE_NAME.tar.gz" "$PACKAGE_NAME"

# tar.bz2
tar cvjf "$PACKAGE_NAME.tar.bz2" "$PACKAGE_NAME"

# zip
7z a -mx9 "$PACKAGE_NAME.zip" "$PACKAGE_NAME"

# 7z
7z a -mx9 "$PACKAGE_NAME.7z" "$PACKAGE_NAME"
DATE_NOW=`date +%F`
cp "$PACKAGE_NAME.7z" "$PACKAGE_NAME-$DATE_NOW-$WCREV.7z"

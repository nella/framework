#!/bin/sh

# Path to this script's directory
dir=$(cd `dirname $0` && pwd)

# Path to test runner script
runnerScript="$dir/../vendor/nette/tester/Tester/coverage-report.php"
if [ ! -f "$runnerScript" ]; then
	echo "Nette Tester is missing. You can install it using Composer:" >&2
	echo "php composer.phar update --dev." >&2
	exit 2
fi

# Runs converter with script's arguments
php "$runnerScript" -c "coverage.dat" -s "$dir/../Nella" -t "Nella Framework" "$@"

<?php

$message = 'THE DEVELOPMENT OF NELLA FRAMEWORK HAS BEEN ABANDONED';

if (PHP_SAPI === 'cli') {
	$message .= PHP_EOL;
} else {
	$message .= PHP_EOL . '<br>' . PHP_EOL;
}

$message .= 'Please use stable version (v0.8.0)';

if (PHP_SAPI === 'cli') {
	$message .= PHP_EOL;
}

die($message);

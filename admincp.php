<?php


require 'assets/init.php';

if (IS_LOGGED == false || IsAdmin() == false) {
	header("Location: " . UrlLink(''));
    exit();
}

// autoload admin panel files
require 'admin-panel/autoload.php';
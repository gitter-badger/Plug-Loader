<?php
/**
 * Registers a new autoloader, this file is part of Plug-Autoloader.
 *
 * @see ./autoloader.php For a description of what this project is all about
 * and how it works.
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @version 0.0.1
 * @since 0.0.1 1st January, 2016
 * @copyright 2015 - 2016 Samuel Adeshina <samueladeshina73@gmail.com> <http://samshal.github.io>
 * @license MIT
 */
	//require __DIR__ . "/autoloader.php";
	require __DIR__ . "/autoloader_register.php";

	$configuration_file = $_SERVER["DOCUMENT_ROOT"]. DIRECTORY_SEPARATOR. "autoload.json";
	
	new Plug\Autoloader\AutoloaderRegister($configuration_file);
 ?>
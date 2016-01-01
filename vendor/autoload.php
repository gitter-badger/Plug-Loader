<?php
/**
 * Registers a new registrar for the autoloader, this file is part of Plug-Autoloader.
 *
 * @see ./autoloader.php For a description of what this project is all about
 * and how it works.
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @version 0.0.1
 * @since 0.0.1 1st January, 2016
 * @copyright 2015 - 2016 Samuel Adeshina <samueladeshina73@gmail.com> <http://samshal.github.io>
 * @license MIT
 */
	require __DIR__ . "/autoloader_register.php";

	/**
	 * We need to specify the location of the configuration file.
	 * It can be an xml or a json file and it can be located anywhere.
	 * It is advised to set this location to the document root, that is the root level folder
	 *
	 * The default is the root level folder, but... feel free to change it to any other location as
	 * you see fit.
	 */
	if (file_exists($_SERVER["DOCUMENT_ROOT"]. DIRECTORY_SEPARATOR. "autoload.json"))
	{
		$configuration_file = $_SERVER["DOCUMENT_ROOT"]. DIRECTORY_SEPARATOR. "autoload.json";
	}
	else if (file_exists($_SERVER["DOCUMENT_ROOT"]. DIRECTORY_SEPARATOR. "autoload.xml"))
	{
		$configuration_file = $_SERVER["DOCUMENT_ROOT"]. DIRECTORY_SEPARATOR. "autoload.xml";
	}
	else
	{
		return;
	}

	new Plug\Autoloader\AutoloaderRegister($configuration_file);
 ?>
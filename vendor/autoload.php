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
	 * @see AutoloadeRegister::ParseConfigFile() For a detailed explanation of this
	 *
	 * It is advised to set this location to the document root, that is the root level folder
	 *
	 * The default is the root level folder, but... feel free to change it to any other location as
	 * you see fit.
	 */
	$base_directory_minus_file_name = $_SERVER["DOCUMENT_ROOT"]. DIRECTORY_SEPARATOR;
	$config_file_name_minus_extension = "autoload";


	if (file_exists($base_directory_minus_file_name. $config_file_name_minus_extension.".json"))
	{
		$configuration_file = $base_directory_minus_file_name. $config_file_name_minus_extension.".json";
	}
	else if (file_exists($base_directory_minus_file_name. $config_file_name_minus_extension.".xml"))
	{
		$configuration_file = $base_directory_minus_file_name. $config_file_name_minus_extension.".xml";
	}
	else
	{
		return;
	}

	new Plug\Autoloader\AutoloaderRegister($configuration_file);
 ?>
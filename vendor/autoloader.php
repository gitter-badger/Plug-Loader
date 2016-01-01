<?php
/**
 * Plug-Autoloader Source: A PSR4 Implementation of a PHP Autoloader. 
 *
 * This library observes the [PSR4 specification | (http://www.php-fig.org/psr/psr-4/)] to implement an autoloader.
 * + The term "class" refers to classes, interfaces, traits, and other similar structures.
 * + A fully qualified class name has the following form:
 *	> \&lt;NamespaceName&gt;(\&lt;SubNamespaceNames&gt;)*\&lt;ClassName&gt;
 * 	- The fully qualified class name MUST have a top-level namespace name, also known as a "vendor namespace".
 * 	- The fully qualified class name MAY have one or more sub-namespace names.
 * 	- The fully qualified class name MUST have a terminating class name.
 * 	- Underscores have no special meaning in any portion of the fully qualified class name.
 * 	- Alphabetic characters in the fully qualified class name MAY be any combination of lower case and upper case.
 *	- All class names MUST be referenced in a case-sensitive fashion.
 * + When loading a file that corresponds to a fully qualified class name ...
 *	- A contiguous series of one or more leading namespace and sub-namespace names, not including the leading namespace separator, in the fully qualified class name (a "namespace prefix") corresponds to at least one "base directory".
 *	- The contiguous sub-namespace names after the "namespace prefix" correspond to a subdirectory within a "base directory", in which the namespace separators represent directory separators. The subdirectory name MUST match the case of the sub-namespace names.
 *	- The terminating class name corresponds to a file name ending in .php. The file name MUST match the case of the terminating class name.
 * + Autoloader implementations MUST NOT throw exceptions, MUST NOT raise errors of any level, and SHOULD NOT return a value.
 * #Example
 * > The table below shows the corresponding file path for a given fully qualified class name, namespace prefix, and base directory.
 * <table>
 *	<thead>
 *		<th>Fully Qualified Name</th>
 *		<th>Namespace Prefix</th>
 *		<th>Base Directory</th>
 *		<th>Resulting File path</th>
 *	</thead>
 *	<tbody>
 *		<tr>
 *			<td>Example\Databases\Querier\Request</td>
 *			<td>Example\Databases\Querier\</td>
 *			<td>./example/databases/querier/</td>
 *			<td>./example/databases/querier/request.php</td>
 *		</tr>
 *	</tbody>
 *	<tr>
 * </table>
 *
 * This file is part of Plug-Autoloader
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @version 0.0.1
 * @since 0.0.1 31st December, 2015
 * @copyright 2015 - 2016 Samuel Adeshina <samueladeshina73@gmail.com> <http://samshal.github.io>
 * @license MIT
 */

/**
 * Plug\Autoloader\Autoloader class.
 *
 * Base Class for Automatically autoloading files from directories.
 * observes the psr4 specification and builds and registers files with the spl_autoload_register function.
 *
 * @method void addNamespace(string $namespace_name, string $corresponding_base_directory, boolean $prepend)
 * @method void register()
 */
namespace Plug\Autoloader;

class Autoloader
{
	/**
	 * @var array $namespaces holds the registered namespaces and their an array of their corresponding base directories in a key - value pair structure
	 * @internal 
	*/
	private $namespaces;

	/**
	 *	publicly accessible constructor method
	 * 
	*/
	public function __construct()
	{
		/* Use a Lazy - Loading strategy to set the data type of the $namespaces variable to an array */
		$this->namespaces = array();
	}

	/**
	 * Register a new namespace
	 *
	 * Make the autoloader aware of a namespace and its corresponding base directory (or directories).
	 * You can also decide to append or prepend the base directory to the array of a namespace depending on how
	 * you want to be accessed.
	 * You can register more than one base directory for a namespace.
	 *
	 * @param string $namespace_name The name of the namespace you are trying to register.
	 * @param string $corresponding_base_directory The file level directory associated with $namespace_name
	 * @param boolean|null $prepend Set this parameter to true if you want the base directory you are registering
	 *		  to be checked first when looking for a class during autoload. Default value is false
	 * @return void
	*/
	public function addNamespace(string $namespace_name, string $corresponding_base_directory, $prepend = false)
	{
		/**
		 * Normalizes the namespace name parameter by removing every trailing slash and appending a new one.
		 * This ensures every namespace name (or namespace prefix) ends with a trailing backslash just like a normal namespace
		*/
		$namespace_name = trim($namespace_name, "\\") . "\\";

		/**
		 * Normalizes the file level directory name by removing every forward slash (or the directory separator used on the
		 * guest machine using the rtrim() function. This is to ensure the directory name is correctly formatted
		*/
		$corresponding_base_directory = rtrim($corresponding_base_directory, DIRECTORY_SEPARATOR)."/";

		/**
		 * Checks to see if the passed namespace has already been set in the global namespaces array.
		 * This is possible since multiple classes belonging to the same namespace may be located in different directories
		*/
		if (isset($this->namespaces[$namespace_name]) === false)
		{
			/**
			 * This means $namespace_name does not exist yet, we create it by making it a key in the global
			 * namespaces array and setting its value to a new array
			*/
			$this->namespaces[$namespace_name] = array();
		}

		/**
		 * We need to check if the corresponding base directory should be appended or prepended to the namespace name array
		*/
		if ($prepend)
		{
			/* Since we are prepending, we need to use the array_unshift() function to add the directory to the array*/
			array_unshift($this->namespaces[$namespace_name], $corresponding_base_directory);
		}
		else
		{
			/* We are appending, so we use the array_push function to add the directory to the array*/
			array_push($this->namespaces[$namespace_name], $corresponding_base_directory);
		}
	}

	/**
	 * Instantiates the autoloader
	 *
	 * Call this method on an instance of the Autoloader class to intantiate it and get it ready for a new session
	 * This method calls the spl_autoload_register() function.
	 *
	 * @return void
	*/
	public function register()
	{
		spl_autoload_register(array($this, "autoloadClass"));
	}

	/**
	 * Performs actual class autoloading
	*/
	protected function autoloadClass($class_name)
	{
		$class_name_temp = $class_name;
		/**
		 * Loop through the $class_name variable to get every namespace name and check if the class exists in that namespace
		*/
		while (false !== $pos = strrpos($class_name_temp, "\\"))
		{
			$class_name_temp = substr($class_name_temp, 0, $pos + 1); //The root namespace
			$relative_class_name = substr($class_name, $pos + 1);

			$mapped_file = $this->loadMappedFile($class_name_temp, $relative_class_name);
			if ($mapped_file)
			{
				return $mapped_file;
			}
			$class_name_temp = rtrim($class_name_temp, "\\"); //removes trailing backslash for a new run of the while loop

			/**
			 * Whats going on here?
			 * Consider the following: we are trying to autoload a class with the following qualified class name
			 * ExampleNamespace\ExampleSubNamespace\ExampleClassName;
			 * During the first iteration; that is, during the first run of the while loop,
			 * the $class_name_temp variables hold ExampleNamespace and
			 * the $relative_class_name variable holds \ExampleSubNamespace\ExampleClassName as the relative class name.
			 * Then we call the loadMappedFile(string, string) method to load the file if it exists, if it doesn't we continue
			 * looping until we find the file. If the file does not exist, we return false. Remember, we can't throw any exception
			 * since its illegal for a psr4-compliant autoloader to echo or throw exceptions.
			*/
		}

		//We couldn't find any class after searching through the mapped namespaces so we return false|null.
		return false;
	}

	/**
	 * A mapped file is any of the base directories located in the $this->namespaces[$namespace_name] array.
	 *
	 * This method as the name implies loads a class from the file system by checking the $namespace_name array
	 * within the $this->namepaces global array to get the file level url of the class.
	 *
	 * @param string $namespace_name
	 * @param string $relative_class_name
	 * @return 
	*/
	protected function loadMappedFile(string $namespace_name, string $relative_class_name) : \bool
	{
		/**
		 * Its possible that this method is called on an inexistent namespace, we need to check if thats not happening
		*/
		if (isset($this->namespaces[$namespace_name]) === false)
		{
			return false;
		}

		foreach ($this->namespaces[$namespace_name] as $corresponding_base_directory)
		{
			$file_level_url = trim(__DIR__, DIRECTORY_SEPARATOR);
			$file_level_url .=  DIRECTORY_SEPARATOR."..".$corresponding_base_directory;
			$file_level_url .= str_replace("\\", "/", $relative_class_name) . ".php";

			$file_level_url = str_replace("/", DIRECTORY_SEPARATOR, $file_level_url);

			if ($this->requireFile($file_level_url))
			{
				return true;
			}
		}

		//No mapped file exists for the supplied namespace
		return false;
	}

	/**
	 * Load a class from the file system
	 *
	 * @param string $file File URL to require/include
	 * @return boolean
	*/
	protected function requireFile(string $file) : \bool
	{
		if (file_exists($file))
		{
			require $file;
			return true;
		}

		return false;
	}
}
?>
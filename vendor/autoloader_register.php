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
namespace Plug\Autoloader;

require __DIR__."/autoloader.php";

class AutoloaderRegister
{
	/**
	 * @var Autoloader $autoloader An instance of the Autoloader object
	 * @access private
	 */
	private $autoloader;

	/**
	 * @var string $config_type Holds JSON or XML. Determines the format used for configuration
	 * @access private
	 */
	private $config_type;

	/**
	 * @var string $config_file_name Stores the full URI of the configuration file
	 * @access private
	*/
	private $config_file_name;

	/**
	 * @var string $document_root @see $_SERVER["DOCUMENT_ROOT"]
	 * @access private
	*/
	private $document_root;

	/**
	 * @var string $document_root_for_automatic_autoloading @see AutoloaderRegister::penetrateAllDirectories()
	 * @access private
	*/
	private $document_root_for_automatic_autoloading;

	/**
	 * @var string $namespace_root_for_automatic_autoloading @see AutoloaderRegister::penetrateAllDirectories()
	 * @access private
	*/
	private $namespace_root_for_automatic_autoloading;

	/**
	 * Constructor
	 *
	 * @param string $config_file_name
	*/
	public function __construct(string $config_file_name, string $document_root = null)
	{
		/**
		  * Uses a Lazy-Loading strategy to set the @autoloader variable to an instance of the Autoloader object
		  * @see Autoloader::__construct();
		*/
		$this->autoloader = new Autoloader();


		$this->config_file_name = $config_file_name;

		$this->document_root = trim($document_root, "\\");

		/**
		 * If the configuration type is successfully set and the configuration file is parsed successfully,
		 * register the autoloader
		 *
		 * @see AutoloaderRegister::setConfigType() To Learn how the setConfigType Method actually works
		 * @see AutoloaderRegister::parseConfigFile() To learn how the parseConfigFile works
		 * @see AutoloaderRegister::XmlConfigParser To learn how an xml file is parsed
		 * @see AutoloaderRegister::JsonConfigParser To learn how a json file is parsed
		*/
		if ($this->setConfigType() && $this->parseConfigFile())
		{
			$this->register();
		}
	}

	/**
	 * Register (and instantiate) the autoloader.
	 *
	 * Call this method after adding all namespaces to the global {$this->autoloader->namespaces} array
	 * to register the autoloader.
	 * @see Autoloader::register()
	 * @return void
	*/
	public function register()
	{
		$this->autoloader->register();
	}

	/**
	 * Add a new namespace together with an array of its corresponding base directories.
	 *
	 * @param string $namespace_name
	 * @param string $corresponding_base_directory
	 * @param boolean|null $prepend
	 * @see Autoloader::addNamespace(string, string, boolean)
	 * @return void
	*/
	public function addNamespace(string $namespace_name, string $corresponding_base_directory, $prepend = false)
	{
		$this->autoloader->addNamespace($namespace_name, $corresponding_base_directory, $prepend);
	}

	/**
	 * Setter object for the $config_type variable
	 * @param string|null $config_type Optional: Supply this if you know the config type before hand.
	 * @return boolean Was the config type set successfully?
	*/
	public function setConfigType(string $config_type = null) : bool
	{
		/**
		 * Was the config file type supplied?
		*/
		if (is_null($config_type) !== false)
		{
			/**
			 * Has the configuration file name been set?
			*/
			if (is_null($this->config_file_name) !== false || $this->config_file_name === "")
			{
				/**
				 * @todo Find another way to get the configuration type.
				*/
				return false;
			}

			/**
			 * Get the extension of the configuration file name by exploding the $config_file_name variable
			*/
			$config_type = (explode(".", strtolower($this->config_file_name)))[1];
		}
		/**
		 * The config Type was supplied. We need to check if its a valid config type format
		*/
		if (strcmp(strtolower($config_type), "json") == 0 || strcmp(strtolower($config_type), "xml") == 0)
		{
			/**
			 *Getting to this point means we have a valid config format.
			*/
			$this->config_type = strtolower($config_type);
			return true;
		}

		//Invalid format detected, return false
		return false;
	}

	/**
	 * Parse the supplied configuration file
	 *
	 * @return boolean
	*/
	public function parseConfigFile() : \bool
	{
		if (is_null($this->config_type) !== false || $this->config_type === "")
		{
			return false;
		}
		/**
		 * Switch through the $config_type variable to delegate the Parser to the appropriate method
		*/
		switch(strtolower($this->config_type))
		{
			case "xml":
			{
				return $this->XmlConfigParser($this->config_file_name);
				break;
			}
			case "json":
			{
				return $this->JsonConfigParser($this->config_file_name);
				break;
			}
			default:
			{
				return false;
			}
		}
	}

	/**
	 * Parse an XML (configuration) file.
	 *
	 * The AutoloaderRegister::XmlConfigParser() method below accepts the URI
	 * of an xml configuration file as a string parameter and then parses this
	 * xml config file using the SimpleXMLElement() function.
	 * A properly formatted xml configuration file must look like the following:
	 *  `<Namespaces>
	 *		<Namespace name="Namespace\\SubNamespace\\And\\So\\On" directory="/path/to/namespace"/>
	 * 		<Namespace name="AnotherNamespace\\AnotherSubNamespace\\And\\So\\On" directory="/path/to/another/namespace"/>
	 *	 </Namespaces>`
	 *
	 * @param string $configuration_file XML Configuration File
	 * @access private
	 * @return boolean
	*/
	private function XmlConfigParser(string $configuration_file) : \bool
	{
		if (file_exists($configuration_file))
		{
			$configuration = file_get_contents($configuration_file);
			$xml_parsed_configuration = new \SimpleXMLElement($configuration);
			foreach ($xml_parsed_configuration->Namespace as $xml_namespace)
			{
				$this->addNamespace($xml_namespace->attributes()->name, $xml_namespace->attributes()->directory);
			}
			return true;
		}

		return false;
	}

	/**
	 * Parse a JSON (configuration) file.
	 *
	 * This method is similar to the XmlConfigParser() method. @see AutoloaderRegister::XmlConfigParser()
	 *
	 * The AutoloaderRegister::JsonConfigParser() method below accepts the URI
	 * of a json configuration file as a string parameter and then parses this
	 * json config file using the json_decode() function.
	 * A properly formatted json configuration file must look like the following:
	 * `{
	 *		"Namespaces" :
	 *		{
	 *			"Namespace\\SubNamespace\\And\\So\\On" : "/path/to/namespace",
	 *			"AnotherNamespace\\AnotherSubNamespace\\And\\So\\On" : "/path/to/another/namespace"
	 *		}
	 *	}`
	 *
	 * or like this:
	 * `{
	 *		"Namespaces" :
	 *		{
	 *			"ROOT" : 
	 *			{
	 *				Namespace\\SubNamespace\\And\\So\\On", "/path/to/namespace"
	 *			}
	 *			"AnotherNamespace\\AnotherSubNamespace\\And\\So\\On" : "/path/to/another/namespace"
	 *		}
	 *	}`
	 *
	 * @param string $configuration_file JSON Configuration File
	 * @access private
	 * @return boolean
	*/
	private function JsonConfigParser(string $configuration_file) : \bool
	{
		if (file_exists($configuration_file))
		{
			$configuration = file_get_contents($configuration_file);
			$json_parsed_configuration = json_decode($configuration);

			if
			(
				isset(((array)$json_parsed_configuration->Namespaces)["ROOT"]) === true //Is there a namespace called "ROOT"?
			)
			{
				//If all conditions are true, delegate the operation of adding namespaces to another method
				$namespace_root = ((array)$json_parsed_configuration->Namespaces)["ROOT"];

				foreach ($namespace_root as $namespace_root_name=>$namespace_root_directory)
				{
					$this->penetrateAllDirectories($namespace_root_name, $namespace_root_directory);
				}

			}
			foreach ($json_parsed_configuration->Namespaces as $json_namespace_name=>$json_namespace_directory)
			{
				if ($json_namespace_name !== "ROOT")
				{
					$this->addNamespace($json_namespace_name, $json_namespace_directory);
				}
				
			}
			return true;
		}

		return false;
	}

	/**
	 * Base Method for moving through a directory-tree to get every sub-directories within all directories
	 *
	 * @param string $namespace_name
	 * @param string $namespace_directory
	 * @return void
	*/
	private function penetrateAllDirectories(string $namespace_name, string $namespace_directory)
	{
		$this->namespace_root_for_automatic_autoloading = ucfirst(trim($namespace_name, "\\") . "\\");
		$this->document_root_for_automatic_autoloading = str_replace("/",DIRECTORY_SEPARATOR, $namespace_directory);

		$this->addNamespace($this->namespace_root_for_automatic_autoloading, $this->document_root_for_automatic_autoloading);
		
		$this->penetrateAllSubDirectories($this->document_root_for_automatic_autoloading);
	}

	/**
	 * Recursive method called by the penetrateAllDirectores() method to load the sub-directories within a directory
	 * @see AutoloaderRegister::penetrateAllDirectories()
	 *
	 * @param string $namespace_directory
	 * @return void
	*/
	private function penetrateAllSubDirectories(string $namespace_directory)
	{
		$namespace_directory = strtolower(str_replace("/", DIRECTORY_SEPARATOR, $namespace_directory));

		$base_directory_path = substr($namespace_directory, strlen($this->document_root_for_automatic_autoloading));

		if (strcmp($base_directory_path, "") !== 0)
		{
			$this->registerNewNamespace($base_directory_path);
		}

		if (false !== isset($this->document_root) && $this->document_root !== null)
		{
			$namespace_directory = str_replace("/", DIRECTORY_SEPARATOR, $namespace_directory);
			$directories_root_uri = $this->document_root.$namespace_directory;
			$directories = glob($directories_root_uri.DIRECTORY_SEPARATOR."*", GLOB_ONLYDIR);

			if (true !== empty($directories))
			{
				foreach ($directories as $directory)
				{
					$directory_full_path = substr($directory, strlen($this->document_root));
					$this->penetrateAllSubDirectories($directory_full_path, $directory_full_path);
				}
			}
		}
	}

	/**
	 * Register a new namespace by calling the addNamespace method.
	 *
	 * @param string $directory
	 * @return void
	*/

	private function registerNewNamespace(string $directory)
	{
		$namespace = trim($directory, "\\") . "\\";
		$namespaces_temporary_array = explode("\\", $namespace);
		for ($count = 0; $count < count($namespaces_temporary_array); $count++)
		{
			$namespaces_temporary_array[$count] = ucfirst($namespaces_temporary_array[$count]);
		}
		$namespace = $this->namespace_root_for_automatic_autoloading.implode("\\", $namespaces_temporary_array);

		$directory = $this->document_root_for_automatic_autoloading . $directory;

		$this->addNamespace($namespace, $directory);

	}
}
?>
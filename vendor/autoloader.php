<?php
	namespace Plug\Autoloader;

/**
 * Plug-Autoloader Source: A PSR4 Implementation of a PHP Autoloader
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
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @version 0.0.1
 * @since 0.0.1 31st December, 2015
 * @copyright 2015 - Samuel Adeshina <samueladeshina73@gmail.com> <http://samshal.github.io>
 * @license MIT
 */

/**
 * Plug\Autoloader\Autoloader class.
 * Base Class for Automatically autoloading files from directories.
 * observes the psr4 specification and builds and registers files with the spl_autoload_register function.
 *
 * @method void addNamespace(string $namespace_name, string $corresponding_base_directory, boolean $prepend)
 * @method void registerAutoloader()
*/
class Autoloader
{
	/**
	 * @var array $namespaces holds the registered namespaces and their an array of their corresponding base directories in a key - value pair structure
	 * @internal 
	*/
	private $namespaces;

	/**
	 *	publicly accessible constructor method	 *
	 * 
	*/

	public function __construct()
	{
		/* Use a Lazy - Loading strategy to set the data type of the $namespaces variable to an array */
		$this->namespaces = array();
	}
}
?>
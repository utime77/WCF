<?php
namespace wcf\data\style\variable;
use wcf\data\DatabaseObject;

/**
 * Represents a style variable.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2015 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.style.variable
 * @category	Community Framework
 *
 * @property-read	integer		$variableID		unique id of the style variable
 * @property-read	string		$variableName		name of the style variable
 * @property-read	string		$defaultValue		default value of the style variable
 */
class StyleVariable extends DatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'style_variable';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'variableID';
	
	const TYPE_COLOR = 'color';
	const TYPE_TEXT = 'text';
	const TYPE_UNIT = 'unit';
}

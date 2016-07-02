<?php
namespace wcf\data\acl\option;
use wcf\data\DatabaseObject;

/**
 * Represents an acl option.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2016 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\Data\Acl\Option
 *
 * @property-read	integer		$optionID		unique id of the acl option
 * @property-read	integer		$packageID		id of the package which delivers the acl option
 * @property-read	integer		$objectTypeID		id of the associated acl object type
 * @property-read	string		$optionName		name and textual identifier of the acl option
 * @property-read	string		$categoryName		name of the acl option category the option belongs to
 */
class ACLOption extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'acl_option';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'optionID';
	
	/**
	 * Returns a list of options by object type id.
	 * 
	 * @param	integer		$objectTypeID
	 * @return	\wcf\data\acl\option\ACLOptionList
	 */
	public static function getOptions($objectTypeID) {
		$optionList = new ACLOptionList();
		$optionList->getConditionBuilder()->add("acl_option.objectTypeID = ?", [$objectTypeID]);
		$optionList->readObjects();
		
		return $optionList;
	}
}

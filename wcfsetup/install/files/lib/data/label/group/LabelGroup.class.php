<?php
namespace wcf\data\label\group;
use wcf\data\DatabaseObject;
use wcf\system\request\IRouteController;
use wcf\system\WCF;

/**
 * Represents a label group.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2015 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.label.group
 * @category	Community Framework
 *
 * @property-read	integer		$groupID		unique id of the label group
 * @property-read	string		$groupName		name of the label group or name of language item which contains the label text
 * @property-read	string		$groupDescription	description of the label group (only shown in ACP)
 * @property-read	integer		$forceSelection		is 1 if a label in the label group has to be selected when creating an object for which the label group is available, otherwise 0
 * @property-read	integer		$showOrder		position of the label group in relation to the other label groups
 */
class LabelGroup extends DatabaseObject implements IRouteController {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'label_group';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseIndexName
	 */
	protected static $databaseTableIndexName = 'groupID';
	
	/**
	 * @see	\wcf\data\ITitledObject::getTitle()
	 */
	public function getTitle() {
		return WCF::getLanguage()->get($this->groupName);
	}
	
	/**
	 * Returns label group title.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return $this->getTitle();
	}
	
	/**
	 * Callback for uasort() to sort label groups by show order and (if equal) group id.
	 * 
	 * @param	\wcf\data\DatabaseObject	$groupA
	 * @param	\wcf\data\DatabaseObject	$groupB
	 * @return	integer
	 */
	public static function sortLabelGroups(DatabaseObject $groupA, DatabaseObject $groupB) {
		if ($groupA->showOrder == $groupB->showOrder) {
			return ($groupA->groupID > $groupB->groupID) ? 1 : -1;
		}
		
		return ($groupA->showOrder > $groupB->showOrder) ? 1 : -1;
	}
}

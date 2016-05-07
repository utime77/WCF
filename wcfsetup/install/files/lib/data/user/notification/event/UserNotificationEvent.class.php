<?php
namespace wcf\data\user\notification\event;
use wcf\data\ProcessibleDatabaseObject;
use wcf\data\TDatabaseObjectOptions;
use wcf\data\TDatabaseObjectPermissions;

/**
 * Represents a user notification event.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2015 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.user.notification.event
 * @category	Community Framework
 *
 * @property-read	integer		$eventID			unique id of the user notification event
 * @property-read	integer		$packageID			id of the package which delivers the user notification event
 * @property-read	string		$eventName			name and textual identifier (within the object type) of the user notification event
 * @property-read	integer		$objectTypeID			id of the user notification object type
 * @property-read	string		$className			name of the PHP class implementing `wcf\system\user\notification\event\IUserNotificationEvent`
 * @property-read	string		$permissions			comma separated list of user group permissions of which the active user needs to have at least one to see the user notification event setting
 * @property-read	string		$options			comma separated list of options of which at least one needs to be enabled for the user notification event setting to be shown
 * @property-read	integer		$preset				is 1 if the user notification event is enabled by default otherwise 0
 * @property-read	string		$presetMailNotificationType	default mail notification type if the user notification event is enabled by defauled, otherwise empty
 */
class UserNotificationEvent extends ProcessibleDatabaseObject {
	use TDatabaseObjectOptions;
	use TDatabaseObjectPermissions;
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'user_notification_event';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'eventID';
	
	/**
	 * @see	\wcf\data\ProcessibleDatabaseObject::$processorInterface
	 */
	protected static $processorInterface = 'wcf\system\user\notification\event\IUserNotificationEvent';
}

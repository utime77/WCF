<?php
namespace wcf\data\stat\daily;
use wcf\data\DatabaseObject;

/**
 * Represents a statistic entry.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2015 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.stat.daily
 * @category	Community Framework
 *
 * @property-read	integer		$statID		unique id of the daily statistic entry
 * @property-read	integer		$objectTypeID	id of the daily statistic object type
 * @property-read	string		$date		date when the daily statistic entry has been created
 * @property-read	integer		$counter	daily statistic entry count for the last day
 * @property-read	integer		$total		cumulative daily statistic entry count up to the date
 */
class StatDaily extends DatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'stat_daily';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'statID';
}

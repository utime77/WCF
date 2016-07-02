<?php
namespace wcf\data\like;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a like of an object.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2016 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\Data\Like
 *
 * @property-read	integer		$likeID			unique id of the like
 * @property-read	integer		$objectID		id of the liked object
 * @property-read	integer		$objectTypeID		id of the object type of the liked object
 * @property-read	integer|null	$objectUserID		id of the user who created the liked object or null if user has been deleted or object was created by guest
 * @property-read	integer		$userID			id of the user who created the like
 * @property-read	integer		$time			timestamp at which the like has been created
 * @property-read	integer		$likeValue		value of the like (+1 = like, -1 = dislike)
 */
class Like extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'like';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'likeID';
	
	/**
	 * like value
	 * @var	integer
	 */
	const LIKE = 1;
	
	/**
	 * dislike value
	 * @var	integer
	 */
	const DISLIKE = -1;
	
	/**
	 * Gets a like by type, object id and user id.
	 * 
	 * @param	integer		$objectTypeID
	 * @param	integer		$objectID
	 * @param	integer		$userID
	 * @return	Like
	 */
	public static function getLike($objectTypeID, $objectID, $userID) {
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_like
			WHERE	objectTypeID = ?
				AND objectID = ?
				AND userID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([
			$objectTypeID,
			$objectID,
			$userID
		]);
		
		$row = $statement->fetchArray();
		
		if (!$row) {
			$row = [];
		}
		
		return new Like(null, $row);
	}
	
	/**
	 * @inheritDoc
	 */
	public static function getDatabaseTableAlias() {
		return 'like_table';
	}
	
	/**
	 * Returns true, if like value is a like.
	 * 
	 * @return	boolean
	 */
	public function isLike() {
		return ($this->likeValue == self::LIKE);
	}
	
	/**
	 * Returns true, if like value is a dislike.
	 * 
	 * @return	boolean
	 */
	public function isDislike() {
		return ($this->likeValue == self::DISLIKE);
	}
}

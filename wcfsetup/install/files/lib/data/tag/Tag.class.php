<?php
namespace wcf\data\tag;
use wcf\data\DatabaseObject;
use wcf\system\request\IRouteController;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Represents a tag.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2016 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\Data\Tag
 * 
 * @property-read	integer		$tagID		unique id of the tag
 * @property-read	integer		$languageID	id of the language the tag belongs to
 * @property-read	string		$name		name/text of the tag
 * @property-read	integer|null	$synonymFor	id of the tag for which the tag is a synoym or `null` if the tag is no synonym
 */
class Tag extends DatabaseObject implements IRouteController {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'tag';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'tagID';
	
	/**
	 * Return the tag with the given name or null of no such tag exists.
	 * 
	 * @param	string		$name
	 * @param	integer		$languageID
	 * @return	mixed
	 */
	public static function getTag($name, $languageID = 0) {
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_tag
			WHERE	languageID = ?
				AND name = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$languageID, $name]);
		$row = $statement->fetchArray();
		if ($row !== false) return new Tag(null, $row);
		
		return null;
	}
	
	/**
	 * Takes a string of comma separated tags and splits it into an array.
	 * 
	 * @param	string		$tags
	 * @param	string		$separators
	 * @return	string[]
	 */
	public static function splitString($tags, $separators = ',;') {
		return array_unique(ArrayUtil::trim(preg_split('/['.preg_quote($separators).']/', $tags)));
	}
	
	/**
	 * Takes a list of tags and builds a comma separated string from it.
	 * 
	 * @param	mixed[]		$tags
	 * @param	string		$separator
	 * @return	string
	 */
	public static function buildString(array $tags, $separator = ', ') {
		// TODO: This method seems to be unused and unnecessary, as it is a simply wrapper around implode now
		return implode($separator, $tags);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->name;
	}
	
	/**
	 * Returns the name of this tag.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return $this->getTitle();
	}
}

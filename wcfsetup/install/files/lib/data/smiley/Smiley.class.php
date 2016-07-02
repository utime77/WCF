<?php
namespace wcf\data\smiley;
use wcf\data\DatabaseObject;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a smiley.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2016 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\Data\Smiley
 * 
 * @property-read	integer		$smileyID	unique id of the smiley
 * @property-read	integer		$packageID	id of the package which delivers the smiley
 * @property-read	integer|null	$categoryID	id of the category the smiley belongs to or `null` if it belongs to the default category
 * @property-read	string		$smileyPath	path to the smiley file relative to wcf's defsult path
 * @property-read	string		$smileyTitle	title of the smiley
 * @property-read	string		$smileyCode	code used for displaying the smiley
 * @property-read	string		$aliases	alternative codes used for displaying the smiley
 * @property-read	integer		$showOrder	position of the smiley in relation to its siblings in the same category
 */
class Smiley extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'smiley';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'smileyID';
	
	/**
	 * Returns the url to this smiley.
	 * 
	 * @return	string
	 */
	public function getURL() {
		return WCF::getPath().$this->smileyPath;
	}
	
	/**
	 * Returns all aliases for this smiley.
	 * 
	 * @return	string[]
	 */
	public function getAliases() {
		if (!$this->aliases) return [];
		
		return explode("\n", StringUtil::unifyNewlines($this->aliases));
	}
}

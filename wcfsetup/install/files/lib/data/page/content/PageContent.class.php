<?php
namespace wcf\data\page\content;
use wcf\data\DatabaseObject;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\WCF;

/**
 * Represents a page content.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2016 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\Data\Page\Content
 * @since	3.0
 *
 * @property-read	integer		$pageContentID		unique id of the page content
 * @property-read	integer		$pageID			id of the page the page content belongs to
 * @property-read	integer		$languageID		id of the page content's language
 * @property-read	string		$title			title of the page in the associated language
 * @property-read	string		$content		actual content of the page in the associated language
 * @property-read	string		$metaDescription	meta description of the page in the associated language
 * @property-read	string		$metaKeywords		meta keywords of the page in the associated language
 * @property-read	string		$customURL		custom url of the page in the associated language
 * @property-read	integer		$hasEmbeddedObjects	is `1` if the content contains embedded objects, otherwise `0`
 */
class PageContent extends DatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'page_content';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'pageContentID';
	
	/**
	 * Returns the page's formatted content.
	 *
	 * @return      string
	 */
	public function getFormattedContent() {
		// assign embedded objects
		MessageEmbeddedObjectManager::getInstance()->setActiveMessage('com.woltlab.wcf.page.content', $this->pageContentID);
		
		$processor = new HtmlOutputProcessor();
		$processor->process($this->content, 'com.woltlab.wcf.page.content', $this->pageContentID);
		
		return $processor->getHtml();
	}
	
	/**
	 * Returns a certain page content.
	 *
	 * @param       integer         $pageID
	 * @param       integer         $languageID
	 * @return      PageContent|null
	 */
	public static function getPageContent($pageID, $languageID) {
		if ($languageID !== null) {
			$sql = "SELECT  *
				FROM    wcf" . WCF_N . "_page_content
				WHERE   pageID = ?
					AND languageID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$pageID, $languageID]);
		}
		else {
			$sql = "SELECT  *
				FROM    wcf" . WCF_N . "_page_content
				WHERE   pageID = ?
					AND languageID IS NULL";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$pageID]);
		}
		
		if (($row = $statement->fetchSingleRow()) !== false) {
			return new PageContent(null, $row);
		}
		
		return null;
	}
}

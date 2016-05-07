<?php
namespace wcf\data\user\menu\item;
use wcf\data\ProcessibleDatabaseObject;
use wcf\system\menu\user\DefaultUserMenuItemProvider;
use wcf\system\menu\ITreeMenuItem;
use wcf\system\request\LinkHandler;
use wcf\system\Regex;
use wcf\system\WCF;

/**
 * Represents an user menu item.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2015 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.user.menu.item
 * @category	Community Framework
 *
 * @property-read	integer		$menuItemID		unique id of the user menu item
 * @property-read	integer		$packageID		id of the package the user menu item belongs to
 * @property-read	string		$menuItem		textual identifier of the user menu item
 * @property-read	string		$parentMenuItem		textual identifier of the menu item's parent menu item or empty if it has no parent menu item
 * @property-read	string		$menuItemController	class name of the user menu item's controller used to generate menu item link
 * @property-read	string		$menuItemLink		additional part of the user menu item link if `$menuItemController` is set or external link
 * @property-read	integer		$showOrder		position of the user menu item in relation to its siblings
 * @property-read	string		$permissions		comma separated list of user group permissions of which the active user needs to have at least one to see the user menu item
 * @property-read	string		$options		comma separated list of options of which at least one needs to be enabled for the user menu item to be shown
 * @property-read	string		$className		name of the class implementing the user menu item provider interface or empty if there is no specific user menu item provider
 * @property-read	string		$iconClassName		FontAwesome CSS class name for user menu items on the first level
 */
class UserMenuItem extends ProcessibleDatabaseObject implements ITreeMenuItem {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'user_menu_item';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'menuItemID';
	
	/**
	 * @see	\wcf\data\ProcessibleDatabaseObject::$processorInterface
	 */
	protected static $processorInterface = 'wcf\system\menu\user\IUserMenuItemProvider';
	
	/**
	 * application abbreviation
	 * @var	string
	 */
	protected $application = '';
	
	/**
	 * menu item controller
	 * @var	string
	 */
	protected $controller = null;
	
	/**
	 * @see	\wcf\data\ProcessibleDatabaseObject::getProcessor()
	 */
	public function getProcessor() {
		if (parent::getProcessor() === null) {
			$this->processor = new DefaultUserMenuItemProvider($this);
		}
		
		return $this->processor;
	}
	
	/**
	 * @see	\wcf\system\menu\ITreeMenuItem::getLink()
	 */
	public function getLink() {
		// external link
		if (!$this->menuItemController) {
			return $this->menuItemLink;
		}
		
		$this->parseController();
		return LinkHandler::getInstance()->getLink($this->controller, array('application' => $this->application), $this->menuItemLink);
	}
	
	/**
	 * Returns application abbreviation.
	 * 
	 * @return	string
	 */
	public function getApplication() {
		$this->parseController();
		
		return $this->application;
	}
	
	/**
	 * Returns controller name.
	 * 
	 * @return	string
	 */
	public function getController() {
		$this->parseController();
		
		return $this->controller;
	}
	
	/**
	 * Parses controller name.
	 */
	protected function parseController() {
		if ($this->controller === null) {
			$this->controller = '';
			
			// resolve application and controller
			if ($this->menuItemController) {
				$parts = explode('\\', $this->menuItemController);
				$this->application = array_shift($parts);
				$menuItemController = array_pop($parts);
				
				// drop controller suffix
				$this->controller = Regex::compile('(Action|Form|Page)$')->replace($menuItemController, '');
			}
		}
	}
	
	/**
	 * Returns the menu item name.
	 * 
	 * @return	string
	 */
	public function __toString() {
		return WCF::getLanguage()->getDynamicVariable($this->menuItem);
	}
	
	/**
	 * Returns FontAwesome icon class name.
	 * 
	 * @return	string
	 */
	public function getIconClassName() {
		return ($this->iconClassName ?: 'fa-bars');
	}
}

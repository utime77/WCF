<?php
namespace wcf\system\request\route;
use wcf\system\application\ApplicationHandler;
use wcf\system\menu\page\PageMenu;
use wcf\system\request\ControllerMap;
use wcf\system\request\RequestHandler;
use wcf\system\request\RouteHandler;

/**
 * Dynamic route implementation to resolve HTTP requests, handling controllers using a distinct pattern.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2015 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	system.request
 * @category	Community Framework
 */
class DynamicRequestRoute implements IRequestRoute {
	/**
	 * schema for outgoing links
	 * @var	array<array>
	 */
	protected $buildSchema = [];
	
	/**
	 * route is restricted to ACP
	 * @var	boolean
	 */
	protected $isACP = false;
	
	/**
	 * pattern for incoming requests
	 * @var	string
	 */
	protected $pattern = '';
	
	/**
	 * primary application's abbreviation (e.g. "wbb")
	 * @var	string
	 */
	protected $primaryApplication = '';
	
	/**
	 * list of required components
	 * @var	array<string>
	 */
	protected $requireComponents = [];
	
	/**
	 * parsed request data
	 * @var	array<mixed>
	 */
	protected $routeData = [];
	
	/**
	 * DynamicRequestRoute constructor.
	 */
	public function __construct() {
		$this->init();
	}
	
	/**
	 * Sets default routing information.
	 */
	protected function init() {
		$this->setPattern('~
			/?
			(?:
				(?P<controller>
					(?:
						[a-z][a-z0-9]+
						(?:-[a-z][a-z0-9]+)*
					)+
				)
				(?:
					/
					(?P<id>\d+)
					(?:
						-
						(?P<title>[^/]+)
					)?
				)?
			)?
		~x');
		$this->setBuildSchema('/{controller}/{id}-{title}/');
	}
	
	/**
	 * @see IRequestRoute::setIsACP()
	 */
	public function setIsACP($isACP) {
		$this->isACP = $isACP;
	}
	
	/**
	 * Sets the build schema used to build outgoing links.
	 *
	 * @param	string		$buildSchema
	 */
	public function setBuildSchema($buildSchema) {
		$this->buildSchema = [];
		
		$buildSchema = ltrim($buildSchema, '/');
		$components = preg_split('~({(?:[a-z]+)})~', $buildSchema, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		
		foreach ($components as $component) {
			$type = 'component';
			if (preg_match('~{([a-z]+)}~', $component, $matches)) {
				$component = $matches[1];
			}
			else {
				$type = 'separator';
			}
			
			$this->buildSchema[] = [
				'type' => $type,
				'value' => $component
			];
		}
	}
	
	/**
	 * Sets the route pattern used to evaluate an incoming request.
	 *
	 * @param	string		$pattern
	 */
	public function setPattern($pattern) {
		$this->pattern = $pattern;
	}
	
	/**
	 * Sets the list of required components.
	 *
	 * @param	array<string>	$requiredComponents
	 */
	public function setRequiredComponents(array $requiredComponents) {
		$this->requireComponents = $requiredComponents;
	}
	
	/**
	 * @see	IRoute::buildLink()
	 */
	public function buildLink(array $components) {
		$application = (isset($components['application'])) ? $components['application'] : null;
		
		// drop application component to avoid being appended as query string
		unset($components['application']);
		
		// handle default values for controller
		$useBuildSchema = true;
		if (count($components) == 1 && isset($components['controller'])) {
			$ignoreController = false;
			
			if (!RequestHandler::getInstance()->isACPRequest()) {
				$landingPage = PageMenu::getInstance()->getLandingPage();
				if ($this->primaryApplication === '') {
					$primaryApplication = ApplicationHandler::getInstance()->getPrimaryApplication();
					$this->primaryApplication = ApplicationHandler::getInstance()->getAbbreviation($primaryApplication->packageID);
				}
				
				// check if this is the default controller
				if (strcasecmp(RouteHandler::getInstance()->getDefaultController($application), $components['controller']) === 0) {
					// check if this matches the primary application
					if ($this->primaryApplication === $application) {
						if (strcasecmp($landingPage->getController(), $components['controller']) === 0) {
							// skip controller if it matches the default controller
							$ignoreController = true;
						}
					}
					else {
						// skip default controller
						$ignoreController = true;
					}
				}
				else if (strcasecmp($landingPage->getController(), $components['controller']) === 0) {
					// landing page
					$ignoreController = true;
				}
			}
			
			// drops controller from route
			if ($ignoreController) {
				$useBuildSchema = false;
				
				// unset the controller, since it would otherwise be added with http_build_query()
				unset($components['controller']);
			}
		}
		
		return $this->buildRoute($components, $application, $useBuildSchema);
	}
	
	/**
	 * Builds the actual link, the parameter $useBuildSchema can be set to false for
	 * empty routes, e.g. for the default page.
	 *
	 * @param	array		$components
	 * @param	string		$application
	 * @param	boolean		$useBuildSchema
	 * @return	string
	 */
	protected function buildRoute(array $components, $application, $useBuildSchema) {
		$link = '';
		
		if ($useBuildSchema) {
			$lastSeparator = null;
			$skipToLastSeparator = false;
			foreach ($this->buildSchema as $component) {
				$value = $component['value'];
				
				if ($component['type'] === 'separator') {
					$lastSeparator = $value;
				}
				else if ($skipToLastSeparator === false) {
					// routes are build from left-to-right
					if (empty($components[$value])) {
						$skipToLastSeparator = true;
						
						// drop empty components to avoid them being appended as query string argument
						unset($components[$value]);
						
						continue;
					}
					
					if ($lastSeparator !== null) {
						$link .= $lastSeparator;
						$lastSeparator = null;
					}
					
					// handle controller names
					if ($value === 'controller') {
						$components[$value] = $this->getControllerName($application, $components[$value]);
					}
					
					$link .= $components[$value];
					unset($components[$value]);
				}
			}
			
			if (!empty($link) && $lastSeparator !== null) {
				$link .= $lastSeparator;
			}
		}
		
		if ($this->isACP || !URL_OMIT_INDEX_PHP) {
			if (!empty($link)) {
				$link = 'index.php?' . $link;
			}
		}
		
		if (!empty($components)) {
			if (strpos($link, '?') === false) $link .= '?';
			else $link .= '&';
			
			$link .= http_build_query($components, '', '&');
		}
		
		return $link;
	}
	
	/**
	 * @see	IRoute::canHandle()
	 */
	public function canHandle(array $components) {
		if (!empty($this->requireComponents)) {
			foreach ($this->requireComponents as $component => $pattern) {
				if (empty($components[$component])) {
					return false;
				}
				
				if ($pattern && !preg_match($pattern, $components[$component])) {
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * @see	IRoute::getRouteData()
	 */
	public function getRouteData() {
		return $this->routeData;
	}
	
	/**
	 * @see	IRoute::isACP()
	 */
	public function isACP() {
		return $this->isACP;
	}
	
	/**
	 * @see	IRoute::matches()
	 */
	public function matches($requestURL) {
		if (preg_match($this->pattern, $requestURL, $matches)) {
			foreach ($matches as $key => $value) {
				if (!is_numeric($key)) {
					$this->routeData[$key] = $value;
				}
			}
			
			$this->routeData['isDefaultController'] = (!isset($this->routeData['controller']));
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the transformed controller name.
	 *
	 * @param	string		$application
	 * @param	string		$controller
	 * @return	string
	 */
	protected function getControllerName($application, $controller) {
		return ControllerMap::getInstance()->lookup($controller);
	}
}

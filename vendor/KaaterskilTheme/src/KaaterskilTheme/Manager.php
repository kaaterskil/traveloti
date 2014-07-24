<?php
/**
 * Kaaterskil Library
 *
 * @package		KaaterskilTheme
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace KaaterskilTheme;

use Zend\Stdlib\PriorityQueue;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Resolver\AggregateResolver;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Resolver\TemplatePathStack;

/**
 * Manager Class
 * @author Blair
 */
class Manager {
	
	/**
	 * @var Zend\Stdlib\PriorityQueue
	 */
	protected $themePaths = null;
	
	/**
	 * @var string
	 */
	protected $currentTheme = null;
	
	/**
	 * @var KaaterskilTheme\Adapter\AdapterInterface
	 */
	protected $lastMatchedAdapter = null;
	
	/**
	 * @var Zend\Stdlib\PriorityQueue
	 */
	protected $adapters = null;
	
	/**
	 * @var Zend\ServiceManager\ServiceLocatorInterface
	 */
	protected $serviceManager;
	
	/**
	 * Constructor
	 *
	 * @param ServiceLocatorInterface $sm
	 * @param unknown $options
	 */
	public function __construct(ServiceLocatorInterface $sm, $options = array()) {
		$this->serviceManager = $sm;
		
		// Set the default theme paths in LIFO order
		$this->themePaths = new PriorityQueue();
		if(isset($options['theme_paths'])) {
			$priority = 1;
			foreach ($options['theme_paths'] as $path) {
				$this->themePaths->insert($path, $priority++);
			}
		}
		
		// Set theme selector adapters in LIFO order
		$this->adapters = new PriorityQueue();
		if(isset($options['adapters'])) {
			$priority = 1;
			foreach ($options['adapters'] as $clazz) {
				$adapter = new $clazz($sm);
				$this->adapters->insert($adapter, $priority++);
			}
		}
	}
	
	/*----- Getter/Setters -----*/
	
	/**
	 * @return string
	 */
	public function getTheme() {
		return $this->currentTheme;
	}
	
	/**
	 * @param string $theme
	 * @return boolean
	 */
	public function setTheme($theme) {
		if(!$this->lastMatchedAdapter) {
			return false;
		}
		$theme = $this->cleanThemeName($theme);
		return $this->lastMatchedAdapter->setTheme($theme);
	}
	
	/*----- Methods -----*/
	
	/**
	 * Returns the theme configuration
	 *
	 * @param string $theme
	 * @return null|array
	 */
	public function getThemeConfig($theme) {
		$theme = $this->cleanThemeName($theme);
		$iter = $this->themePaths->getIterator();
		
		$config = null;
		if($iter->count() > 0) {
			$templatePathStack = array();
			$templateMap = array();
			foreach($iter as $path) {
				if(file_exists($path . $theme . '/config.php')) {
					$moduleConfig = include ($path . $theme . '/config.php');
					$templatePathStack = array_merge(
						$templatePathStack,
						$moduleConfig['template_path_stack']
					);
					$templateMap = array_merge(
						$templateMap,
						$moduleConfig['template_map']
					);
				}
			}
			$config = array(
				'template_path_stack' => $templatePathStack,
				'template_map' => $templateMap,
			);
		}
		return $config;
	}
	
	/**
	 * Initialize the theme by selecting a theme using the adapters and
	 * updating the view resolver
	 *
	 * @return boolean
	 */
	public function init() {
		// Return if already initialized
		if($this->currentTheme) {
			return true;
		}
		
		// Get the current theme
		$theme = $this->selectCurrentTheme();
		if(!$theme) {
			return false;
		}
		
		// Get the theme configuration
		$config = $this->getThemeConfig($theme);
		if(!$config) {
			return false;
		}
		
		$viewResolver = $this->serviceManager->get('ViewResolver');
		$themeResolver = new AggregateResolver();
		if(isset($config['template_map'])) {
			$viewResolverMap = $this->serviceManager->get('ViewTemplateMapResolver');
			$viewResolverMap->add($config['template_map']);
			
			$mapResolver = new TemplateMapResolver($config['template_map']);
			$themeResolver->attach($mapResolver);
		}
		
		if(isset($config['template_path_stack'])) {
			$viewResolverPathStack = $this->serviceManager->get('ViewTemplatePathStack');
			$viewResolverPathStack->addPaths($config['template_path_stack']);
			
			$pathResolver = new TemplatePathStack(array(
				'script_paths' => $config['template_path_stack'],
			));
			$defaultPathStack = $this->serviceManager->get('ViewTemplatePathStack');
			$pathResolver->setDefaultSuffix($defaultPathStack->getDefaultSuffix());
			$themeResolver->attach($pathResolver);
		}
		
		$viewResolver->attach($themeResolver, 100);
		return true;
	}
	
	protected function cleanThemeName($theme) {
		$theme = str_replace('.', '', $theme);
		$theme = str_replace('/', '', $theme);
		return $theme;
	}
	
	protected function selectCurrentTheme() {
		$theme = null;
		$adapter = null;

		$iter = $this->adapters;
		foreach($iter as $adapter) {
			$theme = $adapter->getTheme();
		}
		if(!$theme) {
			return null;
		}
		
		$this->lastMatchedAdapter = $adapter;
		$this->currentTheme = $theme;
		return $theme;
	}
}
?>
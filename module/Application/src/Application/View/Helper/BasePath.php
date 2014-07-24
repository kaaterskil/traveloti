<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_Application
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\View\Helper;

use Zend\View\Helper\BasePath as ZendBasePath;

/**
 * BaseUrl Class
 *
 * @author Blair
 */
class BasePath extends ZendBasePath {
	
	public function __invoke($file = null) {
		$protocol = (isset($_SERVER['HTTPS']) ? 'https' : 'http');
		$server = $_SERVER['HTTP_HOST'];
		$port = ($_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '');
		$path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
		
		$basepath = $protocol . '://' . $server . $port . $path;
		$this->setBasePath($basepath);

        if (null !== $file) {
            $file = '/' . ltrim($file, '/');
        }
        return rtrim($basepath, '/') . $file;
	}
}
?>
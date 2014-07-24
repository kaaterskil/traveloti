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

use Zend\View\Helper\AbstractHtmlElement;

/**
 * ImgElement Class
 *
 * @author Blair
 */
class ImgElement extends AbstractHtmlElement {
	
	private $attribs = '';
	private $name = '';
	private $src = '';
	private $imagePath = '';
	private $fileName = '';
	
	public function getFileName() {
		return $this->fileName;
	}
	
	public function getImagePath() {
		return $this->imagePath;
	}
	
	public function setImagePath($path) {
		$this->imagePath = $path;
		$this->fileName = basename($path);
		$this->src = $path;
		return $this;
	}
	
	public function getSrc() {
		return $this->src;
	}
	
	public function __invoke($name, $path = null, $attribs = array()) {
		$this->name = $name;
		$this->setImagePath($path);
		$this->attribs = $this->htmlAttribs($attribs);
		
		$xhtml = '<img src="' . $this->src . '" ' . $this->attribs . ' id="' . $this->name . '"';
		
		return $xhtml . $this->getClosingBracket();
	}
}
?>
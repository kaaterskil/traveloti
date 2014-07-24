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

use Base\Model\Photo;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * StreamPhotoSizer Class
 *
 * @author Blair
 */
class PhotoSizer extends AbstractHelper {
	const PORTRAIT = 0;
	const LANDSCAPE = 1;

	public function __invoke(Photo $photo, $maxSize, $scaled = false, $cropped = false) {
		$source = $photo->getSource();
		$oldHeight = $photo->getHeight();
		$oldWidth = $photo->getWidth();
		$orientation = $oldWidth > $oldHeight ? self::LANDSCAPE : self::PORTRAIT;

		$attribs = array(
			'height' => $oldHeight,
			'width' => $oldWidth,
			'scaled' => false,
		);

		if(!$scaled && !$cropped) {
			$attribs = $this->scaleToMaxWidth($oldHeight, $oldWidth, $maxSize, $orientation);
		} else {
			if($scaled) {
				$attribs = $this->resizeScaled($oldHeight, $oldWidth, $maxSize, $orientation);
			} else {
				$attribs = $this->resizeCropped($oldHeight, $oldWidth, $maxSize, $orientation);
			}
		}
		return $attribs;
	}

	protected function scaleToMaxWidth($oldHeight, $oldWidth, $maxSize, $orientation) {
		if($oldWidth > $maxSize) {
			$newWidth = $maxSize;
			$newHeight = $this->resize($newWidth, $oldWidth, $oldHeight);
			$scaled = true;
		} else {
			$newHeight = $oldHeight;
			$newWidth = $oldWidth;
			$scaled = false;
		}

		return array(
			'height' => $newHeight,
			'width' => $newWidth,
			'scaled' => $scaled,
		);
	}

	protected function resizeScaled($oldHeight, $oldWidth, $maxSize, $orientation) {
		if(($oldHeight > $maxSize) && ($oldWidth <= $maxSize)) {
			$newHeight = $maxSize;
			$newWidth = $this->resize($newHeight, $oldHeight, $oldWidth);
			$scaled = true;
		} elseif(($oldWidth > $maxSize) && ($oldHeight <= $maxSize)) {
			$newWidth = $maxSize;
			$newHeight = $this->resize($newWidth, $oldWidth, $oldHeight);
			$scaled = true;
		} else if(($oldHeight > $maxSize) && ($oldWidth > $maxSize)) {
			if($orientation == self::PORTRAIT) {
				$newHeight = $maxSize;
				$newWidth = $this->resize($newHeight, $oldHeight, $oldWidth);
			} else {
				$newWidth = $maxSize;
				$newHeight = $this->resize($newWidth, $oldWidth, $oldHeight);
			}
			$scaled = true;
		} else {
			$newHeight = $oldHeight;
			$newWidth = $oldWidth;
			$scaled = false;
		}

		return array(
			'height' => $newHeight,
			'width' => $newWidth,
			'scaled' => $scaled,
		);
	}

	protected function resizeCropped($oldHeight, $oldWidth, $maxSize, $orientation) {
		if(($oldHeight > $maxSize) && ($oldWidth > $maxSize)) {
			if($orientation == self::PORTRAIT) {
				// Will crop the bottom of the image
				$newWidth = $maxSize;
				$newHeight = $this->resize($newWidth, $oldWidth, $oldHeight);
				$scaled = true;
			} else {
				// Will crop the right side of the image
				$newHeight = $maxSize;
				$newWidth = $this->resize($newHeight, $oldHeight, $oldWidth);
				$scaled = true;
			}
		} else {
			$newHeight = $oldHeight;
			$newWidth = $oldWidth;
			$scaled = false;
		}

		return array(
			'height' => $newHeight,
			'width' => $newWidth,
			'scaled' => $scaled,
		);
	}

	private function resize($side1, $maxSize, $side2) {
		return round($side1 / $maxSize * $side2);
	}
}
?>
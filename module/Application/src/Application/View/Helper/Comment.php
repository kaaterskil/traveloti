<?php
/**
 * Traveloti Library
 *
 * @category	Traveloti
 * @package		Traveloti_
 * @author		Blair
 * @copyright	Copyright (c) 2009-2012 Kaaterskil Management, LLC
 * @version		$Id: $
 */

namespace Application\View\Helper;

use Base\Form\CommentForm;
use Base\Model\Post;
use Zend\Form\View\Helper\AbstractHelper;

/**
 * CommentForm Class
 *
 * @author Blair
 */
class Comment extends AbstractHelper {
	
	public function __invoke(Post $post) {
		$form = new CommentForm();
		$form->get('post_id')->setValue($post->getId());
		$form->get('type')->setValue($post->getType());
		
		return $form;
	}
}
?>
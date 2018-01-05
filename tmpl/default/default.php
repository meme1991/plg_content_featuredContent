<?php
/**
 * @version    1.0.0
 * @package    SPEDI Featured Content
 * @author     SPEDI srl - http://www.spedi.it
 * @copyright  Copyright (c) Spedi srl.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<?php foreach($item['result'] as $link) : ?>
	<div class="featured-content d-flex article-list mb-2">
		<span class="d-flex justify-content-center align-items-center py-2 px-3" <?php if(isset($item['color'])) : ?> style="background-color: <?php echo $item['color'] ?>" <?php endif; ?>>
			<i class="fa fa-chevron-right" aria-hidden="true"></i>
		</span>
		<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($link->id, $link->catid)) ?>" title="<?php echo $link->title ?>" class="p-2">
			<?php echo $link->title ?>
		</a>
	</div>
<?php endforeach; ?>

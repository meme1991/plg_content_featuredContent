<?php
# @Author: SPEDI srl
# @Date:   30-01-2018
# @Email:  sviluppo@spedi.it
# @Last modified by:   SPEDI srl
# @Last modified time: 30-01-2018
# @License: GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
# @Copyright: Copyright (c) SPEDI srl

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<?php foreach($item['result'] as $link) : ?>
	<div class="featured-content d-flex article-list mb-2">
		<span class="d-flex justify-content-center align-items-center py-2 px-3" <?php if(isset($item['color'])) : ?> style="background-color: <?php echo $item['color'] ?>" <?php endif; ?>>
			<i class="far fa-chevron-right" aria-hidden="true"></i>
		</span>
		<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($link->id, $link->catid)) ?>" title="<?php echo $link->title ?>" class="p-2">
			<?php echo $link->title ?>
		</a>
	</div>
<?php endforeach; ?>

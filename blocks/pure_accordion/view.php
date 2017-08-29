<?php
/**
 * Created by Pure/Web
 * www.pure-web.ru
 * © 2017
 */
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getCurrentPage();
if ($openedByDefault || $c->isEditMode()) {
    $contentClass = 'open';
} else {
    $contentClass = '';
}
?>
<section class="pure-accordion-block-container <?php echo $contentClass?>" data-pure-accordion-id="<?php echo $bID?>" <?php
if ($showPermalink) { echo 'data-pure-accordion-handle="'.$handle.'"';}
?>>
    <?php
    if ($showPermalink) { ?>
        <a name="<?php echo $handle?>" id="<?php echo $handle?>"></a><?php
    }
    ?>
    <div class="header" data-pure-accordion-id="<?php echo $bID?>">
        <h1 class="title">
            <?php echo $title?>
        </h1>
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <?php
    if ($showPermalink) { ?>
        <div class="permalink">
            <a title="<?php echo t('Permalink')?>" href="<?php echo \URL::to($c)?>#<?php echo $handle?>"><i class="fa fa-link" aria-hidden="true"></i></a>
        </div><?php
    }
    ?>
    <div class="content" data-pure-accordion-id="<?php echo $bID?>">
        <?php echo $content?>
    </div>
</section>

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
<section class="pure-accordion-block-container <?=$contentClass?>" data-pure-accordion-id="<?=$bID?>" <?php
if ($showPermalink) { echo 'data-pure-accordion-handle="'.$handle.'"';}
?>>
    <?php
    if ($showPermalink) { ?>
        <a name="<?=$handle?>" id="<?=$handle?>"></a><?php
    }
    ?>
    <div class="header" data-pure-accordion-id="<?=$bID?>">
        <h1 class="title">
            <?=$title?>
        </h1>
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <?php
    if ($showPermalink) { ?>
        <div class="permalink">
            <a title="<?=t('Permalink')?>" href="<?=\URL::to($c)?>#<?=$handle?>"><i class="fa fa-link" aria-hidden="true"></i></a>
        </div><?php
    }
    ?>
    <div class="content" data-pure-accordion-id="<?=$bID?>">
        <?=$content?>
    </div>
</section>

<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Utility\Service\Identifier $identifier
 * @var int $bID
 * @var Concrete\Core\Page\Page|null $currentPage
 * @var string $currentPageUrl (empty if $currentPage is null)
 * @var string $title
 * @var string $handle
 * @var string $content
 * @var bool|int|string $openedByDefault
 * @var bool|int|string $showPermalink
 */

$uniqueID = $bID . '_' . $identifier->getString(6);
if ($openedByDefault || ($currentPage && $currentPage->isEditMode())) {
    $contentClass = 'open';
} else {
    $contentClass = '';
}
$hHandle = h($handle);
if ($currentPage === null) {
    $showPermalink = false;
}
?>
<section class="pure-accordion-block-container <?= $contentClass ?>" data-pure-accordion-id="<?= $uniqueID?>" <?= $showPermalink ? "data-pure-accordion-handle=\"{$hHandle}\"" : '' ?>>
    <?php
    if ($showPermalink) {
        ?>
        <a name="<?= $hHandle ?>" id="<? $handle ?>"></a>
        <?php
    }
    ?>
    <div class="header" data-pure-accordion-id="<?= $uniqueID ?>">
        <h1 class="title">
            <?= $title ?>
        </h1>
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <?php
    if ($showPermalink) {
        ?>
        <div class="permalink">
            <a title="<?= t('Permalink') ?>" href="<?= h($currentPageUrl . '#' . $handle) ?>"><i class="fa fa-link" aria-hidden="true"></i></a>
        </div>
        <?php
    }
    ?>
    <div class="content" data-pure-accordion-id="<?= $bID ?>">
        <?= $content ?>
    </div>
</section>

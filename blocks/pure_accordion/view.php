<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Page\Page|null $currentPage
 * @var string $title
 * @var string $handle
 * @var string $content
 * @var bool|int|string $openedByDefault
 * @var bool|int|string $showPermalink
 */

if ($openedByDefault || ($currentPage && $currentPage->isEditMode())) {
    $contentClass = 'open';
} else {
    $contentClass = '';
}
?>
<section class="pure-accordion-block-container <?= $contentClass ?>"<?= $showPermalink ? (' id="' . h($handle) . '"') : '' ?>>
    <div class="header">
        <h1 class="title">
            <?= h($title) ?>
        </h1>
        <span class="chevron" aria-hidden="true">&#x2B9F;</span>
    </div>
    <?php
    if ($showPermalink) {
        ?>
        <a class="permalink" title="<?= t('Permalink') ?>" href="<?= h('#' . $handle) ?>">&#x1F517;</a>
        <?php
    }
    ?>
    <div class="content">
        <?= $content ?>
    </div>
</section>

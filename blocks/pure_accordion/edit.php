<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Editor\EditorInterface $editor
 *
 * @var string $title
 * @var string $handle
 * @var string $content
 * @var bool|int|string $openedByDefault
 * @var bool|int|string $showPermalink
 */

?>
<div class="form-group">
    <?= $form->label('title', t('Title')) ?>
    <?= $form->text('title', $title, ['required' => 'required']) ?>
</div>
<div class="form-group">
    <?= $form->label('handle', t('Handle')) ?>
    <?= $form->text('handle', $handle, ['placeholder' => t('Leave empty to automatically determine it')]) ?>
</div>
<div class="form-group">
    <?= $editor->outputStandardEditor('content', $content) ?>
</div>
<div class="form-group">
    <label class="control-label">
        <?= $form->checkbox('openedByDefault', '1', $openedByDefault ? true : false) ?>
        <?= t('Opened by default') ?>
    </label>
</div>
<div class="form-group">
    <label class="control-label">
        <?= $form->checkbox('showPermalink', '1', $showPermalink ? true : false) ?>
        <?= t('Show permalink') ?>
    </label>
</div>
<script>
$(document).ready(function() {

var $showPermalink = $('#ccm-block-form #showPermalink');
var $handle = $('#ccm-block-form #handle').closest('.form-group');

$showPermalink
    .on('change', function() {
        $handle.toggle($showPermalink.is(':checked'));
    })
    .trigger('change')
;

});
</script>

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
 * @var string $groupHandle
 */

?>
<div class="form-group">
    <?= $form->label('title', t('Title')) ?>
    <?= $form->text('title', $title, ['required' => 'required', 'maxlength' => '255']) ?>
</div>
<div class="form-group">
    <?= $form->label('handle', t('Handle')) ?>
    <?= $form->text('handle', $handle, ['placeholder' => t('Leave empty to automatically determine it'), 'maxlength' => '255']) ?>
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
<div class="form-group">
    <?= $form->label('groupHandle', t('Group Handle')) ?>
    <?= $form->text('groupHandle', $groupHandle, ['maxlength' => '255']) ?>
    <div class="small alert-alert-info">
        <?= t('Here you can specify the name of a group of %s blocks (it can be whatever you like).', t('Pure Accordion')) ?><br />
        <?= t('There will be at most one block opened at a time.') ?>
        <div id="groupHandlesOnPage"></div>
    </div>
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

var currentGroupHandlesOnPage = [];
$('.pure-accordion-block-container').each(function (_, el) {
    var handle = $(el).data('group');
    if (typeof handle === 'string' && handle !== '' && currentGroupHandlesOnPage.indexOf(handle) < 0) {
        currentGroupHandlesOnPage.push(handle);
    }
});
var $groupHandlesOnPage = $('#groupHandlesOnPage');
if (currentGroupHandlesOnPage.length === 0) {
    $groupHandlesOnPage.text(<?= json_encode(t("At the moment there's no other named group in the current page.")) ?>);
} else {
    currentGroupHandlesOnPage.sort(
        function(a, b) {
            if (a.toLowerCase() < b.toLowerCase()) return -1;
            if (a.toLowerCase() > b.toLowerCase()) return 1;
            return 0;
        }
    );
    $groupHandlesOnPage.text(<?= json_encode(t("At the moment in the current page there are the following named groups:")) ?>);
    var $ul = $('<ul />'), $li, $a;
    currentGroupHandlesOnPage.forEach(function (g) {
        $a = $('<a href="javascript:void(0)" />')
            .text(g)
            .on('click', function(e) {
                $('#groupHandle').val(g);
            });
        ;
        $a.appendTo($li = $('<li />'));
        $li.appendTo($ul);
    });
    $ul.appendTo($groupHandlesOnPage);
}

});
</script>

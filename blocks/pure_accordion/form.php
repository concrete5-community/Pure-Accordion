<?php
/**
 * Created by Pure/Web
 * www.pure-web.ru
 * © 2017
 */

defined('C5_EXECUTE') or die("Access Denied.");
/** @var \Concrete\Core\Form\Service\Form $form */
$form = \Core::make('helper/form');
?>

<div class="pure-accordion-edit-container">

    <div class="form-group">
        <?=$form->label('title',t('Title'));?>
        <?=$form->text('title', $title);?>
    </div>

    <div class="form-group">
        <?php
        /** @var \Concrete\Core\Editor\CkeditorEditor $editor */
        $editor =  Core::make("editor");
        echo $editor->outputStandardEditor('content', $controller->getContentEditMode());
        ?>
    </div>

    <div class="form-group">
        <label class="control-label">
            <input type="checkbox" name="openedByDefault" value="1" <?php if ($openedByDefault) { ?>checked<?php } ?>>
            <?php echo t('Opened by default')?>
        </label>
    </div>

    <div class="form-group">
        <label class="control-label">
            <input type="checkbox" name="showPermalink" value="1" <?php if ($showPermalink) { ?>checked<?php } ?>>
            <?php echo t('Show permalink')?>
        </label>
    </div>

</div>



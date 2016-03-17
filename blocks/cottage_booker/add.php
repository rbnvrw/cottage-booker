<?php defined('C5_EXECUTE') or die(_("Access Denied.")) ?>
<div class="ccm-ui cottage_booker">
    <div class="alert-message block-message info">
        <?php echo t("Instellingen kunnen na het opslaan bewerkt worden via") ?>&nbsp;
        link.
    </div>
    <?php echo $form->label('cottageName', t('Naam'));?>
    <?php echo $form->text('cottageName'); ?>
</div>

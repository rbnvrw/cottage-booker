<?php defined('C5_EXECUTE') or die(_("Access Denied."));?>
$oLabel = $form->label('cottageName', t('Naam'));
$oText = $form->text('cottageName');
echo TwigTemplate::renderTemplate('block/add', ['label' => $oLabel, 'text' => $oText]);

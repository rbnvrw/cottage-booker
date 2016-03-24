<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

function getSettingsTabs()
{
    $aTabs = array(
        array('general', t('Algemeen'), true),
        array('costs', t('Kosten')),
    );

    return Loader::helper('concrete/interface')->tabs($aTabs);
}

function getSaveForm()
{
    $aSaveForm = [];

    $oForm = Loader::helper('form');
    $aSaveForm['action'] = $this->action('save', $aBlockSettings['bID']);

    $aSaveForm['nameLabel'] = $oForm->label('cottageName', t('Naam'));
    $aSaveForm['nameText'] = $oForm->text('cottageName', $aBlockSettings['cottageName']);

    return $aSaveForm;
}

$aContext = [
    'header' => Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Cottage Booker'), t('Dit is de beheerpagina voor de Cottage Booker.')),
    'task' => $this->controller->getTask(),
    'error' => $error,
    'message' => $message,
    'settingsTabs' => getSettingsTabs(),
    'saveForm' => getSaveForm()
];

echo TwigTemplate::renderTemplate('dashboard', $aContext);

<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * getSettingsTabs
 *
 */
function getSettingsTabs()
{
    $aTabs = array(
        array('general', t('Algemeen'), true),
        array('costs', t('Kosten')),
    );

    return Loader::helper('concrete/interface')->tabs($aTabs);
}

/**
 * getSettingsForm
 *
 */
function getSettingsForm()
{
    $aSettingsForm = [];

    $oForm = Loader::helper('form');
    $aSettingsForm['action'] = $this->action('save', $aBlockSettings['bID']);

    $aSettingsForm['nameLabel'] = $oForm->label('cottageName', t('Naam'));
    $aSettingsForm['nameText'] = $oForm->text('cottageName', $aBlockSettings['cottageName']);

    $aSettingsForm['adminLabel'] = $oForm->label('adminGroup', t('Beheerdersgroep'));
    $aSettingsForm['adminSelect'] = $oForm->select('adminGroup', $aGroups, $aBlockSettings['adminGroup']);

    $aSettingsForm['userLabel'] = $oForm->label('userGroup', t('Gebruikersgroep'));
    $aSettingsForm['userSelect'] = $oForm->select('userGroup', $aGroups, $aBlockSettings['userGroup']);

    $aSettingsForm['canCancelLabel'] = $oForm->label('canBookCancelledBooking', t('Annuleringen'));
    $aSettingsForm['canCancelRadio'] = $oForm->radio('canBookCancelledBookings', 1, $aBlockSettings['canBookCancelledBookings']);
    $aSettingsForm['cannotCancelRadio'] = $oForm->radio('canBookCancelledBookings', 0, $aBlockSettings['canBookCancelledBookings']);

    $aSettingsForm['changeDayLabel'] = $oForm->label('changeDay', t('Wisseldag'));
    $aSettingsForm['changeDaySelect'] = $oForm->select('changeDay', $aDays, $aBlockSettings['changeDay']);

    $aSettingsForm['creditsPerWeekDayLabel'] = $oForm->label('creditsPerWeekDay', t('Kosten per weekdag'));
    $aSettingsForm['creditsPerWeekDayText'] = $oForm->text('creditsPerWeekDay', intval($aBlockSettings['creditsPerWeekDay']));

    $aSettingsForm['creditsPerWeekendDayLabel'] = $oForm->label('creditsPerWeekendDay', t('Kosten per weekenddag'));
    $aSettingsForm['creditsPerWeekendDayText'] = $oForm->text('creditsPerWeekendDay', intval($aBlockSettings['creditsPerWeekendDay']));

    $aSettingsForm['userCreditsAnnualLabel'] = $oForm->label('userCreditsAnnual', t('Nieuwe schelpen per jaar'));
    $aSettingsForm['userCreditsAnnualText'] = $oForm->text('userCreditsAnnual', intval($aBlockSettings['userCreditsAnnual']));

    $aSettingsForm['userCreditsMaxLabel'] = $oForm->label('userCreditsMax', t('Maximum aantal schelpen'));
    $aSettingsForm['userCreditsMaxText'] = $oForm->text('userCreditsMax', intval($aBlockSettings['userCreditsMax']));

    return $aSettingsForm;
}

/**
 * getBookingForm
 *
 */
function getBookingForm()
{
    $aForm = [];

    $oForm = Loader::helper('form');

    $aForm['action'] = $this->action('saveBooking', $bID);
    $aForm['reserveringAction'] = $this->action('reserveringen', $bID);

    $aSaveForm['uIDLabel'] = $oForm->label('uID', t('Gebruiker'));
    $aSaveForm['uIDSelect'] = $oForm->select('uID', $aUsers);
    $aSaveForm['startLabel'] = $oForm->label('start', t('Begindatum'));
    $aSaveForm['startText'] = $oForm->text('start');
    $aSaveForm['endLabel'] = $oForm->label('end', t('Einddatum'));
    $aSaveForm['endText'] = $oForm->text('end');
    $aSaveForm['creditsLabel'] = $oForm->label('credits', t('Kosten'));
    $aSaveForm['creditsText'] = $oForm->text('credits');
    $aSaveForm['personsLabel'] = $oForm->label('persons', t('Aantal personen'));
    $aSaveForm['personsText'] = $oForm->text('persons');
    $aSaveForm['notesLabel'] = $oForm->label('notes', t('Opmerkingen'));
    $aSaveForm['notesText'] = $oForm->textarea('notes');

    return $aForm;
}

/**
 * getExceptionForm
 *
 */
function getExceptionForm()
{
    $aForm = [];

    $oForm = Loader::helper('form');

    $aSaveForm['bIDHidden'] = $oForm->hidden('bID', $aBooking['bID']);
    $aSaveForm['startLabel'] = $oForm->label('start', t('Begindatum'));
    $aSaveForm['startText'] = $oForm->text('start', $aBooking['start'], array('class' => 'form__exception__start'));
    $aSaveForm['endLabel'] = $oForm->label('end', t('Einddatum'));
    $aSaveForm['endText'] = $oForm->text('end', $aBooking['end'], array('class' => 'form__exception__end'));
    $aSaveForm['creditsLabel'] = $oForm->label('credits', t('Kosten per dag'));
    $aSaveForm['creditsText'] = $oForm->text('credits', $aBooking['credits'], array('class' => 'form__exception__credits'));
    $aSaveForm['bookOnlyWeeksLabel'] = $oForm->label('bookOnlyWeeks', t('Alleen gehele week boeken'));
    $aSaveForm['bookOnlyWeeksCheckbox'] = $oForm->checkbox('bookOnlyWeeks', 1, $aBooking['bookOnlyWeeks']);
    $aSaveForm['maxNumberOfDaysLabel'] = $oForm->label('maxNumberOfDays', t('Maximaal aantal dagen'));
    $aSaveForm['maxNumberOfDaysText'] = $oForm->text('maxNumberOfDays', $aBooking['maxNumberOfDays']);
    $aSaveForm['notesLabel'] = $oForm->label('notes', t('Opmerkingen'));
    $aSaveForm['notesTextarea'] = $oForm->textarea('notes', $aBooking['notes']);

    return $aForm;
}
$aContext = [
    'header' => Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Cottage Booker'), t('Dit is de beheerpagina voor de Cottage Booker.')),
    'task' => $this->controller->getTask(),
    'error' => $error,
    'message' => $message,
    'settingsTabs' => getSettingsTabs(),
    'settingsForm' => getSettingsForm(),
    'addBookingForm' => getBookingForm(),
    'footer' => Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(),
    'reserveringen' => $this->action('reserveringen', $bID),
    'updateBookingAction' => $this->action('updateBooking', $aBooking['entryID']),
    'saveExceptionAction' => $this->action('saveException', $aBooking['entryID']),
    'exceptionForm' => getExceptionForm(),
    'usersURL' => $this->url('/dashboard/users')
];

echo TwigTemplate::renderTemplate('dashboard', $aContext);

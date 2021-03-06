<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Cottage Booker'), t('Dit is de beheerpagina voor de Cottage Booker.'));

if(isset($message)){
?>
        <div class="ccm-ui" id="ccm-dashboard-result-message">
                <div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">×</button><?=$message; ?></div>
        </div>
<?php
}elseif(isset($error)){
?>
        <div class="ccm-ui" id="ccm-dashboard-result-message">
                <div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button><?=$error; ?></div>
        </div>
<?php
}

switch ($this->controller->getTask()):
    case 'settings':
    case 'save':
        $aTabs = array(
            array('general', t('Algemeen'), true),
            array('costs', t('Kosten'))
        );
        echo Loader::helper('concrete/interface')->tabs($aTabs);
        ?>
        <div id="ccm-tab-content-general" class="ccm-tab-content">
            <?php
            $oForm = Loader::helper('form');
            ?>
            <form action="<?php echo $this->action('save', $aBlockSettings['bID']); ?>" method="POST">
                <fieldset>
                    <legend style="margin-bottom: 0;"><?php echo t('Systeem'); ?></legend>
                    <div class="control-group">
                        <?php echo $oForm->label('cottageName', t('Naam')); ?>
                        <?php echo $oForm->text('cottageName', $aBlockSettings['cottageName']); ?>
                    </div>
                    <div class="control-group">
                        <?php echo $oForm->label('adminGroup', t('Beheerdersgroep')); ?>
                        <?php echo $oForm->select('adminGroup', $aGroups, $aBlockSettings['adminGroup']); ?>
                        <span class="help-inline"><?php echo t('De groep van gebruikers die reserveringen mogen aanpassen.'); ?></span>
                    </div>
                    <div class="control-group">
                        <?php echo $oForm->label('userGroup', t('Gebruikersgroep')); ?>
                        <?php echo $oForm->select('userGroup', $aGroups, $aBlockSettings['userGroup']); ?>
                        <span class="help-inline"><?php echo t('De groep van gebruikers die reserveringen mogen maken.'); ?></span>
                    </div>
                    <legend style="margin-bottom: 0;"><?php echo t('Reserveringen'); ?></legend>
                    <div class="control-group">
                        <?php echo $oForm->label('canBookCancelledBooking', t('Annuleringen')); ?>
                        <label class="radio">
                            <?php
                                echo $oForm->radio('canBookCancelledBookings', 1, $aBlockSettings['canBookCancelledBookings']);
                                echo t('Geannuleerd verblijf kan opnieuw geboekt worden.');
                            ?>
                        </label>
                        <label class="radio">
                            <?php
                                echo $oForm->radio('canBookCancelledBookings', 0, $aBlockSettings['canBookCancelledBookings']);
                                echo t('Geannuleerd verblijf kan niet opnieuw geboekt worden.');
                            ?>
                        </label>
                    </div>
                                        <div class="control-group">
                        <?php echo $oForm->label('changeDay', t('Wisseldag')); ?>
                        <?php echo $oForm->select('changeDay', $aDays, $aBlockSettings['changeDay']); ?>
                        <span class="help-inline"><?php echo t('Op de wisseldag mogen twee reserveringen overlappen.'); ?></span>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> <?php echo t('Opslaan'); ?></button>
                    </div>
                </fieldset>
            </form>
        </div>
        <div id="ccm-tab-content-costs" class="ccm-tab-content">
            <?php
            $oForm = Loader::helper('form');
            ?>
            <form action="<?php echo $this->action('save', $aBlockSettings['bID']); ?>" method="POST">
                <fieldset>
                    <div class="control-group">
                        <?php echo $oForm->label('creditsPerWeekDay', t('Kosten per weekdag')); ?>
                        <?php echo $oForm->text('creditsPerWeekDay', intval($aBlockSettings['creditsPerWeekDay'])); ?>
                    </div>
                    <div class="control-group">
                        <?php echo $oForm->label('creditsPerWeekendDay', t('Kosten per weekenddag')); ?>
                        <?php echo $oForm->text('creditsPerWeekendDay', intval($aBlockSettings['creditsPerWeekendDay'])); ?>
                    </div>
                                        <div class="control-group">
                        <?php echo $oForm->label('userCreditsAnnual', t('Nieuwe schelpen per jaar')); ?>
                        <?php echo $oForm->text('userCreditsAnnual', intval($aBlockSettings['userCreditsAnnual'])); ?>
                    </div>
                                        <div class="control-group">
                        <?php echo $oForm->label('userCreditsMax', t('Maximum aantal schelpen')); ?>
                        <?php echo $oForm->text('userCreditsMax', intval($aBlockSettings['userCreditsMax'])); ?>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> <?php echo t('Opslaan'); ?></button>
                    </div>
                </fieldset>
            </form>
        </div>
        <?php
        break;
    case 'reserveringen':
        ?>
        <div role="tabpanel">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#reserveringen" aria-controls="reserveringen" role="tab" data-toggle="tab"><?php echo t('Reserveringen'); ?></a></li>
                <li role="presentation"><a href="#annuleringen" aria-controls="annuleringen" role="tab" data-toggle="tab"><?php echo t('Annuleringen'); ?></a></li>
                <li role="presentation"><a href="#uitzonderingen" aria-controls="uitzonderingen" role="tab" data-toggle="tab"><?php echo t('Uitzonderingen'); ?></a></li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active in fade" id="reserveringen">
                                <p>
                                        <a class="btn" href="<?php echo $this->action('addBooking', $bID); ?>"><i class="icon-plus"></i> Reservering toevoegen</a>
                                </p>
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><?php echo t('Door'); ?></th>
                                <th><?php echo t('Begin'); ?></th>
                                <th><?php echo t('Eind'); ?></th>
                                <th><?php echo t('Kosten'); ?></th>
                                <th><?php echo t('Opmerkingen'); ?></th>
                                <th><?php echo t('Personen'); ?></th>
                                                        <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($aBookings as $aBooking){
                                ?>
                            <tr>
                                <td><?php echo $aBooking['user']; ?></td>
                                <td><?php echo $aBooking['start']; ?></td>
                                <td><?php echo $aBooking['end']; ?></td>
                                <td><?php echo $aBooking['credits']; ?></td>
                                <td style="width: 50%"><?php echo $aBooking['notes']; ?></td>
                                <td><?php echo $aBooking['persons']; ?></td>
                                                        <td>
                                                                <a href="<?php echo $this->action('editBooking', $aBooking['entryID']); ?>"><i class="icon-pencil"></i></a>
                                                                <a class="cottage_booker__dashboard__button--delete-booking" href="<?php echo $this->action('cancelBooking', $aBooking['entryID']); ?>"><i class="icon-trash"></i></a>
                                                        </td>
                            </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="annuleringen">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><?php echo t('Door'); ?></th>
                                <th><?php echo t('Begin'); ?></th>
                                <th><?php echo t('Eind'); ?></th>
                                <th><?php echo t('Kosten'); ?></th>
                                <th><?php echo t('Opmerkingen'); ?></th>
                                <th><?php echo t('Personen'); ?></th>
                                                        <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($aCancelled as $aBooking){
                                ?>
                            <tr>
                                <td><?php echo $aBooking['user']; ?></td>
                                <td><?php echo $aBooking['start']; ?></td>
                                <td><?php echo $aBooking['end']; ?></td>
                                <td><?php echo $aBooking['credits']; ?></td>
                                <td style="width: 50%"><?php echo $aBooking['notes']; ?></td>
                                <td><?php echo $aBooking['persons']; ?></td>
                                                        <td>
                                                                <a class="cottage_booker__dashboard__button--delete-cancellation" href="<?php echo $this->action('removeCancellation', $aBooking['entryID']); ?>"><i class="icon-trash"></i></a>
                                                        </td>
                            </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="uitzonderingen">
                                <p>
                                        <a class="btn" href="<?php echo $this->action('addException', $bID); ?>"><i class="icon-plus"></i> Uitzondering toevoegen</a>
                                </p>
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><?php echo t('Door'); ?></th>
                                <th><?php echo t('Begin'); ?></th>
                                <th><?php echo t('Eind'); ?></th>
                                <th><?php echo t('Kosten per dag'); ?></th>
                                <th><?php echo t('Alleen week boeken'); ?></th>
                                                        <th><?php echo t('Maximum aantal dagen'); ?></th>
                                <th><?php echo t('Opmerkingen'); ?></th>
                                                        <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($aExceptions as $aBooking){
                                ?>
                            <tr>
                                <td><?php echo $aBooking['user']; ?></td>
                                <td><?php echo $aBooking['start']; ?></td>
                                <td><?php echo $aBooking['end']; ?></td>
                                <td><?php echo $aBooking['credits']; ?></td>
                                <td><?php echo (($aBooking['bookOnlyWeeks'] == 1) ? 'Ja':'Nee'); ?></td>
                                                        <td><?php echo $aBooking['maxNumberOfDays']; ?></td>
                                <td style="width: 30%"><?php echo $aBooking['notes']; ?></td>
                                                        <td>
                                                                <a href="<?php echo $this->action('editException', $aBooking['entryID']); ?>"><i class="icon-pencil"></i></a>
                                                                <a class="cottage_booker__dashboard__button--delete-exception" href="<?php echo $this->action('deleteException', $aBooking['entryID']); ?>"><i class="icon-trash"></i></a>
                                                        </td>
                            </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

                <div class="cottage_booker__dashboard__confirm__delete--booking hide">
            <p><?php echo t('Weet je zeker dat je deze reservering wilt annuleren?'); ?></p>
        </div>
                <div class="cottage_booker__dashboard__confirm__delete--cancellation hide">
            <p><?php echo t('Weet je zeker dat je deze annulering wilt verwijderen?'); ?></p>
                        <p><?php echo t('Deze gebruiker kan dan weer reserveren in deze periode.'); ?></p>
        </div>
                <div class="cottage_booker__dashboard__confirm__delete--exception hide">
            <p><?php echo t('Weet je zeker dat je deze uitzondering wilt verwijderen?'); ?></p>
        </div>
        <?php
        break;
        case 'addBooking':
                $oForm = Loader::helper('form');
                ?>
                <form action="<?php echo $this->action('saveBooking', $bID); ?>" method="POST">
                        <fieldset>
                                <legend style="margin-bottom: 0;"><?php echo t('Reservering'); ?></legend>
                                <div class="control-group">
                                        <?php echo $oForm->label('uID', t('Gebruiker')); ?>
                                        <?php echo $oForm->select('uID', $aUsers); ?>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('start', t('Begindatum')); ?>
                                        <?php echo $oForm->text('start'); ?>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('end', t('Einddatum')); ?>
                                        <?php echo $oForm->text('end'); ?>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('credits', t('Kosten')); ?>
                                        <?php echo $oForm->text('credits'); ?>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('persons', t('Aantal personen')); ?>
                                        <?php echo $oForm->text('persons'); ?>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('notes', t('Opmerkingen')); ?>
                                        <?php echo $oForm->textarea('notes'); ?>
                                </div>
                                <div class="form-actions">
                                        <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> <?php echo t('Opslaan'); ?></button>
                                </div>
                        </fieldset>
                </form>
                <a class="btn" href="<?php echo $this->action('reserveringen', $bID); ?>"><i class="icon-chevron-left"></i> Terug</a>
        <?php
        break;
        case 'editBooking':
        case 'updateBooking':
                $oForm = Loader::helper('form');
                ?>
                <form action="<?php echo $this->action('updateBooking', $aBooking['entryID']); ?>" method="POST">
                        <fieldset>
                                <legend style="margin-bottom: 0;"><?php echo t('Reservering'); ?></legend>
                                <div class="control-group">
                                        <?php echo $oForm->label('uID', t('Gebruiker')); ?>
                                        <?php echo $oForm->select('uID', $aUsers, $aBooking['uID']); ?>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('start', t('Begindatum')); ?>
                                        <?php echo $oForm->text('start', $aBooking['start']); ?>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('end', t('Einddatum')); ?>
                                        <?php echo $oForm->text('end', $aBooking['end']); ?>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('credits', t('Kosten')); ?>
                                        <?php echo $oForm->text('credits', $aBooking['credits']); ?>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('persons', t('Aantal personen')); ?>
                                        <?php echo $oForm->text('persons', $aBooking['persons']); ?>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('notes', t('Opmerkingen')); ?>
                                        <?php echo $oForm->textarea('notes', $aBooking['notes']); ?>
                                </div>
                                <div class="form-actions">
                                        <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> <?php echo t('Opslaan'); ?></button>
                                </div>
                        </fieldset>
                </form>
                <a class="btn" href="<?php echo $this->action('reserveringen', $aBooking['bID']); ?>"><i class="icon-chevron-left"></i> Terug</a>
        <?php
        break;
        case 'cancelBooking':
        ?>
                <div class="alert alert-success">
                        <?php echo t('Deze reservering is succesvol geannuleerd.'); ?>
                </div>
                <a class="btn" href="<?php echo $this->action('reserveringen', $bID); ?>"><i class="icon-chevron-left"></i> Terug</a>
        <?php
        break;
        case 'removeCancellation':
        ?>
                <div class="alert alert-success">
                        <?php echo t('Deze annulering is succesvol verwijderd.'); ?>
                </div>
                <a class="btn" href="<?php echo $this->action('reserveringen', $bID); ?>"><i class="icon-chevron-left"></i> Terug</a>
        <?php
        break;
        case 'addException':
        case 'editException':
        case 'saveException':
                $oForm = Loader::helper('form');
                ?>
                <form action="<?php echo $this->action('saveException', $aBooking['entryID']); ?>" method="POST">
                        <?php echo $oForm->hidden('bID', $aBooking['bID']); ?>
                        <fieldset>
                                <legend style="margin-bottom: 0;"><?php echo t('Uitzondering'); ?></legend>
                                <div class="control-group">
                                        <?php echo $oForm->label('start', t('Begindatum')); ?>
                                        <?php echo $oForm->text('start', $aBooking['start'], array('class'=>'form__exception__start')); ?>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('end', t('Einddatum')); ?>
                                        <?php echo $oForm->text('end', $aBooking['end'], array('class'=>'form__exception__end')); ?>
                                        <span class="help-inline help-inline--days"></span>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('credits', t('Kosten per dag')); ?>
                                        <?php echo $oForm->text('credits', $aBooking['credits'], array('class'=>'form__exception__credits')); ?>
                                        <span class="help-inline help-inline--credits"></span>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('bookOnlyWeeks', t('Alleen gehele week boeken')); ?>
                                        <?php echo $oForm->checkbox('bookOnlyWeeks', 1, $aBooking['bookOnlyWeeks']); ?>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('maxNumberOfDays', t('Maximaal aantal dagen')); ?>
                                        <?php echo $oForm->text('maxNumberOfDays', $aBooking['maxNumberOfDays']); ?>
                                        <span class="help-inline"><?php echo t('Het maximum aantal dagen dat je aaneengesloten mag boeken in deze periode. Vul 0 in voor onbeperkt.'); ?></span>
                                </div>
                                <div class="control-group">
                                        <?php echo $oForm->label('notes', t('Opmerkingen')); ?>
                                        <?php echo $oForm->textarea('notes', $aBooking['notes']); ?>
                                </div>
                                <div class="form-actions">
                                        <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> <?php echo t('Opslaan'); ?></button>
                                </div>
                        </fieldset>
                </form>
                <a class="btn" href="<?php echo $this->action('reserveringen', $aBooking['bID']); ?>"><i class="icon-chevron-left"></i> Terug</a>
        <?php
        break;
        case 'deleteException':
        ?>
                <div class="alert alert-success">
                        <?php echo t('Deze uitzondering is succesvol verwijderd.'); ?>
                </div>
                <a class="btn" href="<?php echo $this->action('reserveringen', $bID); ?>"><i class="icon-chevron-left"></i> Terug</a>
        <?php
        break;
    default:
        if (!empty($aBlocks)) :
            ?>
            <table class="cottage_booker__dashboard__overview table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th><?php echo t('Naam'); ?></th>
                        <th><?php echo t('Reserveringen'); ?></th>
                        <th><?php echo t('Nieuwste reservering'); ?></th>
                        <th colspan="4">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($aBlocks as $aBlock) {
                        ?>
                        <tr>
                            <td><?php echo $aBlock['cottageName']; ?></td>
                            <td><?php echo $aBlock['totalBookings']; ?></td>
                            <td><?php echo $aBlock['last_modified']; ?></td>
                            <td><a rel="external" target="blank" href="<?php echo $aBlock['page']; ?>">Open pagina</a></td>
                            <td><a href="<?php echo $this->action('reserveringen', $aBlock['bID']); ?>">Reserveringen</a></td>
                            <td><a href="<?php echo $this->url('/dashboard/users'); ?>">Gebruikers</a></td>
                            <td><a href="<?php echo $this->action('settings', $aBlock['bID']); ?>">Instellingen</a></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                <?php echo t("Er zijn nog geen reserveringspagina's."); ?>
                <?php echo t("Maak een nieuwe pagina aan en voeg het blok 'Cottage Booker' toe."); ?>
            </div>
        <?php
        endif;
        break;
endswitch;

echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();

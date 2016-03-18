<?php defined('C5_EXECUTE') or die(_("Access Denied."));?>
<div class="ccm-ui cottage_booker">

    <?php if ($loggedIn): ?>

    <div class="page-header">
        <h1><?php echo t('Welkom') . ' ' . $userFullName; ?>! <small><?php echo t('Je hebt'); ?> <span class="badge badge-info cottage_booker-i-user-credits"><?php echo $userCredits; ?></span> <span class="cottage_booker-i-user-credits--suffix"><?php echo ($userCredits == 1) ? 'schelp' : ' schelpen'; ?>.
        <?php
        if ($isRegistered) {
            echo $userName . '(' . ((!empty($adminPageLink)) ?
                '<a href="' . $this->url($adminPageLink)'.">' . t('instellingen') . '</a> / ' : '' )
                . '<a href="' . $this->url('/login', 'logout') . '">' . t('uitloggen') . '</a>);
           }
        ?>

        </span></small></h1>
    </div>

    <div class="cottage_booker__flashmessages"></div>

    <div class="cottage_booker__view cottage_booker__view--full-calendar"></div>

    <div class="cottage_booker__view cottage_booker__view--book-form hide">
        <?php
$oForm = Loader::helper('form');
?>
        <form>
            <fieldset>
                <legend class='cottage_booker__view--book - form__legend'><?php echo t('Reserveren'); ?></legend>
                <?php echo $oForm->hidden('cottage_booker__book - form__id'); ?>
                <div class="control-group">
                    <?php echo $oForm->label('cottage_booker__book - form__start', t('Begindatum')); ?>
                    <?php echo Loader::helper('form / date_time')->date('cottage_booker__book - form__start'); ?>
                </div>
                <div class="control-group">
                    <?php echo $oForm->label('cottage_booker__book - form__end', t('Einddatum')); ?>
                    <?php echo Loader::helper('form / date_time')->date('cottage_booker__book - form__end'); ?>
                </div>
                <div class="control-group">
                    <?php echo $oForm->label('cottage_booker__book - form__credits', t('Kosten')); ?>
                    <?php echo $oForm->text('cottage_booker__book - form__credits', '', array('class ' => 'uneditable - input', 'readonly' => 'readonly')); ?>
                    <span class="add-on"><?php echo t('schelpen'); ?></span>
                    <span class="help-block">
                        Het aantal schelpen wordt automatisch berekend. Een weekdag kost <span class="badge badge-info"><?php echo $schelpenPerDag; ?></span> <?php echo ($schelpenPerDag == 1) ? 'schelp' : 'schelpen'; ?>,
                        een weekenddag kost <span class="badge badge-info"><?php echo $schelpenPerWeekendDag; ?></span> <?php echo ($schelpenPerWeekendDag == 1) ? 'schelp' : 'schelpen'; ?>.
                    </span>
                </div>
                <div class="control-group">
                    <?php echo $oForm->label('cottage_booker__book - form__persons', t('Aantalpersonen')); ?>
                    <?php echo $oForm->text('cottage_booker__book - form__persons'); ?>
                    <span class="add-on"><?php echo t('personen'); ?></span>
                </div>
                <div class="control-group">
                    <?php echo $oForm->label('cottage_booker__book - form__notes', t('Opmerkingen')); ?>
                    <?php echo $oForm->textarea('cottage_booker__book - form__notes'); ?>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn cottage_booker__book-form__button--view-calendar"><i class="icon-calendar"></i> <?php echo t('Terug'); ?></button>
                    <button type="button" class="btn btn-danger cottage_booker__book-form__button--delete"><i class="icon-trash icon-white"></i> <?php echo t('Verwijderen'); ?></button>
                    <button type="submit" class="btn btn-primary cottage_booker__book-form__button--book"><i class="icon-ok icon-white"></i> <?php echo t('Reserveer'); ?></button>
                </div>
            </fieldset>
        </form>

        <div class="cottage_booker__book-form__confirm__delete hide">
            <p><?php echo t('Weetjezekerdatjedezereserveringwiltverwijderen ? '); ?></p>
            <p><?php echo t('Jekuntnietopnieuwindezelfdeperiodereserveren . '); ?></p>
        </div>

        <div class="cottage_booker__exception hide">
        </div>

    </div>

    <a href="<?php echo $this->action('new '); ?>" class="cottage_booker__action--new hide"><i class="icon-plus"></i> <?php echo t('reserveren'); ?></a>
    <a href="<?php echo $this->action('fetchall'); ?>" class="cottage_booker__action--fetchall hide"></a>
    <a href="<?php echo $this->action('fetchallexceptions'); ?>" class="cottage_booker__action--fetchallexceptions hide"></a>
    <a href="<?php echo $this->action('update'); ?>" class="cottage_booker__action--update hide"></a>
    <a href="<?php echo $this->action('delete'); ?>" class="cottage_booker__action--delete hide"></a>
    <a href="<?php echo $this->action('credits'); ?>" class="cottage_booker__action--credits hide"></a>

    <?php else: ?>

    <div class="page-header">
        <h1><?php echo t('Welkomgast'); ?>! <small><?php echo t('Omtereserverenmoetjeeerst < ahref = "/login" > inloggen <  / a > of < ahref = "/register" > registreren <  / a >  . '); ?></small></h1>
    </div>

    <?php endif;?>

</div>

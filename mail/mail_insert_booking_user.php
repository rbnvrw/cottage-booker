<?php

$subject = 'Bevestiging reservering via Familie Kramer.nl';

$body = t('
Beste %s,

Bedankt voor uw reservering via Familie Kramer.nl!
Hieronder ziet u nog eens alle details van uw reservering:

Periode: %s tot %s

Aantal personen:
%s

Opmerkingen:
%s

Wij wensen u een prettig verblijf in Callantsoog!

%s',
    $sFullName,
    $sStart,
    $sEnd,
    $iPersons,
    $sNotes,
    BASE_URL);

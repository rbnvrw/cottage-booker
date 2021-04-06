<?php

$subject = 'Bijgewerkte reservering via Familie Kramer.nl';

$body = t('
Beste %s,

Uw reservering via Familie Kramer.nl is bijgewerkt.
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

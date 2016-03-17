<?php

$subject = 'Reservering geannuleerd in het schelpensysteem';

$body = t('
Beste schelpenbeheerder,

Er is een reservering geannuleerd door %s (%s), de geannuleerde reservering was van %s tot %s.

Aantal personen:
%s

Opmerkingen:
%s

Zie %s/dashboard/cottage_booker/
',
$sFullName,
$sEmail,
$sStart,
$sEnd,
$iPersons,
$sNotes,
BASE_URL,
$iBookingId);

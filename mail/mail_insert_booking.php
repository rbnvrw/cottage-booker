<?php

$subject = 'Nieuwe reservering in het schelpensysteem';

$body = t('
Beste schelpenbeheerder,

Er is een nieuwe reservering gemaakt door %s (%s), van %s tot %s.

Aantal personen:
%s

Opmerkingen:
%s

Zie %s/dashboard/cottage_booker/editBooking/%d
',
    $sFullName,
    $sEmail,
    $sStart,
    $sEnd,
    $iPersons,
    $sNotes,
    BASE_URL,
    $iBookingId);

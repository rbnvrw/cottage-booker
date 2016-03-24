<?php

$subject = 'Reservering bijgewerkt in het schelpensysteem';

$body = t('
Beste schelpenbeheerder,

Er is een reservering bijgewerkt door %s (%s), de reservering is nu van %s tot %s.

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

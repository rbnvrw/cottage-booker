<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DateTime;

class Booking extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bookings';

    public static function saveUserInput(Request $request)
    {
        $booking = new self;
        $booking->start = self::formatDate($request->input('start'));
        $booking->end = self::formatDate($request->input('end'));
        $booking->costs = 6;
        $booking->persons = $request->input('persons');
        $booking->description = $request->input('description');
        $booking->save();
    }

    protected static function formatDate($date)
    {
        $dateLocale = DateTime::createFromFormat('d-m-Y', $date);
        return $dateLocale->format('Y-m-d H:i:s');
    }
}

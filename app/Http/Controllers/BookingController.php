<?php

namespace App\Http\Controllers;

use App\Booking;
use Illuminate\Http\Request;
use Session;
use Redirect;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::all();

        return view('booking.index', [
            'bookings' => $bookings,
        ]);
    }

    public function create()
    {
        return view('booking.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'start' => 'required|date|date_format:d-m-Y',
            'end' => 'required|date|date_format:d-m-Y',
            'persons' => 'required|numeric',
        ];

        $this->validate($request, $rules);

        Booking::saveUserInput($request);

        Session::flash('message', 'Uw boeking is opgeslagen!');
        return Redirect::to('booking');
    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }

    public function update($id)
    {

    }

    public function destroy($id)
    {

    }
}

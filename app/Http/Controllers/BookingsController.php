<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBookingsRequest;
use App\Models\Bookings;
use App\Models\Invoice;
use App\Models\Surcharge;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe\Refund;
use Stripe\Stripe;

class BookingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Bookings::query();
        $bookings = $query->with(['user', 'vehicle'])->get();

        foreach ($bookings as $key => $booking) {
            $pickupTime = Carbon::parse($booking['pickup_time']);
            $dropoffTime = Carbon::parse($booking['dropoff_time']);
            // Calculate the difference in days
            $daysCount = $dropoffTime->diffInDays($pickupTime);
            $booking['bookingDaysCount'] = $daysCount;
        }

        return view('pages.admin.bookings.index', ['bookings' => $bookings]);
    }

    public function searchs(Request $request)
    {
        $query = Bookings::query();

        if($request->search){
            $term = $request->search;
            $query->where('pickup', 'like', '%' . $term . '%')
                ->orWhere('dropoff', 'like', '%' . $term . '%')
                ->orWhere('pickup_time', 'like', '%' . $term . '%')
                ->orWhere('status', 'like', '%' . $term . '%');
        }
        return view('pages.admin.bookings.index', ['bookings' => $query->get()]);
    }

    public function singlecar($id)
    {
        $query = Bookings::query();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function book(Request $request)
    {
        $request->validate([
            'pickup' => 'required',
            'pickup_time' => 'required|before:dropoff_time',
            'dropoff_time' => 'required|after:pickup_time',
            'dropoff' => 'required'
        ]);

        $Booking = Bookings::create([
            'pickup' => $request->pickup,
            'pickup_time' => $request->pickup_time,
            'dropoff_time' => $request->dropoff_time,
            'dropoff' => $request->dropoff,
            'vehicle_id' => $request->vehicle_id,
            'status' => "Unpaid",
            'user_id' => auth()->id(),

        ]);



        $pickupTime = Carbon::parse($Booking['pickup_time']);
        $dropoffTime = Carbon::parse($Booking['dropoff_time']);
        // Calculate the difference in days
        $daysCount = $dropoffTime->diffInDays($pickupTime);
        $Booking['bookingDaysCount'] = $daysCount;

        session(['BookingData' => $Booking]);

        return redirect()->route('payment')->with('success', 'Vehicle booked successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $Booking = Bookings::create([
            'pickup' => $request->pickup,
            'pickup_time' => $request->pickup_time,
            'dropoff_time' => $request->dropoff_time,
            'dropoff' => $request->dropoff,
            'vehicle_id' => $request->vehicle_id,
            'user_id' => auth()->id(),

        ]);

        // You can add additional logic or redirect here

        return redirect()->route('home')->with('success', 'Vehicle created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bookings $booking)
    {
        return view('pages.admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit_booking(Bookings $booking)
    {
        return view('pages.admin.bookings.edit', compact('booking'));
    }

    public function edit(Bookings $booking)
    {
        return view('pages.admin.bookings.edit', compact('booking'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingsRequest $request, Bookings $booking)
    {
        $request->validate([
          "pickup" => "required",
          "pickup_time" => "required",
          "dropoff" => "required",
          "dropoff_time" => "required",
          "status" => "required",
          "returned_on" => "nullable",
        ]);

        $booking->update([
          "pickup" => $request->pickup,
          "pickup_time" => $request->pickup_time,
          "dropoff" => $request->dropoff,
          "dropoff_time" => $request->dropoff_time,
          "status" => $request->status,
          "returned_on" => $request->returned_on
        ]);

        return back()->with('message', 'Booking updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($booking_id)
    {
        $booking = Bookings::find($booking_id);

        if (!$booking) {
            dd($booking);
            return redirect()->route('vehicles.all')
                ->with('error', 'vehicle not found.');
        }

        $booking->delete();

        return redirect()->route('vehicles.all')
            ->with('success', 'vehicle deleted successfully.');
    }

    public function return(Bookings $booking, Request $request)
    {
        $pickup_time = Carbon::parse($booking->pickup_time)->startOfDay();
        $dropoff_time = Carbon::parse($booking->dropoff_time)->startOfDay();
        $returned_on = Carbon::parse($request->returned_on)->startOfDay();
        $actual_duration = $pickup_time->diffInDays($returned_on);
        $actual_amount = $actual_duration * $booking->vehicle->price;
        $paid_amount = $booking->transactions->sum('amount');
        $due_amount = $actual_amount - $paid_amount;
        $case = $returned_on->isSameDay($dropoff_time) ? 'ontime' : ($returned_on->isBefore($dropoff_time) ? 'early' : 'late');

        return view('pages.admin.bookings.return', compact(
            'booking',
            'pickup_time',
            'dropoff_time',
            'returned_on',
            'actual_duration',
            'actual_amount',
            'paid_amount',
            'due_amount',
            'case',
        ));
    }

    public function return_confirm(Bookings $booking, Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        if ($request->action == 'refund') {
            $refund = Refund::create([
                'payment_intent' => $booking->transactions[0]->stripe_payment_id,
                'amount' => $request->amount * 100,
            ]);
        } else if ($request->action == 'charge') {
            $invoice = Invoice::create([
                'booking_id' => $booking->id,
                'amount' => $request->amount,
                'reason' => 'rental_payment'
            ]);
            // Notify user
        }

        $booking->update([
            'status' => 'returned',
            'returned_on' => $request->returned_on
        ]);

        return redirect()->route('bookings.all');
    }

    public function add_surcharge(Bookings $booking, Request $request)
    {
        $request->validate([
            'type' => 'required|in:toll,fine',
            'date' => 'required',
            'amount' => 'required|min:0',
            'note' => 'nullable',
        ]);

        $surcharge = Surcharge::create([
            'booking_id' => $booking->id,
            'type' => $request->type,
            'date' => $request->date,
            'amount' => $request->amount,
            'note' => $request->note,
            'paid' => false,
        ]);

        return redirect()->route('bookings.show', $booking);
    }
}

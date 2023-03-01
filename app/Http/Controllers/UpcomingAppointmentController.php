<?php

namespace App\Http\Controllers;

use App\Models\UpcomingAppointment;
use App\Http\Requests\StoreUpcomingAppointmentRequest;

use App\Http\Resources\UpcomingCollection;
use App\Http\Resources\UpcomingResource;

use Carbon\Carbon;

class UpcomingAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $appointments = UpcomingAppointment::all();

        return new UpcomingCollection($appointments);
    }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUpcomingAppointmentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpcomingAppointmentRequest $request)
    {
        $this->authorize('upcomings.store');

        $appointment = UpcomingAppointment::create([
            'name' => $request->name,
            'date' => Carbon::parse($request->date),
        ]);

        return new UpcomingResource($appointment);
    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  \App\Models\UpcomingAppointment  $upcomingAppointment
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show(UpcomingAppointment $upcomingAppointment)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  \App\Models\UpcomingAppointment  $upcomingAppointment
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit(UpcomingAppointment $upcomingAppointment)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \App\Http\Requests\UpdateUpcomingAppointmentRequest  $request
    //  * @param  \App\Models\UpcomingAppointment  $upcomingAppointment
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, UpcomingAppointment $upcomingAppointment)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UpcomingAppointment  $upcomingAppointment
     * @return \Illuminate\Http\Response
     */
    public function destroy($upcomingAppointment)
    {
        $upcomingAppointment = UpcomingAppointment::find($upcomingAppointment)->delete();

        return response()->noContent();
    }
}

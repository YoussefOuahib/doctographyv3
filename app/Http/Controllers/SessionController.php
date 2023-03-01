<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Http\Requests\UpdateAppointmentRequest;
use Illuminate\Support\Facades\DB;



/*Collections & Resources */
use App\Http\Resources\AppointmentCollection;
use App\Http\Resources\AppointmentResource;

/*Helpers */
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;




class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sessions = Appointment::orderBy('act', 'ASC')
            ->orderBy('created_at', 'ASC')->paginate(20);

        return new AppointmentCollection($sessions);
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

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \App\Http\Requests\StoreSessionRequest  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(StoreSessionRequest $request)
    // {
    //     //
    // }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  \App\Models\Session  $session
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show(Appointment $session)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  \App\Models\Session  $session
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit(Appointment $session)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSessionRequest  $request
     * @param  \App\Models\Appointment  $session
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAppointmentRequest $request, Appointment $session)
    {

        $remaining = $session->rate == $request->rate ? 0 : $session->rate - $request->rate;
        //700 - 500
        // 4500
        $session->update([
            'act' => $request->act,
            'next_examination_date' => $request->next_examination_date == null ? null : Carbon::parse($request->next_examination_date),
            'note' => $request->note,
            'medical_treatment' => $request->medical_treatment,
            'rate' => $request->rate,
            'remaining_amount' =>  $session->remaining_amount + $remaining,
        ]);

        if ($remaining != 0) {
            $appointments = Appointment::where('patient_id', $session->patient_id)
                ->where('act', $session->act)
                ->where('total_amount', $session->total_amount)
                ->whereDate('created_at', '>', $session->created_at)
                ->get();

            foreach ($appointments as $appointment) {
                $appointment->update([
                    'remaining_amount' => $appointment->remaining_amount + $remaining,
                    'act' => $request->act,
                ]);
            }
        }

        return new AppointmentResource($session);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Session  $session
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $session)
    {
        $appointments = Appointment::where('patient_id', $session->patient_id)
            ->where('act', $session->act)
            ->where('remaining_amount', '>', 0)
            ->where('total_amount', $session->total_amount)
            ->whereDate('created_at', '>', $session->created_at)
            ->get();
        foreach ($appointments as  $appointment) {
            $appointment->remaining_amount =  $appointment->remaining_amount + $session->rate;
            $appointment->save();
        }

        $session->delete();
        return response()->noContent();
    }
    public function search($id)
    {
        $appointments = Appointment::where('patient_id', $id)->orderBy('patient_id', 'ASC')
            ->orderBy('act', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->paginate(20);
        return new AppointmentCollection($appointments);
    }
}

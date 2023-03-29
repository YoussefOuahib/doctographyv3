<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppointmentCollection;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\NextDateCollection;
use App\Models\Appointment;
use App\Models\Condition;
use App\Models\Gallery;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Requests\StoreAppointmentRequest;


class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $appointments = Appointment::all();

        return new AppointmentCollection($appointments);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAppointmentRequest $request)
    {
        $this->authorize('appointments.store');

        $condition = Condition::find($request->act);
        
        $app = Appointment::where('patient_id', $request->patient_id)->where('act', $condition->name)->orderBy('created_at', 'desc')->first();
        if ($app) {
            if($app->remaining_amount == 0 ) {
                $app = null;
            }
            elseif($app->remaining_amount - $request->rate < 0) {
                return 205;
            }
           
            
        }
        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'medical_treatment' => $request->medical_treatment,
            'rate' => $request->rate,
            'type' => "session",
            'next_examination_date' => $request->next_examination_date == "null" ? null : \DateTime::createFromFormat('D M d Y H:i:s e+', $request->next_examination_date)->format('Y-m-d'),
            'note' => $request->note,
            'act' => $condition->name,
            'total_amount' => $request->total_amount == null ? $app->total_amount : $request->total_amount,
            'remaining_amount' => $app ? $app->remaining_amount - $request->rate :  $request->total_amount - $request->rate,
        ]);
        
        if ($request->file('gallery')) {
            foreach ($request->gallery as $image) {

                $filename = $image->hashName();
                $image->move(public_path('images'), $filename);
                Gallery::create([
                    'image' => $filename,
                    'appointment_id' => $appointment->id,

                ]);
            }
        }

        return new AppointmentResource($appointment);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $appointment = Appointment::find($id);
        // $upcoming = UpcomingAppointment::where('appointment_id', $appointment->id)->get();
        return new AppointmentResource($appointment);
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        
        $remaining = $appointment->rate == $request->rate ? 0 : $appointment->rate - $request->rate;
        //700 - 500
        // 4500
        $appointment->update([
            'act' => $request->act,
            'next_examination_date' => $request->next_examination_date == null ? null : Carbon::parse($request->next_examination_date),
            'note' => $request->note,
            'medical_treatment' => $request->medical_treatment,
            'rate' => $request->rate,
            'remaining_amount' =>  $appointment->remaining_amount + $remaining,
        ]);
        
        if($remaining != 0 ) {
        $appointments = Appointment::where('patient_id', $appointment->patient_id)
        ->where('act', $appointment->act)
        ->where('total_amount', $appointment->total_amount)
        ->whereDate('created_at', '>', $appointment->created_at)
        ->get();
    
        foreach ($appointments as $appointment) {
            $appointment->update([
                'remaining_amount' => $appointment->remaining_amount + $remaining,
                'act' => $request->act,
            ]);
        }
    }
      
        return new AppointmentResource($appointment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        $appointments = Appointment::where('patient_id', $appointment->patient_id)
        ->where('act', $appointment->act)
        ->where('remaining_amount', '>', 0)
        ->where('total_amount', $appointment->total_amount)
        ->whereDate('created_at', '>', $appointment->created_at)
        ->get();
        foreach ($appointments as  $appointment) {
            $appointment->remaining_amount =  $appointment->remaining_amount + $appointment->rate;
            $appointment->save();
        }
     
        $appointment->delete();
        return response()->noContent();
    }

    public function payment(Request $request) {
        $condition = Condition::find($request->act);
        $app = Appointment::where('patient_id', $request->patient_id)
        ->where('act', $condition->name)
        ->orderBy('created_at', 'desc')
        ->first();

        if ($app) {
            if($app->remaining_amount == 0 ) {
                $app = null;

                return 206;
            }
            elseif($app->remaining_amount - $request->rate < 0) {
                return 205;
            }  
        }

        Appointment::create([
            'patient_id' => $request->patient_id,
            'rate' => $request->amount,
            'type' => "payment",
            'act' => $condition->name,
            'total_amount' => $app->total_amount,
            'remaining_amount' => $app->remaining_amount - $request->amount,
        ]);
        return response()->noContent();

    }

   
    public function calendar()
    {

        $appointments = Appointment::whereNotNull('next_examination_date')->get();

        return new NextDateCollection($appointments);
    }
    public function deleteDate(Appointment $appointment)
    {
        $appointment->update([
            'next_examination_date' => null,
        ]);
        return response()->json(['status' => 201]);
    }
    public function checkIfExists($patient_id, $condition_id)
    {

        $condition = Condition::find($condition_id);
        $app = Appointment::where('patient_id', $patient_id)
            ->orderBy('created_at', 'desc')
            ->where('act', $condition->name)
            ->first();
        if ($app) {
            if ($app->remaining_amount == 0 || $app->remaining_amount == 0) {

                return response()->json(['checked' => false]);
            } else {
                return response()->json(['checked' => true]);
            }
        } else {
            return response()->json(['checked' => false]);
        }
    }
}

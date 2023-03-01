<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Setting;
use App\Models\Appointment;
/* Resources & Collections */

use App\Http\Resources\PatientResource;
use App\Http\Resources\PatientCollection;

use Illuminate\Http\Request;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;

use Illuminate\Support\Facades\App;
use Carbon\Carbon;


class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $patients = Patient::paginate(10);


        return new PatientCollection($patients);
    }

  

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePatientRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePatientRequest $request)
    {
        
        App::setLocale(session()->get('locale'));
    
        $patient = Patient::create([
            'full_name' => $request->full_name,
            'cin' => $request->cin,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'insurance' => $request->insurance,
            'birth_date' => Carbon::parse($request->birth_date),
        ]);

        return new PatientResource($patient);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $patient = Patient::find($id);
        return new PatientResource($patient);
    }

    public function getPatientsByCondition($id) {
        $patients = Patient::whereHas('condition', function($q) use($id) {
            $q->where('condition_id', $id);
        })->get();

        return new PatientCollection($patients);
    }

  

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePatientRequest  $request
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $this->authorize('patients.update');

        $patient->update([
            'full_name' => $request->full_name,
            'cin' => $request->cin,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'insurance' => $request->insurance,
            'birth_date' => Carbon::parse($request->birth_date),
        ]);
        return new PatientResource($patient);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Patient $patient)
    {
        $this->authorize('patients.delete');
        $patient->delete();
        $patient->condition()->detach();
        Appointment::where('patient_id', $patient->id)->delete();
        return response()->noContent();
    }
    
    public function search($id) {

            $patients = Patient::where('id',$id)->get();
            return new PatientCollection($patients);          
        
    }
}

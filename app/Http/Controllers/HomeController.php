<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;

use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function generalData()
    {
        $this->authorize('home.view');
        $patients = Patient::count();
        $patientsAddedLastMonth = Patient::StartOfMonth()->count();

        $appointments = Appointment::StartOfMonth()->count();
        $consultations = Appointment::StartOfMonth()->count();
    
        $sessions = Appointment::StartOfMonth()->count();
        $paid = Appointment::all();



        $paid = Appointment::selectRaw('(SUM(rate)) as total, (Count(*)) as apps, MONTHNAME(created_at) as month_name')
        ->whereBetween(
            'created_at',
            [Carbon::now()->subYear(), Carbon::now()]
        )
        ->groupBy('month_name')
        ->orderBy('month_name', 'DESC')
        ->get();
        
        $earnings = Appointment::StartOfMonth()->sum('rate');
        $earningsLastWeek = Appointment::where('created_at', '>=', Carbon::now()->subDays(7))->sum('rate');


        $data = collect([
            'patients' => $patients,
            'patientsAddedLastMonth' => $patientsAddedLastMonth,
            'appointments' => $appointments,
            'consultation' => $consultations,
            'sessions' => $sessions,
            'earnings' => $earnings,
            'earningsLastWeek' => $earningsLastWeek,
            'paid' => $paid,

        ]);
        return response()->json([
            'generalData' => $data,
        ]);
    }
   
}

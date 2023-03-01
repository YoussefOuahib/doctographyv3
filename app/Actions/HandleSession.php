<?php 
namespace App\Actions;



/*request */
use Illuminate\Http\Request;

/* models */ 
use App\Models\Appointment;
use App\Models\Condition;

/* helpers */
use Storage;

class HandleSession {
    public function store(Request $request) {
            $condition = Condition::find($request->mycondition);
            $app = Appointment::where('patient_id', $request->patient_id)->where('condition', $condition->name)->IsSession()->orderBy('created_at', 'desc')->first();
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
                'next_examination_date' => $request->next_examination_date == null ? $request->next_examination_date : \DateTime::createFromFormat('D M d Y H:i:s e+', $request->next_examination_date)->format('Y-m-d'),
                'type' => $request->type,
                'note' => $request->note,
                'condition' => $condition->name,
                // 'total_sessions' => $request->total_sessions == null ? $app->total_sessions : $request->total_sessions,
                'total_amount' => $request->total_amount == null ? $app->total_amount : $request->total_amount,
                // 'remaining_sessions' => $app ? $app->remaining_sessions - 1 : $request->total_sessions - 1,
                'remaining_amount' => $app ? $app->remaining_amount - $request->rate :  $request->total_amount - $request->rate,
            ]);

            return $appointment;
            
        
        
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConditionCollection;
use App\Http\Resources\ConditionResource;
use App\Models\Condition;
use App\Models\Patient;
use Illuminate\Http\Request;

class ConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('conditions.manage');
        $conditions = Condition::paginate(10);


        return new ConditionCollection($conditions);
    }

  

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('conditions.manage');

        $condition = Condition::create([
            'name' => $request->name,
        ]);

        return new ConditionResource($condition);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $condition = Condition::find($id);

        return new ConditionResource($condition);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Condition  $condition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Condition $condition)
    {
        $this->authorize('conditions.manage');

        $condition->update([
            'name' => $request->name,
        ]);
        return response()->json(['status' => 201]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  \App\Models\Condition  $condition
     */
    public function destroy($condition)
    {
        $this->authorize('conditions.manage');

        $mycondition = Condition::find($condition);
            $mycondition->delete();
          return response()->json(201);
         
    }
    public function search()
    {

        $searching = request('searching');
        $conditions = Condition::where('name', 'LIKE', '%' . $searching . '%')
            ->orWhere('name', 'LIKE', '%' . $searching . '%')
            ->get();
        return new ConditionCollection($conditions);

    }



}

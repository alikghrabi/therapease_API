<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = Appointment::all();
        return response()->json([
            'status'=>true,
            'data'=>$appointments,
            'message'=>'all appointments returned successfully'
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(AppointmentRequest $request)
    {
        $user = auth()->user();
        if($user->id == $request->get('user_id'))
        {
            $appointment = Appointment::create($request->all());
            return response()->json([
                'status'=>true,
                'data'=>$appointment,
                'message'=>'Appointment Created Successfully'
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'data'=>null,
                'message'=>'User is not authenticated'
            ]);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointment = Appointment::find($id);
        if(!is_null($appointment))
        {
            return response()->json([
                'status'=>true,
                'data'=>$appointment,
                'message'=>'Appointment data returned successfully'
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'data'=>null,
                'message'=>'Appointment data not found'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AppointmentRequest $request, string $id)
    {
        $user = auth()->user();

        if($user->id == $request->get('user_id'))
        {
            $appointment = Appointment::find($id);
            if(!is_null($appointment))
            {
                $appointment->update($request->all());

                return response()->json([
                    'status'=>true,
                    'data'=>$appointment,
                    'message'=>'Appointment data returned successfully'
                ]);
            }else{
                return response()->json([
                    'status'=>false,
                    'data'=>null,
                    'message'=>'Appointment data not found'
                ]);
            }
        }else{
            return response()->json([
                'status'=>false,
                'data'=>null,
                'message'=>'User is not authenticated'
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointment = Appointment::find($id);
        if(!is_null($appointment))
        {
            $appointment->delete();
            return response()->json([
                'status'=>true,
                'data'=>null,
                'message'=>'appointment deleted successfully'
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'data'=>null,
                'message'=>'Appointment not found'
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth as Auth;

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
    
    public function getBookings(Request $request, $id)
    {
        try {
            // Determine if the logged-in user is a therapist or a user (patient)
            $user = Auth::user();
            
            // Fetch appointments based on user type
            if ($user instanceof \App\Models\Therapist) {
                // Fetch appointments for the therapist
                $appointments = Appointment::where('therapist_id', $user->id)
                    ->where('status', '!=', 'cancelled') // Optional: filter by status if needed
                    ->get();
            } elseif ($user instanceof \App\Models\User) {
                // Fetch appointments for the user (patient)
                $appointments = Appointment::where('user_id', $user->id)
                    ->where('status', '!=', 'cancelled') // Optional: filter by status if needed
                    ->get();
            } else {
                // Handle cases where the user type is not recognized
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }
            
            // Return JSON response with appointments data
            return response()->json([
                'status' => true,
                'message' => 'Appointments retrieved successfully',
                'appointments' => $appointments,
            ]);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve appointments. ' . $e->getMessage(),
            ], 500);
        }
    }
}
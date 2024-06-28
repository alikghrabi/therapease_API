<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use Illuminate\Support\Facades\Validator;

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
    // public function DeleteBooking(string $id)
    // {
    //     $appointment = Appointment::find($id);
    //     if(!is_null($appointment))
    //     {
    //         $appointment->delete();
    //         return response()->json([
    //             'status'=>true,
    //             'data'=>null,
    //             'message'=>'appointment deleted successfully'
    //         ]);
    //     }else{
    //         return response()->json([
    //             'status'=>false,
    //             'data'=>null,
    //             'message'=>'Appointment not found'
    //         ]);
    //     }
    // }

    public function addBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'user_id' => 'required|integer|exists:users,id',
            'therapist_id' => 'required|integer|exists:therapists,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        // Assuming you have a model called Appointment
        $appointment = new Appointment();
        $appointment->date = $request->date;
        $appointment->start_time = $request->start_time;
        $appointment->end_time = $request->end_time;
        $appointment->status = $request->status;
        $appointment->notes = $request->notes;
        $appointment->user_id = $request->user_id;
        $appointment->therapist_id = $request->therapist_id;
        $appointment->save();

        return response()->json(['status' => true, 'data' => $appointment, 'message' => 'Appointment Created Successfully'], 201);
    }
    
    public function DeleteBooking($id)
{
    // Find the appointment
    $appointment = Appointment::find($id);

    // Check if the appointment exists
    if (!$appointment) {
        return response()->json(['message' => 'Appointment not found'], 404);
    }

    // Ensure the authenticated user can delete this appointment (if needed)

    // Perform deletion
    try {
        $appointment->delete();
        return response()->json(['message' => 'Appointment deleted successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to delete appointment', 'error' => $e->getMessage()], 500);
    }
}
public function updateBooking(Request $request, $id)
{
    // Find the appointment
    $appointment = Appointment::find($id);

    // Check if the appointment exists
    if (!$appointment) {
        return response()->json(['message' => 'Appointment not found'], 404);
    }

    // Validate incoming request data (if needed)
    $validatedData = $request->validate([
        'date' => 'nullable|date',
        'start_time' => 'nullable|date_format:H:i',
        'end_time' => 'nullable|date_format:H:i',
        'status' => 'nullable|string|in:scheduled,pending,completed',
        'notes' => 'nullable|string',
        'user_id' => 'nullable|exists:users,id',
        'therapist_id' => 'nullable|exists:therapists,id',
    ]);

    // Update appointment attributes
    $appointment->fill($validatedData);
    
    // Save the appointment
    try {
        $appointment->save();
        return response()->json(['message' => 'Appointment updated successfully', 'appointment' => $appointment], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to update appointment', 'error' => $e->getMessage()], 500);
    }
}

}
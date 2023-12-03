<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\V1\Appointment;
use App\Http\Requests\Api\V1\CreateAppointmentRequest;
use App\Http\Requests\Api\V1\UpdateAppointmentRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\Api\V1\AppointmentNotification;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
        Gate::authorize('isProvider');
            $app=Appointment::whereHas('service.serviceLocation.provider.user',function($query){
                $query->where('id',Auth::id());
            })->get();
            return response()->json([
                'status' => 1,
                'appointments' => $app,
            ]);
        }catch(\Exception $e)
            {
                return response()->json([
                    'status' => 0
                ]);

            }
    }

      /**add new appointment */
    public function store(CreateAppointmentRequest $request)
    {
        $data = array_merge($request->all(), ['patient_id' => Auth::id() ,'status' => 'valid']);
        return DB::transaction(function () use ($data)
        {
            $appointment = Appointment::create($data);

            $provider= $appointment->service->serviceLocation->provider->user;
            $provider->notify(new AppointmentNotification($appointment));
            return response()->json([
                'status' => 1,
                'message'=>'Add Appointment Successfully',
            ]);

        });
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try{
            $appointment=Appointment::findOrfail($id);
            $this->authorize('view',$appointment);
            return response()->json([
                'status' => 1,
                'appointment' => $appointment,
            ]);
        }catch(\Exception $e){
                return response()->json([
                    'status' => 0,
                ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request,$id)
    {
        try{
            $appointment = Appointment::findOrfail($id);
            $this->authorize('update',$appointment);
            $data=$request->all();
            $appointment->update($data);
            return response()->json([
                'status' => 1,
                'message'=>'Update Appointment Successfully',
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 0,
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {

        try{
            $appointment=Appointment::findOrfail($id);
            $this->authorize('delete',$appointment);
            $appointment->delete();
            return response()->json([
                'status' => 1,
                'message'=>'Delete Appointment Successfully',
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    /** change a status appointment to done  */

    public function doneAppointment($id){

        try{
            $appointment=Appointment::find($id);
            $this->authorize('done',$appointment);
            $appointment->update(['status'=>'done']);
            return response()->json([
                'status'=>1,
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 0,
            ]);
        }
    }

    /**change status appointment to canceled */

    public function cancelAppointment($id){

        try{
            $appointment=Appointment::find($id);
            $this->authorize('canceled',$appointment);
            $appointment->update(['status'=>'canceled']);
            return response()->json([
                'status'=>1,
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 0,
            ]);
        }
    }
}

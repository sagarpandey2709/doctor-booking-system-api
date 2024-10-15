<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends ApiController
{
    public function createAppointment(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_time' => 'required|date|after:now',
        ]);

        // Check if the doctor_id belongs to a doctor (role 1)
        $doctor = User::where('id', $request->doctor_id)->where('role', 1)->first();

        if (!$doctor) {
            return $this->errorResponse('Selected doctor does not exist', 422);
        }

        // Define the time range for 15 minutes before and after the desired appointment time
        $appointmentTime = Carbon::parse($request->appointment_time);
        $startTime = $appointmentTime->copy()->subMinutes(15);
        $endTime = $appointmentTime->copy()->addMinutes(15);

        // Check if there's an appointment booked within the 15-minute window
        $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->whereBetween('appointment_time', [$startTime, $endTime])
            ->exists();

        if ($existingAppointment) {
            return $this->errorResponse('This time slot is already booked within a 15-minute window', 422);
        }

        // Create the appointment
        $appointment = Appointment::create([
            'patient_id' => auth()->id(),
            'doctor_id' => $request->doctor_id,
            'appointment_time' => $request->appointment_time,
        ]);

        return $this->successResponse([ 'appointment' => $appointment],'Appointment created successfully',201);
    }

    public function updateAppointmentStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:cancelled,rejected,postponed',
            'new_appointment_time' => 'required_if:status,postponed|date|after:now',
        ]);

        $appointment = Appointment::where('id', $id)
            ->where('patient_id', auth()->id())
            ->firstOrFail();

        if ($request->status === 'postponed') {
            $newAppointmentTime = Carbon::parse($request->new_appointment_time);

            // Define the time range for 15 minutes before and after the desired new appointment time
            $startTime = $newAppointmentTime->copy()->subMinutes(15);
            $endTime = $newAppointmentTime->copy()->addMinutes(15);

            // Check if there's an appointment booked within the 15-minute window (excluding current appointment)
            $existingAppointment = Appointment::where('doctor_id', $appointment->doctor_id)
                ->where('id', '!=', $appointment->id) // Exclude current appointment
                ->whereBetween('appointment_time', [$startTime, $endTime])
                ->exists();

            if ($existingAppointment) {
                return $this->errorResponse('This new time slot is already booked within a 15-minute window', 422);
            }

            // Update appointment time if postponed
            $appointment->update([
                'status' => $request->status,
                'appointment_time' => $request->new_appointment_time,
            ]);
        } else {
            // For status 'cancelled' or 'rejected', only update the status
            $appointment->update(['status' => $request->status]);
        }

        return $this->successResponse([ 'appointment' => $appointment],'Appointment status updated',200);
    }

    public function viewAppointments(Request $request)
    {
        // Query to get appointments for the authenticated patient
        $appointments = Appointment::where('patient_id', auth()->id())
            // Apply date filter if the 'date' parameter exists in the request
            ->when($request->has('date'), function ($query) use ($request) {
                return $query->whereDate('appointment_time', $request->date); // Filter by specific date
            })
            // Apply status filter if the 'status' parameter exists in the request
            ->when($request->has('status'), function ($query) use ($request) {
                return $query->where('status', $request->status); // Filter by appointment status
            })
            ->get(); // Get the filtered list of appointments
        // Return a successful JSON response with the list of appointments
        return $this->successResponse([ 'appointment' => $appointments]);
    }

    public function doctorAppointments(Request $request)
    {
        // Query to get appointments for the authenticated doctor
        $appointments = Appointment::where('doctor_id', auth()->id())
            // Apply date filter if the 'date' parameter exists in the request
            ->when($request->has('date'), function ($query) use ($request) {
                return $query->whereDate('appointment_time', $request->date);
            })
            // Apply status filter if the 'status' parameter exists in the request
            ->when($request->has('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->get(); // Get the results
        // Return a successful JSON response with the list of appointments
        return $this->successResponse([ 'appointment' => $appointments]);

    }


    public function updateDoctorAppointmentStatus(Request $request, $id)
    {
        // Validate the incoming request to ensure 'status' is present and has a valid value.
        $request->validate([
            'status' => 'required|in:approved,rejected,cancelled',
        ]);

        // Find the appointment that matches the given ID and belongs to the authenticated doctor.
        // If no such appointment is found, a 404 error will be thrown.
        $appointment = Appointment::where('id', $id)
            ->where('doctor_id', auth()->id()) // Check that the doctor owns the appointment
            ->firstOrFail(); // Return the appointment or fail with 404 if not found

        // Update the status of the appointment with the new status provided in the request.
        $appointment->update(['status' => $request->status]);

        // Return a successful JSON response with the updated appointment details.
        return $this->successResponse([ 'appointment' => $appointment],'Appointment status updated');
    }
}

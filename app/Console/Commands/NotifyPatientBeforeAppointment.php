<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyPatientBeforeAppointment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:patients-before-appointment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notification to patients 30 minutes before their appointment.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       // Get the current time
    $currentDateTime = Carbon::now();

    // Find appointments scheduled within the next 30 minutes
    $upcomingAppointments = Appointment::whereBetween('appointment_time', [
            $currentDateTime->toDateTimeString(),
            $currentDateTime->addMinutes(30)->toDateTimeString(),
        ])
        ->with(['patient', 'doctor'])  // Include both patient and doctor in the query
        ->get();

    // Loop through the appointments and send notifications
    foreach ($upcomingAppointments as $appointment) {
        $patient = $appointment->patient;
        $doctor = $appointment->doctor;  // Fetch the doctor's details

        // Send email notification to the patient
        Mail::send('emails.appointmentReminder', ['appointment' => $appointment], function ($message) use ($patient, $doctor) {
            $message->to($patient->email)
                ->subject('Upcoming Appointment Reminder with Dr. ' . $doctor->name);
        });
    }

    return 0;
    }
}

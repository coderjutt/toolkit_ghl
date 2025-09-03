<?php
namespace App\Services;
use App\Models\Appointment;
use Carbon\Carbon;
use DB;
class AppointmentServices{
    public function handle_appointment(array $data)
 {

 $type = $data['type'];
 $location_id = $data['locationId'] ?? $data['location_id'];
 //dd($location_id);
 // Find the appointment by ghl_appointment_id and location_id
 $appointment = Appointment::where('appointment_id', $data['appointment']['id'])
     ->where('location_id', $location_id)
     ->first();
 if ($type === 'AppointmentDelete') {
     if ($appointment) {
         $appointment->delete();
         \Log::info("Appointment with GHL Appointment ID: {$data['appointment']['id']} deleted successfully and type is {$type}.");
     } else {
         \Log::info("Appointment with GHL Appointment ID: {$data['appointment']['id']} not found and type is {$type}.");
     }
     return 'done';
 }

 // Overwriting the $data with the appointment data
 $data = isset($data['appointment']) ? $data['appointment'] : $data;

 $dateAdded = isset($data['dateAdded']) ? $data['dateAdded'] : null;
 $dateAdded = !is_null($dateAdded) ? Carbon::parse($dateAdded)->format('Y-m-d H:i:s') : null;
 $startTime = isset($data['startTime']) ? $data['startTime'] : null;
 $startTime = !is_null($startTime) ? Carbon::parse($startTime)->format('Y-m-d H:i:s') : null;
 $endTime = isset($data['endTime']) ? $data['endTime'] : null;
 $endTime = !is_null($endTime) ? Carbon::parse($endTime)->format('Y-m-d H:i:s') : null;

 $appointmentData = [
     'appointment_id' => $data['id'],
     'location_id' => $location_id,
     'address' => $data['address'] ?? null,
     'title' => $data['title'] ?? null,
     'calendar_id' => $data['calendarId'] ?? null,
     'contact_id' => $data['contactId'] ?? null,
     'group_id' => $data['groupId'] ?? null,
     'appointment_status' => $data['appointmentStatus'] ?? null,
     'assigned_user_id' => $data['assignedUserId'] ?? null,
     'users' => isset($data['users']) ? json_encode($data['users']) : null,
     'notes' => $data['notes'] ?? null,
     'source' => $data['source'] ?? null,
     'start_time' => $startTime,
     'end_time' => $endTime,
     'date_added' => $dateAdded,
 ];

 // Update the existing appointment or create a new one
 if ($appointment) {
     // Update each field manually
     foreach ($appointmentData as $key => $value) {
         $appointment->$key = $value;
     }

     // Save the updated appointment
     $appointment->save();
     \Log::info("Appointment with GHL Appointment ID: {$data['id']} updated successfully and type is {$type}.");
 } else {
     // Create a new appointment and fill it
     $appointment = new Appointment();
     foreach ($appointmentData as $key => $value) {
         $appointment->$key = $value;
     }

     // Save the new appointment
     $appointment->save();
     \Log::info("Appointment with GHL Appointment ID: {$data['id']} created successfully and type is {$type}.");
 }

 return $appointment;
    }
}

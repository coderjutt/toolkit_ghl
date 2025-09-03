<?php
namespace App\Services;
use App\Models\Contact;
use Carbon\Carbon;
class ContactServices{
    public function handle_contact(array $data){
        //dd($data);
 $type = $data['type'] ?? null;
    $location_id = $data['locationId'] ?? $data['location_id'];
    $contact = Contact::where('contact_id', $data['id'])
        ->where('location_id', $location_id)
        ->first();
    if ($type === 'ContactDelete') {
        if ($contact) {
            $contact->delete();
            Log::info("Contact with GHL Contact ID: {$data['id']} deleted successfully and type is {$type}");
        } else {
            Log::info("Contact with GHL Contact ID: {$data['id']} not found and type is {$type}.");
        }
        return 'done';
    }
    $contactData = [
        'contact_id' => $data['id'],
        'location_id' => $location_id,
        'name' => isset($data['firstName']) && isset($data['lastName']) ? $data['firstName'] . ' ' . $data['lastName'] : null,
        'email' => $data['email'] ?? null,
        'country' => $data['country'] ?? null,
        'city' => $data['city'] ?? null,
        'company_id' => $data['companyName'] ?? null,
        'source' => $data['source'] ?? null,
        'phone' => $data['phone'] ?? null,
        'postal_code' => $data['postalCode'] ?? null,
        'assigned_to' => $data['assignedTo'] ?? null,
        'tags' => isset($data['tags']) ? implode(',', $data['tags']) : null, // Convert tags array to a comma-separated string
        'date_added' => isset($data['dateAdded']) ? Carbon::parse($data['dateAdded'])->format('Y-m-d H:i:s') : null,
        'date_of_birth' => isset($data['dateOfBirth']) ? date('Y-m-d', strtotime($data['dateOfBirth'])) : null,

    ];
       //dd($contactData);
    if ($contact) {
        foreach ($contactData as $key => $value) {
            $contact->$key = $value;
        }
        $contact->save();
    } else {
        $contact = new Contact();
        foreach ($contactData as $key => $value) {
            $contact->$key = $value;
        }
       $contact->save();
    }
    }

}

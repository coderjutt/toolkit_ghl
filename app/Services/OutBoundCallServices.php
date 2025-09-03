<?php
namespace App\Services;
class OutBoundCallServices{
    public function handle_OutBoundCall(array $data){
try {
        $dateAdded = isset($data['dateAdded']) ? $data['dateAdded'] : null;
        $dateAdded = !is_null($dateAdded) ? Carbon::parse($dateAdded)->format('Y-m-d H:i:s') : null;
       $call_id= isset($data['userId']) ? $data['userId'] : null;
        // Data to update or create the call
        $location_id = isset($data['locationId']) ? $data['locationId'] : null;
        $callData = [
            'ghl_call_id' => $call_id,
            'type' => $data['type'],
            'location_id' => $data['locationId'],
            'body' => $data['body'] ??  null,
            'contact_id' => isset($data['contactId']) ? $data['contactId'] : null,
            'attachments' => isset($data['attachments']) ? json_encode($data['attachments']) : null,
             'content_type' => isset($data['contentType']) ? $data['contentType'] : null,
            'conversation_id' => isset($data['conversationId']) ? $data['conversationId'] : null,
            'date_added' => $dateAdded,
            'direction' => isset($data['direction']) ? $data['direction'] : null,
            'message_type' => isset($data['messageType']) ? $data['messageType'] : null,
            'message_id' => isset($data['messageId']) ? $data['messageId'] : null,
            'status' => isset($data['status']) ? $data['status'] : null,
            'source' => isset($data['source']) ? $data['source'] : null,
            'assigned_to' => isset($data['userId']) ? $data['userId'] : null,

        ];
        $call = Call::create($callData);
        \Log::info('Call created successfully.');
        return $call;
    } catch (\Exception $e) {
        \Log::info('-----------------  Array to string conversion error -----------------');
        \Log::info(json_encode($data));
        \Log::alert('Error: ' . $e->getMessage());
        \Log::info('-----------------  Array to string conversion error -----------------');
    }
    }
}

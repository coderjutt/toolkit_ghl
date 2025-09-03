<?php
namespace App\Services;
use App\Models\Opportunity;
use Carbon\Carbon;
class OpportunityServices{
    public function handle_Opportunity(array $data){
    $type = $data['type'];
    $location_id = $data['locationId'] ?? $data['location_id'];
    $opportunity = Opportunity::where('opportunity_id', $data['id'])
        ->where('location_id', $location_id)
        ->first();

    if ($type === 'OpportunityDelete') {
        if ($opportunity) {
            $opportunity->delete();
            Log::info("Opportunity with GHL Opportunity ID: {$data['id']} deleted successfully and type is {$type}.");
        } else {
            Log::info("Opportunity with GHL Opportunity ID: {$data['id']} not found and type is {$type}.");
        }
        return 'done';
    }
    $dateAdded = isset($data['dateAdded']) ? $data['dateAdded'] : null;
    $dateAdded = !is_null($dateAdded) ? Carbon::parse($dateAdded)->format('Y-m-d H:i:s') : null;

    $opportunityData = [
        'opportunity_id' => $data['id'],
        'location_id' => $location_id,
        'assigned_to' => $data['assignedTo'] ?? null,
        'contact_id' => $data['contactId'] ?? null,
        'monetary_value' => $data['monetaryValue'] ?? null,
        'name' => $data['name'] ?? null,
        'pipeline_id' => $data['pipelineId'] ?? null,
        'pipeline_stage_id' => $data['pipelineStageId'] ?? null,
        'source' => $data['source'] ?? null,
        'status' => $data['status'] ?? null,
        'date_added' => $dateAdded,
    ];
    if ($opportunity) {
        foreach ($opportunityData as $key => $value) {
            \Log::info("Opportunity with GHL Opportunity key : {$opportunity->$key} updated successfully and type is {$value} .");
            $opportunity->$key = $value;
        }
        $opportunity->save();
        \Log::info("Opportunity with GHL Opportunity ID: {$data['id']} updated successfully and type is {$type} .");
    } else {
        $opportunity = new Opportunity();
        foreach ($opportunityData as $key => $value) {
            $opportunity->$key = $value;
        }
        $opportunity->save();
        \Log::info("Opportunity with GHL Opportunity ID: {$data['id']} created successfully and type is {$type}.");
    }

    return $opportunity;
}
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ContactServices;
use App\Services\NoteServices;
use App\Services\OpportunityServices;
use App\Services\InboundCallServices;
use App\Services\OutBoundCallServices;
use App\Services\AppointmentServices;

class WebhookController extends Controller
{
    protected $contactServices;
    protected $opportunityServices;
    protected $appointmentServices;
    protected $inboundCallServices;
    protected $outboundCallServices;
    protected $noteServices;

    public function __construct(
        ContactServices $contactServices,
        OpportunityServices $opportunityServices,
        AppointmentServices $appointmentServices,
        NoteServices $noteServices,
        InboundCallServices $inboundCallServices,
        OutBoundCallServices $outboundCallServices
    ) {
        $this->contactServices = $contactServices;
        $this->opportunityServices = $opportunityServices;
        $this->appointmentServices = $appointmentServices;
        $this->noteServices = $noteServices;
        $this->inboundCallServices = $inboundCallServices;
        $this->outboundCallServices = $outboundCallServices;
    }

    public function handleContact(array $data)
    {
        $this->contactServices->handle_contact($data);
    }

    public function handleOpportunity(array $data)
    {
        $this->opportunityServices->handle_Opportunity($data);
    }

    public function handleAppointment(array $data)
    {
        $this->appointmentServices->handle_appointment($data);
    }

    public function handleInboundCall(array $data)
    {
        //dd($data);
        $this->inboundCallServices->handel_Inboundcall($data);
    }

    public function handleNote($data)
    {
        $this->noteServices->handle_note($data);
    }

    public function handle_webhook(Request $request)
    {
        //dd("40440");
        $data = $request->all();
        $type = $data['type'] ?? null;

        if (in_array($type, [
            'ContactCreate', 'ContactUpdate', 'ContactDelete',
            'ContactTagUpdate', 'ContactDndUpdate'
        ])) {
            $this->handleContact($data);
        } elseif (in_array($type, [
            'OpportunityCreate', 'OpportunityUpdate', 'OpportunityDelete',
            'OpportunityAssignedToUpdate', 'OpportunityMonetaryValueUpdate',
            'OpportunityStageUpdate', 'OpportunityStatusUpdate'
        ])) {
            $this->handleOpportunity($data);
        } elseif (in_array($type, [
            'AppointmentCreate', 'AppointmentUpdate', 'AppointmentDelete'
        ])) {
            $this->handleAppointment($data);
        } elseif (in_array($type, ['OutboundMessage', 'InboundMessage'])) {
            $this->handleInboundCall($data);
        } elseif (in_array($type, ['NoteCreate', 'NoteDelete', 'NoteUpdate'])) {
            $this->handleNote($data);
        } else {
            Log::info("Webhook type not handled: {$type}");
        }

        return response()->json([
            'status' => 'success',
            'type' => $type
        ]);
    }
}

<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class InboundCallServices
{
    public function handel_Inboundcall(array $data)
    {
        try {
            $dateAdded = isset($data['dateAdded']) ? Carbon::parse($data['dateAdded'])->format('Y-m-d H:i:s') : null;

            $callData = [
                'ghl_call_id'     => $data['userId'] ?? null,
                'type'            => $data['type'] ?? null,
                'location_id'     => $data['locationId'] ?? null,
                'attachments'     => isset($data['attachments']) ? json_encode($data['attachments']) : null,
                'body'            => isset($data['body']) ? $this->sanitizeAndTruncateBody($data['body']) : null,
                'contact_id'      => $data['contactId'] ?? null,
                'content_type'    => $data['contentType'] ?? null,
                'conversation_id' => $data['conversationId'] ?? null,
                'date_added'      => $dateAdded,
                'direction'       => $data['direction'] ?? null,
                'message_type'    => $data['messageType'] ?? null,
                'message_id'      => $data['messageId'] ?? null,
                'status'          => $data['status'] ?? null,
                'source'          => $data['source'] ?? null,
                'assigned_to'     => $data['userId'] ?? null,
                'subject'         => $data['subject'] ?? null,
                'call_duration'   => $data['callDuration'] ?? null,
                'call_status'     => $data['callStatus'] ?? null,
                'ghl_user_Id'     => $data['userId'] ?? null,
            ];

            $call = Message::updateOrCreate(
                [
                    'type'            => $data['type'] ?? null,
                    'location_id'     => $data['locationId'] ?? null,
                    'contact_id'      => $data['contactId'] ?? null,
                    'conversation_id' => $data['conversationId'] ?? null,
                ],
                $callData
            );

            Log::info('Call record created or updated successfully.');
            return $call;

        } catch (\Exception $e) {
            Log::error('Error processing inbound call webhook: ' . $e->getMessage(), [
                'data' => $data
            ]);
            return null;
        }
    }

    /**
     * Sanitize and safely truncate the body content.
     *
     * @param mixed $body
     * @return string
     */
    private function sanitizeAndTruncateBody($body): string
    {
        // If it's an array or object, encode to JSON string
        if (is_array($body) || is_object($body)) {
            $body = json_encode($body);
        }

        // Cast to string if still not
        $body = (string) $body;

        // Optional: remove unwanted HTML tags (keep basic ones)
        $cleanBody = strip_tags($body, '<p><div><span><br>');

        // Truncate to avoid DB overflow (LONGTEXT max is ~4GB; this is a safe real-world size)
        return mb_substr($cleanBody, 0, 65535, 'UTF-8');
    }
}

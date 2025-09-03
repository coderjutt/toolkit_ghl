<?php

namespace App\Services;

use App\Models\Note;
use Carbon\Carbon;

class NoteServices
{
    public function handle_note(array $data)
{
    try {
        if ($data['type'] === 'NoteDelete') {
            Note::where('location_id', $data['locationId'])
                ->where('note_id', $data['id'])
                ->where('contact_id', $data['contactId'])
                ->delete();

            \Log::info("Note deleted: {$data['id']}");
            return;
        }

        $noteDate = isset($data['dateAdded']) ? Carbon::parse($data['dateAdded'])->format('Y-m-d H:i:s') : null;

        $note = Note::updateOrCreate(
            [
                'location_id' => $data['locationId'],
                'note_id' => $data['id'],
                'contact_id' => $data['contactId'],
            ],
            [
                'type' => $data['type'],
                'body' => $data['body'] ?? null,
                'date_added' => $noteDate,
            ]
        );

        \Log::info('Note synced successfully.', ['note' => $note]);
        return $note;
    } catch (\Exception $e) {
        \Log::error('Error syncing note: ' . $e->getMessage());
        \Log::debug('Note data:', $data);
    }
}

}

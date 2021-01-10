<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Note;
use App\Models\NoteParticipator;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    protected $banMessage = ['status' => 'ERROR', 'error' => 'You are ban or logged out'];

    protected function isBan() {
        if (\Auth::check())
            return \Auth::user()->ban;

        return 0;
    }

    private function checkAccessibility($note, $user) {
        $participator = NoteParticipator::where('note', '=', $note)
            ->where('user', '=', $user)
            ->get();
        return count($participator)? $participator[0]['permission']: 0;
    }

    public function createNote(Request $request) {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');

            $note = Note::create([
                'subject' => $inputs['subject'],
                'text' => $inputs['text'],
                'owner' => \Auth::id(),
                'slug' => $inputs['slug']
            ]);

            Log::create([
                'section' => $note->id,
                'type' => 5,
                'user' => \Auth::id(),
                'text' => '<a href="' . \Auth::id() . '">' . \Auth::user()->username . '</a> created a note'
            ]);

            return json_encode(['status' => 'OK', 'result' => 'Note created successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function editNote(Request $request, $id) {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');

            if ($this->checkAccessibility($id, \Auth::id()) < 2)
                return json_encode(['status' => 'ERROR', 'error' => 'Permission denied']);

            Note::where('id', '=', $id)
                ->update([
                    'subject' => $inputs['subject'],
                    'text' => $inputs['text'],
                    'slug' => $inputs['slug']
                ]);

            Log::create([
                'section' => $id,
                'type' => 5,
                'user' => \Auth::id(),
                'text' => '<a href="' . \Auth::id() . '">' . \Auth::user()->username . '</a> edited a note'
            ]);

            return json_encode(['status' => 'OK', 'result' => 'Note edited successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function deleteNote($id) {
        if (!$this->isBan()) {
            Note::where('id', '=', $id)
                ->where('owner', '=', \Auth::id())
                ->delete();

            Log::create([
                'section' => 0,
                'type' => 5,
                'user' => \Auth::id(),
                'text' => '<a href="' . \Auth::id() . '">' . \Auth::user()->username . '</a> deleted a note'
            ]);

            return json_encode(['status' => 'OK', 'result' => 'Note deleted successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function addParticipator($note, $user, $permission) {
        if (!$this->isBan()) {
            $this->deleteParticipator($note, $user);

            NoteParticipator::create([
                'note' => $note,
                'user' => $user,
                'permission' => $permission
            ]);

            Log::create([
                'section' => $note,
                'type' => 5,
                'user' => \Auth::id(),
                'text' => '<a href="' . \Auth::id() . '">' . \Auth::user()->username . '</a> added a participator to a note'
            ]);

            return json_encode(['status' => 'OK', 'result' => 'Participator added successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function deleteParticipator($note, $user) {
        if (!$this->isBan()) {
            NoteParticipator::where('note', '=', $note)
                ->where('user', '=', $user)
                ->delete();

            Log::create([
                'section' => 0,
                'type' => 5,
                'user' => \Auth::id(),
                'text' => '<a href="' . \Auth::id() . '">' . \Auth::user()->username . '</a> removed a participator to a note'
            ]);

            return json_encode(['status' => 'OK', 'result' => 'Participator deleted successfully']);
        }

        return json_encode($this->banMessage);
    }
}

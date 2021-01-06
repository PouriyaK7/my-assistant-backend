<?php

namespace App\Http\Controllers;

use App\Models\TaskGroup;
use Illuminate\Http\Request;

class TaskManagerController extends Controller
{
    protected function isBan() {
        if (\Auth::check())
            return \Auth::user()->ban;

        return 0;
    }

    public function createTaskGroup(Request $request) {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');
            return TaskGroup::create([
                'title' => $inputs['title'],
                'owner' => \Auth::id(),
                'description' => $inputs['description'],
                'slug' => $inputs['title'] . '-' . \Auth::id(),
            ]);
        }
        return 'You are ban or logged out';
    }
}

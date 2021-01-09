<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Task;
use App\Models\TaskGroup;
use App\Models\TaskGroupUser;
use App\Models\User;
use Illuminate\Http\Request;

class TaskManagerController extends Controller
{
    protected $banMessage = ['status' => 'ERROR', 'error' => 'You are ban or logged out'];

    protected function isBan() {
        if (\Auth::check())
            return \Auth::user()->ban;

        return 0;
    }

    protected function checkAccessibility($id) {
        return TaskGroupUser::where('task_group', '=', $id)
            ->where('user', '=', \Auth::id())
            ->count();
    }

    public function createTaskGroup(Request $request) {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');
            $taskGroup = TaskGroup::create([
                'title' => $inputs['title'],
                'owner' => \Auth::id(),
                'description' => $inputs['description'] ?? null,
                'slug' => $inputs['slug'] ?? $inputs['title'] . '-' . \Auth::id(),
            ]);

            foreach ($inputs['users'] as $user)
                TaskGroupUser::create([
                    'task_group' => $taskGroup->id,
                    'user' => $user
                ]);

            Log::create([
                'section' => $taskGroup->id,
                'type' => 1,
                'user' => \Auth::id(),
                'text' => '<a href="' . \Auth::id() . '">' . \Auth::user()->username . '</a> created a task group'
            ]);

            return json_encode(['status' => 'OK', 'result' => 'Task group added successfully']);
        }
        return json_encode($this->banMessage);
    }

    public function editTaskGroup(Request $request, int $id) {
        if (!$this->isBan()) {
            if ($this->checkAccessibility($id) != 1)
                return json_encode(['status' => 'ERROR', 'error' => 'User does\'nt have access to this task group']);
            $inputs = $request->except('_token');

            $taskGroup = new TaskGroup($id);

            if ($taskGroup->title != $inputs['title'])
                Log::create([
                    'section' => $taskGroup->id,
                    'type' => 1,
                    'user' => \Auth::id(),
                    'text' => '<a href="/' . \Auth::id() . '">' . \Auth::user()->username . '</a> Task group title changed from ' . $taskGroup->title . ' to ' . $inputs['title']
                ]);

            if ($taskGroup->description != $inputs['description'])
                Log::create([
                    'section' => $taskGroup->id,
                    'type' => 1,
                    'user' => \Auth::id(),
                    'text' => '<a href="/' . \Auth::id() . '">' . \Auth::user()->username . '</a> Task group description changed from ' . $taskGroup->description . ' to ' . ($inputs['description'] ?? 'null')
                ]);

            if ($taskGroup->slug != $inputs['slug'])
                Log::create([
                    'section' => $taskGroup->id,
                    'type' => 1,
                    'user' => \Auth::id(),
                    'text' => '<a href="/' . \Auth::id() . '">' . \Auth::user()->username . '</a> Task group slug changed from ' . $taskGroup->description . ' to ' . ($inputs['description'] ?? 'null')
                ]);

            TaskGroup::where('id', '=', $id)
                ->update([
                    'title' => $inputs['title'],
                    'description' => $inputs['description'] ?? null,
                    'slug' => $inputs['slug']
                ]);

            return json_encode(['status' => 'OK', 'result' => 'Task group edited successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function deleteTaskGroup($id) {
        if (!$this->isBan()) {
            $taskGroup = new TaskGroup($id);
            if ($taskGroup->owner != \Auth::id())
                return json_encode(['status' => 'ERROR', 'error' => 'User does\'nt have access to delete this task group']);


            Log::create([
                'section' => 0,
                'type' => 1,
                'user' => \Auth::id(),
                'text' => '<a href="' . \Auth::id() . '">' . \Auth::user()->username . '</a> deleted ' . $taskGroup->title . ' task group'
            ]);

            TaskGroup::where('id', '=', $id)
                ->delete();

            TaskGroupUser::where('task_group', '=', $id)
                ->delete();

            Task::where('group', '=', $id)
                ->delete();

            return json_encode(['status' => 'OK', 'result' => 'Task group deleted successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function removeUserFromTaskGroup($user, $id) {
        if (!$this->isBan()) {
            $user = new User($user);
            $taskGroup = new TaskGroup($id);
            if ($taskGroup->owner != \Auth::id())
                return json_encode(['status' => 'ERROR', 'error' => 'User does\'nt have access to remove user in this task group']);

            Log::create([
                'section' => $id,
                'type' => 1,
                'user' => \Auth::id(),
                'text' => '<a href="' . \Auth::id() . '">' . \Auth::user()->username . '</a> removed <a href="/' . $user->id . '">' . $user->username . '</a> from ' . $taskGroup->title . ' task group'
            ]);

            TaskGroupUser::where('task_group', '=', $taskGroup->id)
                ->where('user', '=', $user->id)
                ->delete();

            return json_encode(['status' => 'OK', 'result' => 'User removed from task group successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function addUserToTaskGroup($user, $id) {
        if (!$this->isBan()) {
            $taskGroup = new TaskGroup($id);
            $user = new User($user);
            if ($taskGroup->owner != \Auth::id())
                return json_encode(['status' => 'ERROR', 'error' => 'User does\'nt have access to add user in this task group']);

            Log::create([
                'section' => $id,
                'type' => 1,
                'user' => \Auth::id(),
                'text' => '<a href="' . \Auth::id() . '">' . \Auth::user()->username . '</a> added <a href="' . $user->id . '">' . $user->username . '</a> to ' . $taskGroup->title . ' task group'
            ]);

            TaskGroupUser::firstOrCreate([
                'task_group' => $id,
                'user' => $user->id
            ]);

            return json_encode(['status' => 'OK', 'result' => 'User added to task group successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function createTask(Request $request) {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');

            if (isset($inputs['group'])) {
                $taskGroup = new TaskGroup($inputs['group']);
                if ($this->checkAccessibility($inputs['group']) != 1)
                    return json_encode(['status' => 'ERROR', 'error' => 'User does\'nt have access to add task in this task group']);
            }

            $task = Task::create([
                'title' => $inputs['title'],
                'priority' => $inputs['priority'] ?? null,
                'date' => $inputs['date'] ?? null,
                'description' => $inputs['description'] ?? null,
                'is_subtask' => $inputs['subtask'] ?? null,
                'user' => \Auth::id(),
                'group' => $inputs['group'] ?? null
            ]);

            Log::create([
                'section' => $task->id,
                'type' => 1,
                'user' => \Auth::id(),
                'text' => '<a href="' . \Auth::id() . '">' . \Auth::user()->username . '</a> created a task' . (isset($taskGroup)? ' in ' . $taskGroup->id . ' task group.': '')
            ]);

            return json_encode(['status' => 'OK', 'result' => 'Task created']);
        }

        return json_encode($this->banMessage);
    }

    public function editTask(Request $request, int $id) {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');

            if (isset($inputs['group'])) {
                $taskGroup = new TaskGroup($inputs['group']);

                if ($this->checkAccessibility($inputs['group']))
                    return json_encode(['status' => 'ERROR', 'error' => 'User does\'nt have access to edit task in this task group']);
            }

            $task = new Task($id);

            Task::where('id', '=', $id)
                ->update([
                    'title' => $inputs['title'],
                    'priority' => $inputs['priority'] ?? $task->priority,
                    'date' => $inputs['date'] ?? $task->date,
                    'description' => $inputs['description'] ?? $task->description,
                    'is_subtask' => $inputs['subtask'] ?? $task->is_subtask,
                    'group' => $taskGroup->id ?? $task->group
                ]);

            Log::create([
                'section' => $id,
                'type' => 1,
                'user' => \Auth::id(),
                'text' => '<a href="' . \Auth::id() . '">' . \Auth::user()->username . '</a> edited a task' . (isset($taskGroup)? ' in ' . $taskGroup->title . ' task group': '')
            ]);

            return json_encode(['status' => 'OK', 'result' => 'Task edited successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function deleteTask($id) {
        if (!$this->isBan()) {
            $task = new Task($id);

            if (!is_null($task->group)) {
                $checkAccessibility = TaskGroupUser::where('user', '=', \Auth::id())
                    ->where('task_group', '=', $task->group)
                    ->count();
                $taskGroup = new TaskGroup($task->group);
                if ($checkAccessibility == 1)
                    Task::where('id', '=', $id)
                        ->delete();
                else
                    return json_encode(['status' => 'ERROR', 'error' => 'User does\'nt have access to delete task in this task group']);
            }
            elseif ($task->user == \Auth::id())
                Task::where('id', '=', $id)
                    ->delete();
            else
                return json_encode(['status' => 'ERROR', 'error' => 'User does\'nt have access to delete task in this task group']);

            Log::create([
                'section' => 0,
                'type' => 1,
                'user' => \Auth::id(),
                'text' => '<a href="' . \Auth::id() . '">' . \Auth::user()->username . '</a> deleted a task' . (isset($checkAccessibility)? ' in ' . $taskGroup->title . ' task group': '')
            ]);

            return json_encode(['status' => 'OK', 'result' => 'task deleted successfully']);
        }

        return json_encode($this->banMessage);
    }
}

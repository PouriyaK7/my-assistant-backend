<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\User;
use App\Models\UserEducation;
use App\Models\UserJobHistory;
use App\Models\UserSkill;
use Illuminate\Http\Request;

class CvBuilderController extends Controller
{
    protected $banMessage = ['status' => 'ERROR', 'error' => 'You are ban or logged out']
    , $noCV = ['status' => 'ERROR', 'error' => 'User does\'nt have CV'];

    protected function isBan()
    {
        if (\Auth::check())
            return \Auth::user()->ban;

        return 0;
    }

    protected function hasCV()
    {
        if (!is_null(\Auth::user()->soldiery_situation))
            return true;

        return false;
    }

    public function showCV()
    {
        if (!$this->isBan()) {
            if ($this->hasCV()) {
                $CV = [
                    'general' => (array)new User(\Auth::id()),
                    'education' => UserEducation::where('user', '=', \Auth::id())->get(),
                    'job_history' => UserJobHistory::where('user', '=', \Auth::id())->get()
                ];
                return json_encode(['status' => 'OK', 'result' => $CV]);
            }
            else
                return json_encode($this->noCV);
        }

        return json_encode($this->banMessage);
    }

    public function updateCV(Request $request)
    {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');
            User::where('id', '=', \Auth::id())
                ->update([
                    'bio' => $inputs['bio'] ?? \Auth::user()->bio ?? null,
                    'address' => $inputs['address'] ?? \Auth::user()->address ?? null,
                    'spotify' => $inputs['spotify'] ?? \Auth::user()->spotify ?? null,
                    'twitter' => $inputs['twitter'] ?? \Auth::user()->twitter ?? null,
                    'discord' => $inputs['discord'] ?? \Auth::user()->discord ?? null,
                    'instagram' => $inputs['instagram'] ?? \Auth::user()->instagram ?? null,
                    'github' => $inputs['github'] ?? \Auth::user()->github ?? null,
                    'gitlab' => $inputs['gitlab'] ?? \Auth::user()->gitlab ?? null,
                    'phone_number' => $inputs['phone_number'] ?? \Auth::user()->phone_number ?? null,
                    'telegram' => $inputs['telegram'] ?? \Auth::user()->telegram ?? null,
                    'relationship_status' => $inputs['relationship_status'] ?? \Auth::user()->relationshp_status ?? null,
                    'age' => $inputs['age'] ?? \Auth::user()->age ?? null,
                    'gender' => $inputs['gender'] ?? \Auth::user()->gender ?? null,
                    'job_samples' => $inputs['job_samples'] ?? \Auth::user()->job_samples ?? null,
                    'projects' => $inputs['projects'] ?? \Auth::user()->projects ?? null,
                    'birth_date' => $inputs['birth_date'] ?? \Auth::user()->birth_date ?? null,
                    'soldiery_situation' => $inputs['soldiery_situation'] ?? \Auth::user()->soldiery_situation ?? null,
                    'status' => $inputs['status'] ?? \Auth::user()->status ?? null,
                    'education_status' => $inputs['education_status'] ?? \Auth::user()->education_status ?? null,
                    'office_number' => $inputs['office_number'] ?? \Auth::user()->office_number ?? null,
                ]);

            return json_encode(['status' => 'OK', 'result' => 'User credentials updated successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function addUserEducation(Request $request) {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');

            $education = UserEducation::create([
                    'title' => $inputs['title'],
                    'school_name' => $inputs['school_name'],
                    'period' => $inputs['period'],
                    'major' => $inputs['major'] ?? null,
                    'start_year' => $inputs['start_year'],
                    'end_year' => $inputs['end_year'] ?? null,
                    'user' => \Auth::id(),
                    'term' => $inputs['term'] ?? null
                ]);

            return json_encode(['status' => 'OK', 'result' => 'User education added successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function editUserEducation(Request $request, $id) {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');

            if (UserEducation::where('id', '=', $id)->count() != 1)
                return json_encode(['status' => 'ERROR', 'error' => 'Education does not exists!']);

            $education = new UserEducation($id);

            UserEducation::where('id', '=', $id)
                ->update([
                    'title' => $inputs['title'],
                    'school_name' => $inputs['school_name'],
                    'period' => $inputs['period'],
                    'major' => $inputs['major'] ?? $education->id ?? null,
                    'start_year' => $inputs['start_year'],
                    'end_year' => $inputs['end_year'] ?? $education->end_year ?? null,
                    'term' => $inputs['term'] ?? $education->term ?? null
                ]);

            return json_encode(['status' => 'OK', 'result' => 'User education updated successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function deleteUserEducation($id) {
        if (!$this->isBan()) {
            UserEducation::where('id', '=', $id)
                ->where('user', '=', \Auth::id())
                ->delete();

            return json_encode(['status' => 'OK', 'result' => 'User education deleted successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function addUserSkill($skill) {
        if (!$this->isBan()) {
            $skill = Skill::where('id', '=', $skill)
                ->get();
            if (count($skill) != 1)
                return json_encode(['status' => 'ERROR', 'error' => 'Skill not found!']);

            UserSkill::create([
                'user' => \Auth::id(),
                'skill' => $skill['id']
            ]);

            return json_encode(['status' => 'OK', 'result' => 'User skill added successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function deleteUserSkill($skill) {
        if (!$this->isBan()) {
            UserSkill::where('user', '=', \Auth::id())
                ->where('skill', '=', $skill)
                ->delete();

            return json_encode(['status' => 'OK', 'result' => 'User skill deleted successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function addUserJobHistory(Request $request) {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');

            UserJobHistory::create([
                'title' => $inputs['title'],
                'roles' => $inputs['roles'],
                'start_year' => $inputs['start_year'],
                'end_year' => $inputs['end_year'] ?? null,
                'start_month' => $inputs['start_month'] ?? null,
                'end_month' => $inputs['end_month'] ?? null,
                'awards' => $inputs['awards'],
                'user' => \Auth::id()
            ]);

            return json_encode(['status' => 'OK', 'result' => 'User job history added successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function editUserJobHistory(Request $request, $id) {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');
            $jobHistory = UserJobHistory::where('id', '=', $id)
                ->get();

            if (count($jobHistory) != 1)
                return json_encode(['status' => 'ERROR', 'error' => 'This job history does not exist!']);

            UserJobHistory::where('id', '=', $id)
                ->update([
                    'title' => $inputs['title'],
                    'roles' => $inputs['roles'],
                    'start_year' => $inputs['start_year'],
                    'end_year' => $inputs['end_year'] ?? $jobHistory['end_year'] ?? null,
                    'start_month' => $inputs['start_month'] ?? $jobHistory['start_month'] ?? null,
                    'end_month' => $inputs['end_month'] ?? $jobHistory['end_month'] ?? null,
                    'awards' => $inputs['awards']
                ]);

            return json_encode(['status' => 'OK', 'result' => 'User job history edited successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function deleteUserJobHistory($id) {
        if (!$this->isBan()) {
            UserJobHistory::where('id', '=', $id)
                ->where('user', '=', \Auth::id())
                ->delete();

            return json_encode(['status' => 'OK', 'result' => 'User job history deleted successfully']);
        }

        return json_encode($this->banMessage);
    }
}

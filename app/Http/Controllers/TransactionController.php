<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $banMessage = ['status' => 'ERROR', 'error' => 'You are ban or logged out'];

    protected function isBan() {
        if (\Auth::check())
            return \Auth::user()->ban;

        return 0;
    }

    public function addTransaction(Request $request) {
        if (!$this->isBan()) {
            $inputs = $request->except('_token');

            $transaction = Transaction::create([
                'type' => $inputs['type'],
                'description' => $inputs['description'],
                'category' => $inputs['category'],
                'amount' => $inputs['amount'],
                'user' => \Auth::id()
            ]);

            if ($inputs['type'])
                \Auth::user()->expenses += $inputs['amount'];
            else
                \Auth::user()->incomes += $inputs['amount'];
            \Auth::user()->save();

            Log::create([
                'section' => $transaction->id,
                'type' => 2,
                'user' => \Auth::id(),
                'text' => '<a href="'.\Auth::id().'">'.\Auth::user()->username.'</a> added ' . number_format($inputs['amount']) . 'T to ' . (\Auth::user()->gender? 'her': 'his') . ' ' . ($inputs['type']? 'expenses': 'incomes') . '.'
            ]);

            return json_encode(['status' => 'OK', 'result' => 'Transaction added successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function editTransaction(Request $request, $id) {
        if (!$this->isBan()) {
            $transaction = new Transaction($id);
            if ($transaction->user != \Auth::id())
                return json_encode(['status' => 'ERROR', 'error' => 'User does\'nt have access to this transaction']);

            $inputs = $request->except('_token');
            $user = new User(\Auth::id());

            if ($transaction->type != $inputs['type']) {
                if ($inputs['type']) {
                    $user->incomes -= $transaction->amount;
                    $user->expenses += $transaction->amount;
                }
                else {
                    $user->incomes += $transaction->amount;
                    $user->expenses -= $transaction->amount;
                }
            }
            $user->save();

            Transaction::where('id', '=', $id)
                ->update([
                    'type' => $inputs['type'],
                    'description' => $inputs['description'],
                    'category' => $inputs['category'],
                    'amount' => $inputs['amount'],
                ]);

            Log::create([
                'section' => $id,
                'type' => 2,
                'user' => \Auth::id(),
                'text' => '<a href="'.\Auth::id().'">'.\Auth::user()->username.'</a> edited a transaction'
            ]);

            return json_encode(['status' => 'OK', 'result' => 'Transaction edited successfully']);
        }

        return json_encode($this->banMessage);
    }

    public function deleteTransaction($id) {
        if (!$this->isBan()) {
            $transaction = new Transaction($id);
            if ($transaction->user != \Auth::id())
                return json_encode(['status' => 'ERROR', 'error' => 'User does\'nt have access to delete this transaction']);

            if ($transaction->type)
                \Auth::user()->expenses -= $transaction->amount;
            else
                \Auth::user()->incomes -= $transaction->amount;

            \Auth::user()->save();

            $transaction->delete();

            Log::create([
                'section' => 0,
                'type' => 2,
                'user' => \Auth::id(),
                'text' => '<a href="'.\Auth::id().'">'.\Auth::user()->username.'</a> deleted a transaction'
            ]);

            return json_encode(['status' => 'OK', 'result' => 'Transaction deleted successfully']);
        }

        return json_encode($this->banMessage);
    }
}

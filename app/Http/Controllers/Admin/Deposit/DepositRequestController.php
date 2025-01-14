<?php

namespace App\Http\Controllers\Admin\Deposit;

use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\User;
use App\Models\WithDrawRequest;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositRequestController extends Controller
{
    public function index()
    {
        $deposits = DepositRequest::with(['user', 'userPayment'])->where('agent_id', Auth::id())->get();

        return view('admin.deposit_request.index', compact('deposits'));
    }

    public function show($id)
    {
        $deposit = DepositRequest::find($id);
        return view('admin.deposit_request.show', compact('deposit'));
    }

    public function statusChange(Request $request, DepositRequest $deposit)
    {

        $request->validate([
            'status' => 'required|in:0,1,2',
        ]);

        try {
            $agent = Auth::user();
            $player = User::find($request->player);
            if ($agent->balanceFloat < $request->amount) {
                return redirect()->back()->with('error', 'You do not have enough balance to transfer!');
            }

            $deposit->update([
                'status' => $request->status,
            ]);

             if ($request->status == 1) {
            app(WalletService::class)->transfer($agent, $player, $request->amount, TransactionName::DebitTransfer);
        }

            // app(WalletService::class)->transfer($agent, $player, $request->amount, TransactionName::DebitTransfer);

            return redirect()->route('admin.agent.deposit')->with('success', 'Agent status switch successfully!');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // amk
    public function statusChangeIndex(Request $request, DepositRequest $deposit)
{
    $request->validate([
        'status' => 'required|in:0,1,2',
        'amount' => 'required|numeric|min:0',
        'player' => 'required|exists:users,id',
    ]);

    try {
        $agent = Auth::user();
        $player = User::find($request->player);

        // Check if the status is being approved and balance is sufficient
        if ($request->status == 1 && $agent->balance < $request->amount) {
            return redirect()->back()->with('error', 'You do not have enough balance to transfer!');
        }

        // Update the deposit status
        $deposit->update([
            'status' => $request->status,
        ]);

        // Transfer the amount if the status is approved
        if ($request->status == 1) {
            app(WalletService::class)->transfer($agent, $player, $request->amount, TransactionName::DebitTransfer);
        }

        return redirect()->route('admin.agent.deposit')->with('success', 'Deposit status updated successfully!');
    } catch (Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}

public function statusChangeReject(Request $request, DepositRequest $deposit)
{
    $request->validate([
        'status' => 'required|in:0,1,2',
    ]);

    try {
        // Update the deposit status
        $deposit->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.agent.deposit')->with('success', 'Deposit status updated successfully!');
    } catch (Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}


}

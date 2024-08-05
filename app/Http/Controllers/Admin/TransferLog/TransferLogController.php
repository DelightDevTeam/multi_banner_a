<?php

namespace App\Http\Controllers\Admin\TransferLog;

use App\Http\Controllers\Controller;
use App\Models\Admin\TransferLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class TransferLogController extends Controller
{
    public function index()
    {
        $this->authorize('transfer_log', User::class);
        $transferLogs = Auth::user()->transactions()->with('targetUser')->latest()->paginate();

        return view('admin.trans_log.index', compact('transferLogs'));
    }

    // public function transferLog($id)
    // {
    //     abort_if(
    //         Gate::denies('make_transfer') || !$this->ifChildOfParent(request()->user()->id, $id),
    //         Response::HTTP_FORBIDDEN,
    //         '403 Forbidden |You cannot  Access this page because you do not have permission'
    //     );
    //     $transferLogs = Auth::user()->transactions()->with("targetUser")->where('target_user_id', $id)->latest()->paginate();

    //     return view('admin.trans_log.detail', compact('transferLogs'));
    // }
    // In your transferLog method
    public function transferLog($id)
    {
        abort_if(
            Gate::denies('make_transfer') || ! $this->ifChildOfParent(request()->user()->id, $id),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden | You cannot access this page because you do not have permission'
        );

        $transferLogs = Auth::user()->transactions()->with('targetUser')->where('target_user_id', $id)->latest()->paginate();

        // Log the transactions for debugging
        Log::info($transferLogs->toArray());

        return view('admin.trans_log.detail', compact('transferLogs'));
    }
}

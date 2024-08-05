<?php

namespace App\Http\Controllers;

use App\Enums\TransactionName;
use App\Models\Admin\UserLog;
use App\Models\SeamlessTransaction;
use App\Models\User;
use App\Services\WalletService;
use App\Settings\AppSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('Admin');
        $getUserCounts = $this->getUserCounts($isAdmin, $user);
        $agent_count = $getUserCounts('Agent');
        $player_count = $getUserCounts('Player');
        $totalDeposit = $this->getTotalDeposit();
        $totalWithdraw = $this->getTotalWithdraw();
        $todayDeposit = $this->getTodayDeposit();
        $todayWithdraw = $this->getTodayWithdraw();
        $provider_balance = (new AppSetting)->provider_initial_balance + SeamlessTransaction::sum('transaction_amount');

        return view('admin.dashboard', compact(
            'provider_balance',
            'agent_count',
            'player_count',
            'user',
            'totalDeposit',
            'totalWithdraw',
            'todayDeposit',
            'todayWithdraw'
        ));
    }

    public function balanceUp(Request $request)
    {
        abort_if(
            Gate::denies('admin_access'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );
        $request->validate([
            'balance' => 'required|numeric',
        ]);

        app(WalletService::class)->deposit($request->user(), $request->balance, TransactionName::CapitalDeposit);

        return back()->with('success', 'Add New Balance Successfully.');
    }

    public function logs($id)
    {
        $logs = UserLog::with('user')->where('user_id', $id)->get();

        return view('admin.logs', compact('logs'));
    }

    private function  getTodayWithdraw()
    {
        return Auth::user()->transactions()->with('targetUser')->select(
                DB::raw('SUM(transactions.amount) as amount'))
                ->where('transactions.name', 'debit_transfer')
                ->where('transactions.type', 'withdraw')
                ->whereDate('created_at', date('Y-m-d'))
                ->first();
    }

    private  function getTodayDeposit()
    {
        return Auth::user()->transactions()->with('targetUser')
                ->select(DB::raw('SUM(transactions.amount) as amount'))
                ->where('transactions.name', 'credit_transfer')
                ->where('transactions.type', 'deposit')
                ->whereDate('created_at', date('Y-m-d'))
                ->first();
    }

    private  function getTotalWithdraw()
    {
        return Auth::user()->transactions()->with('targetUser')->select(
                DB::raw('SUM(transactions.amount) as amount'))
                ->where('transactions.name', 'debit_transfer')
                ->where('transactions.type', 'withdraw')
                ->first();
    }

    private  function getTotalDeposit()
    {
        return Auth::user()->transactions()->with('targetUser')
                ->select(DB::raw('SUM(transactions.amount) as amount'))
                ->where('transactions.name', 'credit_transfer')
                ->where('transactions.type', 'deposit')
                ->first();
    }

    private  function getUserCounts($isAdmin, $user)
    {
        return function ($roleTitle) use ($isAdmin, $user) {
                return User::whereHas('roles', function ($query) use ($roleTitle) {
                    $query->where('title', '=', $roleTitle);
                })->when(!$isAdmin, function ($query) use ($user) {
                    $query->where('agent_id', $user->id);
                })->count();
        };
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Player;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WithdrawRequest;
use App\Http\Resources\HistoryResource;
use App\Models\WithDrawRequest as ModelsWithDrawRequest;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Support\Facades\Auth;

class WithDrawRequestController extends Controller
{
    use HttpResponses;

    public function withdraw(WithdrawRequest $request)
    {
        try {
            $inputs = $request->validated();
            $player = Auth::user();
            if($request->amount < $player->balance){
                $withdraw = ModelsWithDrawRequest::create(array_merge(
                    $inputs,
                    ['user_id' => $player->id, 'agent_id' => $player->agent_id]
                ));
                return $this->success($withdraw, 'Withdraw Request Success');
            }else{
                return $this->error('', 'Insufficient Balance',401);
            }

        } catch (Exception $e) {
            $this->error('', $e->getMessage(), 401);
        }
    }

    public function log()
    {
        $withdraw = ModelsWithDrawRequest::where('user_id', Auth::id())->get();

        return $this->success(HistoryResource::collection($withdraw));
        // return $this->success($withdraw);
    }
}

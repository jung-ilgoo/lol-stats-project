<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Services\TeamBalanceService;

class TeamBalanceController extends Controller
{
    protected $teamBalanceService;
    
    public function __construct(TeamBalanceService $teamBalanceService)
    {
        $this->teamBalanceService = $teamBalanceService;
    }
    
    /**
     * 팀 밸런스 계산 메인 페이지
     */
    public function index()
    {
        $players = Player::orderBy('player_name')->get();
        
        return view('team-balance.index', compact('players'));
    }
    
    /**
     * 팀 밸런스 계산 실행
     */
    public function calculate(Request $request)
    {
        // 유효성 검사
        $validated = $request->validate([
            'players' => 'required|array|size:10',
            'players.*' => 'required|exists:players,id',
            'positions' => 'sometimes|array|size:10',
            'balance_type' => 'required|in:win_rate,position'
        ]);
        
        // 선택된 플레이어
        $playerIds = $validated['players'];
        
        // 팀 밸런스 계산 (포지션 고려 여부에 따라 다른 메소드 호출)
        if ($request->has('positions') && $validated['balance_type'] == 'position') {
            $result = $this->teamBalanceService->calculateTeamBalanceWithPositions($playerIds, $validated['positions']);
        } else {
            $result = $this->teamBalanceService->calculateTeamBalance($playerIds);
        }
        
        // 오류 확인
        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }
        
        // 선택된 플레이어 정보 가져오기
        $players = Player::whereIn('id', $playerIds)->get()->keyBy('id');
        
        // 팀 정보에 플레이어 세부 정보 추가
        foreach ($result['blue_team'] as &$player) {
            $player['details'] = $players[$player['player']->id] ?? null;
        }
        
        foreach ($result['red_team'] as &$player) {
            $player['details'] = $players[$player['player']->id] ?? null;
        }
        
        return view('team-balance.result', [
            'result' => $result,
            'balanceType' => $validated['balance_type']
        ]);
    }
}
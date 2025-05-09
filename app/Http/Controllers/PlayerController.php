<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlayerController extends Controller
{
    // index 메서드
    public function index()
    {
        $players = Player::withCount('matches')
            ->selectRaw('players.*, 
                        (SELECT COUNT(*) FROM match_player 
                        WHERE match_player.player_id = players.id 
                        AND match_player.team = 
                            (SELECT winner FROM matches WHERE matches.id = match_player.match_id)) 
                        as wins,
                        (SELECT AVG((kills + assists) / GREATEST(deaths, 1)) 
                        FROM match_player 
                        WHERE match_player.player_id = players.id) as kda')
            ->selectRaw('CASE WHEN matches_count > 0 THEN wins / matches_count ELSE 0 END as win_rate')
            ->paginate(10);
        
        return view('players.index', compact('players'));
    }

    // create 메서드
    public function create()
    {
        return view('players.create');
    }

    // store 메서드
    public function store(Request $request)
    {
        $request->validate([
            'player_name' => 'required|unique:players,player_name|max:255',
            'rank' => 'required',
            'main_positions' => 'required',
        ]);
        
        Player::create($request->all());
        
        return redirect()->route('players.index')
            ->with('success', '플레이어가 성공적으로 등록되었습니다.');
    }

    // show 메서드
    public function show($id)
    {
        $player = Player::findOrFail($id);
        
        // StatisticsService를 이용하여 통계 데이터 가져오기
        $statistics = app(StatisticsService::class)->getPlayerStatistics($id);
        
        // 최근 경기 기록 가져오기
        $matches = DB::table('match_player')
            ->join('matches', 'match_player.match_id', '=', 'matches.id')
            ->where('match_player.player_id', $id)
            ->select('match_player.*', 'matches.match_date', 'matches.winner')
            ->orderBy('matches.match_date', 'desc')
            ->paginate(10);
        
        return view('players.show', compact('player', 'statistics', 'matches'));
    }

    // edit 메서드
    public function edit($id)
    {
        $player = Player::findOrFail($id);
        return view('players.edit', compact('player'));
    }

    // update 메서드
    public function update(Request $request, $id)
    {
        $request->validate([
            'player_name' => 'required|max:255|unique:players,player_name,'.$id,
            'rank' => 'required',
            'main_positions' => 'required',
        ]);
        
        $player = Player::findOrFail($id);
        $player->update($request->all());
        
        return redirect()->route('players.index')
            ->with('success', '플레이어 정보가 성공적으로 업데이트되었습니다.');
    }

    // destroy 메서드
    public function destroy($id)
    {
        $player = Player::findOrFail($id);
        
        // 관련된 경기 기록이 있는지 확인
        $matchCount = DB::table('match_player')->where('player_id', $id)->count();
        
        if ($matchCount > 0) {
            return redirect()->route('players.index')
                ->with('error', '이 플레이어와 관련된 경기 기록이 있어 삭제할 수 없습니다.');
        }
        
        $player->delete();
        
        return redirect()->route('players.index')
            ->with('success', '플레이어가 성공적으로 삭제되었습니다.');
    }
}

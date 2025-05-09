<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChampionController extends Controller
{
    // index 메서드
    public function index()
    {
        $champions = Champion::leftJoin('match_player', 'champions.id', '=', 'match_player.champion_id')
            ->leftJoin('matches', 'match_player.match_id', '=', 'matches.id')
            ->select('champions.*')
            ->selectRaw('COUNT(DISTINCT match_player.id) as usage_count')
            ->selectRaw('SUM(CASE WHEN match_player.team = matches.winner THEN 1 ELSE 0 END) as wins')
            ->selectRaw('AVG((match_player.kills + match_player.assists) / GREATEST(match_player.deaths, 1)) as kda')
            ->selectRaw('CASE WHEN COUNT(DISTINCT match_player.id) > 0 
                            THEN SUM(CASE WHEN match_player.team = matches.winner THEN 1 ELSE 0 END) / COUNT(DISTINCT match_player.id) 
                            ELSE 0 END as win_rate')
            ->groupBy('champions.id')
            ->paginate(10);
        
        return view('champions.index', compact('champions'));
    }

    // create 메서드
    public function create()
    {
        return view('champions.create');
    }

    // store 메서드
    public function store(Request $request)
    {
        $request->validate([
            'champion_name' => 'required|unique:champions,champion_name|max:255',
        ]);
        
        Champion::create($request->all());
        
        return redirect()->route('champions.index')
            ->with('success', '챔피언이 성공적으로 등록되었습니다.');
    }

    // show 메서드
    public function show($id)
    {
        $champion = Champion::findOrFail($id);
        
        // StatisticsService를 이용하여 통계 데이터 가져오기
        $statistics = app(StatisticsService::class)->getChampionStatistics($id);
        
        // 최근 사용 기록 가져오기
        $matches = DB::table('match_player')
            ->join('matches', 'match_player.match_id', '=', 'matches.id')
            ->join('players', 'match_player.player_id', '=', 'players.id')
            ->where('match_player.champion_id', $id)
            ->select(
                'match_player.*', 
                'matches.match_date', 
                'matches.winner',
                'players.player_name'
            )
            ->orderBy('matches.match_date', 'desc')
            ->paginate(10);
        
        return view('champions.show', compact('champion', 'statistics', 'matches'));
    }

    // edit 메서드
    public function edit($id)
    {
        $champion = Champion::findOrFail($id);
        return view('champions.edit', compact('champion'));
    }

    // update 메서드
    public function update(Request $request, $id)
    {
        $request->validate([
            'champion_name' => 'required|max:255|unique:champions,champion_name,'.$id,
        ]);
        
        $champion = Champion::findOrFail($id);
        $champion->update($request->all());
        
        return redirect()->route('champions.index')
            ->with('success', '챔피언 정보가 성공적으로 업데이트되었습니다.');
    }

    // destroy 메서드
    public function destroy($id)
    {
        $champion = Champion::findOrFail($id);
        
        // 관련된 경기 기록이 있는지 확인
        $matchCount = DB::table('match_player')->where('champion_id', $id)->count();
        
        if ($matchCount > 0) {
            return redirect()->route('champions.index')
                ->with('error', '이 챔피언과 관련된 경기 기록이 있어 삭제할 수 없습니다.');
        }
        
        $champion->delete();
        
        return redirect()->route('champions.index')
            ->with('success', '챔피언이 성공적으로 삭제되었습니다.');
    }
}

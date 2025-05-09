<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MatchController extends Controller
{
    // index 메서드
public function index(Request $request)
{
    $query = Match::query()->with(['bluePlayers.player', 'redPlayers.player']);
    
    // 날짜 필터링
    if ($request->has('date_from') && $request->date_from) {
        $query->where('match_date', '>=', $request->date_from);
    }
    
    if ($request->has('date_to') && $request->date_to) {
        $query->where('match_date', '<=', $request->date_to);
    }
    
    $matches = $query->orderBy('match_date', 'desc')->paginate(10);
    
    return view('matches.index', compact('matches'));
}

// create 메서드
public function create()
{
    $players = Player::orderBy('player_name')->get();
    $champions = Champion::orderBy('champion_name')->get();
    
    return view('matches.create', compact('players', 'champions'));
}

// store 메서드
public function store(Request $request)
{
    // 기본 경기 정보 유효성 검사
    $request->validate([
        'match_date' => 'required|date',
        'winner' => 'required|in:blue,red',
    ]);
    
    // 플레이어와 챔피언 데이터 유효성 검사
    for ($i = 1; $i <= 5; $i++) {
        $request->validate([
            'blue_player_'.$i => 'required|exists:players,id',
            'blue_champion_'.$i => 'required|exists:champions,id',
            'blue_kills_'.$i => 'required|integer|min:0',
            'blue_deaths_'.$i => 'required|integer|min:0',
            'blue_assists_'.$i => 'required|integer|min:0',
            'red_player_'.$i => 'required|exists:players,id',
            'red_champion_'.$i => 'required|exists:champions,id',
            'red_kills_'.$i => 'required|integer|min:0',
            'red_deaths_'.$i => 'required|integer|min:0',
            'red_assists_'.$i => 'required|integer|min:0',
        ]);
    }
    
    // 중복 플레이어 체크
    $bluePlayers = [];
    $redPlayers = [];
    
    for ($i = 1; $i <= 5; $i++) {
        $bluePlayer = $request->input('blue_player_'.$i);
        $redPlayer = $request->input('red_player_'.$i);
        
        if (in_array($bluePlayer, $bluePlayers)) {
            return redirect()->back()->withInput()->with('error', '블루팀에 중복된 플레이어가 있습니다.');
        }
        
        if (in_array($redPlayer, $redPlayers)) {
            return redirect()->back()->withInput()->with('error', '레드팀에 중복된 플레이어가 있습니다.');
        }
        
        if (in_array($bluePlayer, $redPlayers) || in_array($redPlayer, $bluePlayers)) {
            return redirect()->back()->withInput()->with('error', '동일한 플레이어가 양팀에 모두 있습니다.');
        }
        
        $bluePlayers[] = $bluePlayer;
        $redPlayers[] = $redPlayer;
    }
    
    // 경기 생성
    $match = Match::create([
        'match_date' => $request->match_date,
        'winner' => $request->winner,
    ]);
    
    // 플레이어 데이터 저장
    for ($i = 1; $i <= 5; $i++) {
        // 블루팀 플레이어
        $match->players()->attach($request->input('blue_player_'.$i), [
            'team' => 'blue',
            'position' => $i,
            'champion_id' => $request->input('blue_champion_'.$i),
            'kills' => $request->input('blue_kills_'.$i),
            'deaths' => $request->input('blue_deaths_'.$i),
            'assists' => $request->input('blue_assists_'.$i),
        ]);
        
        // 레드팀 플레이어
        $match->players()->attach($request->input('red_player_'.$i), [
            'team' => 'red',
            'position' => $i,
            'champion_id' => $request->input('red_champion_'.$i),
            'kills' => $request->input('red_kills_'.$i),
            'deaths' => $request->input('red_deaths_'.$i),
            'assists' => $request->input('red_assists_'.$i),
        ]);
    }
    
    return redirect()->route('matches.show', $match->id)
        ->with('success', '경기가 성공적으로 기록되었습니다.');
}

// show 메서드
public function show($id)
{
    $match = Match::findOrFail($id);
    
    // 블루팀과 레드팀 플레이어 데이터
    $bluePlayers = $match->matchPlayers()
        ->where('team', 'blue')
        ->orderBy('position')
        ->with('player', 'champion')
        ->get()
        ->keyBy('position');
        
    $redPlayers = $match->matchPlayers()
        ->where('team', 'red')
        ->orderBy('position')
        ->with('player', 'champion')
        ->get()
        ->keyBy('position');
    
    // 팀별 통계 계산
    $blueStats = [
        'kills' => $bluePlayers->sum('kills'),
        'deaths' => $bluePlayers->sum('deaths'),
        'assists' => $bluePlayers->sum('assists'),
    ];
    
    $redStats = [
        'kills' => $redPlayers->sum('kills'),
        'deaths' => $redPlayers->sum('deaths'),
        'assists' => $redPlayers->sum('assists'),
    ];
    
    // 이전/다음 경기
    $prevMatch = Match::where('match_date', '<=', $match->match_date)
        ->where('id', '<>', $match->id)
        ->orderBy('match_date', 'desc')
        ->orderBy('id', 'desc')
        ->first();
        
    $nextMatch = Match::where('match_date', '>=', $match->match_date)
        ->where('id', '<>', $match->id)
        ->orderBy('match_date', 'asc')
        ->orderBy('id', 'asc')
        ->first();
    
    return view('matches.show', compact(
        'match', 
        'bluePlayers', 
        'redPlayers', 
        'blueStats', 
        'redStats', 
        'prevMatch', 
        'nextMatch'
    ));
}

// edit 메서드
public function edit($id)
{
    $match = Match::findOrFail($id);
    $players = Player::orderBy('player_name')->get();
    $champions = Champion::orderBy('champion_name')->get();
    
    // 블루팀과 레드팀 플레이어 데이터
    $bluePlayers = $match->matchPlayers()
        ->where('team', 'blue')
        ->orderBy('position')
        ->get();
        
    $redPlayers = $match->matchPlayers()
        ->where('team', 'red')
        ->orderBy('position')
        ->get();
    
    return view('matches.edit', compact(
        'match', 
        'players', 
        'champions', 
        'bluePlayers', 
        'redPlayers'
    ));
}

// update 메서드
public function update(Request $request, $id)
{
    // 기본 경기 정보 유효성 검사
    $request->validate([
        'match_date' => 'required|date',
        'winner' => 'required|in:blue,red',
    ]);
    
    // 플레이어와 챔피언 데이터 유효성 검사
    for ($i = 1; $i <= 5; $i++) {
        $request->validate([
            'blue_player_'.$i => 'required|exists:players,id',
            'blue_champion_'.$i => 'required|exists:champions,id',
            'blue_kills_'.$i => 'required|integer|min:0',
            'blue_deaths_'.$i => 'required|integer|min:0',
            'blue_assists_'.$i => 'required|integer|min:0',
            'red_player_'.$i => 'required|exists:players,id',
            'red_champion_'.$i => 'required|exists:champions,id',
            'red_kills_'.$i => 'required|integer|min:0',
            'red_deaths_'.$i => 'required|integer|min:0',
            'red_assists_'.$i => 'required|integer|min:0',
        ]);
    }
    
    // 중복 플레이어 체크
    $bluePlayers = [];
    $redPlayers = [];
    
    for ($i = 1; $i <= 5; $i++) {
        $bluePlayer = $request->input('blue_player_'.$i);
        $redPlayer = $request->input('red_player_'.$i);
        
        if (in_array($bluePlayer, $bluePlayers)) {
            return redirect()->back()->withInput()->with('error', '블루팀에 중복된 플레이어가 있습니다.');
        }
        
        if (in_array($redPlayer, $redPlayers)) {
            return redirect()->back()->withInput()->with('error', '레드팀에 중복된 플레이어가 있습니다.');
        }
        
        if (in_array($bluePlayer, $redPlayers) || in_array($redPlayer, $bluePlayers)) {
            return redirect()->back()->withInput()->with('error', '동일한 플레이어가 양팀에 모두 있습니다.');
        }
        
        $bluePlayers[] = $bluePlayer;
        $redPlayers[] = $redPlayer;
    }
    
    // 경기 정보 업데이트
    $match = Match::findOrFail($id);
    $match->update([
        'match_date' => $request->match_date,
        'winner' => $request->winner,
    ]);
    
    // 기존 플레이어 데이터 삭제
    $match->players()->detach();
    
    // 플레이어 데이터 저장
    for ($i = 1; $i <= 5; $i++) {
        // 블루팀 플레이어
        $match->players()->attach($request->input('blue_player_'.$i), [
            'team' => 'blue',
            'position' => $i,
            'champion_id' => $request->input('blue_champion_'.$i),
            'kills' => $request->input('blue_kills_'.$i),
            'deaths' => $request->input('blue_deaths_'.$i),
            'assists' => $request->input('blue_assists_'.$i),
        ]);
        
        // 레드팀 플레이어
        $match->players()->attach($request->input('red_player_'.$i), [
            'team' => 'red',
            'position' => $i,
            'champion_id' => $request->input('red_champion_'.$i),
            'kills' => $request->input('red_kills_'.$i),
            'deaths' => $request->input('red_deaths_'.$i),
            'assists' => $request->input('red_assists_'.$i),
        ]);
    }
    
    return redirect()->route('matches.show', $match->id)
        ->with('success', '경기 정보가 성공적으로 업데이트되었습니다.');
}

// destroy 메서드
public function destroy($id)
{
    $match = Match::findOrFail($id);
    
    // 관련된 매치 플레이어 데이터 삭제
    $match->players()->detach();
    
    // 경기 삭제
    $match->delete();
    
    return redirect()->route('matches.index')
        ->with('success', '경기가 성공적으로 삭제되었습니다.');
}
}

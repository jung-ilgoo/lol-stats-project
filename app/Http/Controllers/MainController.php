<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Champion;
use App\Models\Match;

class MainController extends Controller
{
    /**
     * 홈페이지 표시
     */
    public function index()
    {
        // 최근 경기 기록 (최근 5개)
        $recentMatches = Match::orderBy('match_date', 'desc')->take(5)->get();
        
        // 승률 기준 탑 플레이어 (상위 5명)
        $topPlayers = Player::withCount(['matchesWon as matches_won_count', 'matches as matches_count'])
        ->selectRaw('players.*, 
                    (CASE WHEN matches_count > 0 
                        THEN matches_won_count / matches_count 
                        ELSE 0 END) as win_rate,
                    (SELECT AVG((kills + assists) / GREATEST(deaths, 1)) 
                    FROM match_player 
                    WHERE match_player.player_id = players.id) as kda')
        ->having('matches_count', '>', 0)
        ->orderBy('win_rate', 'desc')
        ->orderBy('kda', 'desc')
        ->take(5)
        ->get();
        
        // 통계 데이터
        $totalPlayers = Player::count();
        $totalMatches = Match::count();
        $totalChampions = Champion::count();
        
        return view('home', compact(
            'recentMatches', 
            'topPlayers', 
            'totalPlayers', 
            'totalMatches', 
            'totalChampions'
        ));
    }
}
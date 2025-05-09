<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StatisticsService;
use App\Models\Player;
use App\Models\Champion;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    protected $statisticsService;
    
    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }
    
    /**
     * 랭킹 메인 페이지
     */
    public function index()
    {
        // 플레이어 승률 랭킹 (상위 10명)
        $playerRankings = Player::withCount(['matchesWon', 'matchesPlayed'])
            ->selectRaw('players.*, 
                        (CASE WHEN matches_played_count > 0 
                            THEN matches_won_count / matches_played_count 
                            ELSE 0 END) as win_rate,
                        (SELECT AVG((kills + assists) / GREATEST(deaths, 1)) 
                        FROM match_player 
                        WHERE match_player.player_id = players.id) as kda')
            ->having('matches_played_count', '>', 0)
            ->orderBy('win_rate', 'desc')
            ->orderBy('kda', 'desc')
            ->take(10)
            ->get();
        
        // 챔피언 승률 랭킹 (상위 10개)
        $championRankings = Champion::leftJoin('match_player', 'champions.id', '=', 'match_player.champion_id')
            ->leftJoin('matches', 'match_player.match_id', '=', 'matches.id')
            ->select('champions.*')
            ->selectRaw('COUNT(DISTINCT match_player.id) as usage_count')
            ->selectRaw('SUM(CASE WHEN match_player.team = matches.winner THEN 1 ELSE 0 END) as wins')
            ->selectRaw('AVG((match_player.kills + match_player.assists) / GREATEST(match_player.deaths, 1)) as kda')
            ->selectRaw('CASE WHEN COUNT(DISTINCT match_player.id) > 0 
                            THEN SUM(CASE WHEN match_player.team = matches.winner THEN 1 ELSE 0 END) / COUNT(DISTINCT match_player.id) 
                            ELSE 0 END as win_rate')
            ->groupBy('champions.id')
            ->having('usage_count', '>', 0)
            ->orderBy('win_rate', 'desc')
            ->orderBy('kda', 'desc')
            ->take(10)
            ->get();
        
        return view('rankings.index', compact('playerRankings', 'championRankings'));
    }
    
    /**
     * 플레이어 랭킹 상세 페이지
     */
    public function players(Request $request)
    {
        $minGames = $request->get('min_games', 1);
        
        $rankings = Player::withCount(['matchesWon', 'matchesPlayed'])
            ->selectRaw('players.*, 
                        (CASE WHEN matches_played_count > 0 
                            THEN matches_won_count / matches_played_count 
                            ELSE 0 END) as win_rate,
                        (SELECT AVG((kills + assists) / GREATEST(deaths, 1)) 
                        FROM match_player 
                        WHERE match_player.player_id = players.id) as kda')
            ->having('matches_played_count', '>=', $minGames)
            ->orderBy('win_rate', 'desc')
            ->orderBy('kda', 'desc')
            ->paginate(15);
        
        // 포지션별 상위 플레이어 계산
        $topPlayersByPosition = $this->getTopPlayersByPosition();
        
        return view('rankings.players', compact('rankings', 'minGames', 'topPlayersByPosition'));
    }
    
    /**
     * 챔피언 랭킹 상세 페이지
     */
    public function champions(Request $request)
    {
        $minGames = $request->get('min_games', 1);
        
        $rankings = Champion::leftJoin('match_player', 'champions.id', '=', 'match_player.champion_id')
            ->leftJoin('matches', 'match_player.match_id', '=', 'matches.id')
            ->select('champions.*')
            ->selectRaw('COUNT(DISTINCT match_player.id) as usage_count')
            ->selectRaw('SUM(CASE WHEN match_player.team = matches.winner THEN 1 ELSE 0 END) as wins')
            ->selectRaw('AVG((match_player.kills + match_player.assists) / GREATEST(match_player.deaths, 1)) as kda')
            ->selectRaw('CASE WHEN COUNT(DISTINCT match_player.id) > 0 
                            THEN SUM(CASE WHEN match_player.team = matches.winner THEN 1 ELSE 0 END) / COUNT(DISTINCT match_player.id) 
                            ELSE 0 END as win_rate')
            ->groupBy('champions.id')
            ->having('usage_count', '>=', $minGames)
            ->orderBy('win_rate', 'desc')
            ->orderBy('kda', 'desc')
            ->paginate(15);
        
        // 포지션별 상위 챔피언 계산
        $topChampionsByPosition = $this->getTopChampionsByPosition();
        
        return view('rankings.champions', compact('rankings', 'minGames', 'topChampionsByPosition'));
    }
    
    /**
     * 포지션별 상위 플레이어 계산
     */
    private function getTopPlayersByPosition()
    {
        $positions = ['top', 'jungle', 'mid', 'adc', 'support'];
        $results = [];
        
        foreach ($positions as $position) {
            $results[$position] = DB::select("
                SELECT p.id, p.player_name, 
                       COUNT(*) as games_count,
                       SUM(CASE WHEN (mp.team = m.winner) THEN 1 ELSE 0 END) as wins,
                       (SUM(CASE WHEN (mp.team = m.winner) THEN 1 ELSE 0 END) / COUNT(*)) as win_rate,
                       AVG((mp.kills + mp.assists) / GREATEST(mp.deaths, 1)) as kda
                FROM players p
                JOIN match_player mp ON p.id = mp.player_id
                JOIN matches m ON mp.match_id = m.id
                WHERE mp.position = ?
                GROUP BY p.id, p.player_name
                HAVING COUNT(*) >= 3
                ORDER BY win_rate DESC, kda DESC
                LIMIT 3
            ", [$position]);
        }
        
        return $results;
    }
    
    /**
     * 포지션별 상위 챔피언 계산
     */
    private function getTopChampionsByPosition()
    {
        $positions = ['top', 'jungle', 'mid', 'adc', 'support'];
        $results = [];
        
        foreach ($positions as $position) {
            $results[$position] = DB::select("
                SELECT c.id, c.champion_name, 
                       COUNT(*) as games_count,
                       SUM(CASE WHEN (mp.team = m.winner) THEN 1 ELSE 0 END) as wins,
                       (SUM(CASE WHEN (mp.team = m.winner) THEN 1 ELSE 0 END) / COUNT(*)) as win_rate,
                       AVG((mp.kills + mp.assists) / GREATEST(mp.deaths, 1)) as kda
                FROM champions c
                JOIN match_player mp ON c.id = mp.champion_id
                JOIN matches m ON mp.match_id = m.id
                WHERE mp.position = ?
                GROUP BY c.id, c.champion_name
                HAVING COUNT(*) >= 3
                ORDER BY win_rate DESC, kda DESC
                LIMIT 3
            ", [$position]);
        }
        
        return $results;
    }
}
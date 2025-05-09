<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Champion;
use App\Models\Match;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * 플레이어 승률 기준 랭킹 조회
     */
    public function getPlayerRankingByWinRate()
    {
        $players = Player::all();
        $rankings = [];
        
        foreach ($players as $player) {
            $matches = $player->allMatches();
            if ($matches->count() > 0) {
                $winRate = $player->getWinRate();
                $rankings[] = [
                    'player' => $player,
                    'win_rate' => $winRate,
                    'matches_count' => $matches->count()
                ];
            }
        }
        
        // 승률 기준 내림차순 정렬
        usort($rankings, function ($a, $b) {
            return $b['win_rate'] <=> $a['win_rate'];
        });
        
        return $rankings;
    }
    
    /**
     * 챔피언 승률 기준 랭킹 조회
     */
    public function getChampionRankingByWinRate()
    {
        $champions = Champion::all();
        $rankings = [];
        
        foreach ($champions as $champion) {
            $matches = $champion->allMatches();
            if ($matches->count() > 0) {
                $winRate = $champion->getWinRate();
                $rankings[] = [
                    'champion' => $champion,
                    'win_rate' => $winRate,
                    'matches_count' => $matches->count()
                ];
            }
        }
        
        // 승률 기준 내림차순 정렬
        usort($rankings, function ($a, $b) {
            return $b['win_rate'] <=> $a['win_rate'];
        });
        
        return $rankings;
    }
    
    /**
     * 포지션별 플레이어 통계 조회
     */
    public function getPlayerStatsByPosition($playerId)
    {
        $player = Player::findOrFail($playerId);
        $positions = ['top', 'jungle', 'mid', 'adc', 'support'];
        $stats = [];
        
        foreach ($positions as $position) {
            $blueMatches = Match::where('blue_' . $position . '_player_id', $playerId)->get();
            $redMatches = Match::where('red_' . $position . '_player_id', $playerId)->get();
            
            $totalMatches = $blueMatches->count() + $redMatches->count();
            
            if ($totalMatches > 0) {
                $blueWins = $blueMatches->where('winner', 'blue')->count();
                $redWins = $redMatches->where('winner', 'red')->count();
                $totalWins = $blueWins + $redWins;
                
                $stats[$position] = [
                    'matches_count' => $totalMatches,
                    'wins' => $totalWins,
                    'losses' => $totalMatches - $totalWins,
                    'win_rate' => ($totalWins / $totalMatches) * 100
                ];
            } else {
                $stats[$position] = [
                    'matches_count' => 0,
                    'wins' => 0,
                    'losses' => 0,
                    'win_rate' => 0
                ];
            }
        }
        
        return $stats;
    }
    
    /**
     * 플레이어의 챔피언별 통계 조회
     */
    public function getPlayerChampionStats($playerId)
    {
        $player = Player::findOrFail($playerId);
        $stats = [];
        
        // 블루팀에서 사용한 챔피언
        $blueChampions = DB::select("
            SELECT c.id, c.champion_name, COUNT(*) as count, 
                   SUM(CASE WHEN m.winner = 'blue' THEN 1 ELSE 0 END) as wins
            FROM matches m
            JOIN champions c ON 
                c.id = m.blue_top_champion_id OR
                c.id = m.blue_jungle_champion_id OR
                c.id = m.blue_mid_champion_id OR
                c.id = m.blue_adc_champion_id OR
                c.id = m.blue_support_champion_id
            WHERE 
                m.blue_top_player_id = ? OR
                m.blue_jungle_player_id = ? OR
                m.blue_mid_player_id = ? OR
                m.blue_adc_player_id = ? OR
                m.blue_support_player_id = ?
            GROUP BY c.id, c.champion_name
        ", [$playerId, $playerId, $playerId, $playerId, $playerId]);
        
        // 레드팀에서 사용한 챔피언
        $redChampions = DB::select("
            SELECT c.id, c.champion_name, COUNT(*) as count, 
                   SUM(CASE WHEN m.winner = 'red' THEN 1 ELSE 0 END) as wins
            FROM matches m
            JOIN champions c ON 
                c.id = m.red_top_champion_id OR
                c.id = m.red_jungle_champion_id OR
                c.id = m.red_mid_champion_id OR
                c.id = m.red_adc_champion_id OR
                c.id = m.red_support_champion_id
            WHERE 
                m.red_top_player_id = ? OR
                m.red_jungle_player_id = ? OR
                m.red_mid_player_id = ? OR
                m.red_adc_player_id = ? OR
                m.red_support_player_id = ?
            GROUP BY c.id, c.champion_name
        ", [$playerId, $playerId, $playerId, $playerId, $playerId]);
        
        // 챔피언별 통계 병합
        $champStats = [];
        
        foreach ($blueChampions as $champ) {
            if (!isset($champStats[$champ->id])) {
                $champStats[$champ->id] = [
                    'champion_id' => $champ->id,
                    'champion_name' => $champ->champion_name,
                    'count' => 0,
                    'wins' => 0
                ];
            }
            
            $champStats[$champ->id]['count'] += $champ->count;
            $champStats[$champ->id]['wins'] += $champ->wins;
        }
        
        foreach ($redChampions as $champ) {
            if (!isset($champStats[$champ->id])) {
                $champStats[$champ->id] = [
                    'champion_id' => $champ->id,
                    'champion_name' => $champ->champion_name,
                    'count' => 0,
                    'wins' => 0
                ];
            }
            
            $champStats[$champ->id]['count'] += $champ->count;
            $champStats[$champ->id]['wins'] += $champ->wins;
        }
        
        // 승률 계산 및 정렬
        foreach ($champStats as &$stat) {
            $stat['win_rate'] = $stat['count'] > 0 ? ($stat['wins'] / $stat['count']) * 100 : 0;
        }
        
        // 승률 기준 내림차순 정렬
        usort($champStats, function ($a, $b) {
            return $b['win_rate'] <=> $a['win_rate'];
        });
        
        return $champStats;
    }
}
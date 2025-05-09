<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Match;
use Illuminate\Support\Collection;

class TeamBalanceService
{
    /**
     * 승률 기반으로 팀 밸런스 계산
     */
    public function calculateTeamBalance(array $playerIds)
    {
        // 10명의 플레이어가 필요합니다
        if (count($playerIds) !== 10) {
            return [
                'error' => '정확히 10명의 플레이어가 필요합니다.'
            ];
        }
        
        $players = Player::whereIn('id', $playerIds)->get();
        
        // 모든 플레이어가 존재하는지 확인
        if ($players->count() !== 10) {
            return [
                'error' => '일부 플레이어를 찾을 수 없습니다.'
            ];
        }
        
        // 플레이어별 승률 계산
        $playerStats = [];
        foreach ($players as $player) {
            $playerStats[$player->id] = [
                'player' => $player,
                'win_rate' => $player->getWinRate()
            ];
        }
        
        // 승률 기준 내림차순 정렬
        uasort($playerStats, function ($a, $b) {
            return $b['win_rate'] <=> $a['win_rate'];
        });
        
        // 팀 구성 시뮬레이션 - 여러 조합 테스트
        $bestBalanceScore = PHP_INT_MAX;
        $bestBlueTeam = [];
        $bestRedTeam = [];
        
        // 상위 5명, 하위 5명 배분 방식
        $blueTeam1 = array_slice($playerStats, 0, 5, true);
        $redTeam1 = array_slice($playerStats, 5, 5, true);
        
        $balanceScore1 = $this->calculateBalanceScore($blueTeam1, $redTeam1);
        
        // 상위 1,3,5,7,9 vs 상위 2,4,6,8,10 배분 방식
        $blueTeam2 = [];
        $redTeam2 = [];
        
        $i = 0;
        foreach ($playerStats as $id => $stats) {
            if ($i % 2 == 0) {
                $blueTeam2[$id] = $stats;
            } else {
                $redTeam2[$id] = $stats;
            }
            $i++;
        }
        
        $balanceScore2 = $this->calculateBalanceScore($blueTeam2, $redTeam2);
        
        // 최적의 팀 구성 선택
        if ($balanceScore1 < $balanceScore2) {
            $bestBlueTeam = $blueTeam1;
            $bestRedTeam = $redTeam1;
            $bestBalanceScore = $balanceScore1;
        } else {
            $bestBlueTeam = $blueTeam2;
            $bestRedTeam = $redTeam2;
            $bestBalanceScore = $balanceScore2;
        }
        
        return [
            'blue_team' => array_values($bestBlueTeam),
            'red_team' => array_values($bestRedTeam),
            'balance_score' => $bestBalanceScore,
            'avg_win_rate_diff' => abs($this->getAverageWinRate($bestBlueTeam) - $this->getAverageWinRate($bestRedTeam))
        ];
    }
    
    /**
     * 팀 밸런스 점수 계산 (낮을수록 균형)
     */
    private function calculateBalanceScore($blueTeam, $redTeam)
    {
        $blueAvgWinRate = $this->getAverageWinRate($blueTeam);
        $redAvgWinRate = $this->getAverageWinRate($redTeam);
        
        return abs($blueAvgWinRate - $redAvgWinRate);
    }
    
    /**
     * 팀의 평균 승률 계산
     */
    private function getAverageWinRate($team)
    {
        if (empty($team)) {
            return 0;
        }
        
        $sum = 0;
        foreach ($team as $player) {
            $sum += $player['win_rate'];
        }
        
        return $sum / count($team);
    }
    
    /**
     * 포지션을 고려한 팀 밸런스 계산
     */
    public function calculateTeamBalanceWithPositions(array $playerIds, array $positions)
    {
        // 10명의 플레이어가 필요합니다
        if (count($playerIds) !== 10 || count($positions) !== 10) {
            return [
                'error' => '정확히 10명의 플레이어와 포지션 정보가 필요합니다.'
            ];
        }
        
        $players = Player::whereIn('id', $playerIds)->get();
        
        // 모든 플레이어가 존재하는지 확인
        if ($players->count() !== 10) {
            return [
                'error' => '일부 플레이어를 찾을 수 없습니다.'
            ];
        }
        
        // 플레이어별 승률 및 포지션별 성과 계산
        $playerStats = [];
        foreach ($players as $index => $player) {
            $position = $positions[$index];
            $positionStats = $this->getPlayerPositionStats($player->id, $position);
            
            $playerStats[$player->id] = [
                'player' => $player,
                'position' => $position,
                'win_rate' => $player->getWinRate(),
                'position_win_rate' => $positionStats['win_rate'] ?? 0
            ];
        }
        
        // 포지션 승률 기준 내림차순 정렬
        uasort($playerStats, function ($a, $b) {
            return $b['position_win_rate'] <=> $a['position_win_rate'];
        });
        
        // 팀 구성 시뮬레이션 (포지션 고려)
        $bestBalanceScore = PHP_INT_MAX;
        $bestBlueTeam = [];
        $bestRedTeam = [];
        
        // 상위 5명, 하위 5명 배분 방식
        $blueTeam1 = array_slice($playerStats, 0, 5, true);
        $redTeam1 = array_slice($playerStats, 5, 5, true);
        
        $balanceScore1 = $this->calculatePositionBalanceScore($blueTeam1, $redTeam1);
        
        // 상위 1,3,5,7,9 vs 상위 2,4,6,8,10 배분 방식
        $blueTeam2 = [];
        $redTeam2 = [];
        
        $i = 0;
        foreach ($playerStats as $id => $stats) {
            if ($i % 2 == 0) {
                $blueTeam2[$id] = $stats;
            } else {
                $redTeam2[$id] = $stats;
            }
            $i++;
        }
        
        $balanceScore2 = $this->calculatePositionBalanceScore($blueTeam2, $redTeam2);
        
        // 최적의 팀 구성 선택
        if ($balanceScore1 < $balanceScore2) {
            $bestBlueTeam = $blueTeam1;
            $bestRedTeam = $redTeam1;
            $bestBalanceScore = $balanceScore1;
        } else {
            $bestBlueTeam = $blueTeam2;
            $bestRedTeam = $redTeam2;
            $bestBalanceScore = $balanceScore2;
        }
        
        return [
            'blue_team' => array_values($bestBlueTeam),
            'red_team' => array_values($bestRedTeam),
            'balance_score' => $bestBalanceScore,
            'avg_position_win_rate_diff' => abs(
                $this->getAveragePositionWinRate($bestBlueTeam) - 
                $this->getAveragePositionWinRate($bestRedTeam)
            )
        ];
    }
    
    /**
     * 플레이어의 특정 포지션 통계 조회
     */
    private function getPlayerPositionStats($playerId, $position)
    {
        $blueMatches = Match::where('blue_' . $position . '_player_id', $playerId)->get();
        $redMatches = Match::where('red_' . $position . '_player_id', $playerId)->get();
        
        $totalMatches = $blueMatches->count() + $redMatches->count();
        
        if ($totalMatches > 0) {
            $blueWins = $blueMatches->where('winner', 'blue')->count();
            $redWins = $redMatches->where('winner', 'red')->count();
            $totalWins = $blueWins + $redWins;
            
            return [
                'matches_count' => $totalMatches,
                'wins' => $totalWins,
                'losses' => $totalMatches - $totalWins,
                'win_rate' => ($totalWins / $totalMatches) * 100
            ];
        }
        
        return [
            'matches_count' => 0,
            'wins' => 0,
            'losses' => 0,
            'win_rate' => 0
        ];
    }
    
    /**
     * 포지션 고려 팀 밸런스 점수 계산
     */
    private function calculatePositionBalanceScore($blueTeam, $redTeam)
    {
        $blueAvgPositionWinRate = $this->getAveragePositionWinRate($blueTeam);
        $redAvgPositionWinRate = $this->getAveragePositionWinRate($redTeam);
        
        return abs($blueAvgPositionWinRate - $redAvgPositionWinRate);
    }
    
    /**
     * 팀의 평균 포지션 승률 계산
     */
    private function getAveragePositionWinRate($team)
    {
        if (empty($team)) {
            return 0;
        }
        
        $sum = 0;
        foreach ($team as $player) {
            $sum += $player['position_win_rate'];
        }
        
        return $sum / count($team);
    }
}
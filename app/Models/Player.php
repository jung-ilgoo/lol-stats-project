<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_name',
        'rank',
        'main_positions',
    ];

    /**
     * 플레이어와 매치간의 관계 설정
     */
    public function blueTopMatches()
    {
        return $this->hasMany(Match::class, 'blue_top_player_id');
    }

    public function blueJungleMatches()
    {
        return $this->hasMany(Match::class, 'blue_jungle_player_id');
    }

    public function blueMidMatches()
    {
        return $this->hasMany(Match::class, 'blue_mid_player_id');
    }

    public function blueAdcMatches()
    {
        return $this->hasMany(Match::class, 'blue_adc_player_id');
    }

    public function blueSupportMatches()
    {
        return $this->hasMany(Match::class, 'blue_support_player_id');
    }

    public function redTopMatches()
    {
        return $this->hasMany(Match::class, 'red_top_player_id');
    }

    public function redJungleMatches()
    {
        return $this->hasMany(Match::class, 'red_jungle_player_id');
    }

    public function redMidMatches()
    {
        return $this->hasMany(Match::class, 'red_mid_player_id');
    }

    public function redAdcMatches()
    {
        return $this->hasMany(Match::class, 'red_adc_player_id');
    }

    public function redSupportMatches()
    {
        return $this->hasMany(Match::class, 'red_support_player_id');
    }

    /**
     * 플레이어의 모든 경기를 가져오는 메소드
     */
    public function allMatches()
    {
        return Match::where('blue_top_player_id', $this->id)
            ->orWhere('blue_jungle_player_id', $this->id)
            ->orWhere('blue_mid_player_id', $this->id)
            ->orWhere('blue_adc_player_id', $this->id)
            ->orWhere('blue_support_player_id', $this->id)
            ->orWhere('red_top_player_id', $this->id)
            ->orWhere('red_jungle_player_id', $this->id)
            ->orWhere('red_mid_player_id', $this->id)
            ->orWhere('red_adc_player_id', $this->id)
            ->orWhere('red_support_player_id', $this->id)
            ->get();
    }

    /**
     * 플레이어의 승률 계산
     */
    public function getWinRate()
    {
        $matches = $this->allMatches();
        
        if ($matches->isEmpty()) {
            return 0;
        }

        $wins = 0;

        foreach ($matches as $match) {
            $isBlueTeam = in_array($this->id, [
                $match->blue_top_player_id,
                $match->blue_jungle_player_id,
                $match->blue_mid_player_id,
                $match->blue_adc_player_id,
                $match->blue_support_player_id
            ]);

            if (($isBlueTeam && $match->winner === 'blue') || (!$isBlueTeam && $match->winner === 'red')) {
                $wins++;
            }
        }

        return ($wins / count($matches)) * 100;
    }

    /**
 * 플레이어가 속한 경기 조회
 */
    public function matches()
    {
        return $this->belongsToMany(Match::class, 'match_player')
                    ->withPivot('team', 'position', 'champion_id', 'kills', 'deaths', 'assists');
    }

    /**
     * 플레이어가 이긴 경기 조회
     */
    public function matchesWon()
    {
        return $this->belongsToMany(Match::class, 'match_player')
                    ->where(function($query) {
                        $query->where(function($q) {
                            $q->where('match_player.team', 'blue')
                            ->whereColumn('matches.winner', 'match_player.team');
                        })->orWhere(function($q) {
                            $q->where('match_player.team', 'red')
                            ->whereColumn('matches.winner', 'match_player.team');
                        });
                    })
                    ->withPivot('team', 'position', 'champion_id', 'kills', 'deaths', 'assists');
    }

    /**
     * 플레이어가 진 경기 조회
     */
    public function matchesLost()
    {
        return $this->belongsToMany(Match::class, 'match_player')
                    ->where(function($query) {
                        $query->where(function($q) {
                            $q->where('match_player.team', 'blue')
                            ->where('matches.winner', '<>', 'match_player.team');
                        })->orWhere(function($q) {
                            $q->where('match_player.team', 'red')
                            ->where('matches.winner', '<>', 'match_player.team');
                        });
                    })
                    ->withPivot('team', 'position', 'champion_id', 'kills', 'deaths', 'assists');
    }
}
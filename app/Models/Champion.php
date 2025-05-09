<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Champion extends Model
{
    use HasFactory;

    protected $fillable = [
        'champion_name',
    ];

    /**
     * 챔피언과 매치간의 관계 설정
     */
    public function blueTopMatches()
    {
        return $this->hasMany(Match::class, 'blue_top_champion_id');
    }

    public function blueJungleMatches()
    {
        return $this->hasMany(Match::class, 'blue_jungle_champion_id');
    }

    public function blueMidMatches()
    {
        return $this->hasMany(Match::class, 'blue_mid_champion_id');
    }

    public function blueAdcMatches()
    {
        return $this->hasMany(Match::class, 'blue_adc_champion_id');
    }

    public function blueSupportMatches()
    {
        return $this->hasMany(Match::class, 'blue_support_champion_id');
    }

    public function redTopMatches()
    {
        return $this->hasMany(Match::class, 'red_top_champion_id');
    }

    public function redJungleMatches()
    {
        return $this->hasMany(Match::class, 'red_jungle_champion_id');
    }

    public function redMidMatches()
    {
        return $this->hasMany(Match::class, 'red_mid_champion_id');
    }

    public function redAdcMatches()
    {
        return $this->hasMany(Match::class, 'red_adc_champion_id');
    }

    public function redSupportMatches()
    {
        return $this->hasMany(Match::class, 'red_support_champion_id');
    }

    /**
     * 챔피언의 모든 경기를 가져오는 메소드
     */
    public function allMatches()
    {
        return Match::where('blue_top_champion_id', $this->id)
            ->orWhere('blue_jungle_champion_id', $this->id)
            ->orWhere('blue_mid_champion_id', $this->id)
            ->orWhere('blue_adc_champion_id', $this->id)
            ->orWhere('blue_support_champion_id', $this->id)
            ->orWhere('red_top_champion_id', $this->id)
            ->orWhere('red_jungle_champion_id', $this->id)
            ->orWhere('red_mid_champion_id', $this->id)
            ->orWhere('red_adc_champion_id', $this->id)
            ->orWhere('red_support_champion_id', $this->id)
            ->get();
    }

    /**
     * 챔피언의 승률 계산
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
                $match->blue_top_champion_id,
                $match->blue_jungle_champion_id,
                $match->blue_mid_champion_id,
                $match->blue_adc_champion_id,
                $match->blue_support_champion_id
            ]);

            if (($isBlueTeam && $match->winner === 'blue') || (!$isBlueTeam && $match->winner === 'red')) {
                $wins++;
            }
        }

        return ($wins / count($matches)) * 100;
    }
}
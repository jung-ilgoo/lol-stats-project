<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_date',
        'winner',
        'blue_top_player_id',
        'blue_top_champion_id',
        'blue_top_kda',
        'blue_jungle_player_id',
        'blue_jungle_champion_id',
        'blue_jungle_kda',
        'blue_mid_player_id',
        'blue_mid_champion_id',
        'blue_mid_kda',
        'blue_adc_player_id',
        'blue_adc_champion_id',
        'blue_adc_kda',
        'blue_support_player_id',
        'blue_support_champion_id',
        'blue_support_kda',
        'red_top_player_id',
        'red_top_champion_id',
        'red_top_kda',
        'red_jungle_player_id',
        'red_jungle_champion_id',
        'red_jungle_kda',
        'red_mid_player_id',
        'red_mid_champion_id',
        'red_mid_kda',
        'red_adc_player_id',
        'red_adc_champion_id',
        'red_adc_kda',
        'red_support_player_id',
        'red_support_champion_id',
        'red_support_kda',
    ];

    /**
     * 매치와 플레이어간의 관계 설정
     */
    public function blueTopPlayer()
    {
        return $this->belongsTo(Player::class, 'blue_top_player_id');
    }

    public function blueJunglePlayer()
    {
        return $this->belongsTo(Player::class, 'blue_jungle_player_id');
    }

    public function blueMidPlayer()
    {
        return $this->belongsTo(Player::class, 'blue_mid_player_id');
    }

    public function blueAdcPlayer()
    {
        return $this->belongsTo(Player::class, 'blue_adc_player_id');
    }

    public function blueSupportPlayer()
    {
        return $this->belongsTo(Player::class, 'blue_support_player_id');
    }

    public function redTopPlayer()
    {
        return $this->belongsTo(Player::class, 'red_top_player_id');
    }

    public function redJunglePlayer()
    {
        return $this->belongsTo(Player::class, 'red_jungle_player_id');
    }

    public function redMidPlayer()
    {
        return $this->belongsTo(Player::class, 'red_mid_player_id');
    }

    public function redAdcPlayer()
    {
        return $this->belongsTo(Player::class, 'red_adc_player_id');
    }

    public function redSupportPlayer()
    {
        return $this->belongsTo(Player::class, 'red_support_player_id');
    }

    /**
     * 매치와 챔피언간의 관계 설정
     */
    public function blueTopChampion()
    {
        return $this->belongsTo(Champion::class, 'blue_top_champion_id');
    }

    public function blueJungleChampion()
    {
        return $this->belongsTo(Champion::class, 'blue_jungle_champion_id');
    }

    public function blueMidChampion()
    {
        return $this->belongsTo(Champion::class, 'blue_mid_champion_id');
    }

    public function blueAdcChampion()
    {
        return $this->belongsTo(Champion::class, 'blue_adc_champion_id');
    }

    public function blueSupportChampion()
    {
        return $this->belongsTo(Champion::class, 'blue_support_champion_id');
    }

    public function redTopChampion()
    {
        return $this->belongsTo(Champion::class, 'red_top_champion_id');
    }

    public function redJungleChampion()
    {
        return $this->belongsTo(Champion::class, 'red_jungle_champion_id');
    }

    public function redMidChampion()
    {
        return $this->belongsTo(Champion::class, 'red_mid_champion_id');
    }

    public function redAdcChampion()
    {
        return $this->belongsTo(Champion::class, 'red_adc_champion_id');
    }

    public function redSupportChampion()
    {
        return $this->belongsTo(Champion::class, 'red_support_champion_id');
    }
}
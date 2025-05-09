<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ChampionController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\TeamBalanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 메인 페이지
Route::get('/', [MainController::class, 'index'])->name('home');

// 플레이어 관리
Route::resource('players', PlayerController::class);
Route::get('players/{player}/stats', [PlayerController::class, 'showStats'])->name('players.stats');

// 챔피언 관리
Route::resource('champions', ChampionController::class);

// 경기 기록
Route::resource('matches', MatchController::class);

// 랭킹
Route::get('rankings', [RankingController::class, 'index'])->name('rankings.index');
Route::get('rankings/players', [RankingController::class, 'players'])->name('rankings.players');
Route::get('rankings/champions', [RankingController::class, 'champions'])->name('rankings.champions');

// 팀 밸런스
Route::get('team-balance', [TeamBalanceController::class, 'index'])->name('team-balance.index');
Route::post('team-balance/calculate', [TeamBalanceController::class, 'calculate'])->name('team-balance.calculate');
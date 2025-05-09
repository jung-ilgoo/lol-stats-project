@extends('layouts.app')

@section('title', '홈 - 팀 밸런스 시스템')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-body text-center py-5">
                <h1 class="display-4">팀 밸런스 시스템에 오신 것을 환영합니다</h1>
                <p class="lead">플레이어 통계, 경기 기록, 팀 밸런스 계산을 한 곳에서 관리하세요.</p>
                <div class="mt-4">
                    <a href="{{ route('team-balance.index') }}" class="btn btn-primary btn-lg">팀 밸런스 계산하기</a>
                    <a href="{{ route('matches.create') }}" class="btn btn-success btn-lg ms-2">새 경기 기록하기</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">최근 경기 기록</h5>
                <a href="{{ route('matches.index') }}" class="btn btn-sm btn-outline-primary">모두 보기</a>
            </div>
            <div class="card-body">
                @if(isset($recentMatches) && count($recentMatches) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>날짜</th>
                                    <th>승리팀</th>
                                    <th>상세정보</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMatches as $match)
                                <tr>
                                    <td>{{ $match->match_date->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="badge {{ $match->winner === 'blue' ? 'bg-primary' : 'bg-danger' }}">
                                            {{ $match->winner === 'blue' ? '블루팀' : '레드팀' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('matches.show', $match->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> 상세
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        아직 기록된 경기가 없습니다. <a href="{{ route('matches.create') }}">새 경기를 기록</a>해보세요.
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">탑 플레이어</h5>
                <a href="{{ route('rankings.index') }}" class="btn btn-sm btn-outline-primary">랭킹 보기</a>
            </div>
            <div class="card-body">
                @if(isset($topPlayers) && count($topPlayers) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>순위</th>
                                    <th>플레이어</th>
                                    <th>승률</th>
                                    <th>KDA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPlayers as $index => $player)
                                <tr class="{{ $index < 3 ? 'rank-'.($index+1) : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('players.show', $player->id) }}">
                                            {{ $player->player_name }}
                                        </a>
                                    </td>
                                    <td>{{ number_format($player->win_rate * 100, 1) }}%</td>
                                    <td>{{ number_format($player->kda, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        플레이어 데이터가 충분하지 않습니다. 더 많은 경기를 기록하면 통계가 표시됩니다.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <h3 class="card-title">{{ $totalPlayers ?? 0 }}</h3>
                <p class="text-muted">등록된 플레이어</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <h3 class="card-title">{{ $totalMatches ?? 0 }}</h3>
                <p class="text-muted">기록된 경기</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stats-card"
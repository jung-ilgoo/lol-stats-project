@extends('layouts.app')

@section('title', '랭킹')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">랭킹</h5>
            </div>
            <div class="card-body">
                <p>플레이어와 챔피언의 승률, KDA 등의 통계 기반 랭킹을 확인하세요.</p>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <a href="{{ route('rankings.players') }}" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-users"></i> 플레이어 랭킹
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('rankings.champions') }}" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-trophy"></i> 챔피언 랭킹
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">플레이어 승률 랭킹 (상위 10명)</h5>
                <a href="{{ route('rankings.players') }}" class="btn btn-sm btn-outline-primary">더 보기</a>
            </div>
            <div class="card-body">
                @if(count($playerRankings) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>순위</th>
                                    <th>플레이어</th>
                                    <th>경기 수</th>
                                    <th>승률</th>
                                    <th>KDA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($playerRankings as $index => $player)
                                <tr class="{{ $index < 3 ? 'rank-'.($index+1) : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('players.show', $player->id) }}">
                                            {{ $player->player_name }}
                                        </a>
                                    </td>
                                    <td>{{ $player->matches_played_count }}</td>
                                    <td>{{ number_format($player->win_rate * 100, 1) }}%</td>
                                    <td>{{ number_format($player->kda, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        아직 플레이어 랭킹 데이터가 없습니다. 경기를 더 기록하세요.
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">챔피언 승률 랭킹 (상위 10개)</h5>
                <a href="{{ route('rankings.champions') }}" class="btn btn-sm btn-outline-primary">더 보기</a>
            </div>
            <div class="card-body">
                @if(count($championRankings) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>순위</th>
                                    <th>챔피언</th>
                                    <th>픽률</th>
                                    <th>승률</th>
                                    <th>KDA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($championRankings as $index => $champion)
                                <tr class="{{ $index < 3 ? 'rank-'.($index+1) : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('champions.show', $champion->id) }}">
                                            {{ $champion->champion_name }}
                                        </a>
                                    </td>
                                    <td>{{ $champion->usage_count }}</td>
                                    <td>{{ number_format($champion->win_rate * 100, 1) }}%</td>
                                    <td>{{ number_format($champion->kda, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        아직 챔피언 랭킹 데이터가 없습니다. 경기를 더 기록하세요.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', '플레이어 랭킹')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">플레이어 랭킹</h5>
        <a href="{{ route('rankings.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> 전체 랭킹으로
        </a>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form method="GET" action="{{ route('rankings.players') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="min_games" class="form-label">최소 경기 수</label>
                    <select class="form-select" id="min_games" name="min_games" onchange="this.form.submit()">
                        <option value="1" {{ $minGames == 1 ? 'selected' : '' }}>1</option>
                        <option value="3" {{ $minGames == 3 ? 'selected' : '' }}>3</option>
                        <option value="5" {{ $minGames == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ $minGames == 10 ? 'selected' : '' }}>10</option>
                    </select>
                </div>
            </form>
        </div>
        
        @if(count($rankings) > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>순위</th>
                            <th>플레이어</th>
                            <th>랭크</th>
                            <th>주 포지션</th>
                            <th>경기 수</th>
                            <th>승</th>
                            <th>패</th>
                            <th>승률</th>
                            <th>KDA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rankings as $index => $player)
                        <tr class="{{ $index < 3 ? 'rank-'.($index+1) : '' }}">
                            <td>{{ $rankings->firstItem() + $index }}</td>
                            <td>
                                <a href="{{ route('players.show', $player->id) }}">
                                    {{ $player->player_name }}
                                </a>
                            </td>
                            <td>{{ $player->rank }}</td>
                            <td>{{ $player->main_positions }}</td>
                            <td>{{ $player->matches_played_count }}</td>
                            <td>{{ $player->matches_won_count }}</td>
                            <td>{{ $player->matches_played_count - $player->matches_won_count }}</td>
                            <td>{{ number_format($player->win_rate * 100, 1) }}%</td>
                            <td>{{ number_format($player->kda, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $rankings->links() }}
            </div>
        @else
            <div class="alert alert-info">
                현재 조건에 맞는 플레이어가 없습니다. 조건을 변경하거나 경기를 더 기록하세요.
            </div>
        @endif
    </div>
</div>

<div class="row">
    @foreach($topPlayersByPosition as $position => $players)
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ ucfirst($position) }} 포지션 TOP 3</h5>
            </div>
            <div class="card-body">
                @if(count($players) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>플레이어</th>
                                    <th>경기</th>
                                    <th>승률</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($players as $index => $player)
                                <tr class="{{ $index < 3 ? 'rank-'.($index+1) : '' }}">
                                    <td>
                                        <a href="{{ route('players.show', $player->id) }}">
                                            {{ $player->player_name }}
                                        </a>
                                    </td>
                                    <td>{{ $player->games_count }}</td>
                                    <td>{{ number_format($player->win_rate * 100, 1) }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        데이터가 없습니다.
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
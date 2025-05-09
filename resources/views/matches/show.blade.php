@extends('layouts.app')

@section('title', '경기 상세 정보')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">경기 #{{ $match->id }} 상세 정보</h5>
        <div>
            <a href="{{ route('matches.edit', $match->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> 수정
            </a>
            <button class="btn btn-danger" onclick="event.preventDefault(); 
                    if(confirm('정말 삭제하시겠습니까?')) { 
                        document.getElementById('delete-match').submit(); 
                    }">
                <i class="fas fa-trash"></i> 삭제
            </button>
            <form id="delete-match" action="{{ route('matches.destroy', $match->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>경기 날짜:</strong> {{ $match->match_date->format('Y-m-d') }}</p>
            </div>
            <div class="col-md-6">
                <p>
                    <strong>승리팀:</strong> 
                    <span class="badge {{ $match->winner === 'blue' ? 'bg-primary' : 'bg-danger' }}">
                        {{ $match->winner === 'blue' ? '블루팀' : '레드팀' }}
                    </span>
                </p>
            </div>
        </div>
        
        <div class="row">
            <!-- 블루팀 -->
            <div class="col-md-6">
                <div class="card mb-4 team-blue">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">블루팀</h5>
                        <span class="badge {{ $match->winner === 'blue' ? 'bg-success' : 'bg-danger' }}">
                            {{ $match->winner === 'blue' ? '승리' : '패배' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>포지션</th>
                                        <th>플레이어</th>
                                        <th>챔피언</th>
                                        <th>KDA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bluePlayers as $position => $player)
                                    <tr>
                                        <td>{{ $position }}</td>
                                        <td>
                                            <a href="{{ route('players.show', $player->player_id) }}">
                                                {{ $player->player->player_name }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('champions.show', $player->champion_id) }}">
                                                {{ $player->champion->champion_name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $player->kills }}/{{ $player->deaths }}/{{ $player->assists }}
                                            <span class="text-muted">({{ number_format(($player->kills + $player->assists) / max(1, $player->deaths), 2) }})</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary">
                                        <td colspan="3" class="text-end"><strong>합계</strong></td>
                                        <td>
                                            {{ $blueStats['kills'] }}/{{ $blueStats['deaths'] }}/{{ $blueStats['assists'] }}
                                            <span class="text-muted">({{ number_format(($blueStats['kills'] + $blueStats['assists']) / max(1, $blueStats['deaths']), 2) }})</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 레드팀 -->
            <div class="col-md-6">
                <div class="card mb-4 team-red">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">레드팀</h5>
                        <span class="badge {{ $match->winner === 'red' ? 'bg-success' : 'bg-danger' }}">
                            {{ $match->winner === 'red' ? '승리' : '패배' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>포지션</th>
                                        <th>플레이어</th>
                                        <th>챔피언</th>
                                        <th>KDA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($redPlayers as $position => $player)
                                    <tr>
                                        <td>{{ $position }}</td>
                                        <td>
                                            <a href="{{ route('players.show', $player->player_id) }}">
                                                {{ $player->player->player_name }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('champions.show', $player->champion_id) }}">
                                                {{ $player->champion->champion_name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $player->kills }}/{{ $player->deaths }}/{{ $player->assists }}
                                            <span class="text-muted">({{ number_format(($player->kills + $player->assists) / max(1, $player->deaths), 2) }})</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-danger">
                                        <td colspan="3" class="text-end"><strong>합계</strong></td>
                                        <td>
                                            {{ $redStats['kills'] }}/{{ $redStats['deaths'] }}/{{ $redStats['assists'] }}
                                            <span class="text-muted">({{ number_format(($redStats['kills'] + $redStats['assists']) / max(1, $redStats['deaths']), 2) }})</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between mb-4">
    <a href="{{ route('matches.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> 목록으로
    </a>
    <div>
        @if($prevMatch)
            <a href="{{ route('matches.show', $prevMatch->id) }}" class="btn btn-outline-primary">
                <i class="fas fa-chevron-left"></i> 이전 경기
            </a>
        @endif
        
        @if($nextMatch)
            <a href="{{ route('matches.show', $nextMatch->id) }}" class="btn btn-outline-primary">
                다음 경기 <i class="fas fa-chevron-right"></i>
            </a>
        @endif
    </div>
</div>
@endsection
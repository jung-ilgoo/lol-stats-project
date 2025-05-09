@extends('layouts.app')

@section('title', $player->player_name . ' - 플레이어 정보')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $player->player_name }} 정보</h5>
                <div>
                    <a href="{{ route('players.edit', $player->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> 수정
                    </a>
                    <button class="btn btn-danger" onclick="event.preventDefault(); 
                            if(confirm('정말 삭제하시겠습니까?')) { 
                                document.getElementById('delete-player').submit(); 
                            }">
                        <i class="fas fa-trash"></i> 삭제
                    </button>
                    <form id="delete-player" action="{{ route('players.destroy', $player->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th style="width: 30%">이름</th>
                                <td>{{ $player->player_name }}</td>
                            </tr>
                            <tr>
                                <th>랭크</th>
                                <td>{{ $player->rank }}</td>
                            </tr>
                            <tr>
                                <th>주 포지션</th>
                                <td>{{ $player->main_positions }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">전체 통계</h5>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <h3>{{ $statistics['total_matches'] ?? 0 }}</h3>
                                        <p class="text-muted">전체 경기</p>
                                    </div>
                                    <div class="col-4">
                                        <h3>{{ number_format($statistics['win_rate'] * 100 ?? 0, 1) }}%</h3>
                                        <p class="text-muted">승률</p>
                                    </div>
                                    <div class="col-4">
                                        <h3>{{ number_format($statistics['kda'] ?? 0, 2) }}</h3>
                                        <p class="text-muted">KDA</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">포지션별 통계</h5>
            </div>
            <div class="card-body">
                @if(isset($statistics['positions']) && count($statistics['positions']) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>포지션</th>
                                    <th>경기 수</th>
                                    <th>승률</th>
                                    <th>KDA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['positions'] as $position => $stats)
                                <tr>
                                    <td>{{ $position }}</td>
                                    <td>{{ $stats['matches'] }}</td>
                                    <td>{{ number_format($stats['win_rate'] * 100, 1) }}%</td>
                                    <td>{{ number_format($stats['kda'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        포지션별 통계 데이터가 없습니다. 경기를 더 기록하면 통계가 표시됩니다.
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">챔피언별 통계</h5>
            </div>
            <div class="card-body">
                @if(isset($statistics['champions']) && count($statistics['champions']) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>챔피언</th>
                                    <th>경기 수</th>
                                    <th>승률</th>
                                    <th>KDA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['champions'] as $champion => $stats)
                                <tr>
                                    <td>{{ $champion }}</td>
                                    <td>{{ $stats['matches'] }}</td>
                                    <td>{{ number_format($stats['win_rate'] * 100, 1) }}%</td>
                                    <td>{{ number_format($stats['kda'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        챔피언별 통계 데이터가 없습니다. 경기를 더 기록하면 통계가 표시됩니다.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">최근 경기 기록</h5>
    </div>
    <div class="card-body">
        @if(isset($matches) && count($matches) > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>날짜</th>
                            <th>팀</th>
                            <th>결과</th>
                            <th>챔피언</th>
                            <th>KDA</th>
                            <th>상세</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($matches as $match)
                        <tr class="{{ $match->team === 'blue' ? 'team-blue' : 'team-red' }}">
                            <td>{{ $match->match->match_date->format('Y-m-d') }}</td>
                            <td>{{ $match->team === 'blue' ? '블루팀' : '레드팀' }}</td>
                            <td>
                                @if($match->team === $match->match->winner)
                                    <span class="badge bg-success">승리</span>
                                @else
                                    <span class="badge bg-danger">패배</span>
                                @endif
                            </td>
                            <td>{{ $match->champion_name }}</td>
                            <td>{{ $match->kills }}/{{ $match->deaths }}/{{ $match->assists }}
                                ({{ number_format(($match->kills + $match->assists) / max(1, $match->deaths), 2) }})
                            </td>
                            <td>
                                <a href="{{ route('matches.show', $match->match_id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $matches->links() }}
            </div>
        @else
            <div class="alert alert-info">
                기록된 경기가 없습니다.
            </div>
        @endif
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', $champion->champion_name . ' - 챔피언 정보')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $champion->champion_name }} 정보</h5>
                <div>
                    <a href="{{ route('champions.edit', $champion->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> 수정
                    </a>
                    <button class="btn btn-danger" onclick="event.preventDefault(); 
                            if(confirm('정말 삭제하시겠습니까?')) { 
                                document.getElementById('delete-champion').submit(); 
                            }">
                        <i class="fas fa-trash"></i> 삭제
                    </button>
                    <form id="delete-champion" action="{{ route('champions.destroy', $champion->id) }}" method="POST" style="display: none;">
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
                                <th style="width: 30%">챔피언 이름</th>
                                <td>{{ $champion->champion_name }}</td>
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
                                        <p class="text-muted">전체 사용</p>
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
                                    <th>사용 횟수</th>
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
                <h5 class="mb-0">플레이어별 통계</h5>
            </div>
            <div class="card-body">
                @if(isset($statistics['players']) && count($statistics['players']) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>플레이어</th>
                                    <th>사용 횟수</th>
                                    <th>승률</th>
                                    <th>KDA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statistics['players'] as $player => $stats)
                                <tr>
                                    <td>{{ $player }}</td>
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
                        플레이어별 통계 데이터가 없습니다. 경기를 더 기록하면 통계가 표시됩니다.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">최근 사용 기록</h5>
    </div>
    <div class="card-body">
        @if(isset($matches) && count($matches) > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>날짜</th>
                            <th>플레이어</th>
                            <th>포지션</th>
                            <th>팀</th>
                            <th>결과</th>
                            <th>KDA</th>
                            <th>상세</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($matches as $match)
                        <tr class="{{ $match->team === 'blue' ? 'team-blue' : 'team-red' }}">
                            <td>{{ $match->match_date->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('players.show', $match->player_id) }}">
                                    {{ $match->player_name }}
                                </a>
                            </td>
                            <td>{{ $match->position }}</td>
                            <td>{{ $match->team === 'blue' ? '블루팀' : '레드팀' }}</td>
                            <td>
                                @if($match->team === $match->winner)
                                    <span class="badge bg-success">승리</span>
                                @else
                                    <span class="badge bg-danger">패배</span>
                                @endif
                            </td>
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
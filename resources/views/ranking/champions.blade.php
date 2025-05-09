@extends('layouts.app')

@section('title', '챔피언 랭킹')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">챔피언 랭킹</h5>
        <a href="{{ route('rankings.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> 전체 랭킹으로
        </a>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form method="GET" action="{{ route('rankings.champions') }}" class="row g-3">
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
                            <th>챔피언</th>
                            <th>사용 횟수</th>
                            <th>승</th>
                            <th>패</th>
                            <th>승률</th>
                            <th>KDA</th>
                            <th>상세정보</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rankings as $index => $champion)
                        <tr class="{{ $index < 3 ? 'rank-'.($index+1) : '' }}">
                            <td>{{ $rankings->firstItem() + $index }}</td>
                            <td>{{ $champion->champion_name }}</td>
                            <td>{{ $champion->usage_count }}</td>
                            <td>{{ $champion->wins }}</td>
                            <td>{{ $champion->usage_count - $champion->wins }}</td>
                            <td>{{ number_format($champion->win_rate * 100, 1) }}%</td>
                            <td>{{ number_format($champion->kda, 2) }}</td>
                            <td>
                                <a href="{{ route('champions.show', $champion->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
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
                현재 조건에 맞는 챔피언이 없습니다. 조건을 변경하거나 경기를 더 기록하세요.
            </div>
        @endif
    </div>
</div>

<div class="row">
    @foreach($topChampionsByPosition as $position => $champions)
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ ucfirst($position) }} 포지션 TOP 3 챔피언</h5>
            </div>
            <div class="card-body">
                @if(count($champions) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>챔피언</th>
                                    <th>픽률</th>
                                    <th>승률</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($champions as $index => $champion)
                                <tr class="{{ $index < 3 ? 'rank-'.($index+1) : '' }}">
                                    <td>
                                        <a href="{{ route('champions.show', $champion->id) }}">
                                            {{ $champion->champion_name }}
                                        </a>
                                    </td>
                                    <td>{{ $champion->games_count }}</td>
                                    <td>{{ number_format($champion->win_rate * 100, 1) }}%</td>
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
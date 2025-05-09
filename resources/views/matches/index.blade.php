@extends('layouts.app')

@section('title', '경기 기록')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">경기 기록 목록</h5>
        <a href="{{ route('matches.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> 새 경기 기록
        </a>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form method="GET" action="{{ route('matches.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="date_from" class="form-label">시작일</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-4">
                    <label for="date_to" class="form-label">종료일</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">적용</button>
                    <a href="{{ route('matches.index') }}" class="btn btn-secondary ms-2">초기화</a>
                </div>
            </form>
        </div>
        
        @if(count($matches) > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>날짜</th>
                            <th>승리팀</th>
                            <th>블루팀</th>
                            <th>레드팀</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($matches as $match)
                        <tr>
                            <td>{{ $match->id }}</td>
                            <td>{{ $match->match_date->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge {{ $match->winner === 'blue' ? 'bg-primary' : 'bg-danger' }}">
                                    {{ $match->winner === 'blue' ? '블루팀' : '레드팀' }}
                                </span>
                            </td>
                            <td>
                                @if(isset($match->bluePlayers))
                                    @foreach($match->bluePlayers as $player)
                                        <span class="badge bg-light text-dark">{{ $player->player_name }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if(isset($match->redPlayers))
                                    @foreach($match->redPlayers as $player)
                                        <span class="badge bg-light text-dark">{{ $player->player_name }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('matches.show', $match->id) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('matches.edit', $match->id) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" 
                                            onclick="event.preventDefault(); 
                                                    if(confirm('정말 삭제하시겠습니까?')) { 
                                                        document.getElementById('delete-match-{{ $match->id }}').submit(); 
                                                    }">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-match-{{ $match->id }}" 
                                          action="{{ route('matches.destroy', $match->id) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
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
                기록된 경기가 없습니다. <a href="{{ route('matches.create') }}">새 경기를 기록</a>해보세요.
            </div>
        @endif
    </div>
</div>
@endsection
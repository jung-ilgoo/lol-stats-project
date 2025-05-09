@extends('layouts.app')

@section('title', '플레이어 관리')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">플레이어 목록</h5>
        <a href="{{ route('players.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> 새 플레이어
        </a>
    </div>
    <div class="card-body">
        @if(count($players) > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>이름</th>
                            <th>랭크</th>
                            <th>주 포지션</th>
                            <th>경기 수</th>
                            <th>승률</th>
                            <th>KDA</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($players as $player)
                        <tr>
                            <td>{{ $player->id }}</td>
                            <td>
                                <a href="{{ route('players.show', $player->id) }}">
                                    {{ $player->player_name }}
                                </a>
                            </td>
                            <td>{{ $player->rank }}</td>
                            <td>{{ $player->main_positions }}</td>
                            <td>{{ $player->matches_count ?? 0 }}</td>
                            <td>
                                @if(isset($player->matches_count) && $player->matches_count > 0)
                                    {{ number_format($player->win_rate * 100, 1) }}%
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ number_format($player->kda ?? 0, 2) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('players.show', $player->id) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('players.edit', $player->id) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" 
                                            onclick="event.preventDefault(); 
                                                    if(confirm('정말 삭제하시겠습니까?')) { 
                                                        document.getElementById('delete-player-{{ $player->id }}').submit(); 
                                                    }">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-player-{{ $player->id }}" 
                                          action="{{ route('players.destroy', $player->id) }}" 
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
                {{ $players->links() }}
            </div>
        @else
            <div class="alert alert-info">
                등록된 플레이어가 없습니다. <a href="{{ route('players.create') }}">새 플레이어를 추가</a>해보세요.
            </div>
        @endif
    </div>
</div>
@endsection
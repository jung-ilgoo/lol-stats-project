@extends('layouts.app')

@section('title', '새 경기 기록')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">새 경기 기록</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('matches.store') }}" method="POST" id="match-form">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="match_date" class="form-label">경기 날짜</label>
                    <input type="date" class="form-control @error('match_date') is-invalid @enderror" 
                           id="match_date" name="match_date" value="{{ old('match_date', date('Y-m-d')) }}" required>
                    @error('match_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="winner" class="form-label">승리팀</label>
                    <select class="form-select @error('winner') is-invalid @enderror" 
                            id="winner" name="winner" required>
                        <option value="">승리팀 선택</option>
                        <option value="blue" {{ old('winner') == 'blue' ? 'selected' : '' }}>블루팀</option>
                        <option value="red" {{ old('winner') == 'red' ? 'selected' : '' }}>레드팀</option>
                    </select>
                    @error('winner')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <button type="button" class="btn btn-secondary" onclick="swapTeams()">
                        <i class="fas fa-exchange-alt"></i> 팀 교체
                    </button>
                </div>
            </div>

            <div class="row">
                <!-- 블루팀 -->
                <div class="col-md-6">
                    <div class="card mb-4 team-blue">
                        <div class="card-header">
                            <h5 class="mb-0">블루팀</h5>
                        </div>
                        <div class="card-body">
                            @for ($i = 1; $i <= 5; $i++)
                                <div class="mb-3">
                                    <h6>포지션 {{ $i }}</h6>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <select class="form-select @error('blue_player_'.$i) is-invalid @enderror" 
                                                    id="blue_player_{{ $i }}" name="blue_player_{{ $i }}" required>
                                                <option value="">플레이어 선택</option>
                                                @foreach($players as $player)
                                                    <option value="{{ $player->id }}" {{ old('blue_player_'.$i) == $player->id ? 'selected' : '' }}>
                                                        {{ $player->player_name }} ({{ $player->rank }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('blue_player_'.$i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-select @error('blue_champion_'.$i) is-invalid @enderror" 
                                                    id="blue_champion_{{ $i }}" name="blue_champion_{{ $i }}" required>
                                                <option value="">챔피언 선택</option>
                                                @foreach($champions as $champion)
                                                    <option value="{{ $champion->id }}" {{ old('blue_champion_'.$i) == $champion->id ? 'selected' : '' }}>
                                                        {{ $champion->champion_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('blue_champion_'.$i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row g-2 mt-1">
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-text">K</span>
                                                <input type="number" class="form-control @error('blue_kills_'.$i) is-invalid @enderror" 
                                                       id="blue_kills_{{ $i }}" name="blue_kills_{{ $i }}" 
                                                       value="{{ old('blue_kills_'.$i, 0) }}" min="0" required>
                                                @error('blue_kills_'.$i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-text">D</span>
                                                <input type="number" class="form-control @error('blue_deaths_'.$i) is-invalid @enderror" 
                                                       id="blue_deaths_{{ $i }}" name="blue_deaths_{{ $i }}" 
                                                       value="{{ old('blue_deaths_'.$i, 0) }}" min="0" required>
                                                @error('blue_deaths_'.$i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-text">A</span>
                                                <input type="number" class="form-control @error('blue_assists_'.$i) is-invalid @enderror" 
                                                       id="blue_assists_{{ $i }}" name="blue_assists_{{ $i }}" 
                                                       value="{{ old('blue_assists_'.$i, 0) }}" min="0" required>
                                                @error('blue_assists_'.$i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
                
                <!-- 레드팀 -->
                <div class="col-md-6">
                    <div class="card mb-4 team-red">
                        <div class="card-header">
                            <h5 class="mb-0">레드팀</h5>
                        </div>
                        <div class="card-body">
                            @for ($i = 1; $i <= 5; $i++)
                                <div class="mb-3">
                                    <h6>포지션 {{ $i }}</h6>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <select class="form-select @error('red_player_'.$i) is-invalid @enderror" 
                                                    id="red_player_{{ $i }}" name="red_player_{{ $i }}" required>
                                                <option value="">플레이어 선택</option>
                                                @foreach($players as $player)
                                                    <option value="{{ $player->id }}" {{ old('red_player_'.$i) == $player->id ? 'selected' : '' }}>
                                                        {{ $player->player_name }} ({{ $player->rank }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('red_player_'.$i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-select @error('red_champion_'.$i) is-invalid @enderror" 
                                                    id="red_champion_{{ $i }}" name="red_champion_{{ $i }}" required>
                                                <option value="">챔피언 선택</option>
                                                @foreach($champions as $champion)
                                                    <option value="{{ $champion->id }}" {{ old('red_champion_'.$i) == $champion->id ? 'selected' : '' }}>
                                                        {{ $champion->champion_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('red_champion_'.$i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row g-2 mt-1">
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-text">K</span>
                                                <input type="number" class="form-control @error('red_kills_'.$i) is-invalid @enderror" 
                                                       id="red_kills_{{ $i }}" name="red_kills_{{ $i }}" 
                                                       value="{{ old('red_kills_'.$i, 0) }}" min="0" required>
                                                @error('red_kills_'.$i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-text">D</span>
                                                <input type="number" class="form-control @error('red_deaths_'.$i) is-invalid @enderror" 
                                                       id="red_deaths_{{ $i }}" name="red_deaths_{{ $i }}" 
                                                       value="{{ old('red_deaths_'.$i, 0) }}" min="0" required>
                                                @error('red_deaths_'.$i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-text">A</span>
                                                <input type="number" class="form-control @error('red_assists_'.$i) is-invalid @enderror" 
                                                       id="red_assists_{{ $i }}" name="red_assists_{{ $i }}" 
                                                       value="{{ old('red_assists_'.$i, 0) }}" min="0" required>
                                                @error('red_assists_'.$i)
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('matches.index') }}" class="btn btn-secondary">취소</a>
                <button type="submit" class="btn btn-primary">저장</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // 플레이어 중복 체크
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('match-form');
        form.addEventListener('submit', function(e) {
            const bluePlayers = [];
            const redPlayers = [];
            
            for (let i = 1; i <= 5; i++) {
                const bluePlayer = document.getElementById(`blue_player_${i}`).value;
                const redPlayer = document.getElementById(`red_player_${i}`).value;
                
                if (bluePlayer && bluePlayers.includes(bluePlayer)) {
                    e.preventDefault();
                    alert('블루팀에 중복된 플레이어가 있습니다.');
                    return;
                }
                
                if (redPlayer && redPlayers.includes(redPlayer)) {
                    e.preventDefault();
                    alert('레드팀에 중복된 플레이어가 있습니다.');
                    return;
                }
                
                if (bluePlayer && redPlayer && bluePlayer === redPlayer) {
                    e.preventDefault();
                    alert('동일한 플레이어가 양팀에 모두 있습니다.');
                    return;
                }
                
                if (bluePlayer) bluePlayers.push(bluePlayer);
                if (redPlayer) redPlayers.push(redPlayer);
            }
        });
    });
</script>
@endsection
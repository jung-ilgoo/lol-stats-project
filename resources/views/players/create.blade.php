@extends('layouts.app')

@section('title', '새 플레이어 등록')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">새 플레이어 등록</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('players.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="player_name" class="form-label">플레이어 이름</label>
                <input type="text" class="form-control @error('player_name') is-invalid @enderror" 
                       id="player_name" name="player_name" value="{{ old('player_name') }}" required>
                @error('player_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="rank" class="form-label">랭크</label>
                <select class="form-select @error('rank') is-invalid @enderror" 
                        id="rank" name="rank" required>
                    <option value="">랭크 선택</option>
                    <option value="Iron" {{ old('rank') == 'Iron' ? 'selected' : '' }}>아이언</option>
                    <option value="Bronze" {{ old('rank') == 'Bronze' ? 'selected' : '' }}>브론즈</option>
                    <option value="Silver" {{ old('rank') == 'Silver' ? 'selected' : '' }}>실버</option>
                    <option value="Gold" {{ old('rank') == 'Gold' ? 'selected' : '' }}>골드</option>
                    <option value="Platinum" {{ old('rank') == 'Platinum' ? 'selected' : '' }}>플래티넘</option>
                    <option value="Diamond" {{ old('rank') == 'Diamond' ? 'selected' : '' }}>다이아몬드</option>
                    <option value="Master" {{ old('rank') == 'Master' ? 'selected' : '' }}>마스터</option>
                    <option value="Grandmaster" {{ old('rank') == 'Grandmaster' ? 'selected' : '' }}>그랜드마스터</option>
                    <option value="Challenger" {{ old('rank') == 'Challenger' ? 'selected' : '' }}>챌린저</option>
                </select>
                @error('rank')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="main_positions" class="form-label">주 포지션</label>
                <input type="text" class="form-control @error('main_positions') is-invalid @enderror" 
                       id="main_positions" name="main_positions" value="{{ old('main_positions') }}" 
                       placeholder="예: Top, Mid, Jungle" required>
                <div class="form-text">주 포지션을 쉼표로 구분하여 입력하세요 (예: Top, Mid)</div>
                @error('main_positions')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('players.index') }}" class="btn btn-secondary">취소</a>
                <button type="submit" class="btn btn-primary">저장</button>
            </div>
        </form>
    </div>
</div>
@endsection
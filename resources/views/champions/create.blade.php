@extends('layouts.app')

@section('title', '새 챔피언 등록')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">새 챔피언 등록</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('champions.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="champion_name" class="form-label">챔피언 이름</label>
                <input type="text" class="form-control @error('champion_name') is-invalid @enderror" 
                       id="champion_name" name="champion_name" value="{{ old('champion_name') }}" required>
                @error('champion_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('champions.index') }}" class="btn btn-secondary">취소</a>
                <button type="submit" class="btn btn-primary">저장</button>
            </div>
        </form>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', '팀 밸런스 계산')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">팀 밸런스 계산</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <p><strong>팀 밸런스 계산 방법:</strong></p>
            <ul>
                <li>10명의 플레이어를 선택하세요.</li>
                <li>승률 기반 밸런스: 플레이어 승률을 기준으로 가장 균형 잡힌 팀을 구성합니다.</li>
                <li>포지션 고려 밸런스: 플레이어의 포지션별 성과를 고려하여 밸런스를 계산합니다.</li>
            </ul>
        </div>
        
        <form action="{{ route('team-balance.calculate') }}" method="POST" id="balance-form">
            @csrf
            
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">밸런스 옵션</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">밸런스 계산 방식:</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="balance_type" 
                                               id="balance_type_win_rate" value="win_rate" checked>
                                        <label class="form-check-label" for="balance_type_win_rate">
                                            승률 기반 밸런스
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="balance_type" 
                                               id="balance_type_position" value="position">
                                        <label class="form-check-label" for="balance_type_position">
                                            포지션 고려 밸런스
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-3">
                    <h5 class="mb-3">플레이어 선택 (10명)</h5>
                    <div class="selected-players-count mb-2">
                        선택된 플레이어: <span id="selected-count">0</span>/10
                    </div>
                    <div class="alert alert-warning" id="player-warning" style="display: none;">
                        정확히 10명의 플레이어를 선택해야 합니다.
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="row player-selection">
                        @foreach($players as $player)
                        <div class="col-md-3 mb-3">
                            <div class="card player-card">
                                <div class="card-body p-2">
                                    <div class="form-check">
                                        <input class="form-check-input player-checkbox" type="checkbox" 
                                               name="players[]" value="{{ $player->id }}" 
                                               id="player_{{ $player->id }}">
                                        <label class="form-check-label" for="player_{{ $player->id }}">
                                            {{ $player->player_name }} ({{ $player->rank }})
                                            <div class="text-muted small">{{ $player->main_positions }}</div>
                                        </label>
                                    </div>
                                    
                                    <div class="position-select mt-2" style="display: none;">
                                        <select class="form-select form-select-sm" name="positions[]" disabled>
                                            <option value="">포지션 선택</option>
                                            <option value="top">Top</option>
                                            <option value="jungle">Jungle</option>
                                            <option value="mid">Mid</option>
                                            <option value="adc">ADC</option>
                                            <option value="support">Support</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="col-md-12 mt-3">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg" id="calculate-btn" disabled>
                            팀 밸런스 계산하기
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const playerCheckboxes = document.querySelectorAll('.player-checkbox');
        const calculateBtn = document.getElementById('calculate-btn');
        const selectedCountElem = document.getElementById('selected-count');
        const playerWarning = document.getElementById('player-warning');
        const positionSelects = document.querySelectorAll('.position-select');
        const balanceTypeRadios = document.querySelectorAll('input[name="balance_type"]');
        
        // 밸런스 타입 변경 이벤트
        balanceTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const isPositionBased = this.value === 'position';
                
                positionSelects.forEach(select => {
                    if (isPositionBased) {
                        select.style.display = 'block';
                    } else {
                        select.style.display = 'none';
                    }
                });
                
                updatePositionSelectsState();
            });
        });
        
        // 플레이어 선택 이벤트
        playerCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const selectedCount = document.querySelectorAll('.player-checkbox:checked').length;
                selectedCountElem.textContent = selectedCount;
                
                if (selectedCount === 10) {
                    calculateBtn.disabled = false;
                    playerWarning.style.display = 'none';
                } else {
                    calculateBtn.disabled = true;
                    if (selectedCount > 10) {
                        playerWarning.textContent = '10명보다 많은 플레이어가 선택되었습니다. 정확히 10명을 선택하세요.';
                        playerWarning.style.display = 'block';
                    } else if (selectedCount < 10) {
                        playerWarning.textContent = '10명보다 적은 플레이어가 선택되었습니다. 정확히 10명을 선택하세요.';
                        playerWarning.style.display = selectedCount > 0 ? 'block' : 'none';
                    }
                }
                
                updatePositionSelectsState();
            });
        });
        
        // 포지션 선택 상태 업데이트
        function updatePositionSelectsState() {
            const isPositionBased = document.getElementById('balance_type_position').checked;
            
            playerCheckboxes.forEach((checkbox, index) => {
                const selectElem = positionSelects[index].querySelector('select');
                
                if (checkbox.checked && isPositionBased) {
                    selectElem.disabled = false;
                    selectElem.required = true;
                } else {
                    selectElem.disabled = true;
                    selectElem.required = false;
                }
            });
        }
        
        // 폼 제출 전 유효성 검사
        document.getElementById('balance-form').addEventListener('submit', function(e) {
            const selectedCount = document.querySelectorAll('.player-checkbox:checked').length;
            
            if (selectedCount !== 10) {
                e.preventDefault();
                playerWarning.textContent = '정확히 10명의 플레이어를 선택해야 합니다.';
                playerWarning.style.display = 'block';
                return;
            }
            
            const isPositionBased = document.getElementById('balance_type_position').checked;
            
            if (isPositionBased) {
                const selectedPositions = [];
                const positionSelects = document.querySelectorAll('.player-checkbox:checked')
                    .forEach(checkbox => {
                        const playerCard = checkbox.closest('.player-card');
                        const selectElem = playerCard.querySelector('select');
                        
                        if (!selectElem.value) {
                            e.preventDefault();
                            alert('모든 선택된 플레이어의 포지션을 선택해주세요.');
                            return;
                        }
                        
                        if (selectedPositions.includes(selectElem.value)) {
                            e.preventDefault();
                            alert('중복된 포지션이 있습니다. 각 포지션은 한 번씩만 선택해야 합니다.');
                            return;
                        }
                        
                        selectedPositions.push(selectElem.value);
                    });
                
                // 모든 포지션이 선택되었는지 확인
                const requiredPositions = ['top', 'jungle', 'mid', 'adc', 'support'];
                const allPositionsSelected = requiredPositions.every(pos => 
                    selectedPositions.filter(p => p === pos).length === 2
                );
                
                if (!allPositionsSelected) {
                    e.preventDefault();
                    alert('각 팀에 모든 포지션(Top, Jungle, Mid, ADC, Support)이 필요합니다.');
                    return;
                }
            }
        });
    });
</script>
@endsection
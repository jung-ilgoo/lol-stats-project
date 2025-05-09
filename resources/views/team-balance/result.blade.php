@extends('layouts.app')

@section('title', '팀 밸런스 결과')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">팀 밸런스 결과</h5>
        <a href="{{ route('team-balance.index') }}" class="btn btn-primary">
            <i class="fas fa-calculator"></i> 새로운 팀 밸런스 계산
        </a>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <p><strong>팀 밸런스 점수: {{ number_format($result['balance_score'], 3) }}</strong></p>
            <p>
                @if($balanceType == 'win_rate')
                    평균 승률 차이: {{ number_format($result['avg_win_rate_diff'], 2) }}%
                @else
                    평균 포지션 승률 차이: {{ number_format($result['avg_position_win_rate_diff'], 2) }}%
                @endif
            </p>
            <p class="mb-0">
                균형 점수가 낮을수록 더 균형 잡힌 팀 구성입니다. 0에 가까울수록 이상적입니다.
            </p>
        </div>
        
        <div class="row">
            <!-- 블루팀 -->
            <div class="col-md-6">
                <div class="card mb-4 team-blue">
                    <div class="card-header">
                        <h5 class="mb-0">블루팀</h5>
                        @if($balanceType == 'win_rate')
                            <div class="text-muted">평균 승률: {{ number_format($result['blue_team_avg_win_rate'] * 100, 2) }}%</div>
                        @else
                            <div class="text-muted">평균 포지션 승률: {{ number_format($result['blue_team_avg_position_win_rate'] * 100, 2) }}%</div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        @if($balanceType == 'position')
                                            <th>포지션</th>
                                        @endif
                                        <th>플레이어</th>
                                        <th>랭크</th>
                                        <th>주 포지션</th>
                                        @if($balanceType == 'win_rate')
                                            <th>승률</th>
                                        @else
                                            <th>포지션 승률</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($result['blue_team'] as $player)
                                    <tr>
                                        @if($balanceType == 'position')
                                            <td>{{ ucfirst($player['position']) }}</td>
                                        @endif
                                        <td>
                                            <a href="{{ route('players.show', $player['player']->id) }}">
                                                {{ $player['player']->player_name }}
                                            </a>
                                        </td>
                                        <td>{{ $player['details']->rank }}</td>
                                        <td>{{ $player['details']->main_positions }}</td>
                                        @if($balanceType == 'win_rate')
                                            <td>{{ number_format($player['win_rate'] * 100, 2) }}%</td>
                                        @else
                                            <td>{{ number_format($player['position_win_rate'] * 100, 2) }}%</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 레드팀 -->
            <div class="col-md-6">
                <div class="card mb-4 team-red">
                    <div class="card-header">
                        <h5 class="mb-0">레드팀</h5>
                        @if($balanceType == 'win_rate')
                            <div class="text-muted">평균 승률: {{ number_format($result['red_team_avg_win_rate'] * 100, 2) }}%</div>
                        @else
                            <div class="text-muted">평균 포지션 승률: {{ number_format($result['red_team_avg_position_win_rate'] * 100, 2) }}%</div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        @if($balanceType == 'position')
                                            <th>포지션</th>
                                        @endif
                                        <th>플레이어</th>
                                        <th>랭크</th>
                                        <th>주 포지션</th>
                                        @if($balanceType == 'win_rate')
                                            <th>승률</th>
                                        @else
                                            <th>포지션 승률</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($result['red_team'] as $player)
                                    <tr>
                                        @if($balanceType == 'position')
                                            <td>{{ ucfirst($player['position']) }}</td>
                                        @endif
                                        <td>
                                            <a href="{{ route('players.show', $player['player']->id) }}">
                                                {{ $player['player']->player_name }}
                                            </a>
                                        </td>
                                        <td>{{ $player['details']->rank }}</td>
                                        <td>{{ $player['details']->main_positions }}</td>
                                        @if($balanceType == 'win_rate')
                                            <td>{{ number_format($player['win_rate'] * 100, 2) }}%</td>
                                        @else
                                            <td>{{ number_format($player['position_win_rate'] * 100, 2) }}%</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">밸런스 정보</h5>
                    </div>
                    <div class="card-body">
                        <p>밸런스 방식: <strong>{{ $balanceType == 'win_rate' ? '승률 기반' : '포지션 고려' }}</strong></p>
                        
                        <p>이 결과는 {{ $balanceType == 'win_rate' ? '플레이어 전체 승률' : '포지션별 성과' }}를 기준으로 
                           최적의 밸런스를 찾은 결과입니다.</p>
                        
                        <div class="alert alert-warning">
                            <p><strong>참고 사항:</strong></p>
                            <ul>
                                <li>이 계산은 과거 기록에 기반하여 밸런스를 예측한 것입니다.</li>
                                <li>실제 경기 결과는 플레이어의 컨디션, 챔피언 선택 등 다양한 요소에 따라 달라질 수 있습니다.</li>
                                <li>두 팀의 평균 승률 차이가 적을수록 더 균형 잡힌 경기가 예상됩니다.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
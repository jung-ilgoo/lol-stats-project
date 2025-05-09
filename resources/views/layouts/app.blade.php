<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '팀 밸런스 시스템')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- 커스텀 CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    
    @yield('styles')
</head>
<body>
    <!-- 네비게이션 메뉴 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">팀 밸런스 시스템</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">홈</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('players.*') ? 'active' : '' }}" href="{{ route('players.index') }}">플레이어</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('champions.*') ? 'active' : '' }}" href="{{ route('champions.index') }}">챔피언</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('matches.*') ? 'active' : '' }}" href="{{ route('matches.index') }}">경기 기록</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('rankings.*') ? 'active' : '' }}" href="{{ route('rankings.index') }}">랭킹</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('team-balance.*') ? 'active' : '' }}" href="{{ route('team-balance.index') }}">팀 밸런스</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 메인 컨텐츠 -->
    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
    </div>

    <!-- 푸터 -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p>&copy; {{ date('Y') }} 팀 밸런스 시스템. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- 커스텀 JavaScript -->
    <script src="{{ asset('js/script.js') }}"></script>
    
    @yield('scripts')
</body>
</html>
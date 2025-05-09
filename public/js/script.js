// 공통 함수 및 이벤트 핸들러

// 페이지 로딩이 완료되면 실행
document.addEventListener('DOMContentLoaded', function() {
    // 경고창 자동 닫기 (5초 후)
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // 툴팁 초기화 (Bootstrap)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // 데이터 테이블 정렬 기능 (옵션)
    if(typeof $.fn.DataTable !== 'undefined' && $('.data-table').length > 0) {
        $('.data-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Korean.json"
            }
        });
    }
});

// KDA 자동 계산 함수
function calculateKDA(kills, deaths, assists) {
    if(deaths == 0) {
        return (parseInt(kills) + parseInt(assists)).toFixed(2);
    }
    return ((parseInt(kills) + parseInt(assists)) / parseInt(deaths)).toFixed(2);
}

// 팀 교체 함수
function swapTeams() {
    // 블루팀 데이터 저장
    let blueTeamData = {};
    for(let i = 1; i <= 5; i++) {
        blueTeamData[`player_${i}`] = document.querySelector(`#blue_player_${i}`).value;
        blueTeamData[`champion_${i}`] = document.querySelector(`#blue_champion_${i}`).value;
        blueTeamData[`kills_${i}`] = document.querySelector(`#blue_kills_${i}`).value;
        blueTeamData[`deaths_${i}`] = document.querySelector(`#blue_deaths_${i}`).value;
        blueTeamData[`assists_${i}`] = document.querySelector(`#blue_assists_${i}`).value;
    }
    
    // 블루팀에 레드팀 데이터 설정
    for(let i = 1; i <= 5; i++) {
        document.querySelector(`#blue_player_${i}`).value = document.querySelector(`#red_player_${i}`).value;
        document.querySelector(`#blue_champion_${i}`).value = document.querySelector(`#red_champion_${i}`).value;
        document.querySelector(`#blue_kills_${i}`).value = document.querySelector(`#red_kills_${i}`).value;
        document.querySelector(`#blue_deaths_${i}`).value = document.querySelector(`#red_deaths_${i}`).value;
        document.querySelector(`#blue_assists_${i}`).value = document.querySelector(`#red_assists_${i}`).value;
    }
    
    // 레드팀에 저장해둔 블루팀 데이터 설정
    for(let i = 1; i <= 5; i++) {
        document.querySelector(`#red_player_${i}`).value = blueTeamData[`player_${i}`];
        document.querySelector(`#red_champion_${i}`).value = blueTeamData[`champion_${i}`];
        document.querySelector(`#red_kills_${i}`).value = blueTeamData[`kills_${i}`];
        document.querySelector(`#red_deaths_${i}`).value = blueTeamData[`deaths_${i}`];
        document.querySelector(`#red_assists_${i}`).value = blueTeamData[`assists_${i}`];
    }
}
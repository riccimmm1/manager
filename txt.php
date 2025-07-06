<?php
define('_IN_JOHNCMS', 1);
$headmod = 'txt';
require_once("../incfiles/core.php");
require_once("../incfiles/head.php");
require_once("func_game.php");
require_once("style2.css");

// Используем подключение из ядра JohnCMS
if (!$db_host || !$db_name || !$db_user) {
    die('Database configuration error');
}

$db = @mysqli_connect(
    $db_host,
    $db_user,
    $db_pass,
    $db_name
);

if (!$db) {
    die('Ошибка подключения к базе данных: ' . mysqli_connect_error());
}

// Функция для безопасной работы с БД
function recordMatchEvent($match_id, $minute, $event_type, $team, $player_id, $x, $y, $to_x = null, $to_y = null, $comment = '') {
    global $db;
    
    $stmt = mysqli_prepare($db, "INSERT INTO r_match_replay 
        (match_id, minute, event_type, team, player_id, x, y, to_x, to_y, comment) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) return false;
    
    mysqli_stmt_bind_param($stmt, "idsiiiiids", 
        $match_id,
        $minute,
        $event_type,
        $team,
        $player_id,
        $x,
        $y,
        $to_x,
        $to_y,
        $comment
    );
    
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

// Получение ID матча
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die('Неверный ID матча');

// Загрузка данных матча
$match = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM r_game WHERE id = $id"));
if (!$match) die('Матч не найден');

// Симуляция матча (если еще не была проведена)
if ($match['status'] == 'scheduled') {
    simulateMatch($id);
    // Перезагружаем данные матча после симуляции
    $match = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM r_game WHERE id = $id"));
}

// Расчет времени матча
$realtime = time();
$mt = max(0, min(93, floor(($realtime - $match['time']) * 18 / 60)));

// Загрузка информации о командах
$team1 = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM r_team WHERE id = {$match['id_team1']}"));
$team2 = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM r_team WHERE id = {$match['id_team2']}"));

if (!$team1 || !$team2) die('Команды не найдены');

// Загрузка событий матча
$events = [];
$result = mysqli_query($db, "SELECT * FROM r_match_replay WHERE match_id = $id ORDER BY minute, id");
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}

// Подсчет голов
$goal1 = $goal2 = 0;
foreach ($events as $event) {
    if ($event['event_type'] == 'goal') {
        if ($event['team'] == 1) $goal1++;
        else $goal2++;
    }
}

// Загрузка игроков
function loadPlayers($team_id) {
    global $db;
    $players = [];
    $result = mysqli_query($db, "SELECT * FROM r_player WHERE team = $team_id AND sostav = 1");
    while ($row = mysqli_fetch_assoc($result)) {
        $players[] = $row;
    }
    return $players;
}

$players1 = loadPlayers($match['id_team1']);
$players2 = loadPlayers($match['id_team2']);

// Автоматическое создание недостающих игроков
$default_poz = ['GK', 'LD', 'CD', 'CD', 'RD', 'LM', 'CM', 'CM', 'RM', 'ST', 'ST'];
  
function createDefaultPlayers($team_prefix, $count, $default_poz) {
    $players = [];
    $poz_count = count($default_poz);
    
    for ($i = 1; $i <= $count; $i++) {
        $poz_index = ($i - 1) % $poz_count;
        $poz = $default_poz[$poz_index];
        
        $line = 3; // По умолчанию
        if ($poz == 'GK') $line = 1;
        elseif (in_array($poz, ['LD', 'CD', 'RD'])) $line = 2;
        elseif (in_array($poz, ['AM', 'ST'])) $line = 4;
        
        $players[] = [
            'id' => $team_prefix * 1000 + $i,
            'nomer' => $i,
            'name' => 'Игрок ' . $team_prefix . '.' . $i,
            'poz' => $poz,
            'line' => $line,
            'x' => 0,
            'y' => 0
        ];
    }
    return $players;
}

if (count($players1) < 11) {
    $players1 = array_merge($players1, createDefaultPlayers(1, 11 - count($players1), $default_poz));
}
if (count($players2) < 11) {
    $players2 = array_merge($players2, createDefaultPlayers(2, 11 - count($players2), $default_poz));
}

// Загрузка данных стадиона
$stadium_data = ['name' => 'Неизвестный стадион'];
if (!empty($match['id_stadium'])) {
    $result = mysqli_query($db, "SELECT name FROM r_stadium WHERE id = {$match['id_stadium']} LIMIT 1");
    if ($row = mysqli_fetch_assoc($result)) {
        $stadium_data = $row;
    }
}

// Функция симуляции матча
function simulateMatch($match_id) {
    global $db;
    
    // Загрузка данных матча
    $match = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM r_game WHERE id = $match_id"));
    if (!$match) return false;
    
    // Загрузка команд
    $team1 = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM r_team WHERE id = {$match['id_team1']}"));
    $team2 = mysqli_fetch_assoc(mysqli_query($db, "SELECT * FROM r_team WHERE id = {$match['id_team2']}"));
    
    // Загрузка игроков
    $players1 = loadPlayers($match['id_team1']);
    $players2 = loadPlayers($match['id_team2']);
    
    // Инициализация параметров
    $score1 = 0;
    $score2 = 0;
    $minute = 0;
    $max_minute = 90;
    
    // Запись старта матча
    recordMatchEvent($match_id, 0, 'start', null, null, 50, 50, null, null, 'Начало матча');
    
    // Основной цикл симуляции
    while ($minute <= $max_minute) {
        // Перерыв между таймами
        if ($minute >= 45 && $minute <= 45.5) {
            $minute = 45.5;
            recordMatchEvent($match_id, 45, 'break', null, null, 50, 50, null, null, 'Перерыв');
            continue;
        }
        
        // Случайное событие
        if (mt_rand(1, 100) <= 15) {
            $event_type = getRandomEvent();
            $team = mt_rand(1, 2);
            $players = ($team == 1) ? $players1 : $players2;
            $player = $players[array_rand($players)];
            
            // Координаты события
            $x = mt_rand(10, 90);
            $y = mt_rand(10, 90);
            $to_x = null;
            $to_y = null;
            $comment = '';
            
            // Обработка событий
            switch ($event_type) {
                case 'goal':
                    if ($team == 1) $score1++;
                    else $score2++;
                    $comment = "Гол! {$player['name']}";
                    recordMatchEvent($match_id, $minute, 'goal', $team, $player['id'], $x, $y, null, null, $comment);
                    recordMatchEvent($match_id, $minute, 'pass', $team, $player['id'], 50, 50, 50, 50, 'Начало игры');
                    break;
                    
                case 'yellow_card':
                    $comment = "Желтая карточка. {$player['name']}";
                    recordMatchEvent($match_id, $minute, 'yellow_card', $team, $player['id'], $x, $y, null, null, $comment);
                    break;
                    
                case 'red_card':
                    $comment = "Красная карточка. {$player['name']} удален!";
                    recordMatchEvent($match_id, $minute, 'red_card', $team, $player['id'], $x, $y, null, null, $comment);
                    break;
                    
                case 'pass':
                    $to_x = mt_rand(max(0, $x-30), min(100, $x+30));
                    $to_y = mt_rand(max(0, $y-30), min(100, $y+30));
                    $comment = "Передача от {$player['name']}";
                    recordMatchEvent($match_id, $minute, 'pass', $team, $player['id'], $x, $y, $to_x, $to_y, $comment);
                    break;
                    
                case 'shot':
                    $comment = "Удар по воротам от {$player['name']}";
                    recordMatchEvent($match_id, $minute, 'shot', $team, $player['id'], $x, $y, null, null, $comment);
                    break;
                    
                case 'corner':
                    $comment = "Угловой удар";
                    $x = ($team == 1) ? 100 : 0;
                    $y = mt_rand(30, 70);
                    recordMatchEvent($match_id, $minute, 'corner', $team, $player['id'], $x, $y, null, null, $comment);
                    break;
                    
                case 'foul':
                    $comment = "Нарушение правил";
                    recordMatchEvent($match_id, $minute, 'foul', $team, $player['id'], $x, $y, null, null, $comment);
                    break;
            }
        }
        
        // Переход к следующей минуте
        $minute += 0.1;
    }
    
    // Запись финального свистка
    recordMatchEvent($match_id, $max_minute, 'finish', null, null, 50, 50, null, null, 'Матч завершен');
    
    // Обновление счета и статуса матча
    mysqli_query($db, "UPDATE r_game 
        SET score1 = $score1, 
            score2 = $score2,
            status = 'completed'
        WHERE id = $match_id");
    
    return true;
}

function getRandomEvent() {
    $events = [
        'pass' => 40,
        'shot' => 20,
        'foul' => 15,
        'corner' => 10,
        'yellow_card' => 7,
        'goal' => 5,
        'red_card' => 3
    ];
    
    $rand = mt_rand(1, 100);
    $cumulative = 0;
    
    foreach ($events as $event => $probability) {
        $cumulative += $probability;
        if ($rand <= $cumulative) {
            return $event;
        }
    }
    
    return 'pass';
}

// Функция отрисовки игроков
function renderPlayers($players, $team_class, $is_home) {
    $output = '';
    foreach ($players as $player) {
        $x = $is_home ? $player['x'] : 100 - $player['x'];
        $y = $player['y'];
        $output .= sprintf(
            '<div class="player team%d-player" style="left:%d%%;top:%d%%;" data-id="%d" data-team="%d">%d</div>',
            $team_class,
            $x,
            $y,
            $player['id'],
            $team_class,
            $player['nomer']
        );
    }
    return $output;
}

// Загрузка менеджеров команд
function loadManager($team) {
    global $db;
    if (!$team['id_admin']) return '';
    
    $result = mysqli_query($db, "SELECT * FROM users WHERE id = {$team['id_admin']} LIMIT 1");
    if (!$manager = mysqli_fetch_assoc($result)) return '';
    
    $vipData = [
        0 => ['img' => 'vip0_m.png', 'title' => 'Базовый аккаунт'],
        1 => ['img' => 'vip1_m.png', 'title' => 'Премиум-аккаунт'],
        2 => ['img' => 'vip2_m.png', 'title' => 'VIP-аккаунт'],
        3 => ['img' => 'vip3_m.png', 'title' => 'Gold-аккаунт']
    ];
    
    $vipLevel = isset($manager['vip']) ? $manager['vip'] : 0;
    $vipInfo = isset($vipData[$vipLevel]) ? $vipData[$vipLevel] : $vipData[0];
    
    return '<span style="opacity:0.4">
        <img src="/images/ico/'.$vipInfo['img'].'" title="'.$vipInfo['title'].'" style="width:12px;border:none;vertical-align:middle;">
        ' . htmlspecialchars($manager['name']) . '
    </span>';
}

// Подготовка данных для JavaScript
$events_js = json_encode($events, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
$players1_js = json_encode(array_values($players1));
$players2_js = json_encode(array_values($players2));

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2D Симмуляция матча</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #1a2a6c);
            color: #fff;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .match-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 20px;
        }

        .team-info {
            display: flex;
            align-items: center;
            flex: 1;
        }

        .team-name {
            font-size: 1.8rem;
            font-weight: bold;
            text-align: center;
            padding: 0 20px;
        }

        .team-logo-container {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .team-logo {
            max-width: 90%;
            max-height: 90%;
        }

        .match-score-time {
            text-align: center;
            flex: 0 0 200px;
        }

        .match-score {
            font-size: 3rem;
            font-weight: bold;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
            margin-bottom: 5px;
        }

        .match-minute {
            font-size: 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
        }

        .match-info {
            text-align: center;
            padding: 10px 0;
            margin-bottom: 20px;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 10px;
        }

        .match-time {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffcc00;
            margin-bottom: 5px;
        }

        .match-stadium {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .timeline-container {
            position: relative;
            height: 40px;
            margin-bottom: 25px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
        }

        .timeline {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: var(--progress, 0%);
            background: linear-gradient(90deg, #00b09b, #96c93d);
            border-radius: 20px;
            transition: width 0.5s linear;
        }

        .current-time {
            position: absolute;
            top: -30px;
            left: var(--progress, 0%);
            transform: translateX(-50%);
            background: #ff5722;
            color: white;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: bold;
            white-space: nowrap;
        }

        .timeline-markers {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
        }

        .timeline-marker {
            width: 3px;
            height: 20px;
            background: rgba(255, 255, 255, 0.3);
        }

        .timeline-event {
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            top: 50%;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: all 0.3s;
        }

        .timeline-event:hover {
            transform: translate(-50%, -50%) scale(1.5);
        }

        .goal-event-marker {
            background: #ffeb3b;
            color: #000;
            box-shadow: 0 0 10px #ffeb3b;
        }

        .yellow-card-event-marker {
            background: #ffc107;
            color: #000;
            box-shadow: 0 0 10px #ffc107;
        }

        .red-card-event-marker {
            background: #f44336;
            color: #fff;
            box-shadow: 0 0 10px #f44336;
        }

        .corner-event-marker {
            background: #4caf50;
            color: #fff;
            box-shadow: 0 0 10px #4caf50;
        }

        .penalty-event-marker {
            background: #9c27b0;
            color: #fff;
            box-shadow: 0 0 10px #9c27b0;
        }

        .foul-event-marker {
            background: #ff9800;
            color: #000;
            box-shadow: 0 0 10px #ff9800;
        }

        .pitch-container {
            position: relative;
            width: 100%;
            padding-top: 60%;
            margin-bottom: 20px;
            background: #2e7d32;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            border: 5px solid #f5f5f5;
        }

        #pitch {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .pitch-markings {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 3px solid white;
        }

        .center-line {
            position: absolute;
            top: 0;
            left: 50%;
            width: 3px;
            height: 100%;
            background: white;
        }

        .center-circle {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 15%;
            height: 30%;
            border: 3px solid white;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        .penalty-area-home, .penalty-area-away {
            position: absolute;
            width: 20%;
            height: 60%;
            top: 20%;
            border: 3px solid white;
        }

        .penalty-area-home {
            left: 0;
        }

        .penalty-area-away {
            right: 0;
        }

        .goal-home, .goal-away {
            position: absolute;
            width: 2%;
            height: 25%;
            top: 37.5%;
            background: white;
        }

        .goal-home {
            left: 0;
        }

        .goal-away {
            right: 0;
        }

        .player {
            position: absolute;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            transform: translate(-50%, -50%);
            z-index: 10;
            transition: all 0.5s ease;
        }

        .team1-player {
            background: linear-gradient(135deg, #005bbb, #004a99);
            color: white;
            border: 2px solid white;
        }

        .team2-player {
            background: linear-gradient(135deg, #ffcc00, #ff9900);
            color: #333;
            border: 2px solid white;
        }

        .ball-owner {
            box-shadow: 0 0 0 3px #ff0000;
            z-index: 20;
        }

        #game-ball {
            position: absolute;
            width: 18px;
            height: 18px;
            background: white;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            z-index: 15;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            transition: left 0.5s ease-out, top 0.5s ease-out;
        }

        .referee {
            position: absolute;
            font-size: 24px;
            transform: translate(-50%, -50%);
            z-index: 5;
            text-shadow: 0 0 5px black;
        }

        .commentary-box {
            height: 200px;
            overflow-y: auto;
            background: rgba(0, 0, 0, 0.4);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .commentary-entry {
            padding: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .event-icon {
            font-size: 1.5rem;
            margin-right: 10px;
            min-width: 30px;
            text-align: center;
        }

        .match-controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .control-btn {
            background: linear-gradient(135deg, #2196f3, #0d47a1);
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 1.1rem;
            border-radius: 50px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .control-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
        }

        .control-btn:disabled {
            background: #607d8b;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .goal-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,215,0,0.7) 0%, transparent 70%);
            animation: pulseGoal 1.5s ease-out;
            z-index: 100;
            pointer-events: none;
        }

        @keyframes pulseGoal {
            0% { opacity: 0; transform: scale(0.5); }
            50% { opacity: 1; }
            100% { opacity: 0; transform: scale(1.5); }
        }

        .event-indicator {
            position: absolute;
            font-size: 24px;
            z-index: 100;
            animation: fadeOut 1.5s forwards;
            transform: translate(-50%, -50%);
        }

        @keyframes fadeOut {
            0% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
            100% { opacity: 0; transform: translate(-50%, -50%) scale(2); }
        }

        .match-end {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            text-align: center;
        }

        .match-end h2 {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #ffcc00;
        }

        .match-end p {
            font-size: 2rem;
            margin-bottom: 30px;
        }

        .match-end-buttons {
            display: flex;
            gap: 20px;
        }

        #restart-btn, #close-btn {
            background: linear-gradient(135deg, #4caf50, #2e7d32);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
        }

        #close-btn {
            background: linear-gradient(135deg, #f44336, #d32f2f);
        }

        #restart-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(76, 175, 80, 0.7);
        }

        #close-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(244, 67, 54, 0.7);
        }

        @media (max-width: 768px) {
            .match-header {
                flex-direction: column;
            }
            
            .team-info {
                width: 100%;
                justify-content: center;
                margin-bottom: 15px;
            }
            
            .match-score-time {
                order: -1;
                margin-bottom: 15px;
            }
            
            .team-name {
                font-size: 1.5rem;
            }
            
            .match-score {
                font-size: 2.5rem;
            }
            
            .match-end-buttons {
                flex-direction: column;
                gap: 10px;
            }
            .event-indicator {
    position: absolute;
    font-size: 24px;
    z-index: 100;
    transform: translate(-50%, -50%);
    animation: pulse 1.5s ease-out;
}

@keyframes pulse {
    0% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    100% { transform: translate(-50%, -50%) scale(3); opacity: 0; }
}
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Шапка матча -->
    <div class="match-header">
        <div class="team-info">
            <?php if (!empty($team1['logo'])): ?>
                <a href="/team/<?= $team1['id'] ?>">
                    <img src="/manager/logo/big<?= htmlspecialchars($team1['logo']) ?>" alt="<?= htmlspecialchars($team1['name']) ?>"/>
                </a>
            <?php else: ?>
                <a href="/team/<?= $team1['id'] ?>">
                    <img src="/manager/logo/b_0.jpg" alt="<?= htmlspecialchars($team1['name']) ?>" width="37px"/>
                </a>
            <?php endif; ?>
            <div>
                <a href="/team/<?= $team1['id'] ?>"><?= htmlspecialchars($team1['name']) ?></a>
                <span class="flags c_<?= $team1['flag'] ?>_14" title=""></span>
                <br>
                <?= loadManager($team1) ?>
            </div>
        </div>
        
        <div class="match-score-time">
            <div class="match-score" id="match-score"><?= $goal1 ?> : <?= $goal2 ?></div>
            <div class="match-minute" id="match-minute">0'</div>
        </div>
        
        <div class="team-info">
            <?php if (!empty($team2['logo'])): ?>
                <a href="/team/<?= $team2['id'] ?>">
                    <img src="/manager/logo/big<?= htmlspecialchars($team2['logo']) ?>" alt="<?= htmlspecialchars($team2['name']) ?>"/>
                </a>
            <?php else: ?>
                <a href="/team/<?= $team2['id'] ?>">
                    <img src="/manager/logo/b_0.jpg" alt="<?= htmlspecialchars($team2['name']) ?>" width="37px"/>
                </a>
            <?php endif; ?>
            <div>
                <a href="/team/<?= $team2['id'] ?>"><?= htmlspecialchars($team2['name']) ?></a>
                <span class="flags c_<?= $team2['flag'] ?>_14" title=""></span>
                <br>
                <?= loadManager($team2) ?>
            </div>
        </div>
    </div>

   
    
    <!-- Информация о матче -->
    <div class="match-info">
        <div class="match-time">
            <?php if ($mt < 0): ?>
                Матч еще не начался
            <?php elseif ($mt <= 45): ?>
                <?= $mt ?> минута (1 тайм)
            <?php elseif ($mt <= 48): ?>
                Перерыв
            <?php elseif ($mt <= 93): ?>
                <?= $mt - 45 ?> минута (2 тайм)
            <?php else: ?>
                Матч завершен
            <?php endif; ?>
        </div>
        <div class="match-stadium">
            Стадион: <?= htmlspecialchars($stadium_data['name']) ?> | 
            Зрителей: <?= number_format($match['zritel'], 0, ',', ' ') ?>
        </div>
    </div>
    
    <!-- Тайм-лайн -->
    <div class="timeline-container">
        <div class="timeline" id="timeline">
            <div class="current-time" id="current-time">0'</div>
        </div>
        <div class="timeline-markers" id="timeline-markers"></div>
    </div>
    
    <!-- Футбольное поле -->
    <div class="pitch-container">
        <div id="pitch">
            <!-- Разметка поля -->
            <div class="pitch-markings"></div>
            <div class="center-line"></div>
            <div class="center-circle"></div>
            <div class="penalty-area-home"></div>
            <div class="penalty-area-away"></div>
            <div class="goal-home"></div>
            <div class="goal-away"></div>
            
            <!-- Игроки команд -->
            <div class="team1-players">
                <?= renderPlayers($players1, 1, true) ?>
            </div>
            <div class="team2-players">
                <?= renderPlayers($players2, 2, false) ?>
            </div>
            
            <!-- Мяч -->
            <div id="game-ball" class="ball"></div>
            
            <!-- Судья -->
            <div id="referee" class="referee">Р</div>
        </div>
    </div>
    
    <!-- Комментарии -->
    <div id="commentary" class="commentary-box"></div>
    
    <!-- Управление воспроизведением -->
    <div class="match-controls">
        <button class="control-btn" id="play-btn">
            <i class="fas fa-play"></i> Воспроизвести
        </button>
        <button class="control-btn" id="pause-btn" disabled>
            <i class="fas fa-pause"></i> Пауза
        </button>
        <button class="control-btn" id="reset-btn">
            <i class="fas fa-undo"></i> С начала
        </button>
        <button class="control-btn" id="speed-btn">
            <i class="fas fa-tachometer-alt"></i> Скорость: 1x
        </button>
    </div>
</div>


<script>
// Константы для позиций игроков
const POSITION_STRATEGY = {
    'GK': { minX: 5, maxX: 15, minY: 30, maxY: 70 },
    'LD': { minX: 15, maxX: 35, minY: 15, maxY: 35 },
    'CD': { minX: 15, maxX: 35, minY: 40, maxY: 60 },
    'RD': { minX: 15, maxX: 35, minY: 65, maxY: 85 },
    'DM': { minX: 35, maxX: 55, minY: 40, maxY: 60 },
    'LM': { minX: 35, maxX: 55, minY: 15, maxY: 35 },
    'CM': { minX: 35, maxX: 55, minY: 40, maxY: 60 },
    'RM': { minX: 35, maxX: 55, minY: 65, maxY: 85 },
    'AM': { minX: 55, maxX: 75, minY: 40, maxY: 60 },
    'ST': { minX: 75, maxX: 90, minY: 30, maxY: 70 }
};

// Объект анимации
const animation = {
    running: false,
    speed: 1,
    players: [],
    ball: { x: 50, y: 50 },
    ballOwner: null,
    events: <?= $events_js ?>,
    playerData: {
        team1: <?= $players1_js ?>,
        team2: <?= $players2_js ?>
    },
    currentTime: 0,
    matchEnded: false,
    lastTimestamp: null,
    frameId: null,
    playerLookup: {},
    referee: { x: 50, y: 50 },
    ballTarget: null,
    goalsTeam1: <?= $goal1 ?>,
    goalsTeam2: <?= $goal2 ?>,
    lastBallOwnerChange: 0
};

// Инициализация игроков
function initPlayers() {
    animation.players = [];
    animation.playerLookup = {};
    const isFirstHalf = animation.currentTime <= 45;
     // Удалите старых игроков
    document.querySelectorAll('.player').forEach(player => player.remove());
    
    // Добавьте новых игроков
    const pitch = document.getElementById('pitch');
    const team1Container = document.createElement('div');
    team1Container.className = 'team1-players';
    pitch.appendChild(team1Container);
    
    const team2Container = document.createElement('div');
    team2Container.className = 'team2-players';
    pitch.appendChild(team2Container);
    
    // Рендер игроков
    team1Container.innerHTML = renderPlayers(animation.playerData.team1, 1, true);
    team2Container.innerHTML = renderPlayers(animation.playerData.team2, 2, false);
    // Инициализация игроков команды 1
    document.querySelectorAll('.team1-player').forEach(playerEl => {
        initSinglePlayer(playerEl, 1, isFirstHalf);
    });
    
    // Инициализация игроков команды 2
    document.querySelectorAll('.team2-player').forEach(playerEl => {
        initSinglePlayer(playerEl, 2, isFirstHalf);
    });
    
    // Начальная позиция мяча
    animation.ball = { x: 50, y: 50 };
    animation.ballOwner = null;
    updateBallPosition();
    // Добавить позиционирование игроков по линиям
    animation.players.forEach(player => {
        const position = playerData?.poz || 'CM';
        const strategy = POSITION_STRATEGY[position] || POSITION_STRATEGY['CM'];
        
        player.x = strategy.minX + Math.random() * (strategy.maxX - strategy.minX);
        player.y = strategy.minY + Math.random() * (strategy.maxY - strategy.minY);
        
        // Для гостевой команды зеркалим позиции
        if (player.team === 2) {
            player.x = 100 - player.x;
        }
        
        // Обновить DOM-элемент
        player.element.style.left = player.x + '%';
        player.element.style.top = player.y + '%';
    });
    // Назначение мяча случайному игроку в центре
    const centerPlayers = animation.players.filter(p => {
        const data = getPlayerData(p.id);
        return data && ['CM', 'AM', 'DM'].includes(data.poz);
    });
    
    if (centerPlayers.length > 0) {
        const randomPlayer = centerPlayers[Math.floor(Math.random() * centerPlayers.length)];
        animation.ballOwner = randomPlayer.id;
        animation.ball.x = randomPlayer.x;
        animation.ball.y = randomPlayer.y;
        animation.lastBallOwnerChange = animation.currentTime;
    }
    
    // Очистка комментариев
    document.getElementById('commentary').innerHTML = '';
}

function initSinglePlayer(playerEl, team, isFirstHalf) {
    const playerId = parseInt(playerEl.dataset.id);
    const playerData = getPlayerData(playerId);
    
    // Получаем позицию игрока
    const position = playerData?.poz || 'CM';
    const strategy = POSITION_STRATEGY[position] || POSITION_STRATEGY['CM'];
    
    // Вычисляем позицию с учетом команды и тайма
    let x, y;
    if ((team === 1 && isFirstHalf) || (team === 2 && !isFirstHalf)) {
        // Команда 1 в первом тайме или команда 2 во втором тайме - слева
        x = strategy.minX + Math.random() * (strategy.maxX - strategy.minX);
        y = strategy.minY + Math.random() * (strategy.maxY - strategy.minY);
    } else {
        // Команда 2 в первом тайме или команда 1 во втором тайме - справа
        x = 100 - (strategy.minX + Math.random() * (strategy.maxX - strategy.minX));
        y = strategy.minY + Math.random() * (strategy.maxY - strategy.minY);
    }
    
    x = Math.max(2, Math.min(98, x));
    y = Math.max(2, Math.min(98, y));
    
    playerEl.style.left = x + '%';
    playerEl.style.top = y + '%';
    
    const playerObj = {
        id: playerId,
        element: playerEl,
        x: x, 
        y: y,
        team: team,
        targetX: x, 
        targetY: y,
        removed: false,
        speed: 3 + Math.random() * 2,
        hasBall: false
    };
    
    animation.players.push(playerObj);
    animation.playerLookup[playerId] = playerObj;
    return playerObj;
}

function getPlayerData(playerId) {
    const allPlayers = [...animation.playerData.team1, ...animation.playerData.team2];
    return allPlayers.find(p => p.id == playerId);
}

function updateBallPosition() {
    const ball = document.getElementById('game-ball');
    if (!ball) return;

    ball.style.left = animation.ball.x + '%';
    ball.style.top = animation.ball.y + '%';
    
    // Автоматическое обновление позиции при смене владельца
    if (animation.ballOwner) {
        const owner = animation.playerLookup[animation.ballOwner];
        if (owner) {
            animation.ball.x = owner.x;
            animation.ball.y = owner.y;
        }
    }
}

function animate(timestamp) {
    if (!animation.lastTimestamp) animation.lastTimestamp = timestamp;
    
    const deltaTime = (timestamp - animation.lastTimestamp) / 1000;
    animation.lastTimestamp = timestamp;
    
    if (animation.running) {
        const effectiveDelta = deltaTime * animation.speed;
        animation.currentTime = Math.min(93, animation.currentTime + effectiveDelta);
        
        updatePlayers(effectiveDelta);
        updateRefereePosition(effectiveDelta);
        updateBallPosition();
        updateTimeline();
        checkEvents();
    }
    
    // Условие завершения матча (93 минуты)
    if (animation.running && animation.currentTime < 93) {
        animation.frameId = requestAnimationFrame(animate);
    } else if (animation.currentTime >= 93) {
        endMatch();
    }
}

function initTimeline() {
    const timelineMarkers = document.getElementById('timeline-markers');
    if (!timelineMarkers) return;
    
    timelineMarkers.innerHTML = '';
    
    // Маркеры каждые 5 минут
    for (let i = 0; i <= 93; i += 5) {
        const marker = document.createElement('div');
        marker.className = 'timeline-marker';
        marker.style.left = `${(i / 93) * 100}%`;
        timelineMarkers.appendChild(marker);
    }
    
    // Маркеры событий
    animation.events.forEach(event => {
        const eventMarker = document.createElement('div');
        eventMarker.className = 'timeline-event';
        
        const eventType = getEventType(event);
        const eventData = getEventData(eventType, event);
        
        eventMarker.title = `${Math.floor(event.minute)}' - ${eventData.description}`;
        eventMarker.innerHTML = eventData.icon;
        eventMarker.classList.add(`${eventType}-event-marker`);
        
        eventMarker.style.left = `${(event.minute / 93) * 100}%`;
        timelineMarkers.appendChild(eventMarker);
    });
}

function getEventType(event) {
    // Сопоставление типов событий
    const typeMap = {
        'goal': 'goal',
        'yellow_card': 'yellow',
        'red_card': 'red',
        'corner': 'corner',
        'penalty': 'penalty',
        'foul': 'foul',
        'shot': 'shot',
        'pass': 'pass',
        'tackle': 'tackle',
        'warning': 'warning',
        'ofs': 'offside',
        'twist': 'start',
        'finish': 'finish',
        'break': 'break',
        'yellow': 'yellow_card',
        'red1': 'red_card',
        'red2': 'red_card',
        'goal1': 'goal',
        'goal2': 'goal'
    };
    
    return typeMap[event.event_type] || 'default';
}

function getEventTitle(event) {
    const minute = Math.floor(event.minute) + "'";
    let title = minute + ' - ';
    const eventType = getEventType(event);
    
    if (eventType === 'goal') {
        title += 'Гол! ' + (event.comment || '');
    } else if (eventType === 'yellow_card') {
        title += 'Желтая карточка';
    } else if (eventType === 'red_card') {
        title += 'Красная карточка';
    } else if (eventType === 'corner') {
        title += 'Угловой удар';
    } else if (eventType === 'penalty') {
        title += 'Пенальти';
    } else if (eventType === 'foul') {
        title += 'Нарушение правил';
    } else if (eventType === 'shot') {
        title += 'Удар по воротам';
    } else if (eventType === 'pass') {
        title += 'Передача';
    } else if (eventType === 'tackle') {
        title += 'Отбор мяча';
    } else if (eventType === 'warning') {
        title += 'Игровой момент';
    } else if (eventType === 'offside') {
        title += 'Офсайд';
    } else if (eventType === 'start') {
        title += 'Начало матча';
    } else if (eventType === 'finish') {
        title += 'Конец матча';
    } else {
        title += 'Игровое событие';
    }
    
    return title;
}

function addEventCommentary(event) {
    const commentaryBox = document.getElementById('commentary');
    if (!commentaryBox) return;
    
    const entry = document.createElement('div');
    entry.className = 'commentary-entry';
    
    const eventType = getEventType(event);
    const eventData = getEventData(eventType, event);
    
    entry.innerHTML = `
        <span class="event-icon">${eventData.icon}</span>
        <strong>${Math.floor(event.minute)}'</strong>: ${event.comment || eventData.description}
    `;
    commentaryBox.appendChild(entry);
    commentaryBox.scrollTop = commentaryBox.scrollHeight;
}

function getEventData(eventType, event) {
    const defaultComment = {
        goal: 'Гол!',
        yellow_card: 'Желтая карточка',
        red_card: 'Красная карточка',
        corner: 'Угловой удар',
        penalty: 'Пенальти',
        foul: 'Нарушение правил',
        shot: 'Удар по воротам',
        pass: 'Передача',
        tackle: 'Отбор мяча',
        warning: 'Игровой момент',
        offside: 'Офсайд',
        start: 'Начало матча',
        finish: 'Конец матча',
        default: 'Игровое событие'
    };
    
    const icons = {
        goal: '⚽',
        yellow_card: '🟨',
        red_card: '🟥',
        corner: '🚩',
        penalty: '💢',
        foul: '⚠️',
        shot: '⚽',
        pass: '⇨',
        tackle: '⚔️',
        warning: '⚽',
        offside: '🚩',
        start: '🏁',
        finish: '🏁',
        default: '⚽'
    };
    
    return {
        icon: icons[eventType] || '⚽',
        description: event.comment || defaultComment[eventType] || 'Игровое событие'
    };
}

function updateTimeline() {
    const timeline = document.getElementById('timeline');
    const currentTimeElement = document.getElementById('current-time');
    const matchMinuteElement = document.getElementById('match-minute');
    
    if (!timeline || !currentTimeElement || !matchMinuteElement) return;
    
    const progress = Math.min(100, (animation.currentTime / 93) * 100);
    timeline.style.setProperty('--progress', progress + '%');
    
    const minutes = Math.floor(animation.currentTime);
    currentTimeElement.textContent = minutes + "'";
    matchMinuteElement.textContent = minutes + "'";
}

function checkEvents() {
    for (let i = 0; i < animation.events.length; i++) {
        const event = animation.events[i];
        if (event.minute <= animation.currentTime && !event.processed) {
            processEvent(event);
            event.processed = true;
        }
    }
}

function processEvent(event) {
    // Определяем тип события
    const eventType = getEventType(event);
    
 
    
    // Обработка специальных событий
    if (eventType === 'goal') {
        // Гол
        if (event.team == 1) {
            animation.goalsTeam1++;
        } else {
            animation.goalsTeam2++;
        }
        document.getElementById('match-score').textContent = `${animation.goalsTeam1} : ${animation.goalsTeam2}`;
        
        // Анимация гола
        const pitch = document.getElementById('pitch');
        const goalAnimation = document.createElement('div');
        goalAnimation.className = 'goal-animation';
        pitch.appendChild(goalAnimation);
        
        setTimeout(() => {
            goalAnimation.remove();
        }, 1500);
        
        // Сброс мяча в центр
        animation.ball = { x: 50, y: 50 };
        animation.ballOwner = null;
        updateBallPosition();
        
    } else if (eventType === 'start') {
        // Начало матча или тайма
        if (event.event_type.includes('one')) {
            // Первый тайм
            animation.ball = { x: 50, y: 50 };
            animation.ballOwner = null;
            initPlayers();
        } else if (event.event_type.includes('two')) {
            // Второй тайм
            animation.ball = { x: 50, y: 50 };
            animation.ballOwner = null;
            initPlayers();
        }
    } else if (eventType === 'finish') {
        // Конец матча
        if (event.minute > 90) {
            animation.running = false;
            endMatch();
        } else {
            // Конец тайма - пауза
            animation.running = false;
            document.getElementById('pause-btn').disabled = true;
            document.getElementById('play-btn').disabled = false;
            
            // Комментарий о перерыве
            addEventCommentary({
                minute: event.minute,
                event_type: 'break',
                comment: 'Конец тайма. Перерыв.'
            });
        }
    } else if (eventType === 'pass' || eventType === 'shot') {
        // Передача или удар
        if (event.to_x && event.to_y) {
            animation.ballTarget = {
                x: event.to_x,
                y: event.to_y,
                callback: () => {
                    animation.ballTarget = null;
                    if (event.player_id) {
                        animation.ballOwner = event.player_id;
                    }
                }
            };
        }
    }
       // Добавление комментария
    addEventCommentary(event);
        // Добавить визуализацию для ВСЕХ типов событий
    showEventIndicator(event);
}
// Новая функция для отображения индикатора события
function showEventIndicator(event) {
    const indicator = document.createElement('div');
    indicator.className = 'event-indicator';
    
    // Настройка внешнего вида по типу события
    switch(event.event_type) {
        case 'yellow':
            indicator.innerHTML = '🟨';
            indicator.style.color = '#ffcc00';
            break;
        case 'red':
            indicator.innerHTML = '🟥';
            indicator.style.color = '#ff0000';
            break;
        case 'corner':
            indicator.innerHTML = '🚩';
            indicator.style.color = '#ffffff';
            break;
        default:
            indicator.innerHTML = '⚽';
            indicator.style.color = '#ffffff';
    }
    
    // Позиционирование по координатам события
    indicator.style.left = (event.x || 50) + '%';
    indicator.style.top = (event.y || 50) + '%';
    
    // Добавить на поле
    document.getElementById('pitch').appendChild(indicator);
    
    // Автоматическое удаление через 3 секунды
    setTimeout(() => {
        indicator.remove();
    }, 3000);
}
function updatePlayers(deltaTime) {
    const ballX = animation.ball.x;
    const ballY = animation.ball.y;
    
    if (animation.ballTarget) {
        const dx = animation.ballTarget.x - animation.ball.x;
        const dy = animation.ballTarget.y - animation.ball.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        const speed = 0.5 * deltaTime * 60;
        
        if (distance > 0.5) {
            const ratio = speed / Math.max(speed, distance);
            animation.ball.x += dx * ratio;
            animation.ball.y += dy * ratio;
        } else {
            if (animation.ballTarget.callback) {
                animation.ballTarget.callback();
            }
            animation.ballTarget = null;
        }
    }
    
    animation.players.forEach(player => {
        if (player.removed) return;
        
        const playerData = getPlayerData(player.id);
        if (!playerData) return;

        let targetChanged = false;
        let speedFactor = player.speed;

        if (player.id === animation.ballOwner && !animation.ballTarget) {
            player.hasBall = true;
            player.element.classList.add('ball-owner');
            
            player.targetX = player.team === 1 ? 85 : 15;
            player.targetY = 50;
            speedFactor *= 1.3;
            targetChanged = true;
        } else {
            player.hasBall = false;
            player.element.classList.remove('ball-owner');
            
            if (!animation.ballOwner && !animation.ballTarget) {
                const dx = player.x - ballX;
                const dy = player.y - ballY;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < 20) {
                    player.targetX = ballX;
                    player.targetY = ballY;
                    targetChanged = true;
                    
                    // Игрок забирает мяч при приближении
                    if (distance < 2 && !animation.ballOwner) {
                        animation.ballOwner = player.id;
                        animation.lastBallOwnerChange = animation.currentTime;
                    }
                }
            }
            
            if (!targetChanged) {
                const position = POSITION_STRATEGY[playerData.poz] || 
                                { minX: 40, maxX: 60, minY: 30, maxY: 70 };
                
                // Случайная позиция в зоне
                if (player.team === 1) {
                    player.targetX = position.minX + 
                                Math.random() * (position.maxX - position.minX);
                    player.targetY = position.minY + 
                                Math.random() * (position.maxY - position.minY);
                } else {
                    player.targetX = 100 - (position.minX + 
                                Math.random() * (position.maxX - position.minX));
                    player.targetY = position.minY + 
                                Math.random() * (position.maxY - position.minY);
                }
            }
        }

        const dx = player.targetX - player.x;
        const dy = player.targetY - player.y;
        const distance = Math.sqrt(dx * dx + dy * dy);
        
        if (distance > 0.5) {
            const moveSpeed = speedFactor * deltaTime * 60;
            const ratio = moveSpeed / Math.max(moveSpeed, distance);
            
            player.x += dx * ratio;
            player.y += dy * ratio;
            
            player.x = Math.max(2, Math.min(98, player.x));
            player.y = Math.max(2, Math.min(98, player.y));

            player.element.style.left = player.x + '%';
            player.element.style.top = player.y + '%';
            
            if (player.id === animation.ballOwner && !animation.ballTarget) {
                animation.ball.x = player.x;
                animation.ball.y = player.y;
            }
        }
    });
}

function updateRefereePosition(deltaTime) {
    const referee = document.getElementById('referee');
    if (!referee) return;
    
    const targetX = animation.ball.x + (Math.random() * 10 - 5);
    const targetY = animation.ball.y + (Math.random() * 10 - 5);
    
    const dx = targetX - animation.referee.x;
    const dy = targetY - animation.referee.y;
    const distance = Math.sqrt(dx * dx + dy * dy);
    
    if (distance > 5) {
        const speed = 0.05 * deltaTime * 60;
        const ratio = speed / Math.max(speed, distance);
        
        animation.referee.x += dx * ratio;
        animation.referee.y += dy * ratio;
        
        animation.referee.x = Math.max(30, Math.min(70, animation.referee.x));
        animation.referee.y = Math.max(30, Math.min(70, animation.referee.y));
        
        referee.style.left = animation.referee.x + '%';
        referee.style.top = animation.referee.y + '%';
    }
}

function endMatch() {
    animation.running = false;
    animation.matchEnded = true;
    
    document.getElementById('play-btn').disabled = true;
    document.getElementById('pause-btn').disabled = true;
    
    const finalScore = `${animation.goalsTeam1} : ${animation.goalsTeam2}`;
    const matchEndElement = document.createElement('div');
    matchEndElement.className = 'match-end';
    matchEndElement.innerHTML = `
        <h2>Матч завершен!</h2>
        <p>Финальный счет: ${finalScore}</p>
        <div class="match-end-buttons">
            <button id="restart-btn">Смотреть заново</button>
            <button id="close-btn">Закрыть</button>
        </div>
    `;
    document.body.appendChild(matchEndElement);
    
    document.getElementById('restart-btn').addEventListener('click', function() {
        location.reload();
    });
    
    document.getElementById('close-btn').addEventListener('click', function() {
        matchEndElement.remove();
    });
}

// Управление воспроизведением
document.getElementById('play-btn').addEventListener('click', function() {
    if (animation.matchEnded) return;
    
    if (!animation.running) {
        // Продолжить после перерыва
        if (animation.currentTime >= 45 && animation.currentTime <= 48) {
            animation.currentTime = 48; // Пропустить перерыв
        }
        animation.running = true;
        this.disabled = true;
        document.getElementById('pause-btn').disabled = false;
        
        if (!animation.frameId) {
            animation.lastTimestamp = null;
            animation.frameId = requestAnimationFrame(animate);
        }
    }
});

document.getElementById('pause-btn').addEventListener('click', function() {
    animation.running = false;
    document.getElementById('play-btn').disabled = false;
    this.disabled = true;
});

document.getElementById('reset-btn').addEventListener('click', function() {
    animation.running = false;
    animation.currentTime = 0;
    animation.ball = { x: 50, y: 50 };
    animation.ballOwner = null;
    animation.goalsTeam1 = <?= $goal1 ?>;
    animation.goalsTeam2 = <?= $goal2 ?>;
    animation.matchEnded = false;
    animation.lastBallOwnerChange = 0;
    
    document.getElementById('play-btn').disabled = false;
    document.getElementById('pause-btn').disabled = true;
    
    document.getElementById('timeline').style.setProperty('--progress', '0%');
    document.getElementById('current-time').textContent = "0'";
    document.getElementById('match-minute').textContent = "0'";
    document.getElementById('match-score').textContent = '<?= $goal1 ?> : <?= $goal2 ?>';
    
    const commentaryBox = document.getElementById('commentary');
    if (commentaryBox) commentaryBox.innerHTML = '';
    
    const endScreen = document.querySelector('.match-end');
    if (endScreen) endScreen.remove();
    
    // Переинициализация игроков
    initPlayers();
    
    // Сброс судьи
    animation.referee = { x: 50, y: 50 };
    const referee = document.getElementById('referee');
    if (referee) {
        referee.style.left = '50%';
        referee.style.top = '50%';
    }
    
    updateBallPosition();
});

document.getElementById('speed-btn').addEventListener('click', function() {
    animation.speed = animation.speed % 3 + 1;
    this.innerHTML = `<i class="fas fa-tachometer-alt"></i> Скорость: ${animation.speed}x`;
});

// Запуск
document.addEventListener('DOMContentLoaded', function() {
    initPlayers();
    initTimeline();
    updateBallPosition();
});
</script>
</body>
</html>
<?


/////////////////////////////////////////////// ПРЕСС-КОНФЕРЕНЦИЯ ПОСЛЕ МАТЧА

/////////////////////////////////////////////// ПРЕСС-КОНФЕРЕНЦИЯ ПОСЛЕ МАТЧА

// Проверка авторизации пользователя
$is_logged_in = isset($user_id) && $user_id > 0;

// Определение владельцев команд (исправлено определение ID команд)
$is_owner_kom1 = ($is_logged_in && isset($kom1['id_admin']) && $user_id == $kom1['id_admin']);
$is_owner_kom2 = ($is_logged_in && isset($kom2['id_admin']) && $user_id == $kom2['id_admin']);

// Обработка отправки нового вопроса
if ($is_logged_in && isset($_POST['ask_question'])) {
    $team_id = (int)$_POST['team_id'];
    $question = trim($_POST['question']);
    $show_login = isset($_POST['show_login']) ? 1 : 0;
    
    if (!empty($question)) {
        $sql = "INSERT INTO r_press_conference SET
                match_id = $id,
                team_id = $team_id,
                user_id = $user_id,
                question = '".mysqli_real_escape_string($link, $question)."',
                time = ".time().",
                show_login = $show_login";
        mysqli_query($link, $sql);
        
        // Перенаправление без очистки буфера
        echo "<script>window.location.href = '?id=$id';</script>";
        exit;
    }
}

// Обработка отправки ответа
if (isset($_POST['submit_answer'])) {
    $answer = trim($_POST['answer']);
    $question_id = (int)$_POST['question_id'];
    $team_id = (int)$_POST['team_id'];
    
    if (!empty($answer) && $question_id > 0) {
        $sql = "UPDATE r_press_conference SET 
                answer = '".mysqli_real_escape_string($link, $answer)."'
                WHERE id = $question_id
                AND team_id = $team_id
                AND match_id = $id";
        
        mysqli_query($link, $sql);
        
        // Перенаправление через JavaScript
        echo "<script>window.location.href = '?id=$id';</script>";
        exit;
    }
}

// Получение вопросов из базы данных (исправлено: добавил проверку существования команды)
$press_conf = [
    'team1' => [],
    'team2' => []
];

// Исправленный SQL-запрос с проверкой существования команды
$sql = "SELECT pc.*, u.username 
        FROM r_press_conference pc
        LEFT JOIN users u ON pc.user_id = u.id
        WHERE match_id = $id
        ORDER BY time ASC";
$result = mysqli_query($link, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Проверка существования команды перед добавлением
        if ($row['team_id'] == $match_info['id_team1']) {
            $press_conf['team1'][] = $row;
        } elseif ($row['team_id'] == $match_info['id_team2']) {
            $press_conf['team2'][] = $row;
        }
    }
}

// Добавляем стандартные вопросы, если матч завершен и нет вопросов
if ($mt > 93) {
    $standard_questions = [
        "Какие впечатления от сегодняшнего матча?",
        "Как вы оцениваете игру своей команды?",
        "Были ли ключевые моменты, решившие исход игры?",
        "Какие планы на следующие матчи?"
    ];
    
    // Для команды 1
    if (empty($press_conf['team1'])) {
        foreach ($standard_questions as $index => $question) {
            $press_conf['team1'][] = [
                'id' => -($index + 1),
                'question' => $question,
                'answer' => '',
                'username' => 'Система',
                'time' => time(),
                'show_login' => 1
            ];
        }
    }
    
    // Для команды 2
    if (empty($press_conf['team2'])) {
        foreach ($standard_questions as $index => $question) {
            $press_conf['team2'][] = [
                'id' => -($index + 100),
                'question' => $question,
                'answer' => '',
                'username' => 'Система',
                'time' => time(),
                'show_login' => 1
            ];
        }
    }
}
?>

<div class="press-conference">
    <table>
        <thead>
            <tr>
                <td>
                    <img src="<?= isset($kom1['logo']) ? $kom1['logo'] : '' ?>" alt="<?= htmlspecialchars(isset($kom1['name']) ? $kom1['name'] : '') ?>">
                </td>
                <td style="text-align: left;">
                    <div>Пресс-конференция</div>
                    <div>
                        <?= htmlspecialchars(isset($kom1['name']) ? $kom1['name'] : '') ?>
                        <?php if ((isset($kom1['vip_status']) ? $kom1['vip_status'] : 0) > 0): ?>
                            <img src="/images/ico/vip<?= $kom1['vip_status'] ?>.png" title="VIP-статус">
                        <?php endif; ?>
                    </div>
                    <?php if ($is_logged_in && !$is_owner_kom1): ?>
                        <div class="pc_ask_question" data-team="1">
                            задать вопрос
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <div>Пресс-конференция</div>
                    <div>
                        <?= htmlspecialchars(isset($kom2['name']) ? $kom2['name'] : '') ?>
                        <?php if ((isset($kom2['vip_status']) ? $kom2['vip_status'] : 0) > 0): ?>
                            <img src="/images/ico/vip<?= $kom2['vip_status'] ?>.png" title="VIP-статус">
                        <?php endif; ?>
                    </div>
                    <?php if ($is_logged_in && !$is_owner_kom2): ?>
                        <div class="pc_ask_question" data-team="2">
                            задать вопрос
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <img src="<?= isset($kom2['logo']) ? $kom2['logo'] : '' ?>" alt="<?= htmlspecialchars(isset($kom2['name']) ? $kom2['name'] : '') ?>">
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2">
                    <!-- Форма вопроса для команды 1 -->
                    <?php if ($is_logged_in && !$is_owner_kom1): ?>
                        <form method="post" id="pc_ask_question_1" style="display: none;">
                            <textarea name="question" placeholder="Ваш вопрос..." required></textarea>
                            <input type="submit" name="ask_question" value="Отправить вопрос">
                            <label>
                                <input type="checkbox" name="show_login" value="1" checked>
                                показать логин
                            </label>
                            <input type="hidden" name="team_id" value="<?= $match_info['id_team1'] ?>">
                        </form>
                    <?php endif; ?>
                    
                    <!-- Список вопросов команды 1 -->
                    <table class="pc_question_list">
                        <?php if (!empty($press_conf['team1'])): ?>
                            <?php foreach ($press_conf['team1'] as $qa): ?>
                                <tr class="pc_question_<?= $qa['id'] ?>">
                                    <td width="50">
                                        <?= date('H:i', $qa['time']) ?>
                                    </td>
                                    <td class="left">
                                        <?= htmlspecialchars($qa['question']) ?>
                                       <?php if ((isset($qa['show_login']) ? $qa['show_login'] : 0) && !empty($qa['username'])): ?>
    <span class="pc_question_user">
        <?= htmlspecialchars($qa['username']) ?>
    </span>
<?php endif; ?>
                                    </td>
                                </tr>
                                <tr class="pc_question_<?= $qa['id'] ?>">
                                    <td></td>
                                    <td class="left">
                                        <?php if (!empty($qa['answer'])): ?>
                                            <?= htmlspecialchars($qa['answer']) ?>
                                        <?php elseif ($is_owner_kom1 && $qa['id'] > 0): ?>
                                            <form method="post">
                                                <textarea name="answer" required></textarea>
                                                <input type="submit" name="submit_answer" value="Ответить">
                                                <input type="hidden" name="question_id" value="<?= $qa['id'] ?>">
                                                <input type="hidden" name="team_id" value="<?= $match_info['id_team1'] ?>">
                                            </form>
                                        <?php else: ?>
                                            <em>Ожидается ответ тренера...</em>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">Вопросов пока не поступало</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </td>
                <td colspan="2">
                    <!-- Форма вопроса для команды 2 -->
                    <?php if ($is_logged_in && !$is_owner_kom2): ?>
                        <form method="post" id="pc_ask_question_2" style="display: none;">
                            <textarea name="question" placeholder="Ваш вопрос..." required></textarea>
                            <input type="submit" name="ask_question" value="Отправить вопрос">
                            <label>
                                <input type="checkbox" name="show_login" value="1" checked>
                                показать логин
                            </label>
                            <input type="hidden" name="team_id" value="<?= $match_info['id_team2'] ?>">
                        </form>
                    <?php endif; ?>
                    
                    <!-- Список вопросов команды 2 -->
                    <table class="pc_question_list">
                        <?php if (!empty($press_conf['team2'])): ?>
                            <?php foreach ($press_conf['team2'] as $qa): ?>
                                <tr class="pc_question_<?= $qa['id'] ?>">
                                    <td width="50">
                                        <?= date('H:i', $qa['time']) ?>
                                    </td>
                                    <td class="left">
                                        <?= htmlspecialchars($qa['question']) ?>
                                       <?php if ((isset($qa['show_login']) ? $qa['show_login'] : 0) && !empty($qa['username'])): ?>
    <span class="pc_question_user">
        <?= htmlspecialchars($qa['username']) ?>
    </span>
<?php endif; ?>
                                    </td>
                                </tr>
                                <tr class="pc_question_<?= $qa['id'] ?>">
                                    <td></td>
                                    <td class="left">
                                        <?php if (!empty($qa['answer'])): ?>
                                            <?= htmlspecialchars($qa['answer']) ?>
                                        <?php elseif ($is_owner_kom2 && $qa['id'] > 0): ?>
                                            <form method="post">
                                                <textarea name="answer" required></textarea>
                                                <input type="submit" name="submit_answer" value="Ответить">
                                                <input type="hidden" name="question_id" value="<?= $qa['id'] ?>">
                                                <input type="hidden" name="team_id" value="<?= $match_info['id_team2'] ?>">
                                            </form>
                                        <?php else: ?>
                                            <em>Ожидается ответ тренера...</em>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">Вопросов пока не поступало</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Показать/скрыть форму вопроса
    document.querySelectorAll('.pc_ask_question').forEach(function(el) {
        el.addEventListener('click', function() {
            const teamId = this.getAttribute('data-team');
            const form = document.getElementById(`pc_ask_question_${teamId}`);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });
    });
});
</script>

<style>
.press-conference {
    margin: 15px 0;
    padding: 15px;
    background: rgba(0, 0, 0, 0.7);
    border: 1px solid #444;
    border-radius: 5px;
    font-size: 11px;
}

.press-conference table {
    width: 100%;
    border-collapse: collapse;
}

.press-conference thead td {
    text-align: center;
    vertical-align: middle;
    padding: 5px;
}

.press-conference thead td img {
    width: 55px;
    height: 55px;
    border: 1px solid #444;
    border-radius: 3px;
}

.press-conference thead div {
    padding: 2px 0;
}

.press-conference .pc_ask_question {
    color: #888;
    cursor: pointer;
    text-decoration: underline;
    margin-top: 3px;
}

.press-conference .pc_ask_question:hover {
    color: #ccc;
}

.press-conference textarea {
    width: 100%;
    height: 60px;
    padding: 5px;
    margin: 5px 0;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid #555;
    color: #fff;
    border-radius: 3px;
}

.press-conference input[type="submit"] {
    background: #2196F3;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
    margin-top: 5px;
}

.press-conference input[type="submit"]:hover {
    background: #0b7dda;
}

.press-conference .pc_question_list {
    width: 100%;
    margin-top: 10px;
    border-collapse: separate;
    border-spacing: 0 5px;
}

.press-conference .pc_question_list tr td {
    padding: 5px;
    vertical-align: top;
}

.press-conference .pc_question_list .left {
    text-align: left;
}

.press-conference .pc_question_user {
    color: #aaa;
    font-style: italic;
    margin-left: 5px;
    display: block;
    font-size: 10px;
}

.press-conference .pc_question_list tr {
    background: rgba(50, 50, 50, 0.3);
    margin-bottom: 5px;
}

.press-conference .pc_question_list tr + tr {
    background: rgba(40, 40, 40, 0.3);
    margin-top: 0;
}
</style>

<?

/////////////////////////////////////////////////////////////
////////////////           Опыт менеджеру          //////////////////
/////////////////////////////////////////////////////////////

/**
 * Оптимизированный код для обработки результатов матча в JohnCMS 3.2.2
 * Основные улучшения:
 * 1. Улучшенная безопасность SQL-запросов
 * 2. Оптимизация запросов к базе данных
 * 3. Лучшая структура кода с разделением на функции
 * 4. Уменьшение дублирования кода
 * 5. Использование подготовленных выражений
 */





// Обработка кубков
$cupTypes = [
    'cup_en', 'cup_netto', 'cup_charlton', 'cup_muller', 
    'cup_puskas', 'cup_distefano', 'cup_fachetti', 'cup_kopa', 'cup_garrinca'
];
if (in_array($arr['chemp'], $cupTypes) && $mt >= 92 && $arr['bet'] == '0') {
    processCupUpdate($arr, 'bomb_fedcup');
}

// Обработка чемпионатов
$champTypes = [
    'champ' => ['bomb_champ', 'act_champ'],
    'champ_retro' => ['bomb_champ_retro', 'act_champ_retro'],
    'cupcom' => ['bomb_cupcom', null],
    'maradona' => ['bomb_maradona', null],
    'liga_r' => ['bomb_liga_r', null],
    'liberta' => ['bomb_liberta', null],
    'liga' => ['bomb_liga', null],
    'le' => ['bomb_le', null],
    'vsch' => ['bomb_vsch', null],
    'msch' => ['bomb_msch', null]
];

foreach ($champTypes as $type => $fields) {
    if ($arr['chemp'] == $type && $mt >= 92 && $arr['bet'] == '0') {
        processChampUpdate($arr, $fields[0], $fields[1]);
    }
}






// Вывод ссылки на отчет
if ($mt > 92) {
    echo '<div class="cardview-wrapper" bis_skin_checked="1">
        <a class="cardview" href="/report'.$dirs.''.$id.'">
            <div class="left px50" bis_skin_checked="1"><i class="font-icon font-icon-whistle"></i></div>
            <div class="right px50 arrow" bis_skin_checked="1"><div class="text" bis_skin_checked="1">Посмотреть отчет</div></div>
        </a>
    </div>';
}




// Функции обработки
function processCupUpdate($arr, $field) {
    global $prefix;
    $menus = explode("\r\n", $arr['menus']);
    foreach ($menus as $menuLine) {
        $menu = explode("|", $menuLine);
        $r5 = mysql_query("SELECT * FROM `r_player` WHERE id = '".$menu[2]."' LIMIT 1");
        $byy = mysql_fetch_array($r5);
        
        $bomplus = $byy[$field] + 1;
        mysql_query("UPDATE `r_player` SET `$field` = '$bomplus' WHERE id = '".$byy['id']."' LIMIT 1");
        mysql_query("UPDATE `r".$prefix."game` SET `bet` = '1' WHERE id = '".$arr['id']."' LIMIT 1");
    }
}

function processChampUpdate($arr, $bombField, $actField) {
    global $prefix;
    $menus = explode("\r\n", $arr['menus']);
    foreach ($menus as $menuLine) {
        $menu = explode("|", $menuLine);
        $r5 = mysql_query("SELECT * FROM `r_player` WHERE id = '".$menu[2]."' LIMIT 1");
        $byy = mysql_fetch_array($r5);
        
        $bomplus = $byy[$bombField] + 1;
        mysql_query("UPDATE `r_player` SET `$bombField` = '$bomplus' WHERE id = '".$byy['id']."' LIMIT 1");
        
        if ($actField) {
            mysql_query("UPDATE `r_player` SET `$actField` = '".$arr['kubok']."' WHERE id = '".$byy['id']."' LIMIT 1");
        }
        
        mysql_query("UPDATE `r".$prefix."game` SET `bet` = '1' WHERE id = '".$arr['id']."' LIMIT 1");
    }
}




	
if($mt>93 and $arr['step']=='0'){
	
	
	/////////////////////////////////////////////////////////////
    ////////////////           Опыт менеджеру          //////////////////
    /////////////////////////////////////////////////////////////
$g = @mysql_query("select * from `r".$prefix."game` where id = '" . $id . "' LIMIT 1;");
$game = @mysql_fetch_array($g);
$q1 = @mysql_query("select * from `r_team` where id='" . $game[id_team1] . "' LIMIT 1;");
$count1 = mysql_num_rows($q1);
$arr1 = @mysql_fetch_array($q1);

$q2 = @mysql_query("select * from `r_team` where id='" . $game[id_team2] . "' LIMIT 1;");
$count2 = mysql_num_rows($q2);
$arr2 = @mysql_fetch_array($q2);
$rezult[0]=$goal1;
$rezult[1]=$goal2;









$g1 = @mysql_query("select * from `r".$prefix."game` where id = '" . $id . "' and `gr`='1/4' and `tur`='1' LIMIT 1;");
$game1 = @mysql_fetch_array($g1);
$g11 = @mysql_query("select * from `r".$prefix."game` where id = '" . $id . "' and `gr`='1/4' and `tur`='2' LIMIT 1;");
$game11 = @mysql_fetch_array($g11);



if($game1[rez1] == $game1[rez2])
{
	
	echo'LOXXXXXX';
}
if($game11[rez1] == $game11[rez2])
{
	echo'PIDARRRR';
}
$xyi1=$game1[rez1] + $game11[rez2];
$xyi2=$game1[rez2] + $game11[rez1];
	
if ($xyi2 > $xyi1){
echo'$xyi2';}
if ($xyi1 > $xyi2){
	echo'$xyi1';}
	if ($xyi1 == $xyi2){
		echo'penalti';
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	


$g11 = @mysql_query("select * from `r".$prefix."game` where id = '" . $id . "' and `gr`='1/2' and `tur`='1' LIMIT 1;");
$game11 = @mysql_fetch_array($g11);
$g111 = @mysql_query("select * from `r".$prefix."game` where id = '" . $id . "' and `gr`='1/2' and `tur`='2' LIMIT 1;");
$game111 = @mysql_fetch_array($g111);



if($game11[rez1] == $game11[rez2])
{
	
	echo'LOXXXXXX';
}
if($game111[rez1] == $game111[rez2])
{
	echo'PIDARRRR';
}
$xyi1=$game11[rez1] + $game111[rez2];
$xyi2=$game11[rez2] + $game111[rez1];
	
if ($xyi2 > $xyi1){
echo'$xyi22';}
if ($xyi1 > $xyi2){
	echo'$xyi12';}
	if ($xyi1 == $xyi2){
		echo'penalti2';
	}
$q11 = @mysql_query("select * from `r_team` where id='" . $game[id_team1] . "' LIMIT 1;");
$count11 = mysql_num_rows($q11);
$arr11 = @mysql_fetch_array($q11);

$q21 = @mysql_query("select * from `r_team` where id='" . $game[id_team2] . "' LIMIT 1;");
$count21 = mysql_num_rows($q21);
$arr21 = @mysql_fetch_array($q21);



 /*    ///////////////////////////////////////////////////////////////////
    //////////////////           ПЕНАЛЬТИ          //////////////////////////
    ///////////////////////////////////////////////////////////////////

	
     if ($rezult[0] == $rezult[1] || $game['chemp'] == 'liga_r')
   // if ($rezult[0] == $rezult[1])
    {
    // $input = array ("11:10", "10:9", "8:7", "7:6", "6:5", "5:3", "5:4", "4:2", "4:3", "3:2", "3:5", "4:5", "2:4", "3:4", "2:3", "10:11", "9:10", "7:8", "6:7", "5:6");
    $input = array ("8:7","7:8");
    $rand_keys = array_rand ($input);

    $penult = explode(":",$input[$rand_keys]);

    $pen1 = $penult[0];
    $pen2 = $penult[1];
    }  */
	
	    ///////////////////////////////////////////////////////////////////
    //////////////////           ПЕНАЛЬТИ          //////////////////////////
    ///////////////////////////////////////////////////////////////////



// and ($game[gr]='1/8' or $game[gr]='1/4' OR $game[gr]='1/2' or $game[gr]='1/1') 


   if ($rezult[0] == $rezult[1])
{
    $input = array ("11:10", "10:9", "8:7", "7:6", "6:5", "5:3", "5:4", "4:2", "4:3", "3:2", "3:5", "4:5", "2:4", "3:4", "2:3", "10:11", "9:10", "7:8", "6:7", "5:6");
   $rand_keys = array_rand ($input);

    $penult = explode(":",$input[$rand_keys]);

    $pen1 = $penult[0];
    $pen2 = $penult[1];
    }
	
	// Записываем счет
	

	 
mysql_query("update `r".$prefix."game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id] . "' LIMIT 1;");

 
 
 
 
 
 
 
 
 
$h_zwm_1 = explode("|", $arr1['zad_win_match']);
$p_zwm_1 = $h_zwm_1[1]+1;

$h_zwm_2 = explode("|", $arr2['zad_win_match']);
$p_zwm_2 = $h_zwm_2[1]+1;


	

    // Команда 1
    if ($rezult[0] > $rezult[1])
    {
    //П1
	$x22=mysql_query("SELECT * FROM `sponsors` where id='".$arr1['sponsor']."'");
$ns2 = mysql_fetch_array($x22);
    $oputman1 = $arr1[oput]+1;
$fansman1 = $arr1[fans]+10;
if($arr1[sponsor] != '0'){
    $moneyn1 = $arr1[money] + round($game[zritel]*0.01);
    $moneyman1 = $ns2['money']+ $moneyn1;
	$m1 = $ns2['money'] + round($game[zritel]*0.01);
}
else{
	   $moneyn1 = $arr1[money] + round($game[zritel]*0.01);
	   $moneyman1 = $moneyn1;
	   $m1 =  round($game[zritel]*0.01);
}
    $winman1 = $arr1[win]+1;

mysql_query("update `r_team` set `zad_win_match`='".$h_zwm_1[0]."|".$p_zwm_1."', `oput`='" . $oputman1 . "', `money`='" . $moneyn1 . "', `win`='" . $winman1 . "' where id='" . $arr1[id] . "' LIMIT 1;");
mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$m1."',
`text`='Победа',
`old_club`='".$arr2[id]."',
`team_id`='".$arr1[id]."'
;");  
  }
    elseif ($rezult[0] == $rezult[1])
    {
    //Н
    if($arr1[sponsor] != '0'){
    $moneyn1 = $arr1[money] + round($game[zritel]*0.005);
    $moneyman1 = $ns2['money']+ $moneyn1;
	$m1 = $ns2['money'] + round($game[zritel]*0.01);
}
else{
	$moneyn1 = $arr1[money] + round($game[zritel]*0.005);
	   $moneyman1 = $moneyn1;
	   $m1 = round($game[zritel]*0.01);
}

    $nnman1 = $arr1[nn]+1;
mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$m1."',
`text`='Ничья',
`old_club`='".$arr2[id]."',
`team_id`='".$arr1[id]."'
;"); 
mysql_query("update `r_team` set `money`='" . $moneyman1 . "', `nn`='" . $nnman1 . "' where id='" . $arr1[id] . "' LIMIT 1;");
    }
    else
    {
    //П2
    $losman1 = $arr1[los]+1;
mysql_query("update `r_team` set `los`='" . $losman1 . "' where id='" . $arr1[id] . "' LIMIT 1;");
    }







    // Команда 2
    if ($rezult[1] > $rezult[0])
    {
    //П2
    $x22=mysql_query("SELECT * FROM `sponsors` where id='".$arr2['sponsor']."'");
$ns2 = mysql_fetch_array($x22);
$oputman2 = $arr2[oput]+1;
$fansman2 = $arr1[fans]+10;
if($arr2[sponsor] != '0'){
	    $moneyn2 = $arr2[money] + round($game[zritel]*0.01);
    $moneyman2 = $ns2['money']+ $moneyn2;
$m2 = $ns2['money'] + round($game[zritel]*0.01);
}
else{
	 $moneyn2 = $arr2[money] + round($game[zritel]*0.01);
    $moneyman2 = $moneyn2;
	$m2 =  round($game[zritel]*0.01);
}
    // $moneyman2 = $ns2['money'] + $arr2[money] + round($game[zritel]*0.01);
    $winman2 = $arr2[win]+1;
mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$m2."',
`text`='Победа',
`old_club`='".$arr1[id]."',
`team_id`='".$arr2[id]."'
;"); 
mysql_query("update `r_team` set `zad_win_match`='".$h_zwm_2[0]."|".$p_zwm_2."', `oput`='" . $oputman2 . "', `money`='" . $moneyman2 . "', `win`='" . $winman2 . "' where id='" . $arr2[id] . "' LIMIT 1;");
    }
    elseif ($rezult[1] == $rezult[0])
    {
    //Н
	
	if($arr2[sponsor] != '0'){
	    $moneyn2 = $arr2[money] + round($game[zritel]*0.005);
    $moneyman2 = $ns2['money']+ $moneyn2;
$m2 = $ns2['money'] + round($game[zritel]*0.005);
}
else{
	 $moneyn2 = $arr2[money] + round($game[zritel]*0.005);
    $moneyman2 = $moneyn2;
	$m2 =  round($game[zritel]*0.005);
}

		   
    // $moneyman2 = $ns2['money'] + $arr2[money] + round($game[zritel]*0.005);
    $nnman2 = $arr2[nn]+1;
mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$m2."',
`text`='Ничья',
`old_club`='".$arr1[id]."',
`team_id`='".$arr2[id]."'
;"); 
mysql_query("update `r_team` set `money`='" . $moneyman2 . "', `nn`='" . $nnman2 . "' where id='" . $arr2[id] . "' LIMIT 1;");
    }
    else
    {
    //П2
    $losman2 = $arr2[los]+1;

mysql_query("update `r_team` set `los`='" . $losman2 . "' where id='" . $arr2[id] . "' LIMIT 1;");
    }
	
$nat1 = $game[per1]+$rezult[0];
$nat2 = $game[per2]+$rezult[1]; 
	
	
	
	
	
/* 	
	$g1 = @mysql_query("select * from `r_union_cupgame` where id = '" . $id . "' LIMIT 1;");
$game1 = @mysql_fetch_array($g1);
	// ЕСЛИ КУБОК
if ($game1[chemp] == 'cup')
{
mysql_query("update `r_union_cupgame` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game1[id_match] . "' LIMIT 1;");
echo'lox';

}

 */

// ЕСЛИ КУБОК
if ($game[chemp] == 'brend')
{
mysql_query("update `b_cupgame` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}
// ЕСЛИ КУБОК
if ($game[chemp] == 'cup')
{
mysql_query("update `r_cupgame` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}// ЕСЛИ КУБОК
if ($game[chemp] == 'z_cup')
{
mysql_query("update `z_cupgame` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}// ЕСЛИ КУБОК
// if ($game[chemp] == 'b_cup')
// {
// mysql_query("update `b".$prefix."cupgame` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
// }

// ЕСЛИ ЧЕМПИОНАТ 
if ($game[chemp] == 'champ_retro')
{

// mysql_query("update `champ_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
mysql_query("update `champ_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");


if ($rezult[0] > $rezult[1])
{
$p1 = '1';
}
else if ($rezult[1] > $rezult[0])
{
$p1 = '2';
}
else{
	$p1 = '3';
}
$g6 = @mysql_query("select * from `r_game` where `id`='" . $id . "' ;");
$game6 = @mysql_fetch_array($g6);
mysql_query("update `t_games` set  `score`='".$rezult[0]."|".$rezult[1]."', `winner`='".$p1."' where `id_match`='" . $game6[id_match] . "' LIMIT 1;");

 

$req37 = mysql_query("SELECT * FROM `t_games` where `id_match`='" . $game6[id_match] . "' ;");
$kom337 = @mysql_fetch_array($req37);
 $milsQuery = mysql_query("SELECT * FROM `t_mils` WHERE `refid` = '".$game6[id]."';");
                while($mil = mysql_fetch_array($milsQuery))
                {
				
					$req379 = mysql_query("SELECT * FROM `r_team` where `id_admin`='".$mil[user]."' ;");
$kom3379 = @mysql_fetch_array($req379);

$teams = explode('|', $kom337['teams']); $teamsCount = sizeof($teams);
    $coefs = explode('|', $kom337['coefs']);

   
        $no_winner = TRUE;
        $scores = array();
        for($i = 0; $i < $teamsCount; $i++)
        {
            $score = 0;
            if($_POST['score' . $i] > 0)
                $score = htmlspecialchars(trim($_POST['score' . $i]));
            $scores[$i] = $score;
            if($i > 0 && $score != $scores[$i - 1])
                $no_winner = FALSE;
        }

        $sortedScores = array_flip($scores);
        ksort($sortedScores);

        $winner = end($sortedScores) + 1;
        if($no_winner)
            $winner = sizeof($coefs);
		
                    if($mil['winner'] == $winner){
mysql_query("UPDATE `r_team` SET `money` = (`money` + " . ($mil['mil'] * $coefs[$winner - 1]) . ") WHERE `id` = '".$kom3379[id]."';");
	if($winner == 1){
					$aaa=''.$teams[0].' <b>П1</b> ';
				}
				elseif($winner == 2){
				$aaa=''.$teams[1].' <b>П2</b> ';
				}
				else{
				$aaa=''.$teams[0].'-'.$teams[1].' <b>Ничья</b> ';
				}        
		mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$mil['mil'] * $coefs[$winner - 1]."',
`text`='Ставка ".$aaa."',
`team_id`='" . $kom3379[id] . "'
;");
			mysql_query("DELETE FROM `t_mils` WHERE `id` = " . $mil['id'] . ";");
					} }
				
$l1 = @mysql_query("select * from `champ_table` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `champ_table` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);


if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `champ_table` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `champ_table` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `champ_table` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `champ_table` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `champ_table` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `champ_table` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}

}
// ЕСЛИ ЧЕМПИОНАТ 
if ($game[chemp] == 'champ')
{
// mysql_query("update `champ_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
mysql_query("update `champ_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");


if ($rezult[0] > $rezult[1])
{
$p1 = '1';
}
else if ($rezult[1] > $rezult[0])
{
$p1 = '2';
}
else{
	$p1 = '3';
}
$g6 = @mysql_query("select * from `r_game` where `id`='" . $id . "' ;");
$game6 = @mysql_fetch_array($g6);
mysql_query("update `t_games` set  `score`='".$rezult[0]."|".$rezult[1]."', `winner`='".$p1."' where `id_match`='" . $game6[id_match] . "' LIMIT 1;");

 

$req37 = mysql_query("SELECT * FROM `t_games` where `id_match`='" . $game6[id_match] . "' ;");
$kom337 = @mysql_fetch_array($req37);
 $milsQuery = mysql_query("SELECT * FROM `t_mils` WHERE `refid` = '".$game6[id]."';");
                while($mil = mysql_fetch_array($milsQuery))
                {
				
					$req379 = mysql_query("SELECT * FROM `r_team` where `id_admin`='".$mil[user]."' ;");
$kom3379 = @mysql_fetch_array($req379);

$teams = explode('|', $kom337['teams']); $teamsCount = sizeof($teams);
    $coefs = explode('|', $kom337['coefs']);

   
        $no_winner = TRUE;
        $scores = array();
        for($i = 0; $i < $teamsCount; $i++)
        {
            $score = 0;
            if($_POST['score' . $i] > 0)
                $score = htmlspecialchars(trim($_POST['score' . $i]));
            $scores[$i] = $score;
            if($i > 0 && $score != $scores[$i - 1])
                $no_winner = FALSE;
        }

        $sortedScores = array_flip($scores);
        ksort($sortedScores);

        $winner = end($sortedScores) + 1;
        if($no_winner)
            $winner = sizeof($coefs);
		
                    if($mil['winner'] == $winner){
mysql_query("UPDATE `r_team` SET `money` = (`money` + " . ($mil['mil'] * $coefs[$winner - 1]) . ") WHERE `id` = '".$kom3379[id]."';");
	if($winner == 1){
					$aaa=''.$teams[0].' <b>П1</b> ';
				}
				elseif($winner == 2){
				$aaa=''.$teams[1].' <b>П2</b> ';
				}
				else{
				$aaa=''.$teams[0].'-'.$teams[1].' <b>Ничья</b> ';
				}        
		mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$mil['mil'] * $coefs[$winner - 1]."',
`text`='Ставка ".$aaa."',
`team_id`='" . $kom3379[id] . "'
;");
			mysql_query("DELETE FROM `t_mils` WHERE `id` = " . $mil['id'] . ";");
					} }
$l1 = @mysql_query("select * from `champ_table` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `champ_table` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);


if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `champ_table` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `champ_table` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `champ_table` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `champ_table` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `champ_table` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `champ_table` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}

}













// ЕСЛИ Лига чемпинов
if ($game[chemp] == 'liga')
{
	$nat15 = $arr[rez1]+$arr[per1]+$arr[pen1];
$nat25 = $arr[rez2]+$arr[per2]+$arr[pen2];
mysql_query("update `liga_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");

if ($game[chemp] == 'liga' && $nat15 == $nat25){
mysql_query("update `liga_game` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");

}

if ($game['final'] == 'final' && $rezult[0] == $rezult[1]){
mysql_query("update `liga_game` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}

if ($rezult[0] > $rezult[1])
{
$p1 = '1';
}
else if ($rezult[1] > $rezult[0])
{
$p1 = '2';
}
else{
	$p1 = '3';
}
$g6 = @mysql_query("select * from `r_game` where `id`='" . $id . "' ;");
$game6 = @mysql_fetch_array($g6);
mysql_query("update `t_games` set  `score`='".$rezult[0]."|".$rezult[1]."', `winner`='".$p1."' where `id_match`='" . $game6[id_match] . "' LIMIT 1;");

 

$req37 = mysql_query("SELECT * FROM `t_games` where `id_match`='" . $game6[id_match] . "' ;");
$kom337 = @mysql_fetch_array($req37);
 $milsQuery = mysql_query("SELECT * FROM `t_mils` WHERE `refid` = '".$game6[id]."';");
                while($mil = mysql_fetch_array($milsQuery))
                {
				
					$req379 = mysql_query("SELECT * FROM `r_team` where `id_admin`='".$mil[user]."' ;");
$kom3379 = @mysql_fetch_array($req379);

$teams = explode('|', $kom337['teams']); $teamsCount = sizeof($teams);
    $coefs = explode('|', $kom337['coefs']);

   
        $no_winner = TRUE;
        $scores = array();
        for($i = 0; $i < $teamsCount; $i++)
        {
            $score = 0;
            if($_POST['score' . $i] > 0)
                $score = htmlspecialchars(trim($_POST['score' . $i]));
            $scores[$i] = $score;
            if($i > 0 && $score != $scores[$i - 1])
                $no_winner = FALSE;
        }

        $sortedScores = array_flip($scores);
        ksort($sortedScores);

        $winner = end($sortedScores) + 1;
        if($no_winner)
            $winner = sizeof($coefs);
		
                    if($mil['winner'] == $winner){
mysql_query("UPDATE `r_team` SET `money` = (`money` + " . ($mil['mil'] * $coefs[$winner - 1]) . ") WHERE `id` = '".$kom3379[id]."';");
	if($winner == 1){
					$aaa=''.$teams[0].' <b>П1</b> ';
				}
				elseif($winner == 2){
				$aaa=''.$teams[1].' <b>П2</b> ';
				}
				else{
				$aaa=''.$teams[0].'-'.$teams[1].' <b>Ничья</b> ';
				}        
		mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$mil['mil'] * $coefs[$winner - 1]."',
`text`='Ставка ".$aaa."',
`team_id`='" . $kom3379[id] . "'
;");
			mysql_query("DELETE FROM `t_mils` WHERE `id` = " . $mil['id'] . ";");
					} }
$l1 = @mysql_query("select * from `liga_group` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `liga_group` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);

if ($game['etap'] == 'gr'){
if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `liga_group` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `liga_group` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `liga_group` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `liga_group` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `liga_group` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `liga_group` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
}

}

// ЕСЛИ Лига чемпинов
if ($game[chemp] == 'liga_r')
{
	$nat15 = $arr[rez1]+$arr[per1]+$arr[pen1];
$nat25 = $arr[rez2]+$arr[per2]+$arr[pen2];
mysql_query("update `liga_game_r` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");

if ($game[chemp] == 'liga_r' && $nat15 == $nat25){
mysql_query("update `liga_game_r` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}

if ($game['final'] == 'final' && $rezult[0] == $rezult[1]){
mysql_query("update `liga_game_r` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}

if ($rezult[0] > $rezult[1])
{
$p1 = '1';
}
else if ($rezult[1] > $rezult[0])
{
$p1 = '2';
}
else{
	$p1 = '3';
}
$g6 = @mysql_query("select * from `r_game` where `id`='" . $id . "' ;");
$game6 = @mysql_fetch_array($g6);
mysql_query("update `t_games` set  `score`='".$rezult[0]."|".$rezult[1]."', `winner`='".$p1."' where `id_match`='" . $game6[id_match] . "' LIMIT 1;");

 

$req37 = mysql_query("SELECT * FROM `t_games` where `id_match`='" . $game6[id_match] . "' ;");
$kom337 = @mysql_fetch_array($req37);
 $milsQuery = mysql_query("SELECT * FROM `t_mils` WHERE `refid` = '".$game6[id]."';");
                while($mil = mysql_fetch_array($milsQuery))
                {
				
					$req379 = mysql_query("SELECT * FROM `r_team` where `id_admin`='".$mil[user]."' ;");
$kom3379 = @mysql_fetch_array($req379);

$teams = explode('|', $kom337['teams']); $teamsCount = sizeof($teams);
    $coefs = explode('|', $kom337['coefs']);

   
        $no_winner = TRUE;
        $scores = array();
        for($i = 0; $i < $teamsCount; $i++)
        {
            $score = 0;
            if($_POST['score' . $i] > 0)
                $score = htmlspecialchars(trim($_POST['score' . $i]));
            $scores[$i] = $score;
            if($i > 0 && $score != $scores[$i - 1])
                $no_winner = FALSE;
        }

        $sortedScores = array_flip($scores);
        ksort($sortedScores);

        $winner = end($sortedScores) + 1;
        if($no_winner)
            $winner = sizeof($coefs);
		
                    if($mil['winner'] == $winner){
mysql_query("UPDATE `r_team` SET `money` = (`money` + " . ($mil['mil'] * $coefs[$winner - 1]) . ") WHERE `id` = '".$kom3379[id]."';");
	if($winner == 1){
					$aaa=''.$teams[0].' <b>П1</b> ';
				}
				elseif($winner == 2){
				$aaa=''.$teams[1].' <b>П2</b> ';
				}
				else{
				$aaa=''.$teams[0].'-'.$teams[1].' <b>Ничья</b> ';
				}        
		mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$mil['mil'] * $coefs[$winner - 1]."',
`text`='Ставка ".$aaa."',
`team_id`='" . $kom3379[id] . "'
;");
			mysql_query("DELETE FROM `t_mils` WHERE `id` = " . $mil['id'] . ";");
					} }
$l1 = @mysql_query("select * from `liga_group_r` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `liga_group_r` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);

if ($game['etap'] == 'gr'){
if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `liga_group_r` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `liga_group_r` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `liga_group_r` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `liga_group_r` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `liga_group_r` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `liga_group_r` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
}

}



// ЕСЛИ Лига чемпинов
if ($game[chemp] == 'liga_r2')
{
	$nat15 = $arr[rez1]+$arr[per1]+$arr[pen1];
$nat25 = $arr[rez2]+$arr[per2]+$arr[pen2];
mysql_query("update `liga_game_r2000` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");

if ($game[chemp] == 'liga_r2' && $nat15 == $nat25){
mysql_query("update `liga_game_r2000` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}

if ($game['final'] == 'final' && $rezult[0] == $rezult[1]){
mysql_query("update `liga_game_r2000` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}

if ($rezult[0] > $rezult[1])
{
$p1 = '1';
}
else if ($rezult[1] > $rezult[0])
{
$p1 = '2';
}
else{
	$p1 = '3';
}
$g6 = @mysql_query("select * from `r_game` where `id`='" . $id . "' ;");
$game6 = @mysql_fetch_array($g6);
mysql_query("update `t_games` set  `score`='".$rezult[0]."|".$rezult[1]."', `winner`='".$p1."' where `id_match`='" . $game6[id_match] . "' LIMIT 1;");

 

$req37 = mysql_query("SELECT * FROM `t_games` where `id_match`='" . $game6[id_match] . "' ;");
$kom337 = @mysql_fetch_array($req37);
 $milsQuery = mysql_query("SELECT * FROM `t_mils` WHERE `refid` = '".$game6[id]."';");
                while($mil = mysql_fetch_array($milsQuery))
                {
				
					$req379 = mysql_query("SELECT * FROM `r_team` where `id_admin`='".$mil[user]."' ;");
$kom3379 = @mysql_fetch_array($req379);

$teams = explode('|', $kom337['teams']); $teamsCount = sizeof($teams);
    $coefs = explode('|', $kom337['coefs']);

   
        $no_winner = TRUE;
        $scores = array();
        for($i = 0; $i < $teamsCount; $i++)
        {
            $score = 0;
            if($_POST['score' . $i] > 0)
                $score = htmlspecialchars(trim($_POST['score' . $i]));
            $scores[$i] = $score;
            if($i > 0 && $score != $scores[$i - 1])
                $no_winner = FALSE;
        }

        $sortedScores = array_flip($scores);
        ksort($sortedScores);

        $winner = end($sortedScores) + 1;
        if($no_winner)
            $winner = sizeof($coefs);
		
                    if($mil['winner'] == $winner){
mysql_query("UPDATE `r_team` SET `money` = (`money` + " . ($mil['mil'] * $coefs[$winner - 1]) . ") WHERE `id` = '".$kom3379[id]."';");
	if($winner == 1){
					$aaa=''.$teams[0].' <b>П1</b> ';
				}
				elseif($winner == 2){
				$aaa=''.$teams[1].' <b>П2</b> ';
				}
				else{
				$aaa=''.$teams[0].'-'.$teams[1].' <b>Ничья</b> ';
				}        
		mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$mil['mil'] * $coefs[$winner - 1]."',
`text`='Ставка ".$aaa."',
`team_id`='" . $kom3379[id] . "'
;");
			mysql_query("DELETE FROM `t_mils` WHERE `id` = " . $mil['id'] . ";");
					} }
$l1 = @mysql_query("select * from `liga_group_r2000` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `liga_group_r2000` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);

if ($game['etap'] == 'gr'){
if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `liga_group_r2000` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `liga_group_r2000` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `liga_group_r2000` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `liga_group_r2000` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `liga_group_r2000` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `liga_group_r2000` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
}

}



// ЕСЛИ Кубок Лиретадорес
if ($game[chemp] == 'liberta')
{
	$nat15 = $arr[rez1]+$arr[per1]+$arr[pen1];
$nat25 = $arr[rez2]+$arr[per2]+$arr[pen2];
mysql_query("update `liberta_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");

if ($game[chemp] == 'liberta' && $nat15 == $nat25){
mysql_query("update `liberta_game` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}

if ($game['final'] == 'final' && $rezult[0] == $rezult[1]){
mysql_query("update `liberta_game` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}

if ($rezult[0] > $rezult[1])
{
$p1 = '1';
}
else if ($rezult[1] > $rezult[0])
{
$p1 = '2';
}
else{
	$p1 = '3';
}
$g6 = @mysql_query("select * from `r_game` where `id`='" . $id . "' ;");
$game6 = @mysql_fetch_array($g6);
mysql_query("update `t_games` set  `score`='".$rezult[0]."|".$rezult[1]."', `winner`='".$p1."' where `id_match`='" . $game6[id_match] . "' LIMIT 1;");

 

$req37 = mysql_query("SELECT * FROM `t_games` where `id_match`='" . $game6[id_match] . "' ;");
$kom337 = @mysql_fetch_array($req37);
 $milsQuery = mysql_query("SELECT * FROM `t_mils` WHERE `refid` = '".$game6[id]."';");
                while($mil = mysql_fetch_array($milsQuery))
                {
				
					$req379 = mysql_query("SELECT * FROM `r_team` where `id_admin`='".$mil[user]."' ;");
$kom3379 = @mysql_fetch_array($req379);

$teams = explode('|', $kom337['teams']); $teamsCount = sizeof($teams);
    $coefs = explode('|', $kom337['coefs']);

   
        $no_winner = TRUE;
        $scores = array();
        for($i = 0; $i < $teamsCount; $i++)
        {
            $score = 0;
            if($_POST['score' . $i] > 0)
                $score = htmlspecialchars(trim($_POST['score' . $i]));
            $scores[$i] = $score;
            if($i > 0 && $score != $scores[$i - 1])
                $no_winner = FALSE;
        }

        $sortedScores = array_flip($scores);
        ksort($sortedScores);

        $winner = end($sortedScores) + 1;
        if($no_winner)
            $winner = sizeof($coefs);
		
                    if($mil['winner'] == $winner){
mysql_query("UPDATE `r_team` SET `money` = (`money` + " . ($mil['mil'] * $coefs[$winner - 1]) . ") WHERE `id` = '".$kom3379[id]."';");
	if($winner == 1){
					$aaa=''.$teams[0].' <b>П1</b> ';
				}
				elseif($winner == 2){
				$aaa=''.$teams[1].' <b>П2</b> ';
				}
				else{
				$aaa=''.$teams[0].'-'.$teams[1].' <b>Ничья</b> ';
				}        
		mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$mil['mil'] * $coefs[$winner - 1]."',
`text`='Ставка ".$aaa."',
`team_id`='" . $kom3379[id] . "'
;");
			mysql_query("DELETE FROM `t_mils` WHERE `id` = " . $mil['id'] . ";");
					} }
$l1 = @mysql_query("select * from `liberta_group` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `liberta_group` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);

if ($game['etap'] == 'gr'){
if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `liberta_group` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `liberta_group` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `liberta_group` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `liberta_group` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `liberta_group` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `liberta_group` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
}

}




// ЕСЛИ КУБОК УЕФА 1980
if ($game[chemp] == 'le')
{
$nat15 = $arr[rez1]+$arr[per1]+$arr[pen1];
$nat25 = $arr[rez2]+$arr[per2]+$arr[pen2];
mysql_query("update `le_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");

if ($game[chemp] == 'le' && $nat15 == $nat25){
mysql_query("update `le_game` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}

if ($game['final'] == 'final' && $rezult[0] == $rezult[1]){
mysql_query("update `le_game` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}

if ($rezult[0] > $rezult[1])
{
$p1 = '1';
}
else if ($rezult[1] > $rezult[0])
{
$p1 = '2';
}
else{
	$p1 = '3';
}
$g6 = @mysql_query("select * from `r_game` where `id`='" . $id . "' ;");
$game6 = @mysql_fetch_array($g6);
mysql_query("update `t_games` set  `score`='".$rezult[0]."|".$rezult[1]."', `winner`='".$p1."' where `id_match`='" . $game6[id_match] . "' LIMIT 1;");

 

$req37 = mysql_query("SELECT * FROM `t_games` where `id_match`='" . $game6[id_match] . "' ;");
$kom337 = @mysql_fetch_array($req37);
 $milsQuery = mysql_query("SELECT * FROM `t_mils` WHERE `refid` = '".$game6[id]."';");
                while($mil = mysql_fetch_array($milsQuery))
                {
				
					$req379 = mysql_query("SELECT * FROM `r_team` where `id_admin`='".$mil[user]."' ;");
$kom3379 = @mysql_fetch_array($req379);

$teams = explode('|', $kom337['teams']); $teamsCount = sizeof($teams);
    $coefs = explode('|', $kom337['coefs']);

   
        $no_winner = TRUE;
        $scores = array();
        for($i = 0; $i < $teamsCount; $i++)
        {
            $score = 0;
            if($_POST['score' . $i] > 0)
                $score = htmlspecialchars(trim($_POST['score' . $i]));
            $scores[$i] = $score;
            if($i > 0 && $score != $scores[$i - 1])
                $no_winner = FALSE;
        }

        $sortedScores = array_flip($scores);
        ksort($sortedScores);

        $winner = end($sortedScores) + 1;
        if($no_winner)
            $winner = sizeof($coefs);
		
                    if($mil['winner'] == $winner){
mysql_query("UPDATE `r_team` SET `money` = (`money` + " . ($mil['mil'] * $coefs[$winner - 1]) . ") WHERE `id` = '".$kom3379[id]."';");
	if($winner == 1){
					$aaa=''.$teams[0].' <b>П1</b> ';
				}
				elseif($winner == 2){
				$aaa=''.$teams[1].' <b>П2</b> ';
				}
				else{
				$aaa=''.$teams[0].'-'.$teams[1].' <b>Ничья</b> ';
				}        
		mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$mil['mil'] * $coefs[$winner - 1]."',
`text`='Ставка ".$aaa."',
`team_id`='" . $kom3379[id] . "'
;");
			mysql_query("DELETE FROM `t_mils` WHERE `id` = " . $mil['id'] . ";");
					} }
$l1 = @mysql_query("select * from `le_group` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `le_group` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);


if ($game['etap'] == 'gr'){
if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `le_group` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `le_group` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `le_group` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `le_group` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `le_group` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `le_group` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
}

}




// ЕСЛИ Кубок УЕФА 2000
if ($game[chemp] == 'kuefa2')
{
$nat15 = $arr[rez1]+$arr[per1]+$arr[pen1];
$nat25 = $arr[rez2]+$arr[per2]+$arr[pen2];
mysql_query("update `le_game_2000` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");

if ($game[chemp] == 'kuefa2' && $nat15 == $nat25){
mysql_query("update `le_game_2000` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}

if ($game['final'] == 'final' && $rezult[0] == $rezult[1]){
mysql_query("update `le_game_2000` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}



 
 /////////////РАСЧЁТ СТАВОК/////////////////////
 
if ($rezult[0] > $rezult[1])
{
$p1 = '1';
}
else if ($rezult[1] > $rezult[0])
{
$p1 = '2';
}
else{
	$p1 = '3';
}
$g6 = @mysql_query("select * from `r_game` where `id`='" . $id . "' ;");
$game6 = @mysql_fetch_array($g6);
mysql_query("update `t_games` set  `score`='".$rezult[0]."|".$rezult[1]."', `winner`='".$p1."' where `id_match`='" . $game6[id_match] . "' LIMIT 1;");

 

$req37 = mysql_query("SELECT * FROM `t_games` where `id_match`='" . $game6[id_match] . "' ;");
$kom337 = @mysql_fetch_array($req37);
 $milsQuery = mysql_query("SELECT * FROM `t_mils` WHERE `refid` = '".$game6[id]."';");
                while($mil = mysql_fetch_array($milsQuery))
                {
				
					$req379 = mysql_query("SELECT * FROM `r_team` where `id_admin`='".$mil[user]."' ;");
$kom3379 = @mysql_fetch_array($req379);

$teams = explode('|', $kom337['teams']); $teamsCount = sizeof($teams);
    $coefs = explode('|', $kom337['coefs']);

   
        $no_winner = TRUE;
        $scores = array();
        for($i = 0; $i < $teamsCount; $i++)
        {
            $score = 0;
            if($_POST['score' . $i] > 0)
                $score = htmlspecialchars(trim($_POST['score' . $i]));
            $scores[$i] = $score;
            if($i > 0 && $score != $scores[$i - 1])
                $no_winner = FALSE;
        }

        $sortedScores = array_flip($scores);
        ksort($sortedScores);

        $winner = end($sortedScores) + 1;
        if($no_winner)
            $winner = sizeof($coefs);
		
                    if($mil['winner'] == $winner){
mysql_query("UPDATE `r_team` SET `money` = `money` +  (".$mil['mil'] * $coefs[$winner - 1]." ) WHERE `id` = '".$kom3379[id]."';");
	if($winner == 1){
					$aaa=''.$teams[0].' <b>П1</b> ';
				}
				elseif($winner == 2){
				$aaa=''.$teams[1].' <b>П2</b> ';
				}
				else{
				$aaa=''.$teams[0].'-'.$teams[1].' <b>Ничья</b> ';
				}        
		mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$mil['mil'] * $coefs[$winner - 1]."',
`text`='Ставка ".$aaa."',
`team_id`='" . $kom3379[id] . "'
;");
			mysql_query("DELETE FROM `t_mils` WHERE `refid` = " . $game6['id'] . ";");
					} }
 /////////////РАСЧЁТ СТАВОК/////////////////////

$l1 = @mysql_query("select * from `le_group_2000` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `le_group_2000` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);


if ($game['etap'] == 'gr'){
if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `le_group_2000` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `le_group_2000` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `le_group_2000` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `le_group_2000` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `le_group_2000` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `le_group_2000` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
}

}





// ЕСЛИ Лига чемпинов
if ($game[chemp] == 'afc_chl')
{
mysql_query("update `afc_chl_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");


$l1 = @mysql_query("select * from `afc_chl_group` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `afc_chl_group` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);


if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `afc_chl_group` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `afc_chl_group` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `afc_chl_group` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `afc_chl_group` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `afc_chl_group` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `afc_chl_group` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}

}




// ЕСЛИ Лига европы
if ($game[chemp] == 'afc_cup')
{

mysql_query("update `afc_cup_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");


$l1 = @mysql_query("select * from `afc_cup_group` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `afc_cup_group` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);


if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `afc_cup_group` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `afc_cup_group` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `afc_cup_group` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `afc_cup_group` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `afc_cup_group` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `afc_cup_group` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}

}









// ЕСЛИ ЧЕМПИОНАТ ПО Азия
if ($game[chemp] == 'asiachamp')
{
mysql_query("update `asiachamp_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");


$l1 = @mysql_query("select * from `asiachamp_table` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `asiachamp_table` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);


if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `asiachamp_table` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `asiachamp_table` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `asiachamp_table` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `asiachamp_table` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `asiachamp_table` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `asiachamp_table` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}

}





// ЕСЛИ ЧЕМПИОНАТ Союзный Чемпионат
if ($game[chemp] == 'unchamp')
{
mysql_query("update `union_champ_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");


$l1 = @mysql_query("select * from `union_champ_table` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `union_champ_table` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);


if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `union_champ_table` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `union_champ_table` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `union_champ_table` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `union_champ_table` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `union_champ_table` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `union_champ_table` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
}


// ЕСЛИ КУБОК КОНФЕДЕРАЦИЙ
if ($game[chemp] == 'cupcom')
{
mysql_query("update `cupcom_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");

if ($game[turnir] == 'cupcom' && $nat1 == $nat2){
mysql_query("update `cupcom_game` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}

if ($game['final'] == 'final' && $rezult[0] == $rezult[1]){
mysql_query("update `cupcom_game` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}

$l1 = @mysql_query("select * from `cupcom_group` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `cupcom_group` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);


if ($game['etap'] == 'gr'){
if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `cupcom_group` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `cupcom_group` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `cupcom_group` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `cupcom_group` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `cupcom_group` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `cupcom_group` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
}

}


// ЕСЛИ КУБОК КОНФЕДЕРАЦИЙ ASIA
if ($game[chemp] == 'afc_cupcom')
{
mysql_query("update `afc_cupcom_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");


$l1 = @mysql_query("select * from `afc_cupcom_group` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `afc_cupcom_group` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);


if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `afc_cupcom_group` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `afc_cupcom_group` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `afc_cupcom_group` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `afc_cupcom_group` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$raz1 = $lrr1[raz]+($rezult[0] - $rezult[1]);
$raz2 = $lrr2[raz]+($rezult[1] - $rezult[0]);

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `afc_cupcom_group` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `raz`='" . $raz1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `afc_cupcom_group` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `raz`='" . $raz2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}

}



// ЕСЛИ Кубок Марадонны
if ($game[chemp] == 'maradona')
{

mysql_query("update `maradona_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");


$l1 = @mysql_query("select * from `maradona_group` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `maradona_group` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);


if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `maradona_group` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `maradona_group` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `maradona_group` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `maradona_group` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `maradona_group` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `maradona_group` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}

}
// ЕСЛИ Лига европы
if ($game[chemp] == 'continent')
{

mysql_query("update `continent_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");


$l1 = @mysql_query("select * from `continent_group` where id_team='" . $game[id_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `continent_group` where id_team='" . $game[id_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);


if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `continent_group` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `continent_group` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `continent_group` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `continent_group` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `continent_group` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `continent_group` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}

}


// ЕСЛИ СУПЕР КУБОК
if ($game[chemp] == 'super_cup')
{
mysql_query("update `super_cup_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");


if ($rezult[0] > $rezult[1])
{
$p1 = '1';
}
else if ($rezult[1] > $rezult[0])
{
$p1 = '2';
}
else{
	$p1 = '3';
}
$g6 = @mysql_query("select * from `r_game` where `id`='" . $id . "' ;");
$game6 = @mysql_fetch_array($g6);
mysql_query("update `t_games` set  `score`='".$rezult[0]."|".$rezult[1]."', `winner`='".$p1."' where `id_match`='" . $game6[id_match] . "' LIMIT 1;");

 

$req37 = mysql_query("SELECT * FROM `t_games` where `id_match`='" . $game6[id_match] . "' ;");
$kom337 = @mysql_fetch_array($req37);
 $milsQuery = mysql_query("SELECT * FROM `t_mils` WHERE `refid` = '".$game6[id]."';");
                while($mil = mysql_fetch_array($milsQuery))
                {
				
					$req379 = mysql_query("SELECT * FROM `r_team` where `id_admin`='".$mil[user]."' ;");
$kom3379 = @mysql_fetch_array($req379);

$teams = explode('|', $kom337['teams']); $teamsCount = sizeof($teams);
    $coefs = explode('|', $kom337['coefs']);

   
        $no_winner = TRUE;
        $scores = array();
        for($i = 0; $i < $teamsCount; $i++)
        {
            $score = 0;
            if($_POST['score' . $i] > 0)
                $score = htmlspecialchars(trim($_POST['score' . $i]));
            $scores[$i] = $score;
            if($i > 0 && $score != $scores[$i - 1])
                $no_winner = FALSE;
        }

        $sortedScores = array_flip($scores);
        ksort($sortedScores);

        $winner = end($sortedScores) + 1;
        if($no_winner)
            $winner = sizeof($coefs);
		
                    if($mil['winner'] == $winner){
mysql_query("UPDATE `r_team` SET `money` = (`money` + " . ($mil['mil'] * $coefs[$winner - 1]) . ") WHERE `id` = '".$kom3379[id]."';");
	if($winner == 1){
					$aaa=''.$teams[0].' <b>П1</b> ';
				}
				elseif($winner == 2){
				$aaa=''.$teams[1].' <b>П2</b> ';
				}
				else{
				$aaa=''.$teams[0].'-'.$teams[1].' <b>Ничья</b> ';
				}        
		mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$mil['mil'] * $coefs[$winner - 1]."',
`text`='Ставка ".$aaa."',
`team_id`='" . $kom3379[id] . "'
;");
			mysql_query("DELETE FROM `t_mils` WHERE `id` = " . $mil['id'] . ";");
					} }

}
// ЕСЛИ СУПЕР КУБОК
if ($game[chemp] == 'super_cup2')
{
mysql_query("update `super_cup_game_2000` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");


if ($rezult[0] > $rezult[1])
{
$p1 = '1';
}
else if ($rezult[1] > $rezult[0])
{
$p1 = '2';
}
else{
	$p1 = '3';
}
$g6 = @mysql_query("select * from `r_game` where `id`='" . $id . "' ;");
$game6 = @mysql_fetch_array($g6);
mysql_query("update `t_games` set  `score`='".$rezult[0]."|".$rezult[1]."', `winner`='".$p1."' where `id_match`='" . $game6[id_match] . "' LIMIT 1;");

 

$req37 = mysql_query("SELECT * FROM `t_games` where `id_match`='" . $game6[id_match] . "' ;");
$kom337 = @mysql_fetch_array($req37);
 $milsQuery = mysql_query("SELECT * FROM `t_mils` WHERE `refid` = '".$game6[id]."';");
                while($mil = mysql_fetch_array($milsQuery))
                {
				
					$req379 = mysql_query("SELECT * FROM `r_team` where `id_admin`='".$mil[user]."' ;");
$kom3379 = @mysql_fetch_array($req379);

$teams = explode('|', $kom337['teams']); $teamsCount = sizeof($teams);
    $coefs = explode('|', $kom337['coefs']);

   
        $no_winner = TRUE;
        $scores = array();
        for($i = 0; $i < $teamsCount; $i++)
        {
            $score = 0;
            if($_POST['score' . $i] > 0)
                $score = htmlspecialchars(trim($_POST['score' . $i]));
            $scores[$i] = $score;
            if($i > 0 && $score != $scores[$i - 1])
                $no_winner = FALSE;
        }

        $sortedScores = array_flip($scores);
        ksort($sortedScores);

        $winner = end($sortedScores) + 1;
        if($no_winner)
            $winner = sizeof($coefs);
		
                    if($mil['winner'] == $winner){
mysql_query("UPDATE `r_team` SET `money` = (`money` + " . ($mil['mil'] * $coefs[$winner - 1]) . ") WHERE `id` = '".$kom3379[id]."';");
	if($winner == 1){
					$aaa=''.$teams[0].' <b>П1</b> ';
				}
				elseif($winner == 2){
				$aaa=''.$teams[1].' <b>П2</b> ';
				}
				else{
				$aaa=''.$teams[0].'-'.$teams[1].' <b>Ничья</b> ';
				}        
		mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='+".$mil['mil'] * $coefs[$winner - 1]."',
`text`='Ставка ".$aaa."',
`team_id`='" . $kom3379[id] . "'
;");
			mysql_query("DELETE FROM `t_mils` WHERE `id` = " . $mil['id'] . ";");
					} }

}

// ЕСЛИ КУБОК Эйсебио
if ($game[chemp] == 'eusebio')
{
mysql_query("update `game_eusebio` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}

// ЕСЛИ КУБОК Англии
if ($game[chemp] == 'cup_en')
{
mysql_query("update `cup_en` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}
// ЕСЛИ КУБОК Нетто
if ($game[chemp] == 'cup_netto')
{
mysql_query("update `cup_netto` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}
// ЕСЛИ КУБОК Чарльтона
if ($game[chemp] == 'cup_charlton')
{
mysql_query("update `cup_charlton` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}
// ЕСЛИ КУБОК Мюллера
if ($game[chemp] == 'cup_muller')
{
mysql_query("update `cup_muller` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}
// ЕСЛИ КУБОК Пушкаша
if ($game[chemp] == 'cup_puskas')
{
mysql_query("update `cup_puskas` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}
// ЕСЛИ КУБОК Факкетти
if ($game[chemp] == 'cup_fachetti')
{
mysql_query("update `cup_fachetti` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}
// ЕСЛИ КУБОК Копа
if ($game[chemp] == 'cup_kopa')
{
mysql_query("update `cup_kopa` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}
// ЕСЛИ КУБОК Ди Стефано
if ($game[chemp] == 'cup_distefano')
{
mysql_query("update `cup_distefano` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}
// ЕСЛИ КУБОК Гарринчи
if ($game[chemp] == 'cup_garrinca')
{
mysql_query("update `cup_garrinca` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}
// ЕСЛИ КУБОК en
if ($game[chemp] == 'cup_en')
{
mysql_query("update `cup_en` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}




// ЕСЛИ КУБОК ru
if ($game[chemp] == 'cup_ru')
{
mysql_query("update `cup_ru` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}

// ЕСЛИ КУБОК pt
if ($game[chemp] == 'cup_pt')
{
mysql_query("update `cup_pt` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}
// ЕСЛИ КУБОК nl
if ($game[chemp] == 'cup_nl')
{
mysql_query("update `cup_nl` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}


// ЕСЛИ КУБОК ua
if ($game[chemp] == 'cup_ua')
{
mysql_query("update `cup_ua` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}


// ЕСЛИ КУБОК es
if ($game[chemp] == 'cup_es')
{
mysql_query("update `cup_es` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}


// ЕСЛИ КУБОК it
if ($game[chemp] == 'cup_it')
{
mysql_query("update `cup_it` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}


// ЕСЛИ КУБОК de
if ($game[chemp] == 'cup_de')
{
mysql_query("update `cup_de` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}


// ЕСЛИ КУБОК fr
if ($game[chemp] == 'cup_fr')
{
mysql_query("update `cup_fr` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}



// ЕСЛИ КУБОК po
if ($game[chemp] == 'cup_po')
{
mysql_query("update `cup_po` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}


// ЕСЛИ СУПЕР КУБОК
if ($game[chemp] == 'afc_super_cup')
{
mysql_query("update `afc_super_cup_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}



// ЕСЛИ КУБОК avs
if ($game[chemp] == 'cup_avs')
{
mysql_query("update `cup_avs` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}




// ЕСЛИ КУБОК az
if ($game[chemp] == 'cup_az')
{
mysql_query("update `cup_az` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}


// ЕСЛИ КУБОК iran
if ($game[chemp] == 'cup_iran')
{
mysql_query("update `cup_iran` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}


// ЕСЛИ КУБОК kaz
if ($game[chemp] == 'cup_kaz')
{
mysql_query("update `cup_kaz` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}


// ЕСЛИ КУБОК kyr
if ($game[chemp] == 'cup_kyr')
{
mysql_query("update `cup_kyr` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}


// ЕСЛИ КУБОК taj
if ($game[chemp] == 'cup_taj')
{
mysql_query("update `cup_taj` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}


// ЕСЛИ КУБОК tur
if ($game[chemp] == 'cup_tur')
{
mysql_query("update `cup_tur` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}



// ЕСЛИ КУБОК uzb
if ($game[chemp] == 'cup_uzb')
{
mysql_query("update `cup_uzb` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}


if ($game[chemp] == 'afs')
{
mysql_query("update `afs_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");
}

// ЕСЛИ Лига европы
if ($game[chemp] == 'msch' && $game[msch_holat] != ok)
{

mysql_query("update `msch_game` set `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `id_report`='".$id."' where id='" . $game[id_match] . "' LIMIT 1;");


$l1 = @mysql_query("select * from `msch_table` where union_id='" . $game[union_team1] . "' LIMIT 1;");
$lrr1 = @mysql_fetch_array($l1);

$l2 = @mysql_query("select * from `msch_table` where union_id='" . $game[union_team2] . "' LIMIT 1;");
$lrr2 = @mysql_fetch_array($l2);



$n1 = @mysql_query("select * from `msch_union_game` where union_id1='" . $game[union_team1] . "' AND union_tur='".$game[msch_tur]."' LIMIT 1;");
$uni1 = @mysql_fetch_array($n1);

$n2 = @mysql_query("select * from `msch_union_game` where union_id2='" . $game[union_team2] . "' AND union_tur='".$game[msch_tur]."' LIMIT 1;");
$uni2 = @mysql_fetch_array($n2);



if ($rezult[0] > $rezult[1])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$uniwin1 = $uni1[union_rez1]+1;

$win1 = $lrr1[win]+1;
$los2 = $lrr2[los]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+3;
$ochey2 = $lrr2[ochey]+0;

mysql_query("update `r".$prefix."game` set `msch_holat`='ok' where id='" . $game[id] . "' LIMIT 1;");
mysql_query("update `msch_union_game` set `union_rez1`='" . $uniwin1 . "' where id='" . $uni1[id] . "' LIMIT 1;");
mysql_query("update `msch_table` set `igr`='" . $igr1 . "', `win`='" . $win1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `msch_table` set `igr`='" . $igr2 . "', `los`='" . $los2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
elseif ($rezult[1] > $rezult[0])
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$los1 = $lrr1[los]+1;
$win2 = $lrr2[win]+1;

$uniwin2 = $uni2[union_rez2]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+0;
$ochey2 = $lrr2[ochey]+3;

mysql_query("update `r".$prefix."game` set `msch_holat`='ok' where id='" . $game[id] . "' LIMIT 1;");
mysql_query("update `msch_union_game` set `union_rez2`='" . $uniwin2 . "' where id='" . $uni2[id] . "' LIMIT 1;");
mysql_query("update `msch_table` set `igr`='" . $igr1 . "', `los`='" . $los1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `msch_table` set `igr`='" . $igr2 . "', `win`='" . $win2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}
else
{
$igr1 = $lrr1[igr]+1;
$igr2 = $lrr2[igr]+1;

$nn1 = $lrr1[nn]+1;
$nn2 = $lrr2[nn]+1;

$gz1 = $lrr1[gz]+$rezult[0];
$gz2 = $lrr2[gz]+$rezult[1];

$gp1 = $lrr1[gp]+$rezult[1];
$gp2 = $lrr2[gp]+$rezult[0];

$ochey1 = $lrr1[ochey]+1;
$ochey2 = $lrr2[ochey]+1;

mysql_query("update `r".$prefix."game` set `msch_holat`='ok' where id='" . $game[id] . "' LIMIT 1;");
mysql_query("update `msch_table` set `igr`='" . $igr1 . "', `nn`='" . $nn1 . "', `gz`='" . $gz1 . "', `gp`='" . $gp1 . "', `ochey`='" . $ochey1 . "' where id='" . $lrr1[id] . "' LIMIT 1;");
mysql_query("update `msch_table` set `igr`='" . $igr2 . "', `nn`='" . $nn2 . "', `gz`='" . $gz2 . "', `gp`='" . $gp2 . "', `ochey`='" . $ochey2 . "' where id='" . $lrr2[id] . "' LIMIT 1;");
}

}





mysql_query("update `r".$prefix."game` set `step`='1' where id='" . $game[id] . "' LIMIT 1;");














	$q1auy = @mysql_query("select * from `r_judge` WHERE `id`='".$game[judge]."'  LIMIT 1;");
$aayr = @mysql_fetch_array($q1auy);
$gggs = $aayr[game]+1;

mysql_query("update `r_judge` set `game`='".$gggs."' where id='" . $game[judge] . "' LIMIT 1;");









/* $k1 = @mysql_query("select * from `r_team` where id='" . $arr[id_team1] . "' LIMIT 1;");
$kom1 = @mysql_fetch_array($k1);

$k2 = @mysql_query("select * from `r_team` where id='" . $arr[id_team2] . "' LIMIT 1;");
$kom2 = @mysql_fetch_array($k2); */ 
//////////////////judge

if($kom1[ref]){
	$reff = $kom1[ref]-1;
mysql_query("update `r_team` set `ref`='".$reff."' where id='" . $kom1[id] . "' LIMIT 1;");
}
if($kom2[ref]){
	$reff2 = $kom2[ref]-1;
mysql_query("update `r_team` set `ref`='".$reff2."' where id='" . $kom2[id] . "' LIMIT 1;");
}
//////////////////judge










}
//echo'<div class="cardview-wrapper" bis_skin_checked="1">				<a class="cardview" href="/report/'.$id.'">		<div class="left px50" bis_skin_checked="1"><i class="font-icon font-icon-whistle"></i></div>		<div class="right px50 arrow" bis_skin_checked="1">			<div class="text" bis_skin_checked="1">Посмотреть отчет</div>		</div>	</a></div>';




// if($mt<=93){
//echo'</div>';
// }
// else{
// echo'конец';}



/* 

////////////////////////ПРОВЕРКА ПЕНАЛЬТИ 

$g = @mysql_query("select * from `r_game` where id = '" . $id . "' LIMIT 1;");
$game = @mysql_fetch_array($g);

$nat1 = $game[rez1]+$game[per1];
$nat2 = $game[rez2]+$game[per2];
if(!$pen1 || !$pen2){
if($nat11 == $nat22){

if($game['chemp'] == 'liga_r2' || $game['chemp'] == 'kuefa2'){
  
    $input = array ("11:10", "10:9", "8:7", "7:6", "6:5", "5:3", "5:4", "4:2", "4:3", "3:2", "3:5", "4:5", "2:4", "3:4", "2:3", "10:11", "9:10", "7:8", "6:7", "5:6");

    $rand_keys = array_rand ($input);

    $penult = explode(":",$input[$rand_keys]);

    $pen1 = $penult[0];
    $pen2 = $penult[1];
    
 mysql_query("update `r_game` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $id . "' LIMIT 1;");
 
 
 if ($game['final'] != 'final' && $game[chemp] == 'kuefa2' && $nat1 == $nat2){
mysql_query("update `le_game_2000` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}
 if ($game['final'] != 'final' && $game[chemp] == 'liga_r2' && $nat1 == $nat2){
mysql_query("update `liga_game_r2000` set `pen1`='".$pen1."', `pen2`='".$pen2."' where id='" . $game[id_match] . "' LIMIT 1;");
}

}
}
}
////////////////////////ПРОВЕРКА ПЕНАЛЬТИ 



 */








 
 
 
 
 

 mysqli_close($link);
 
require_once ("../incfiles/end.php");
//require_once ("end.php");
?>
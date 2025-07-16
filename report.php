<?php
define('_IN_JOHNCMS', 1);
$headmod = 'report';
require_once("../incfiles/core.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Инициализация переменных
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$prefix = !empty($_GET['union']) ? '_union_' : '_';
$dirs = !empty($_GET['union']) ? '/union/' : '/';
$textl = 'Игра';
$issetun = !empty($_GET['union']) ? '&amp;union=isset' : '';

// Проверка ID матча
if (!$id) {
    echo display_error('Не указан ID матча');
    require_once("../incfiles/end.php");
    exit;
}

// Запрос данных матча
$q = mysql_query("SELECT * FROM `r" . $prefix . "game` WHERE id = '" . mysql_real_escape_string($id) . "' LIMIT 1");
$arr = mysql_fetch_assoc($q);

if (!$arr) {
    echo display_error('Матч не найден');
    require_once("../incfiles/end.php");
    exit;
}

// Проверка времени матча
$mt = floor(($realtime - $arr['time']) * 18 / 60);
if ($mt <= 93) {
    header("Location: /txt" . $dirs . $id);
    exit;
}

// Запрос данных команд
$team1 = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_team` WHERE id = '" . (int)$arr['id_team1'] . "' LIMIT 1"));
$team2 = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_team` WHERE id = '" . (int)$arr['id_team2'] . "' LIMIT 1"));
$textl = $team1['name'] . ' - ' . $team2['name'] . ' ' . $arr['rez1'] . ':' . $arr['rez2'];

require_once("../incfiles/head.php");

// Проверка тактик
if (empty($arr['tactics1']) || empty($arr['tactics2'])) {
    header('Location: /game' . $dirs . $id);
    exit;
}

// Инициализация кубков
$c_names = array();
for ($i = 1; $i <= 50; $i++) {
    $c_names[$i] = '${c_' . $i . '}'; // Заполнители будут заменены реальными значениями
}

$special_cups = array(
    'cup_netto' => 'Кубок Нетто',
    'cup_charlton' => 'Кубок Чарльтона',
    'cup_muller' => 'Кубок Мюллера',
    'cup_puskas' => 'Кубок Пушкаша',
    'cup_fachetti' => 'Кубок Факкетти',
    'cup_kopa' => 'Кубок Копа',
    'cup_distefano' => 'Кубок Ди Стефано',
    'cup_garrinca' => 'Кубок Гарринчи'
);

// Определение названия кубка
$c_name = '';
if (isset($special_cups[$arr['kubok']])) {
    $c_name = $special_cups[$arr['kubok']];
} elseif (isset($c_names[$arr['kubok']])) {
    $c_name = $c_names[$arr['kubok']];
}

// Обработка типа турнира
$tournament_html = '';
switch ($arr['chemp']) {
    case 'super_cup':
    case 'super_cup2':
        $tournament_html = '<link rel="stylesheet" href="/theme/cups/super_cup.css" type="text/css" />
            <div class="phdr_le" style="text-align:left"><font color="white">' . $arr['kubok_nomi'] . '</font>
            <b class="rlink"><font color="white">' . date("d.m.Y H:i", $arr['time']) . '</b></font></div>
            <div class="phdr_le" style="text-align:left"><center>
            <a href="/super_cup' . ($arr['chemp'] == 'super_cup2' ? '2' : '') . '/"><b>' . $arr['kubok_nomi'] . '</b></a></center></div>
            <div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
            <img src="/images/cup/b_super_cup.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
    
    case 'cup_en':
    case 'cup_ru':
    case 'cup_de':
    case 'cup_pt':
    case 'cup_es':
    case 'cup_it':
    case 'cup_fr':
    case 'cup_nl':
        $country = substr($arr['chemp'], 4);
        $tournament_html = '<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />
            <div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=' . $country . '">' . $arr['kubok_nomi'] . '</a></font>
            <b class="rlink"><font color="white">' . date("d.m.Y H:i", $arr['time']) . '</b></font></div>
            <div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
            <img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
    
    case 'cup_netto':
    case 'cup_charlton':
    case 'cup_muller':
    case 'cup_puskas':
    case 'cup_fachetti':
    case 'cup_kopa':
    case 'cup_distefano':
    case 'cup_garrinca':
        $cup_name = substr($arr['chemp'], 4);
        $tournament_html = '<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />
            <div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=' . $cup_name . '">' . $arr['kubok_nomi'] . '</a></font>
            <b class="rlink"><font color="white">' . date("d.m.Y H:i", $arr['time']) . '</b></font></div>
            <div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
            <img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
    
    case 'frend':
        $tournament_html = '<a href="/friendly/" class="cardview x-pt-3 x-block-center x-rounded x-bg-cover x-onhover-wrapper" 
            style="background-image: url(/images/cup/friendly.png);width: 75px;height: 75px;overflow: visible;" 
            title="Перейти в кубок"></a>
            <div class="gmenu"><center><a href="/friendly/"><b>Товарищеский матч</b></a></center></div>';
        break;
    
    case 'z_cup':
        $tournament_html = '<div class="gmenu"><center><a href="/cup3/' . $arr['id_kubok'] . '"><b>' . $arr['kubok_nomi'] . '</b></a></center></div>
            <div class="gmenu"><center><img src="/images/cup/b_' . $arr['kubok'] . '.png" alt="Кубок"/></center></div>';
        break;
    
    case 'cup':
        $tournament_html = '<div class="gmenu"><center><a href="/cup/' . $arr['id_kubok'] . '"><b>' . $c_name . '</b></a></center></div>';
        break;
    
    case 'unchamp':
        $tournament_html = '<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />
            <div class="phdr_lk"><font color="white">' . $arr['kubok_nomi'] . '</font>
            <b class="rlink"><font color="white">' . date("d.m.Y H:i", $arr['time']) . '</b></font></div>
            <div class="phdr_lk"><center>
            <a href="/union_champ/index.php?id=' . $arr['id_kubok'] . '"><b>' . $arr['kubok_nomi'] . '</b></a></center></div>
            <div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
            <img src="/union/logo/cup' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
    
    case 'champ':
        $tournament_html = '<div class="phdr">Чемпионат<b class="rlink">' . date("d.m.Y H:i", $arr['time']) . '</b></div>
            <div class="gmenu"><center><a href="/champ00/index.php?act=' . $arr['kubok'] . '"><b>' . $arr['kubok_nomi'] . '</b></a></center></div>
            <div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
            <img src="/images/cup/b_00' . $arr['kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
    
    case 'champ_retro':
        $tournament_html = '<div class="gmenu"><center><a href="/champ_retro/index.php?act=' . $arr['id_kubok'] . '"><b>' . $arr['kubok_nomi'] . '</b></a></center></div>';
        break;
    
    case 'liga_r':
    case 'liga_r2':
        $tournament_html = '<link rel="stylesheet" href="/theme/cups/lc.css" type="text/css" />
            <div class="phdr_lc"><center>
            <a href="/' . $arr['id_kubok'] . '/"><b>' . $arr['kubok_nomi'] . '</b></a></center></div>
            <div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
            <img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
    
    case 'le':
    case 'kuefa2':
        $tournament_html = '<link rel="stylesheet" href="/theme/cups/le.css" type="text/css" />
            <div class="phdr_le"><font color="white"><a href="/' . $arr['id_kubok'] . '/"><b>' . $arr['kubok_nomi'] . '</b></a></font>
            <b class="rlink"><font color="white">' . date("d.m.Y H:i", $arr['time']) . '</b></font></div>
            <div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
            <img src="/images/cup/b_le.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
    
    case 'maradona':
        $tournament_html = '<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />
            <div class="gmenu"><center><a href="/' . $arr['id_kubok'] . '/"><b></b></a></center></div>
            <div xmlns="http://www.w3.org/1999/xhtml" class="phdr_lk">' . $arr['kubok_nomi'] . '</div>
            <div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
            <img src="/images/cup/b_cupcom.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
    
    case 'lk':
        $tournament_html = '<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />
            <div class="gmenu"><center><a href="/' . $arr['id_kubok'] . '/"><b>' . $arr['kubok_nomi'] . '</b></a></center></div>
            <div xmlns="http://www.w3.org/1999/xhtml" class="phdr_lk">Кубок</div>
            <div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
            <img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
    
    default:
        $tournament_html = '<div class="gmenu"><center><img src="/images/cup/b_' . $arr['kubok'] . '.png" alt="Кубок"/></center></div>';
        break;
}

// Вывод информации о турнире
echo $tournament_html;
echo '<div class="gmenu" style="text-align: center"><div style="font-weight: bold">' . date("d.m.Y H:i", $arr['time']) . '</div>';

// Вывод судьи
$judge = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_judge` WHERE `id` = '" . (int)$arr['judge'] . "' LIMIT 1"));
echo '<div class="game22">
    <div><b><img src="/images/gen4/whistle.png" class="va" alt=""> Главный арбитр матча</b></div>
    <div><a href="/judge/index.php?id=' . $judge['id'] . '">
        <span class="flags c_' . $judge['flag'] . '_18" style="vertical-align: middle;" title="' . $judge['flag'] . '"></span> ' . $judge['name'] . '
    </a></div>
</div>';
echo'lox1';
// Вывод команд и счета
echo '<div style="display: flex; justify-content: space-around;">';
echo render_team_block($team1);
echo render_score_block($arr, $team1, $team2);
echo render_team_block($team2);
echo '</div>';

// Функция рендеринга команды
function render_team_block($team) {
    $logo = !empty($team['logo']) 
        ? '<img src="/manager/logo/big' . $team['logo'] . '" alt="Logo"/>' 
        : '<img src="/manager/logo/b_0.jpg" alt="Logo" width="37"/>';
    
    $manager = '';
    if ($team['id_admin'] > 0) {
        $user = mysql_fetch_assoc(mysql_query("SELECT * FROM `users` WHERE `id` = " . (int)$team['id_admin'] . " LIMIT 1"));
        if ($user) {
            $vip_icon = '/images/ico/vip' . $user['vip'] . '_m.png';
            $manager = '<span style="opacity:0.4"><img src="' . $vip_icon . '" title="VIP статус" 
                style="width: 12px;border: none;vertical-align: middle;">' . $user['name'] . '</span>';
        }
    }
    
    return '<a href="/team/' . $team['id'] . '" class="x-color-black x-hover" 
        style="align-items: center;display: flex;flex-direction: column;justify-content: center;flex-basis: 0;flex-grow: 1;">
        ' . $logo . '
        <div class="x-py-2">' . $team['name'] . '<br>' . $manager . '</div>
    </a>';
}

// Функция рендеринга счета
function render_score_block($match, $team1, $team2) {
    $score = '<b>' . $match['rez1'] . '</b>:<b>' . $match['rez2'] . '</b>';
    
    $cups_with_penalties = array('cup', 'b_cup', 'z_cup', 'cup_continent', 'super_cup', 'super_cup2', 'cupcom',
        'cup_netto', 'cup_charlton', 'cup_muller', 'cup_puskas', 'cup_fachetti', 'cup_kopa', 'cup_distefano', 
        'cup_garrinca', 'cup_ru', 'cup_en', 'cup_de', 'cup_pt', 'cup_es', 'cup_it', 'cup_fr', 'cup_nl');
    
    if (in_array($match['chemp'], $cups_with_penalties)) {
        if ($match['pen1'] || $match['pen2']) {
            $score .= '<br/> (п. ' . $match['pen1'] . ':' . $match['pen2'] . ')';
        }
    }
    
    // Дополнительное время
    if ($match['per1'] || $match['per2']) {
        if ($match['per1'] == $match['per2'] && ($match['pen1'] || $match['pen2'])) {
            $score .= '<br/> (пен. ' . $match['pen1'] . ':' . $match['pen2'] . ')';
        }
    }
    
    return '<div style="align-items: center;display: flex;flex-direction: column;justify-content: center;">
        <div class="x-font-150 x-color-red"><div class="x-font-bold">' . $score . '</div><div class="x-font-75"></div></div>
    </div>';
}

// События матча (голы, карточки)
echo '<div class="textcols"><div class="textcols-item">';
if ($arr['teh_end'] != 1) {
    render_match_events($arr['menus'], $team1, 'g.gif', '');
    if ($arr['chemp'] != 'frend') {
        render_match_events($arr['menus1'], $team1, 'yc.png', 'Желтая');
        render_match_events($arr['menus2'], $team1, 'rc.png', 'Красная');
    }
}
echo '</div><div class="textcols-item">';
if ($arr['teh_end'] != 1) {
    render_match_events($arr['menus'], $team2, 'g.gif', '');
    if ($arr['chemp'] != 'frend') {
        render_match_events($arr['menus1'], $team2, 'yc.png', 'Желтая карточка');
        render_match_events($arr['menus2'], $team2, 'rc.png', 'Красная карточка');
    }
}
echo '</div></div>';

// Функция рендеринга событий
function render_match_events($events, $team, $icon, $title) {
    $events = explode("\r\n", $events);
    if (!is_array($events)) return;
    
    foreach ($events as $event) {
        if (empty($event)) continue;
        
        $parts = explode("|", $event);
        if (count($parts) < 5) continue;
        
        $player = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_player` WHERE `id` = '" . (int)$parts[2] . "'"));
        if (!$player || $player['team'] != $team['id']) continue;
        
        echo '<table><tbody><tr>                        
            <td>' . $parts[0] . '’ <img src="/images/' . $icon . '" alt="' . $title . '" style="vertical-align: middle;"></td>
            <td class="x-text-center"><a href="/player/' . $parts[2] . '">' . $parts[3] . ' ' . $parts[4] . '</a></td>
        </tr></tbody></table>';
    }
}

// Стадион
$stadium = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_stadium` WHERE `id` = '" . (int)$arr['id_stadium'] . "'"));
$stadium_img = $stadium && $stadium['std'] ? $arr['id_stadium'] : 'stadium';

echo '<div class="gmenu"><center>';
if ($stadium) {
    echo '<img src="/images/stadium/' . $stadium_img . '.jpg" alt="' . $stadium['name'] . '"/>';
} else {
    echo '<img src="/images/stadium/stadium.jpg" alt="Стадион"/>';
}
echo '<div class="error" style="max-width: 480px;">' . $arr['zritel'] . ' Зрителей на Стадионе ';
if ($stadium) {
    echo '<a href="/buildings/stadium.php?id=' . $stadium['id'] . '"><b>' . $stadium['name'] . '</b></a>';
} else {
    echo 'Неизвестный стадион';
}
echo '</center></div>';

// Техническое поражение
if ($arr['teh_end'] == 1) {
    echo '<div class="info">В матче зафиксировано техническое поражение одной из команд. 
        Матч отменён, победителем признана команда которая не нарушила регламент.</div>';
} else {
    echo '<div class="cardview-wrapper" bis_skin_checked="1">                
        <a class="cardview" href="/txt' . $dirs . $id . '">
            <div class="left px50" bis_skin_checked="1"><i class="font-icon font-icon-whistle"></i></div>
            <div class="right px50 arrow" bis_skin_checked="1">
                <div class="text" bis_skin_checked="1">Посмотреть трансляцию</div>
            </div>
        </a>
    </div>';
}

// Вкладки
echo '<div style="display: flex; text-align: center; width: 100%; justify-content: center; align-items: center;">
    <div class="tab-p but head_button" type="button" id="addteam">Состав</div>
    <div class="tab-p but head_button" type="button" id="h2h">H2H</div>
</div>';

// Скрипт для вкладок
echo '<script>
$(function() {
    $(".but").on("click", function(e) {
        e.preventDefault();
        $(".content").hide();
        $("#" + this.id + "div").show();
    });
});
</script>';

// Стили для вкладок
echo '<style>
.content { display: none; }
div.tab-a, div.tab-p {
    background-color: var(--player-primary);
    border: 1px solid var(--stripy-border);
    width: 110px;
    height: 20px;
    cursor: pointer;
    vertical-align: middle;
    text-align: center;
    font-weight: 100;
    border-radius: 2px;
    padding-top: 4px;
    font-size: 11px;
}
div.tab-a {
    color: #fff;
    background: url("/images/bgs/squard-top-active.png") repeat-x #8dc578;
}
.textcols { white-space: nowrap; }
.textcols-item { display: inline-block; width: 30%; margin: 0 auto; vertical-align: top; }
.textcols .textcols-item:first-child { width: 55%; }
@media (min-width: 640px) {
    #content, #content2 { width: 45%; padding: 3% 4%; float: left; }
    #sidebar, #sidebar2 { width: 45%; float: right; }
}
</style>';

// Составы команд
echo '<div id="addteamdiv" class="content">';
echo '<div class="cardview-wrapper3" bis_skin_checked="1">';
render_team_lineup($arr['players1'], $team1);
render_team_lineup($arr['players2'], $team2);
echo '</div></div>';

// Функция рендеринга состава
function render_team_lineup($players, $team) {
    $players = explode("\r\n", $players);
    // Удаляем ненужную проверку is_array (всегда true) 
    // и добавляем проверку на пустоту данных:
    if (empty($players)) return; // Выходим, если нет игроков

    echo '<table id="content" class="t-table x-text-center" style="margin: 0 auto;">
        <tr class="whiteheader"><th colspan="3"><b>' . $team['name'] . '</b></th><th>Опыт</th></tr>';
    
    foreach ($players as $player) {
        if (empty($player)) continue;
        
        $parts = explode("|", $player);
        if (count($parts) < 4) continue; // Пропускаем некорректные строки
        
        $player_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_player` WHERE `id` = '" . (int)$parts[1] . "'"));
        if (!$player_data) continue;
        
        $line_class = '';
        switch ($player_data['line']) {
            case 1: $line_class = 'background-color: #F5FFEF;'; break;
            case 2: $line_class = 'background-color: #E2FFD2;'; break;
            case 3: $line_class = 'background-color: #ccf3b5;'; break;
            case 4: $line_class = 'background-color: #b0ea8f;'; break;
            default: $line_class = 'background:#FFF7E7;'; break;
        }
        
        $photo = $player_data['photo'] 
            ? '<img src="/images/players/' . $player_data['photo'] . '" width="25px" style="margin:-3px 0px;" alt=""/>'
            : ($player_data['line'] == 1 
                ? '<img src="/images/players/gk.png" width="25px" style="margin:-3px 0px;" alt=""/>'
                : '<img src="/images/players/cm.png" width="25px" style="margin:-3px 0px;" alt=""/>');
        
        // Исправлена структура ячеек (был лишний <th> внутри <td>)
        echo '<tr style="' . $line_class . '">
            <td>' . $parts[0] . '</td>
            <td style="text-align: left;"><a href="/player/' . $parts[1] . '" style="width: 100%; display: block;">
                ' . $photo . ' ' . $parts[2] . '
            </a></td>
            <td style="font-weight: bold;">' . $parts[3] . '</td>
        </tr>';
    }
    echo '</table>';
}

// История матчей
echo '<div id="h2hdiv" class="content">';
render_match_history($team1, $arr['id']);
render_match_history($team2, $arr['id']);
render_head_to_head($team1, $team2, $arr['id']);
echo '</div>';

// Функция рендеринга истории матчей
function render_match_history($team, $current_match_id) {
    echo '<div class="phdr">Последние игры: ' . $team['name'] . '</div>
        <div class="c">
            <table id="example">
                <tbody>';
    
    $matches = mysql_query("SELECT * FROM `r_game` 
        WHERE (`id_team1` = '" . (int)$team['id'] . "' OR `id_team2` = '" . (int)$team['id'] . "') 
        AND `id` != '" . (int)$current_match_id . "'
        ORDER BY time DESC LIMIT 5");
    
    if (mysql_num_rows($matches) == 0) {
        echo '<tr><td colspan="4"><div class="content_empty" bis_skin_checked="1">
                <img src="/images/no_report.png" alt="x" style="vertical-align:middle">
                <p>Последних матчей не было</p>
            </div></td></tr>';
    } else {
        while ($match = mysql_fetch_assoc($matches)) {
            $is_home = ($match['id_team1'] == $team['id']);
            $opponent_id = $is_home ? $match['id_team2'] : $match['id_team1'];
            $opponent = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_team` WHERE id = $opponent_id"));
            
            $date = (date("d.m.y", $match['time']) == date("d.m.y")) 
                ? '<span style="color:#A9A9A9;"><span class="today">Сегодня</span></span>' 
                : date("d.m.y", $match['time']);
            
            $result = '';
            if (!empty($match['rez1']) || !empty($match['rez2'])) {
                $score = $is_home ? $match['rez1'] . ':' . $match['rez2'] : $match['rez2'] . ':' . $match['rez1'];
                $result = '<a href="/report/' . $match['id'] . '"><font color="green"><b>' . $score . '</b></font></a>';
            } else {
                $result = '<a href="/game/' . $match['id'] . '"><font color="green"><b>?:?</b></font></a>';
            }
            
            $result_class = '';
            if ($is_home) {
                if ($match['rez1'] > $match['rez2']) $result_class = 'background-color: #15BB16;';
                elseif ($match['rez1'] < $match['rez2']) $result_class = 'background-color: #DD2729;';
                else $result_class = 'background-color: #F4A62E;';
            } else {
                if ($match['rez2'] > $match['rez1']) $result_class = 'background-color: #15BB16;';
                elseif ($match['rez2'] < $match['rez1']) $result_class = 'background-color: #DD2729;';
                else $result_class = 'background-color: #F4A62E;';
            }
            
            echo '<tr>
                <td width="20%" align="center">' . $date . '</td>
                <td>
                    <span class="flags c_' . $opponent['flag'] . '_14" title="' . $opponent['flag'] . '"></span>
                    <a href="/team/' . $opponent_id . '">' . $opponent['name'] . '</a>
                </td>
                <td width="15%"><center>' . $result . '</center></td>
                <td align="center">
                    <div style="border-radius: 5px;min-width: 20px;min-height: 20px;width: 20px;height: 20px;' . $result_class . 'text-align: center;">
                        <span style="line-height: 22px;font-weight: bold;text-align: center; border-radius: 2px; color: white;">
                            ' . (strpos($result_class, '#15BB16') !== false ? 'В' : (strpos($result_class, '#DD2729') !== false ? 'П' : 'Н')) . '
                        </span>
                    </div>
                </td>
            </tr>';
        }
    }
    echo '</tbody></table></div>';
}

// Функция рендеринга очных встреч
function render_head_to_head($team1, $team2, $current_match_id) {
    echo '<div class="phdr">Очные встречи: ' . $team1['name'] . '-' . $team2['name'] . '</div>
        <div class="c">
            <table id="example">
                <tbody>';
    
    $matches = mysql_query("SELECT * FROM `r_game` 
        WHERE ((`id_team1` = '" . (int)$team1['id'] . "' AND `id_team2` = '" . (int)$team2['id'] . "')
            OR (`id_team1` = '" . (int)$team2['id'] . "' AND `id_team2` = '" . (int)$team1['id'] . "'))
        AND `id` != '" . (int)$current_match_id . "'
        AND (`rez1` != '' OR `rez2` != '')
        ORDER BY time DESC LIMIT 5");
    
    if (mysql_num_rows($matches) == 0) {
        echo '<tr><td colspan="4">
            <div class="game-ui__history">
                <div style="font-size:140%;">История противостояния
                    <span class="green">' . $team1['name'] . ' - ' . $team2['name'] . '</span>
                </div>
                <div id="history-prematch" style="margin-bottom:20px;"><br>
                    Данная статистика доступна для владельцев 
                    <a href="/vip.php?action=compare&amp;type=1">
                        <img src="/images/ico/vip1.png" title="Улучшенный Премиум-аккаунт" style="width: 40px;border: none;vertical-align: middle;">
                    </a>
                </div>
            </div>
        </td></tr>';
    } else {
        while ($match = mysql_fetch_assoc($matches)) {
            $home_team = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_team` WHERE id = " . $match['id_team1']));
            $away_team = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_team` WHERE id = " . $match['id_team2']));
            
            $date = (date("d.m.y", $match['time']) == date("d.m.y")) 
                ? '<span style="color:#A9A9A9;"><span class="today">Сегодня</span></span>' 
                : date("d.m.y", $match['time']);
            
            $result = '';
            if (!empty($match['rez1']) || !empty($match['rez2'])) {
                $result = '<a href="/report/' . $match['id'] . '"><font color="green"><b>' . $match['rez1'] . ':' . $match['rez2'] . '</b></font></a>';
            } else {
                $result = '<a href="/game/' . $match['id'] . '"><font color="green"><b>?:?</b></font></a>';
            }
            
            echo '<tr>
                <td width="20%" align="center">' . $date . '</td>
                <td>
                    <span class="flags c_' . $home_team['flag'] . '_14" title="' . $home_team['flag'] . '"></span>
                    <a href="/team/' . $home_team['id'] . '">' . $home_team['name'] . '</a> - 
                    <span class="flags c_' . $away_team['flag'] . '_14" title="' . $away_team['flag'] . '"></span>
                    <a href="/team/' . $away_team['id'] . '">' . $away_team['name'] . '</a>
                </td>
                <td width="15%"><center>' . $result . '</center></td>
            </tr>';
        }
    }
    echo '</tbody></table></div>';
}

// Статистика игры
$bb = mysql_query("SELECT * FROM `news_2` WHERE `tid` = '" . (int)$id . "' ORDER BY `time` DESC");
if (mysql_num_rows($bb) > 0) {
    echo '<div class="gmenu">Статистика игры:</div>';
    $i = 0;
    while ($bb1 = mysql_fetch_assoc($bb)) {
        $class = ($i % 2 == 0) ? 'list1' : 'list2';
        $parts = explode("|", $bb1['news']);
        echo '<div class="' . $class . '">
            <img src="/imgages/txt/m_' . $parts[1] . '.gif" alt=""/> ' . $parts[2] . '
        </div>';
        $i++;
    }
}

require_once("../incfiles/end.php");
?>

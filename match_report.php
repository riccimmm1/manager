<?php
define('_IN_JOHNCMS', 1);
$headmod = 'match_report';
require_once("../incfiles/core.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Определение режима работы (report/txt)
$mod = isset($_GET['mod']) ? $_GET['mod'] : 'report';

// Обработка параметров
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$union = isset($_GET['union']) ? (int)$_GET['union'] : 0;
$prefix = $union ? '_union_' : '_';
$dirs = $union ? '/union/' : '/';

// Проверка ID матча
if (!$id) {
    $textl = 'Ошибка';
    require_once("../incfiles/head.php");
    echo display_error('Не указан ID матча');
    require_once("../incfiles/end.php");
    exit;
}

// Загрузка данных матча
$q = mysql_query("SELECT * FROM `r" . $prefix . "game` WHERE id = '" . mysql_real_escape_string($id) . "' LIMIT 1");
$arr = mysql_fetch_assoc($q);

if (!$arr) {
    $textl = 'Ошибка';
    require_once("../incfiles/head.php");
    echo display_error('Матч не найден');
    require_once("../incfiles/end.php");
    exit;
}

// Проверка тактик
if (empty($arr['tactics1']) || empty($arr['tactics2'])) {
    header('Location: /game' . $dirs . $id);
    exit;
}

// Расчет времени матча
$realtime = time();
$mt = floor(($realtime - $arr['time']) * 18 / 60);

// Автоматический выбор режима если не указан
if (!isset($_GET['mod'])) {
    $mod = ($mt > 93) ? 'report' : 'txt';
}

// Автоматическое перенаправление
if ($mod == 'report' && $mt <= 93) {
    header("Location: ?mod=txt&id=$id&union=$union");
    exit;
} elseif ($mod == 'txt' && $mt > 93) {
    header("Location: ?mod=report&id=$id&union=$union");
    exit;
}

// Загрузка данных команд
$team1 = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_team` WHERE id = '" . (int)$arr['id_team1'] . "' LIMIT 1"));
$team2 = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_team` WHERE id = '" . (int)$arr['id_team2'] . "' LIMIT 1"));

// Установка заголовка страницы
$textl = ($mod == 'report')
    ? $team1['name'] . ' - ' . $team2['name'] . ' ' . $arr['rez1'] . ':' . $arr['rez2']
    : htmlspecialchars($arr['name_team1']) . ' - ' . htmlspecialchars($arr['name_team2']) . ' ' . $arr['rez1'] . ':' . $arr['rez2'];

require_once("../incfiles/head.php");

// ================== ОБЩАЯ ЧАСТЬ ДЛЯ ОБОИХ РЕЖИМОВ ==================
$c_names = array();
for ($i = 1; $i <= 50; $i++) {
    $c_names[$i] = '${c_' . $i . '}';
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

// ================== РЕЖИМ ОТЧЕТА (REPORT) ==================
if ($mod == 'report') {
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

    // Вывод команд и счета
    echo '<div style="display: flex; justify-content: space-around;">';
    echo render_team_block($team1);
    echo render_score_block($arr, $team1, $team2);
    echo render_team_block($team2);
    echo '</div>';

    // Функции для отображения команд и счета
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
        if (empty($players)) return;

        echo '<table id="content" class="t-table x-text-center" style="margin: 0 auto;">
            <tr class="whiteheader"><th colspan="3"><b>' . $team['name'] . '</b></th><th>Опыт</th></tr>';
        
        foreach ($players as $player) {
            if (empty($player)) continue;
            
            $parts = explode("|", $player);
            if (count($parts) < 4) continue;
            
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
}
// ================== РЕЖИМ ТРАНСЛЯЦИИ (TXT) ==================
else {
    // Стили для трансляции
    echo '<style>
        table #head { background:url("/images/stretch/bgvip.jpg") top center no-repeat; }
        .score_board {
            background: url("/images/ico/res.gif") center center no-repeat; width:70px; height:40px;
            font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 20pt; color: #e6ffc6; text-align: center; font-weight: bold;
        }
        .head_separator { border-top:1px #aeaeae solid; width: 440px; margin: 8px auto; }
        #s1 canvas { position: absolute; top: 0; left: 0; }
        .vista { margin-top: 10px; max-width: 200px; }
        #add-comment-field { position:absolute; z-index:500; width:786px; background-color:#e0f2da; padding:6px; text-align:center; border-bottom:1px solid #b3c6b3; }
        .ac-inner { padding:6px; text-align:left; margin-left:20px; position:relative; top:-4px; }
        #input-comment { width:580px; margin-bottom:5px; }
        .stseat { border-radius:0 0 8px 8px; width:786px; background-color:#e0f2da; padding:6px; text-align:center; }
        .rose-item { display:inline-block; margin-right: 2px; cursor:pointer; padding:3px; }
        table#liverep { width:200px; border-spacing:0; border-collapse:collapse; border:1px solid #BBC7B5; }
        table#liverep td { background-color:#EDFFE3; border-bottom:1px solid #BBC7B5; padding:2px; }
        .newresult { margin-top:6px; padding:3px; color:#ffffff; background-color:#000000; opacity:0.6; cursor:pointer; }
        .lms { margin-left: 3px; font-size: 10px; float: left; overflow: hidden; color: #a7a7a7; }
        table.tornt2 { width: 200px; border: 1px solid #cccccc; margin-top: 3px; }
        table.tornt2 td { padding: 2px; }
        table.tornt2 tr:nth-child(2n+1){ background-color:#ddf6d1; }
        table.tornt2 tr:nth-child(2n){ background-color:#edffe4; }
        tr.parts4 td a.original, tr.parts4 td { font-weight: bold; }
        table.tornt2 td:nth-child(1){ color: #A3A3A3; }
        .ws1, .ws2, .ws3, .ws4, .ws5 { color: #fff; padding: 1px; display: inline-block; margin-bottom: 3px; }
        .ws1 { background-color: #5db81a; }
        .ws2 { background-color: #1A69B8; }
        .ws3 { background-color: #b81a1a; }
        .ws4 { background-color: #1AB99E; }
        .ws5 { background-color: #A91AB9; }
        .aws { text-align:center; margin-bottom: 6px; cursor: pointer; width: 50px; display: inline-block; white-space: nowrap; }
        .tech_def_inf { width:800px; padding:8px; text-align:center; background-color:#fdfcd0; border: 1px solid #b81103; margin: 3px auto; }
        .layer-t { width: 800px; height: 655px; overflow: hidden; background-color:#ffffff; position:absolute; z-index:60; }
        .layer-hm { width: 800px; height: 550px; overflow: hidden; }
        #waitscreen { width: 800px; height: 454px; background-color: #000000; opacity: 0.7; text-align: center; margin-top: 20px; color: #fff; position:absolute; z-index:200; font-size:200% }
        #match2 { width: 800px; height: 550px; overflow: hidden; text-align: left; }
        #positions { position: absolute; z-index: 900; width: 800px; }
        #game-info { position: absolute; z-index: 16; }
        .hidel { font-size: 16px; text-align: center; border: 1px solid #d5d5d5; position: relative; top: 20px; left: 690px; background-color: #fff; border-radius: 18px; height: 18px; width: 18px; padding: 3px; cursor: pointer; }
        .someh { z-index: 1; margin-top: 40px; margin-left: 40px; opacity:0.5; background-color: #000000;width: 700px; height: 410px; position: absolute; }
        #historycont { margin-top: 50px; margin-left: 55px; background-color: #ffffff; padding:12px; position: absolute; z-index: 2; width: 650px; height: 360px; }
        #audio_allow { position: relative; top: -5px; cursor: pointer; opacity: 1; }
        #lacom { cursor: pointer; opacity: 0.5; margin-right: 6px; }
        #lacom:hover { cursor: pointer; opacity: 1; margin-right: 6px; }
        .scarfs__list { text-align:left;margin-left:60px; }
        .stat-after { padding: 6px; text-align: left; min-height: 200px; width: 788px; background-color: #f5fcef; border-radius: 6px; margin-top: 6px; margin-bottom: 6px; }
        .sl8 { padding: 1px; height: 12px; background-color: #549b1a; float:left; overflow: hidden; color: #dfebd6; font-size: 8px; }
        table.stat-af { margin-top: 2px; margin-bottom: 10px; }
        .hrp { background-image: url("/images/bgs/history.jpg"); background-color: #f7f7f7; border-top: 2px solid #fff; padding: 8px; }
        #gamecomfiled { font-size: 10px; background-image: url("/images/bgs/g4/nocomp.jpg"); border: 1px solid #cecece; border-radius: 3px; max-height:400px; min-height: 400px; overflow: auto; max-width: 800px; }
        #gamecomfiled2 { font-size: 10px;  border: 1px solid #cecece; border-radius: 3px; max-height:400px; min-height: 400px; overflow: auto; max-width: 800px; }
        .commhead { text-align:left; }
        div.ttxlist div { margin-bottom:5px; }
        .nochange { text-align: center; padding: 10px; background-color: #f2fff0; font-size:10px; color:#676767; }
        #cont-goals-1 { text-align: left; margin-top: 10px; }
        #cont-goals-2 { text-align: right; margin-top: 10px;  }
        .top11 { margin-top: 15px; font-size:10px; }
        .tvis { margin-top: 14px; font-size: 10px; text-align: center; }
        button.load { margin-top: 4px; margin-bottom: 4px;font-size: 11px; width: 100%; }
        .stack { float: left; width: 260px; overflow: hidden; margin-right: 10px; }
        .logs4 { float:left; width:400px; overflow:hidden; margin-right: 10px; }
        .strike { text-decoration: line-through; opacity: 0.5; }
        #input-comment2 { width: 710px; height: 54px; margin-bottom: 5px; }
        .com8 { padding: 6px; text-align: left; max-width: 790px; }
        .mava { width: 60px; height: 60px; }
        div.stat_ct { border: 1px solid #ccc; padding: 2px; display: flex; }
        .stat_ct div { margin: 0; text-align: center; color: #fdfdfd; font-size: 10px; padding: 2px; }
        .ms-1 { background-color: red; }
        .ms0 { background-color: #4c97bb; }
        .ms1 {  background-color: #77bd3f; }
        #nComm_user { width: 175px; margin-top: 3px; margin-bottom: 3px; height: 60px; }
        .fullcomm { border-bottom: 1px solid #e9e9e9; }
        .pl_logo { float:left; margin:3px; color:#fff; }
        img.pl_photo { width: 80px; height: 80px; }
        .head_form { font-size: 140%; color: #fff; margin-top: 5px; }
        .pots_ind { display: flex;  justify-content: space-around; cursor: help; }
        table.ttx_change td:nth-child(2) { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 10px; }
        tr.pl_line_suspect td { background-color: #f5bcbc; }
        .w14 { width: 14px; }
        div.tab-a, div.tab-p { width: 130px !IMPORTANT; }
        #g5_bug {margin-top: 20px; width: 100%; display: flex; flex-direction: column;}
        #g5_bug button {background-color: #bbb; height: 30px; border: none; cursor: pointer; transition: .3s;}
        #g5_bug button:hover {color: white; background-color: #777;}
        #g5_bug input {padding: 5px;}
        .coach_name { font-size: 140%; white-space: nowrap; max-width: 200px; overflow: hidden; text-overflow: ellipsis; }
        .noise_button { opacity: 0.5; cursor: pointer; }
        .noise_button:hover { opacity: 1; }

        /* таймлайн матча*/
        .game__pre_timeline { margin-top: 25px; display: flex; justify-content: space-between; margin-bottom: 2px; }
        .game__pre_timeline div { color: #bfc2c5; font-size: 8px; }
        .game__timeline { display: grid; grid-auto-flow: column; grid-gap: 1px; position: relative; top: -20px; }
        .game__timeline_item { background-color: #bfc2c5; height: 8px; display: flex; justify-content: flex-start; }
        .game_timeline_active { background-color: #478317; height: 8px; }
        .game__timeline_minute { float: right;  background-color: red; width: 11px; height: 11px; border-radius: 50%; position: relative; left: 7px; top: -1px; -webkit-animation: live 2s linear infinite; animation: live 2s linear infinite; }
        .game__timeline_minute::after { content: attr(data-minute); color: #000; position: relative; top: -15px; font-size: 9px; font-variant: small-caps; }
        ul.game__timeline_events { list-style: none; }
        ul.game__timeline_events li { display: block; white-space: nowrap; position: absolute; z-index: 2; }
        ul.game__timeline_events li[data-side="1"] .event-type { top: -27px; left: -5px; }
        ul.game__timeline_events li[data-side="2"] .event-type { top: 0; }
        .event-type { position: absolute; }
        .event-type__goal { width: 14px; height: 14px; background: url("/images/ico/goal.gif") center center no-repeat; }
        .event-type__yellow { width: 9px; height: 12px; background: url("/images/gen4/yc.png") center center no-repeat; background-size: cover; }
        .event-type__red { width: 9px; height: 12px; background: url("/images/gen4/rc.png") center center no-repeat; }
        .event-type__yellow_red { width: 9px; height: 12px; background: url("/images/gen4/yrc.png") center center no-repeat; }
        .event-type__injury { width: 16px; height: 16px; background: url("/images/gen4/injury.png") center center no-repeat; }
        .event-type__change { width: 16px; height: 16px; background: url("/images/gen4/changes.png") center center no-repeat; }
        .event-minute {
            right: auto; left: 50%; margin-top: 0; height: 16px; vertical-align: middle; line-height: 16px;
            color: #fff;
            font-size: 7px;
            font-variant: small-caps;
            text-align: center;
            display: inline-block;
            position: absolute;
            top: -16px;
            pointer-events: none;
        }
        @keyframes live {
            0% { background-color: red; }
            33% { background-color: red; }
            66% { background-color: pink; }
            100% { background-color: red; }
        }

        /* текстовая трансляция */
        table.game_comments { border-collapse: separate; border-spacing: 0; width: 99%; }
        table.game_comments td { padding: 5px; }
        table.game_comments td:nth-child(1){ color: #bd2828; vertical-align: top; }
        table.game_comments td:nth-child(2){ text-align: left; }
        table.game_comments td img.__ico { vertical-align: middle; margin-right: 5px; }
        table.game_comments .__logo { float: left; margin: 3px 3px 3px 0; }
        table.game_comments tr:nth-child(n+2) .__logo { display: none; }
        table.game_comments tr:nth-child(1) td { text-align: left; font-size: 15px; vertical-align: top; }
        table.game_comments tr:nth-child(1) b { font-weight: normal; }
        table.game_comments tr.event_12 td, tr.event_15 td, tr.event_19 td { background-color: #E1FECF; }
        table.game_comments tr.event_-11 { background-color: #ffd2e0; }
        table.game_comments tr.event_-7 { background-color: #ffffe1; }
        table.game_comments tr.event_-9, tr.event_-19 { background-color: #fdded7; }
    </style>';

    // Вывод информации о турнире для трансляции
    switch ($arr['chemp']) {
        case "cup_en":
        case "cup_ru":
        case "cup_de":
        case "cup_pt":
        case "cup_es":
        case "cup_it":
        case "cup_fr":
        case "cup_nl":
            $act_map = [
                "cup_en" => "en",
                "cup_ru" => "ru",
                "cup_de" => "de",
                "cup_pt" => "pt",
                "cup_es" => "es",
                "cup_it" => "it",
                "cup_fr" => "fr",
                "cup_nl" => "nl"
            ];
            $act = $act_map[$arr['chemp']];
            echo '<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
            echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act='.$act.'">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
            echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
            echo '<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
            break;

        case "cup_netto":
        case "cup_charlton":
        case "cup_muller":
        case "cup_puskas":
        case "cup_fachetti":
        case "cup_kopa":
        case "cup_distefano":
        case "cup_garrinca":
            $act_map = [
                "cup_netto" => "netto",
                "cup_charlton" => "charlton",
                "cup_muller" => "muller",
                "cup_puskas" => "puskas",
                "cup_fachetti" => "fachetti",
                "cup_kopa" => "kopa",
                "cup_distefano" => "distefano",
                "cup_garrinca" => "garrinca"
            ];
            $act = $act_map[$arr['chemp']];
            echo '<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
            echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act='.$act.'">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
            echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
            echo '<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
            break;

        case "frend":
            echo '<a href="/friendly/" class="cardview x-pt-3 x-block-center x-rounded x-bg-cover x-onhover-wrapper" style="background-image: url(/images/cup/friendly.png);width: 75px;height: 75px;overflow: visible;" title="Перейти в кубок"></a>';
            echo '<div class="gmenu"><center><a href="/friendly/"><b>Товарищеский матч</b></a></center> </div>';
            break;

        case "maradona":
            echo '<link rel="stylesheet" href="/theme/cups/maradona.css" type="text/css" />';
            echo '<div class="gmenu"><center><a href="/' . $arr['id_kubok'] . '"><b></b></a></center> </div>';
            echo '<div xmlns="http://www.w3.org/1999/xhtml" class="phdr_lk">'.$arr['kubok_nomi'].'</div>';
            echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/cup/b_maradona.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
            break;

        case "unchamp":
            echo '<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />';
            echo '<div class="phdr_lk"><font color="white"><a href="/' . $arr['id_kubok'] . '">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
            echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
            echo '<img src="/union/logo/cup' . $arr['id_kubok'] . '.jpg" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
            break;

        case "champ":
            echo '<div class="phdr">Чемпионат<b class="rlink">'.date("d.m.Y H:i", $arr['time']).'</b></div>';
            echo '<div class="gmenu"><center><a href="/champ00/index.php?act=' . $arr['kubok'] . '"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';
            echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
            echo '<img src="/images/cup/b_00' . $arr['kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
            break;

        case "champ_retro":
            echo '<div class="phdr">Чемпионат<b class="rlink">'.date("d.m.Y H:i", $arr['time']).'</b></div>';
            echo '<div class="gmenu"><center><a href="/champ/index.php?act=' . $arr['id_kubok'] . '"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';
            break;

        case "cup":
            echo '<div class="phdr"><a href="/cup/' . $arr['id_kubok'] . '">'.$c_name.'</a><b class="rlink">'.date("d.m.Y H:i", $arr['time']).'</b></div>';
            break;

        case "brend":
            echo '<div class="phdr"><a href="/brendcup/' . $arr['id_kubok'] . '">'.$c_name.'</a><b class="rlink">'.date("d.m.Y H:i", $arr['time']).'</b></div>';
            break;

        case "liberta":
            echo '<link rel="stylesheet" href="/theme/cups/liberta.css" type="text/css" />';
            echo '<div class="phdr_le"><font color="white"><a href="/' . $arr['id_kubok'] . '/"><b>'.$arr['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
            break;

        case "liga_r":
        case "liga_r2":
            echo '<link rel="stylesheet" href="/theme/cups/lc.css" type="text/css" />';
            if ($arr['chemp'] == "liga_r") {
                echo '<div class="phdr_lc"><font color="white"><a href="/' . $arr['id_kubok'] . '/"><b>'.$arr['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
            } else {
                echo '<div class="phdr_lc"><center><a href="/' . $arr['id_kubok'] . '/"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';
            }
            echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
            echo '<img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
            break;

        case "kuefa2":
        case "le":
            echo '<link rel="stylesheet" href="/theme/cups/le.css" type="text/css" />';
            echo '<div class="phdr_le"><font color="white"><a href="/' . $arr['id_kubok'] . '/"><b>'.$arr['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
            echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
            echo '<img src="/images/cup/b_le.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
            break;

        case "super_cup":
        case "super_cup2":
            $link = ($arr['chemp'] == "super_cup") ? "/super_cup/" : "/super_cup2/";
            echo '<link rel="stylesheet" href="/theme/cups/super_cup.css" type="text/css" />';
            echo '<div class="phdr_le"><font color="white"><a href="'.$link.'"><b>'.$arr['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
            echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
            echo '<img src="/images/cup/b_super_cup.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
            break;

        case "lk":
            echo '<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />';
            echo '<div class="phdr_lk"><font color="white">'.$arr['kubok_nomi'].'</font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
            echo '<div class="phdr_lk"><center><a href="/' . $arr['id_kubok'] . '"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';
            echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
            echo '<img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
            break;

        default:
            echo '<div class="phdr">Матч<b class="rlink">'.date("d.m.Y H:i", $arr['time']).'</b></div>';
            echo '<div class="gmenu"><center><a href="/cup/' . $arr['id_kubok'] . '"><b>'.$c_name.'</b></a></center> </div>';
            break;
    }

    // Судейская информация
    $q1auy = mysql_query("SELECT * FROM `r_judge` WHERE `id` = '" . (int)$arr['judge'] . "' LIMIT 1");
    $aayr = mysql_fetch_assoc($q1auy);

    // Автообновление если матч в процессе
    if($mt <= 93): ?>
    <script>
    $(document).ready(function(){
        setInterval(function(){
            $("#display1").load(window.location.href + " #display1");
        }, 5000);
    });
    </script>
    <?php endif; ?>

    <div id="display1">
        <div class="gmenu">
            <table id="pallet">
                <tr>
                    <td width="47%">
                        <center>
                            <?php if (!empty($team1['logo'])): ?>
                                <a href="/team/<?=$team1['id']?>">
                                    <img src="/manager/logo/big<?=$team1['logo']?>" alt="">
                                </a>
                            <?php else: ?>
                                <a href="/team/<?=$team1['id']?>">
                                    <img src="/manager/logo/b_0.jpg" alt="" width="37">
                                </a>
                            <?php endif; ?>

                            <div>
                                <a href="/team/<?=$team1['id']?>">
                                    <span class="flags c_<?=$team1['flag']?>_14" style="vertical-align: middle;"></span>
                                    <?=$team1['name']?>
                                </a><br>
                                <?php if($team1['id_admin'] > 0):
                                    $us1 = mysql_query("SELECT * FROM `users` WHERE `id` = " . (int)$team1['id_admin'] . " LIMIT 1");
                                    $uss1 = mysql_fetch_assoc($us1);
                                    $vip_icons = array(
                                        0 => '/images/ico/vip0_m.png',
                                        1 => '/images/ico/vip1_m.png',
                                        2 => '/images/ico/vip2_m.png',
                                        3 => '/images/ico/vip3_m.png'
                                    );
                                    $vip_titles = array(
                                        0 => 'Базовый аккаунт',
                                        1 => 'Улучшенный Премиум-аккаунт',
                                        2 => 'Улучшенный VIP-аккаунт',
                                        3 => 'Представительский Gold-аккаунт'
                                    );
                                    ?>
                                    <span style="opacity:0.4">
                                        <img src="<?=$vip_icons[$uss1['vip']]?>" 
                                             title="<?=$vip_titles[$uss1['vip']]?>" 
                                             style="width: 12px; border: none; vertical-align: middle;">
                                        <?=$uss1['name']?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </center>
                    </td>
                    
                    <td width="6%">
                        <center>
                            <?php
                            // Расчет текущего счета
                            $goal1 = 0;
                            $goal2 = 0;
                            if (!empty($arr['text'])) {
                                $text = explode("\r\n", $arr['text']);
                                arsort($text);
                                
                                foreach ($text as $val) {
                                    $menu = explode("|", $val);
                                    $minute = isset($menu[0]) ? intval($menu[0]) : 0;
                                    $event = isset($menu[1]) ? $menu[1] : '';
                                    
                                    if ($minute > 0 && $mt > $minute) {
                                        if ($event == 'goal1' || $event == 'goal1_pen') $goal1++;
                                        if ($event == 'goal2' || $event == 'goal2_pen') $goal2++;
                                    }
                                }
                            }
                            ?>
                            <b><font size="+4"><?=$goal1?>:<?=$goal2?></font></b>
                            <div>
                                <?php
                                if($mt < 0) $smt = 'Матч еще не начался';
                                if($mt >= 0 && $mt <= 92) {
                                    $smt = "<font size='+1.5'><b>$mt минута</b></font>";
                                    echo '<img src="/images/ico/flash.gif" class="va" alt="минута">';
                                }
                                if($mt > 92) $smt = "<font size='-1.5'><b>Матч завершен</b></font>";
                                echo "<font size='+1'>$smt</font>";
                                ?>
                            </div>
                        </center>
                    </td>
                    
                    <td width="47%">
                        <center>
                            <?php if (!empty($team2['logo'])): ?>
                                <a href="/team/<?=$team2['id']?>">
                                    <img src="/manager/logo/big<?=$team2['logo']?>" alt="">
                                </a>
                            <?php else: ?>
                                <a href="/team/<?=$team2['id']?>">
                                    <img src="/manager/logo/b_0.jpg" alt="" width="37px">
                                </a>
                            <?php endif; ?>

                            <div>
                                <a href="/team/<?=$team2['id']?>"><?=$team2['name']?></a>
                                <span class="flags c_<?=$team2['flag']?>_14" style="vertical-align: middle;"></span><br>
                                <?php if($team2['id_admin'] > 0):
                                    $us2 = mysql_query("SELECT * FROM `users` WHERE `id` = " . (int)$team2['id_admin'] . " LIMIT 1");
                                    $uss2 = mysql_fetch_assoc($us2);
                                    ?>
                                    <span style="opacity:0.4">
                                        <img src="<?=$vip_icons[$uss2['vip']]?>" 
                                             title="<?=$vip_titles[$uss2['vip']]?>" 
                                             style="width: 12px; border: none; vertical-align: middle;">
                                        <?=$uss2['name']?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </center>
                    </td>
                </tr>
            </table>

            <div class="game22">
                <div>
                    <b><img src="/images/gen4/whistle.png" class="va" alt=""> Главный арбитр матча</b>
                </div>
                <div>
                    <a href="/judge/index.php?id=<?=$aayr['id']?>">
                        <span class="flags c_<?=$aayr['flag']?>_18" style="vertical-align: middle;"></span>
                        <?=$aayr['name']?>
                    </a>
                </div>
            </div>

            <?php if($mt > 92): ?>
                <div class="cardview-wrapper" bis_skin_checked="1">
                    <a class="cardview" href="?mod=report&id=<?=$id?>&union=<?=$union?>">
                        <div class="left px50" bis_skin_checked="1">
                            <i class="font-icon font-icon-whistle"></i>
                        </div>
                        <div class="right px50 arrow" bis_skin_checked="1">
                            <div class="text" bis_skin_checked="1">Посмотреть отчёт</div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <?php
            // Стадион
            $std11 = array();
            if($arr['id_stadium']) {
                $std_res = mysql_query("SELECT * FROM `r_stadium` WHERE `id` = '" . (int)$arr['id_stadium'] . "'");
                $std11 = mysql_fetch_assoc($std_res);
            }
            ?>
            
            <?php if($arr['id_stadium']): ?>
                <div class="gmenu">
                    <center>
                        <img width="50%" src="/images/stadium/<?=$std11['std'] ? $arr['id_stadium'] : 'stadium'?>.jpg" 
                             alt="<?=htmlspecialchars($std11['name'])?>">
                    </center>
                </div>
            <?php else: ?>
                <div class="gmenu">
                    <center>
                        <img src="/images/stadium/stadium.jpg" alt="">
                    </center>
                </div>
            <?php endif; ?>

            <?php if($arr['teh_end'] == 0): ?>
                <div id="gamecomfiled2" style="height: 400px; min-height: 400px;">
                    <table>
                        <?php
                        $text = explode("\r\n", $arr['text']);
                        $text = array_reverse($text);
                        
                        foreach($text as $val) {
                            $menu = explode("|", $val);
                            if(count($menu) < 4) continue;
                            
                            if($mt >= intval($menu[0]) && intval($menu[0]) > 0):
                                $til_txt = $menu[2];
                                echo '<tr>
                                    <td width="5%">
                                        '.(!empty($menu[1]) ? '<img src="/images/txt/m_'.$menu[1].'.gif" alt="">' : '').'
                                    </td>
                                    <td width="5%"><b>'.intval($menu[0]).'\'</b></td>
                                    <td>'.$til_txt.'</td>
                                </tr>';
                            endif;
                        }
                        ?>
                    </table>
                </div>
            <?php else: ?>
                <div id="gamecomfiled2" style="height: 400px; min-height: 400px;">
                    <div class="error" style="text-align: left">
                        Техническое поражение одной из команд. Тех-поражение выдают в тех случаях, 
                        когда на матч одной из команд вышло меньше семи игроков!
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php
    // Обновление статистики после завершения матча
    if ($mt > 92 && $arr['step'] == '0') {
        // Получение данных о матче
        $g = mysql_query("SELECT * FROM `r" . $prefix . "game` WHERE id = '" . $id . "' LIMIT 1");
        $game = mysql_fetch_array($g);
        
        // Получение данных команд
        $q1 = mysql_query("SELECT * FROM `r_team` WHERE id = '" . $game['id_team1'] . "' LIMIT 1");
        $arr1 = mysql_fetch_array($q1);
        
        $q2 = mysql_query("SELECT * FROM `r_team` WHERE id = '" . $game['id_team2'] . "' LIMIT 1");
        $arr2 = mysql_fetch_array($q2);
        
        $rezult = array($goal1, $goal2);
        $pen1 = $pen2 = 0;

        // Обработка пенальти при ничье
        if ($rezult[0] == $rezult[1]) {
            $penalties = array(
                "11:10", "10:9", "8:7", "7:6", "6:5", "5:3", "5:4", "4:2", "4:3", "3:2",
                "3:5", "4:5", "2:4", "3:4", "2:3", "10:11", "9:10", "7:8", "6:7", "5:6"
            );
            $selected = explode(":", $penalties[array_rand($penalties)]);
            list($pen1, $pen2) = $selected;
        }

        // Обновление счета матча
        mysql_query("UPDATE `r" . $prefix . "game` 
            SET `rez1` = '" . $rezult[0] . "', 
                `rez2` = '" . $rezult[1] . "', 
                `pen1` = '" . $pen1 . "', 
                `pen2` = '" . $pen2 . "' 
            WHERE id = '" . $game['id'] . "' 
            LIMIT 1");

        // Обработка статистики команд
        $h_zwm_1 = explode("|", $arr1['zad_win_match']);
        $h_zwm_2 = explode("|", $arr2['zad_win_match']);
        $p_zwm_1 = (isset($h_zwm_1[1]) ? $h_zwm_1[1] : 0);
        $p_zwm_2 = (isset($h_zwm_2[1]) ? $h_zwm_2[1] : 0);
        
        // Команда 1
        if ($rezult[0] > $rezult[1]) {
            $oputman1 = $arr1['oput'] + 1;
            $m1 = round($game['zritel'] * 0.01);
            $moneyn1 = $arr1['money'] + $m1;
            $winman1 = $arr1['win'] + 1;
            
            mysql_query("UPDATE `r_team` 
                SET `zad_win_match` = '" . $h_zwm_1[0] . "|" . ($p_zwm_1 + 1) . "', 
                    `oput` = '" . $oputman1 . "', 
                    `money` = '" . $moneyn1 . "', 
                    `win` = '" . $winman1 . "' 
                WHERE id = '" . $arr1['id'] . "' 
                LIMIT 1");
            
            // Команда 2
            $losman2 = $arr2['los'] + 1;
            mysql_query("UPDATE `r_team` SET `los` = '" . $losman2 . "' WHERE id = '" . $arr2['id'] . "' LIMIT 1");
        } 
        elseif ($rezult[0] == $rezult[1]) {
            $m1 = round($game['zritel'] * 0.005);
            $moneyn1 = $arr1['money'] + $m1;
            $nnman1 = $arr1['nn'] + 1;
            
            mysql_query("UPDATE `r_team` 
                SET `money` = '" . $moneyn1 . "', 
                    `nn` = '" . $nnman1 . "' 
                WHERE id = '" . $arr1['id'] . "' 
                LIMIT 1");
            
            // Команда 2
            $m2 = round($game['zritel'] * 0.005);
            $moneyn2 = $arr2['money'] + $m2;
            $nnman2 = $arr2['nn'] + 1;
            
            mysql_query("UPDATE `r_team` 
                SET `money` = '" . $moneyn2 . "', 
                    `nn` = '" . $nnman2 . "' 
                WHERE id = '" . $arr2['id'] . "' 
                LIMIT 1");
        } 
        else {
            $losman1 = $arr1['los'] + 1;
            mysql_query("UPDATE `r_team` SET `los` = '" . $losman1 . "' WHERE id = '" . $arr1['id'] . "' LIMIT 1");
            
            // Команда 2
            $oputman2 = $arr2['oput'] + 1;
            $m2 = round($game['zritel'] * 0.01);
            $moneyn2 = $arr2['money'] + $m2;
            $winman2 = $arr2['win'] + 1;
            
            mysql_query("UPDATE `r_team` 
                SET `zad_win_match` = '" . $h_zwm_2[0] . "|" . ($p_zwm_2 + 1) . "', 
                    `oput` = '" . $oputman2 . "', 
                    `money` = '" . $moneyn2 . "', 
                    `win` = '" . $winman2 . "' 
                WHERE id = '" . $arr2['id'] . "' 
                LIMIT 1");
        }

        // Обработка рефери
        if ($arr1['ref'] > 0) {
            $new_ref1 = $arr1['ref'] - 1;
            mysql_query("UPDATE `r_team` SET `ref` = '$new_ref1' WHERE id = '" . $arr1['id'] . "'");
        }
        if ($arr2['ref'] > 0) {
            $new_ref2 = $arr2['ref'] - 1;
            mysql_query("UPDATE `r_team` SET `ref` = '$new_ref2' WHERE id = '" . $arr2['id'] . "'");
        }
        
        // Финализация матча
        mysql_query("UPDATE `r" . $prefix . "game` SET `step` = '1' WHERE id = '" . $game['id'] . "' LIMIT 1");
        
        // Обновление статистики судьи
        $q_judge = mysql_query("SELECT * FROM `r_judge` WHERE `id` = '" . $game['judge'] . "' LIMIT 1");
        $judge_data = mysql_fetch_array($q_judge);
        $new_games = $judge_data['game'] + 1;
        mysql_query("UPDATE `r_judge` SET `game` = '" . $new_games . "' WHERE id = '" . $game['judge'] . "' LIMIT 1");
    }
}

// Завершение страницы
require_once("../incfiles/end.php");
?>
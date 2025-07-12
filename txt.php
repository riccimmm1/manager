<style>
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
</style>

<?php
define('_IN_JOHNCMS', 1);
$headmod = 'txt';
require_once("../incfiles/core.php");

// Безопасное получение параметров
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_union = isset($_GET['union']);
$prefix = $is_union ? '_union_' : '_';
$dirs = $is_union ? '/union/' : '/';

// Получаем данные матча
$query = "SELECT * FROM `r{$prefix}game` WHERE id = '{$id}' LIMIT 1";
$result = mysql_query($query);
$match = mysql_fetch_assoc($result);

// Валидация данных
if (empty($match['id'])) {
    echo display_error('Отчёт не найден');
    require_once("../incfiles/end.php");
    exit;
}

// Проверка тактик
if (empty($match['tactics1']) || empty($match['tactics2'])) {
    header("Location: /game{$dirs}{$id}");
    exit;
}

// Устанавливаем заголовок страницы
$textl = htmlspecialchars(
    $match['name_team1'] . ' - ' . $match['name_team2'] . ' ' . 
    $match['rez1'] . ':' . $match['rez2']
);
require_once("../incfiles/head.php");

// Рассчитываем минуту матча
$mt = floor((($realtime - $match['time']) * 18) / 60);

// Подсчет голов
$goals = ['1' => 0, '2' => 0];
if (!empty($match['text'])) {
    $events = explode("\r\n", $match['text']);
    arsort($events);
    
    foreach ($events as $event) {
        $parts = explode("|", $event);
        $minute = (int)$parts[0];
        
        if ($mt > $minute && $minute > 0) {
            if (strpos($parts[1], 'goal1') === 0) $goals['1']++;
            elseif (strpos($parts[1], 'goal2') === 0) $goals['2']++;
        }
    }
}

// Получаем данные команд (один запрос вместо двух)
$team_ids = [(int)$match['id_team1'], (int)$match['id_team2']];
$query = "SELECT * FROM `r_team` WHERE id IN (" . implode(',', $team_ids) . ")";
$result = mysql_query($query);

$teams = [];
while ($row = mysql_fetch_assoc($result)) {
    $teams[$row['id']] = $row;
}

// Определяем название турнира
$cup_names = [
    '1' => isset($c_1) ? $c_1 : '', '2' => isset($c_2) ? $c_2 : '', '3' => isset($c_3) ? $c_3 : '', '4' => isset($c_4) ? $c_4 : '',
    '5' => isset($c_5) ? $c_5 : '', '6' => isset($c_6) ? $c_6 : '', '7' => isset($c_7) ? $c_7 : '', '8' => isset($c_8) ? $c_8 : '',
    '9' => isset($c_9) ? $c_9 : '', '10' => isset($c_10) ? $c_10 : '', '11' => isset($c_11) ? $c_11 : '', '12' => isset($c_12) ? $c_12 : '',
    '13' => isset($c_13) ? $c_13 : '', '14' => isset($c_14) ? $c_14 : '', '15' => isset($c_15) ? $c_15 : '', '16' => isset($c_16) ? $c_16 : '',
    '17' => isset($c_17) ? $c_17 : '', '18' => isset($c_18) ? $c_18 : '', '19' => isset($c_19) ? $c_19 : '', '20' => isset($c_20) ? $c_20 : '',
    '21' => isset($c_21) ? $c_21 : '', '22' => isset($c_22) ? $c_22 : '', '23' => isset($c_23) ? $c_23 : '', '24' => isset($c_24) ? $c_24 : '',
    '25' => isset($c_25) ? $c_25 : '', '26' => isset($c_26) ? $c_26 : '', '27' => isset($c_27) ? $c_27 : '', '28' => isset($c_28) ? $c_28 : '',
    '29' => isset($c_29) ? $c_29 : '', '30' => isset($c_30) ? $c_30 : '', '31' => isset($c_31) ? $c_31 : '', '32' => isset($c_32) ? $c_32 : '',
    '33' => isset($c_33) ? $c_33 : '', '34' => isset($c_34) ? $c_34 : '', '35' => isset($c_35) ? $c_35 : '', '36' => isset($c_36) ? $c_36 : '',
    '37' => isset($c_37) ? $c_37 : '', '38' => isset($c_38) ? $c_38 : '', '39' => isset($c_39) ? $c_39 : '', '40' => isset($c_40) ? $c_40 : '',
    '41' => isset($c_41) ? $c_41 : '', '42' => isset($c_42) ? $c_42 : '', '43' => isset($c_43) ? $c_43 : '', '44' => isset($c_44) ? $c_44 : '',
    '45' => isset($c_45) ? $c_45 : '', '46' => isset($c_46) ? $c_46 : '', '47' => isset($c_47) ? $c_47 : '', '48' => isset($c_48) ? $c_48 : '',
    '49' => isset($c_49) ? $c_49 : '', '50' => isset($c_50) ? $c_50 : '', '60' => isset($c_60) ? $c_60 : '', '61' => isset($c_61) ? $c_61 : '',
    '62' => isset($c_62) ? $c_62 : '', '63' => isset($c_63) ? $c_63 : '', '64' => isset($c_64) ? $c_64 : '', '65' => isset($c_65) ? $c_65 : '',
    '66' => isset($c_66) ? $c_66 : '', '67' => isset($c_67) ? $c_67 : '', '68' => isset($c_68) ? $c_68 : '', '69' => isset($c_69) ? $c_69 : '',
    '70' => isset($c_70) ? $c_70 : '', '71' => isset($c_71) ? $c_71 : '', '72' => isset($c_72) ? $c_72 : '', '73' => isset($c_73) ? $c_73 : '',
    '74' => isset($c_74) ? $c_74 : '', '75' => isset($c_75) ? $c_75 : '', '76' => isset($c_76) ? $c_76 : '', '77' => isset($c_77) ? $c_77 : '',
    '78' => isset($c_78) ? $c_78 : '', '79' => isset($c_79) ? $c_79 : '', '80' => isset($c_80) ? $c_80 : '', '81' => isset($c_81) ? $c_81 : '',
    '82' => isset($c_82) ? $c_82 : '', '83' => isset($c_83) ? $c_83 : '', '84' => isset($c_84) ? $c_84 : '', '85' => isset($c_85) ? $c_85 : '',
    '86' => isset($c_86) ? $c_86 : '', '87' => isset($c_87) ? $c_87 : '', '88' => isset($c_88) ? $c_88 : '', '89' => isset($c_89) ? $c_89 : '',
    '90' => isset($c_90) ? $c_90 : '', '91' => isset($c_91) ? $c_91 : '', '92' => isset($c_92) ? $c_92 : '', '93' => isset($c_93) ? $c_93 : '',
    '94' => isset($c_94) ? $c_94 : '', '95' => isset($c_95) ? $c_95 : '', '96' => isset($c_96) ? $c_96 : '', '97' => isset($c_97) ? $c_97 : '',
    '98' => isset($c_98) ? $c_98 : '', '99' => isset($c_99) ? $c_99 : '', '100' => isset($c_100) ? $c_100 : '', '101' => isset($c_101) ? $c_101 : '',
    '102' => isset($c_102) ? $c_102 : '', '103' => isset($c_103) ? $c_103 : '', '104' => isset($c_104) ? $c_104 : '', '105' => isset($c_105) ? $c_105 : '',
    '106' => isset($c_106) ? $c_106 : '', '107' => isset($c_107) ? $c_107 : '', '108' => isset($c_108) ? $c_108 : '', '109' => isset($c_109) ? $c_109 : '',
    '150' => isset($c_150) ? $c_150 : '', '151' => isset($c_151) ? $c_151 : '', '152' => isset($c_152) ? $c_152 : '', '153' => isset($c_153) ? $c_153 : '',
    '154' => isset($c_154) ? $c_154 : '', '155' => isset($c_155) ? $c_155 : '', '156' => isset($c_156) ? $c_156 : '', '157' => isset($c_157) ? $c_157 : '',
    '158' => isset($c_158) ? $c_158 : '', '159' => isset($c_159) ? $c_159 : '', '160' => isset($c_160) ? $c_160 : '',
    'cup_netto' => 'Кубок Нетто',
    'cup_charlton' => 'Кубок Чарльтона',
    'cup_en' => 'Кубок Англии',
    'cup_muller' => 'Кубок Мюллера',
    'cup_puskas' => 'Кубок Пушкаша',
    'cup_fachetti' => 'Кубок Факкетти',
    'cup_kopa' => 'Кубок Копа',
    'cup_distefano' => 'Кубок Ди Стефано'
];

$tournament_name = isset($cup_names[$match['kubok']]) ? $cup_names[$match['kubok']] : '';

// Рендерим заголовок турнира
switch ($match['chemp']) {
    case "cup_en":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=en">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_ru":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=ru">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_de":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=de">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_pt":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=pt">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_es":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=es">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_it":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=it">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_fr":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=fr">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_nl":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=nl">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_netto":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=netto">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_charlton":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=charlton">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_muller":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=muller">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_puskas":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=puskas">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_fachetti":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=fachetti">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_kopa":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=kopa">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_distefano":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=distefano">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "cup_garrinca":
        echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=garrinca">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_' . $match['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "frend":
        echo'<a href="/friendly/" class="cardview x-pt-3 x-block-center x-rounded x-bg-cover x-onhover-wrapper" style="background-image: url(/images/cup/friendly.png);width: 75px;height: 75px;overflow: visible;" title="Перейти в кубок"></a>';
        echo '<div class="gmenu"><center><a href="/friendly/"><b>Товарищеский матч</b></a></center> </div>';
        break;

    case "maradona":
        echo'<link rel="stylesheet" href="/theme/cups/maradona.css" type="text/css" />';
        echo '<div class="gmenu"><center><a href="/' . $match['id_kubok'] . '"><b></b></a></center> </div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="phdr_lk">'.$match['kubok_nomi'].'</div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/cup/b_maradona.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "unchamp":
        echo'<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />';
        echo '<div class="phdr_lk"><font color="white"><a href="/' . $match['id_kubok'] . '">'.$match['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/union/logo/cup' . $match['id_kubok'] . '.jpg" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "champ":
        echo '<div class="phdr">Чемпионат<b class="rlink">'.date("d.m.Y H:i", $match['time']).'</b></div>';
        echo '<div class="gmenu"><center><a href="/champ00/index.php?act=' . $match['kubok'] . '"><b>'.$match['kubok_nomi'].'</b></a></center> </div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_00' . $match['kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "champ_retro":
        echo '<div class="phdr">Чемпионат<b class="rlink">'.date("d.m.Y H:i", $match['time']).'</b></div>';
        echo '<div class="gmenu"><center><a href="/champ/index.php?act=' . $match['id_kubok'] . '"><b>'.$match['kubok_nomi'].'</b></a></center> </div>';
        break;

    case "cup":
        echo '<div class="phdr"><a href="/cup/' . $match['id_kubok'] . '">'.$tournament_name.'</a><b class="rlink">'.date("d.m.Y H:i", $match['time']).'</b></div>';
        break;

    case "brend":
        echo '<div class="phdr"><a href="/brendcup/' . $match['id_kubok'] . '">'.$tournament_name.'</a><b class="rlink">'.date("d.m.Y H:i", $match['time']).'</b></div>';
        break;

    case "liberta":
        echo'<link rel="stylesheet" href="/theme/cups/liberta.css" type="text/css" />';
        echo '<div class="phdr_le"><font color="white"><a href="/' . $match['id_kubok'] . '/"><b>'.$match['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        break;

    case "liga_r":
        echo'<link rel="stylesheet" href="/theme/cups/lc.css" type="text/css" />';
        echo '<div class="phdr_lc"><font color="white"><a href="/' . $match['id_kubok'] . '/"><b>'.$match['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "liga_r2":
        echo'<link rel="stylesheet" href="/theme/cups/lc.css" type="text/css" />';
        echo '<div class="phdr_lc"><center>
        <a href="/' . $match['id_kubok'] . '/"><b>'.$match['kubok_nomi'].'</b></a></center> </div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "kuefa2":
        echo'<link rel="stylesheet" href="/theme/cups/le.css" type="text/css" />';
        echo '<div class="phdr_le"><font color="white"><a href="/' . $match['id_kubok'] . '/"><b>'.$match['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_le.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "le":
        echo'<link rel="stylesheet" href="/theme/cups/le.css" type="text/css" />';
        echo '<div class="phdr_le"><font color="white"><a href="/' . $match['id_kubok'] . '/"><b>'.$match['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_le.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "super_cup":
        echo'<link rel="stylesheet" href="/theme/cups/super_cup.css" type="text/css" />';
        echo '<div class="phdr_le"><font color="white"><a href="/super_cup/"><b>'.$match['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_super_cup.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "super_cup2":
        echo'<link rel="stylesheet" href="/theme/cups/super_cup.css" type="text/css" />';
        echo '<div class="phdr_le"><font color="white"><a href="/super_cup2/"><b>'.$match['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/cup/b_super_cup.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    case "lk":
        echo'<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />';
        echo '<div class="phdr_lk"><font color="white">'.$match['kubok_nomi'].'</font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $match['time']).'</b></font></div>';
        echo '<div class="phdr_lk"><center>
        <a href="/' . $match['id_kubok'] . '"><b>'.$match['kubok_nomi'].'</b></a></center> </div>';
        echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
        <img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;

    default:
        echo '<div class="phdr">Матч<b class="rlink">'.date("d.m.Y H:i", $match['time']).'</b></div>';
        echo '<div class="gmenu"><center><a href="/cup/' . $match['id_kubok'] . '"><b>'.$tournament_name.'</b></a></center> </div>';
        break;
}

// Получаем данные судьи
$q1auy = mysql_query("SELECT * FROM `r_judge` WHERE `id`='".$match['judge']."' LIMIT 1");
$aayr = mysql_fetch_array($q1auy);

if ($mt <= 93) {
    ?>
    <script> 
    $(document).ready(function() {
        setInterval(function() {
            $("#display1").load(window.location.href + " #display1");
        }, 5000);
    });
    </script>
    <?php
}

echo '<div id="display1">';
echo '<div class="gmenu">';
echo '<table id="pallet"><tr>';
echo '<td width="47%"><center>';

if (!empty($teams[$match['id_team1']]['logo'])) {
    echo '<a href="/team/' . $teams[$match['id_team1']]['id'] . '"><img src="/manager/logo/big' . $teams[$match['id_team1']]['logo'] . '" alt=""/></a>';
} else {
    echo '<a href="/team/' . $teams[$match['id_team1']]['id'] . '"><img src="/manager/logo/b_0.jpg" alt="" width="37"/></a>';
}

echo '<div><a href="/team/'.$teams[$match['id_team1']]['id'].'"><span class="flags c_'.$teams[$match['id_team1']]['flag'].'_14" style="vertical-align: middle;" title=""></span> '.$teams[$match['id_team1']]['name'].'</a><br>';

if ($teams[$match['id_team1']]['id_admin'] > 0) {
    $us1 = mysql_query("SELECT * FROM `users` WHERE `id`=".$teams[$match['id_team1']]['id_admin']." LIMIT 1");
    $uss1 = mysql_fetch_array($us1);   
    switch ($uss1['vip']) {
        case 0:
            echo '<span style="opacity:0.4"><img src="/images/ico/vip0_m.png" title="Базовый аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss1['name'] . '</span>';
            break;
        case 1:
            echo '<span style="opacity:0.4"><img src="/images/ico/vip1_m.png" title="Улучшенный Премиум-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss1['name'] . '</span>';
            break;
        case 2:
            echo '<span style="opacity:0.4"><img src="/images/ico/vip2_m.png" title="Улучшенный VIP-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss1['name'] . '</span>';
            break;
        case 3:
            echo '<span style="opacity:0.4"><img src="/images/ico/vip3_m.png" title="Представительский Gold-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss1['name'] . '</span>';
            break;
    }
}

echo '</center></td>';
echo '<td width="6%"><center>';
echo '<b><font size="+4">'.$goals['1'].':'.$goals['2'].'</font></b>';

echo '<div>';
if ($mt < 0) {
    $smt = 'Матч еще не начался';
} elseif ($mt >= 0 && $mt <= 92) {
    $smt = "<font size='+1.5'><b>$mt минута</b></font>";
    echo '<img src="/images/ico/flash.gif" class="va" alt="минута">';
} else {
    $smt = '<font size="-1.5"><b>Матч завершен</b></font>';
}

echo "<font size='+1'>".$smt."</font>";
echo '</div>';
echo '</center></td>';

echo '<td width="47%"><center>';
if (!empty($teams[$match['id_team2']]['logo'])) {
    echo '<a href="/team/' . $teams[$match['id_team2']]['id'] . '"><img src="/manager/logo/big' . $teams[$match['id_team2']]['logo'] . '" alt=""/></a>';
} else {
    echo '<a href="/team/' . $teams[$match['id_team2']]['id'] . '"><img src="/manager/logo/b_0.jpg" alt="" width="37px"/></a>';
}

echo '<div><a href="/team/'.$teams[$match['id_team2']]['id'].'">'.$teams[$match['id_team2']]['name'].'</a> <span class="flags c_'.$teams[$match['id_team2']]['flag'].'_14" style="vertical-align: middle;" title=""></span><br>';

if ($teams[$match['id_team2']]['id_admin'] > 0) {
    $us2 = mysql_query("SELECT * FROM `users` WHERE `id`=".$teams[$match['id_team2']]['id_admin']." LIMIT 1");
    $uss2 = mysql_fetch_array($us2);   
    switch ($uss2['vip']) {
        case 0:
            echo '<span style="opacity:0.4"><img src="/images/ico/vip0_m.png" title="Базовый аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss2['name'] . '</span>';
            break;
        case 1:
            echo '<span style="opacity:0.4"><img src="/images/ico/vip1_m.png" title="Улучшенный Премиум-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss2['name'] . '</span>';
            break;
        case 2:
            echo '<span style="opacity:0.4"><img src="/images/ico/vip2_m.png" title="Улучшенный VIP-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss2['name'] . '</span>';
            break;
        case 3:
            echo '<span style="opacity:0.4"><img src="/images/ico/vip3_m.png" title="Представительский Gold-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss2['name'] . '</span>';
            break;
    }
}

echo '</div>';
echo '</center></td>';
echo '</tr></table>';

echo '<div class="game22">
        <div>
            <b><img src="/images/gen4/whistle.png" class="va" alt=""> Главный арбитр матча</b>
        </div>
        <div>
            <a href="/judge/index.php?id='.$aayr['id'].'"><span class="flags c_'.$aayr['flag'].'_18" style="vertical-align: middle;" title="'.$aayr['flag'].'"></span> '.$aayr['name'].'</a>
        </div>
    </div>';

if ($mt > 92) {
    echo '<div class="cardview-wrapper" bis_skin_checked="1">
            <a class="cardview" href="/report'.$dirs.'' . $id . '">
                <div class="left px50" bis_skin_checked="1"><i class="font-icon font-icon-whistle"></i></div>
                <div class="right px50 arrow" bis_skin_checked="1">
                    <div class="text" bis_skin_checked="1">Посмотреть отчёт</div>
                </div>
            </a>
        </div>';
}

$std11 = mysql_query("SELECT * FROM `r_stadium` WHERE `id`='".$match['id_stadium']."'");
$std11 = mysql_fetch_array($std11);

if ($match['id_stadium']) {
    $stadiumImage = $std11['std'] ? $match['id_stadium'] : 'stadium';
    echo '<div class="gmenu"><center><img width="50%" health="50%" src="/images/stadium/'.$stadiumImage.'.jpg" alt="'.$std11['name'].'"/>';
    echo '</div></center>';
} else {
    echo '<div class="gmenu"><center><img src="/images/stadium/stadium.jpg" alt=""/>';
    echo '</div></center>';
}

if ($match['teh_end'] == 0) {
    echo '<div id="gamecomfiled2" style="height: 400px; min-height: 400px;">';
    $text = explode("\r\n", $match['text']);
    arsort($text);
    next($text);
    echo "<table>";
    
    foreach ($text as $key => $val) {
        $menu = explode("|", $text[$key]);
        if ($mt >= intval($menu[0]) && intval($menu[0]) > 0) {
            echo '<tr><td width="5%">' . ($menu[1] == '' ? '' : '<img src="/images/txt/'.($theme == "wap" ? 's' : 'm').'_'.$menu[1].'.gif" alt=""/>') . '</td>';
            echo '<td width="5%"><b>'.intval($menu[0])."'</b></td>";

            if ($menu[3] == 'play_for_25') {
                $tj = mysql_query("SELECT * FROM `r_team` WHERE id='".$menu[4]."' LIMIT 1");
                $teja = mysql_fetch_array($tj);
                $teja_img = !empty($teja['logo']) ? 
                    '<img src="/manager/logo/small' . $teja['id'] . '.jpeg" alt=""/>' : 
                    '<img src="/manager/logo/smallnologo.jpg" alt=""/>';
                $temp_jam = $teja['name'];
            }

            $til_txt = $menu[2]; // Default value
            
            $textMap = array(
                'twist_one_1' => $txt_twist_one_1,
                'twist_one_2' => $txt_twist_one_2,
                'twist_one_3' => $txt_twist_one_3,
                'twist_one_4' => $txt_twist_one_4,
                'twist_one_5' => $txt_twist_one_5,
                'twist_two_1' => $txt_twist_two_1,
                'twist_two_2' => $txt_twist_two_2,
                'twist_two_3' => $txt_twist_two_3,
                'twist_two_4' => $txt_twist_two_4,
                'twist_two_5' => $txt_twist_two_5,
                'finish_one_1' => $txt_finish_one_1,
                'finish_one_2' => $txt_finish_one_2,
                'finish_one_3' => $txt_finish_one_3,
                'finish_one_4' => $txt_finish_one_4,
                'finish_one_5' => $txt_finish_one_5,
                'finish_one_6' => $txt_finish_one_6,
                'finish_two_1' => $txt_finish_two_1,
                'finish_two_2' => $txt_finish_two_2,
                'finish_two_3' => $txt_finish_two_3,
                'finish_two_4' => $txt_finish_two_4,
                'finish_two_5' => $txt_finish_two_5,
                'finish_two_6' => $txt_finish_two_6,
                'finish_two_7' => $txt_finish_two_7,
                'finish_two_8' => $txt_finish_two_8,
                'finish_two_9' => $txt_finish_two_9,
                'twist_three' => $twist_three_1,
                'twist_four' => $twist_four_1,
                'finish_three' => $finish_three_1,
                'finish_four' => $finish_four_1,
                'play_for_25' => $teja_img.' <a href="/team/'.$teja['id'].'"><b>'.$temp_jam.'</b></a> '.$txt_play_for_25
            );

            if (isset($textMap[$menu[3]])) {
                $til_txt = $textMap[$menu[3]];
            }

            echo '<td>' . $til_txt . '</td></tr>';
        }
    }
    
    echo "</table>";
    echo "</div>";
} else {
    echo '<div id="gamecomfiled2" style="height: 400px; min-height: 400px;">
            <div style="float:right; overflow:hidden; padding:8px; cursor:pointer;">
                <div class="error" style="text-align: left">
                    Техническое поражение одной из команд. Тех-поражение выдают в тех случаях, когда на матч одной из команд вышло меньше семи игроков!
                </div>
            </div>
            <table class="game_comments" data-last-id="0">
                <tbody>
                    <tr class="event_104"></tr>
                    <tr class="event_108"></tr>
                </tbody>
            </table>
        </div>';
}

echo '</div>';

if ($mt > 92 && $match['step'] == '0') {
    // Process match results and update database
    $g = mysql_query("SELECT * FROM `r".$prefix."game` WHERE id = '" . $id . "' LIMIT 1");
    $game = mysql_fetch_array($g);
    $q1 = mysql_query("SELECT * FROM `r_team` WHERE id='" . $game['id_team1'] . "' LIMIT 1");
    $count1 = mysql_num_rows($q1);
    $arr1 = mysql_fetch_array($q1);

    $q2 = mysql_query("SELECT * FROM `r_team` WHERE id='" . $game['id_team2'] . "' LIMIT 1");
    $count2 = mysql_num_rows($q2);
    $arr2 = mysql_fetch_array($q2);
    
    $rezult = array($goals['1'], $goals['2']);
    $pen1 = 0;
    $pen2 = 0;

    if ($rezult[0] == $rezult[1]) {
        $penaltyScores = array("11:10", "10:9", "8:7", "7:6", "6:5", "5:3", "5:4", "4:2", "4:3", "3:2", "3:5", "4:5", "2:4", "3:4", "2:3", "10:11", "9:10", "7:8", "6:7", "5:6");
        $randomKey = array_rand($penaltyScores);
        $penult = explode(":", $penaltyScores[$randomKey]);
        $pen1 = $penult[0];
        $pen2 = $penult[1];
    }

    mysql_query("UPDATE `r".$prefix."game` SET `rez1`='".$rezult[0]."', `rez2`='".$rezult[1]."', `pen1`='".$pen1."', `pen2`='".$pen2."' WHERE id='" . $game['id'] . "' LIMIT 1");

    // Update team statistics based on match result
    $h_zwm_1 = explode("|", $arr1['zad_win_match']);
    $p_zwm_1 = $h_zwm_1[1] + 1;

    $h_zwm_2 = explode("|", $arr2['zad_win_match']);
    $p_zwm_2 = $h_zwm_2[1] + 1;

    // Team 1 updates
    if ($rezult[0] > $rezult[1]) {
        $oputman1 = $arr1['oput'] + 1;
        $fansman1 = $arr1['fans'] + 10;
        
        if ($arr1['sponsor'] != '0') {
            $x22 = mysql_query("SELECT * FROM `sponsors` WHERE id='".$arr1['sponsor']."'");
            $ns2 = mysql_fetch_array($x22);
            $moneyn1 = $arr1['money'] + round($game['zritel'] * 0.01);
            $moneyman1 = $ns2['money'] + $moneyn1;
            $m1 = $ns2['money'] + round($game['zritel'] * 0.01);
        } else {
            $moneyn1 = $arr1['money'] + round($game['zritel'] * 0.01);
            $moneyman1 = $moneyn1;
            $m1 = round($game['zritel'] * 0.01);
        }
        
        $winman1 = $arr1['win'] + 1;
        mysql_query("UPDATE `r_team` SET `zad_win_match`='".$h_zwm_1[0]."|".$p_zwm_1."', `oput`='" . $oputman1 . "', `money`='" . $moneyn1 . "', `win`='" . $winman1 . "' WHERE id='" . $arr1['id'] . "' LIMIT 1");
        mysql_query("INSERT INTO `news` SET
            `time`='".$realtime."',
            `money`='+".$m1."',
            `text`='Победа',
            `old_club`='".$arr2['id']."',
            `team_id`='".$arr1['id']."'");
    } elseif ($rezult[0] == $rezult[1]) {
        if ($arr1['sponsor'] != '0') {
            $moneyn1 = $arr1['money'] + round($game['zritel'] * 0.005);
            $moneyman1 = $ns2['money'] + $moneyn1;
            $m1 = $ns2['money'] + round($game['zritel'] * 0.01);
        } else {
            $moneyn1 = $arr1['money'] + round($game['zritel'] * 0.005);
            $moneyman1 = $moneyn1;
            $m1 = round($game['zritel'] * 0.01);
        }

        $nnman1 = $arr1['nn'] + 1;
        mysql_query("INSERT INTO `news` SET
            `time`='".$realtime."',
            `money`='+".$m1."',
            `text`='Ничья',
            `old_club`='".$arr2['id']."',
            `team_id`='".$arr1['id']."'");
        mysql_query("UPDATE `r_team` SET `money`='" . $moneyman1 . "', `nn`='" . $nnman1 . "' WHERE id='" . $arr1['id'] . "' LIMIT 1");
    } else {
        $losman1 = $arr1['los'] + 1;
        mysql_query("UPDATE `r_team` SET `los`='" . $losman1 . "' WHERE id='" . $arr1['id'] . "' LIMIT 1");
    }

    // Team 2 updates
    if ($rezult[1] > $rezult[0]) {
        $x22 = mysql_query("SELECT * FROM `sponsors` WHERE id='".$arr2['sponsor']."'");
        $ns2 = mysql_fetch_array($x22);
        $oputman2 = $arr2['oput'] + 1;
        $fansman2 = $arr1['fans'] + 10;
        
        if ($arr2['sponsor'] != '0') {
            $moneyn2 = $arr2['money'] + round($game['zritel'] * 0.01);
            $moneyman2 = $ns2['money'] + $moneyn2;
            $m2 = $ns2['money'] + round($game['zritel'] * 0.01);
        } else {
            $moneyn2 = $arr2['money'] + round($game['zritel'] * 0.01);
            $moneyman2 = $moneyn2;
            $m2 = round($game['zritel'] * 0.01);
        }
        
        $winman2 = $arr2['win'] + 1;
        mysql_query("INSERT INTO `news` SET
            `time`='".$realtime."',
            `money`='+".$m2."',
            `text`='Победа',
            `old_club`='".$arr1['id']."',
            `team_id`='".$arr2['id']."'");
        mysql_query("UPDATE `r_team` SET `zad_win_match`='".$h_zwm_2[0]."|".$p_zwm_2."', `oput`='" . $oputman2 . "', `money`='" . $moneyman2 . "', `win`='" . $winman2 . "' WHERE id='" . $arr2['id'] . "' LIMIT 1");
    } elseif ($rezult[1] == $rezult[0]) {
        if ($arr2['sponsor'] != '0') {
            $moneyn2 = $arr2['money'] + round($game['zritel'] * 0.005);
            $moneyman2 = $ns2['money'] + $moneyn2;
            $m2 = $ns2['money'] + round($game['zritel'] * 0.005);
        } else {
            $moneyn2 = $arr2['money'] + round($game['zritel'] * 0.005);
            $moneyman2 = $moneyn2;
            $m2 = round($game['zritel'] * 0.005);
        }

        $nnman2 = $arr2['nn'] + 1;
        mysql_query("INSERT INTO `news` SET
            `time`='".$realtime."',
            `money`='+".$m2."',
            `text`='Ничья',
            `old_club`='".$arr1['id']."',
            `team_id`='".$arr2['id']."'");
        mysql_query("UPDATE `r_team` SET `money`='" . $moneyman2 . "', `nn`='" . $nnman2 . "' WHERE id='" . $arr2['id'] . "' LIMIT 1");
    } else {
        $losman2 = $arr2['los'] + 1;
        mysql_query("UPDATE `r_team` SET `los`='" . $losman2 . "' WHERE id='" . $arr2['id'] . "' LIMIT 1");
    }

    $nat1 = $game['per1'] + $rezult[0];
    $nat2 = $game['per2'] + $rezult[1];

    // Tournament-specific updates
    $tournamentUpdates = array(
        'brend' => array('table' => 'b_cupgame', 'field' => 'id_match'),
        'cup' => array('table' => 'r_cupgame', 'field' => 'id_match'),
        'z_cup' => array('table' => 'z_cupgame', 'field' => 'id_match'),
        'champ_retro' => array('table' => 'champ_game', 'field' => 'id_match'),
        'champ' => array('table' => 'champ_game', 'field' => 'id_match'),
        'liga' => array('table' => 'liga_game', 'field' => 'id_match'),
        'liga_r' => array('table' => 'liga_game_r', 'field' => 'id_match'),
        'liga_r2' => array('table' => 'liga_game_r2000', 'field' => 'id_match'),
        'liberta' => array('table' => 'liberta_game', 'field' => 'id_match'),
        'le' => array('table' => 'le_game', 'field' => 'id_match'),
        'kuefa2' => array('table' => 'le_game_2000', 'field' => 'id_match'),
        'afc_chl' => array('table' => 'afc_chl_game', 'field' => 'id_match'),
        'afc_cup' => array('table' => 'afc_cup_game', 'field' => 'id_match'),
        'asiachamp' => array('table' => 'asiachamp_game', 'field' => 'id_match'),
        'unchamp' => array('table' => 'union_champ_game', 'field' => 'id_match'),
        'cupcom' => array('table' => 'cupcom_game', 'field' => 'id_match'),
        'afc_cupcom' => array('table' => 'afc_cupcom_game', 'field' => 'id_match'),
        'maradona' => array('table' => 'maradona_game', 'field' => 'id_match'),
        'continent' => array('table' => 'continent_game', 'field' => 'id_match'),
        'super_cup' => array('table' => 'super_cup_game', 'field' => 'id_match'),
        'super_cup2' => array('table' => 'super_cup_game_2000', 'field' => 'id_match')
    );

    if (isset($tournamentUpdates[$game['chemp']])) {
        $updateData = $tournamentUpdates[$game['chemp']];
        $sql = "UPDATE `{$updateData['table']}` SET `rez1`='{$rezult[0]}', `rez2`='{$rezult[1]}', `id_report`='{$id}'";
        
        if (($game['chemp'] == $game['turnir'] && $nat1 == $nat2) || ($game['final'] == 'final' && $rezult[0] == $rezult[1])) {
            $sql .= ", `pen1`='{$pen1}', `pen2`='{$pen2}'";
        }
        
        $sql .= " WHERE id='{$game[$updateData['field']]}' LIMIT 1";
        mysql_query($sql);
    }

    // Update betting results if needed
    if (in_array($game['chemp'], array('champ_retro', 'champ', 'liga', 'liga_r', 'liga_r2', 'liberta', 'le', 'kuefa2', 'super_cup', 'super_cup2'))) {
        $winner = ($rezult[0] > $rezult[1]) ? '1' : (($rezult[1] > $rezult[0]) ? '2' : '3');
        $g6 = mysql_query("SELECT * FROM `r_game` WHERE `id`='" . $id . "'");
        $game6 = mysql_fetch_array($g6);
        
        mysql_query("UPDATE `t_games` SET `score`='{$rezult[0]}|{$rezult[1]}', `winner`='{$winner}' WHERE `id_match`='{$game6['id_match']}' LIMIT 1");

        $req37 = mysql_query("SELECT * FROM `t_games` WHERE `id_match`='{$game6['id_match']}'");
        $kom337 = mysql_fetch_array($req37);
        
        $milsQuery = mysql_query("SELECT * FROM `t_mils` WHERE `refid` = '{$game6['id']}'");
        while ($mil = mysql_fetch_array($milsQuery)) {
            $req379 = mysql_query("SELECT * FROM `r_team` WHERE `id_admin`='{$mil['user']}'");
            $kom3379 = mysql_fetch_array($req379);

            $teams = explode('|', $kom337['teams']);
            $teamsCount = count($teams);
            $coefs = explode('|', $kom337['coefs']);

            $no_winner = true;
            $scores = array();
            
            for ($i = 0; $i < $teamsCount; $i++) {
                $score = 0;
                if ($_POST['score' . $i] > 0) {
                    $score = htmlspecialchars(trim($_POST['score' . $i]));
                }
                $scores[$i] = $score;
                if ($i > 0 && $score != $scores[$i - 1]) {
                    $no_winner = false;
                }
            }

            $sortedScores = array_flip($scores);
            ksort($sortedScores);
            $winner = end($sortedScores) + 1;
            
            if ($no_winner) {
                $winner = count($coefs);
            }
            
            if ($mil['winner'] == $winner) {
                $winAmount = $mil['mil'] * $coefs[$winner - 1];
                mysql_query("UPDATE `r_team` SET `money` = (`money` + {$winAmount}) WHERE `id` = '{$kom3379['id']}'");
                
                if ($winner == 1) {
                    $aaa = $teams[0] . ' <b>П1</b>';
                } elseif ($winner == 2) {
                    $aaa = $teams[1] . ' <b>П2</b>';
                } else {
                    $aaa = $teams[0] . '-' . $teams[1] . ' <b>Ничья</b>';
                }
                
                mysql_query("INSERT INTO `news` SET
                    `time`='{$realtime}',
                    `money`='+{$winAmount}',
                    `text`='Ставка {$aaa}',
                    `team_id`='{$kom3379['id']}'");
                
                mysql_query("DELETE FROM `t_mils` WHERE `id` = {$mil['id']}");
            }
        }
    }
}

// Определяем функцию для обновления результатов матча
function updateMatchResult($table, $game, $rezult, $pen1, $pen2, $id) {
    $query = "UPDATE `$table` SET 
        `rez1` = '" . mysql_real_escape_string($rezult[0]) . "',
        `rez2` = '" . mysql_real_escape_string($rezult[1]) . "',
        `pen1` = '" . mysql_real_escape_string($pen1) . "',
        `pen2` = '" . mysql_real_escape_string($pen2) . "',
        `id_report` = '" . mysql_real_escape_string($id) . "'
        WHERE id = '" . mysql_real_escape_string($game['id_match']) . "' 
        LIMIT 1";
    return mysql_query($query);
}

// Маппинг типов турниров к таблицам
$tournamentTables = array(
    'eusebio' => 'game_eusebio',
    'cup_en' => 'cup_en',
    'cup_netto' => 'cup_netto',
    'cup_charlton' => 'cup_charlton',
    'cup_muller' => 'cup_muller',
    'cup_puskas' => 'cup_puskas',
    'cup_fachetti' => 'cup_fachetti',
    'cup_kopa' => 'cup_kopa',
    'cup_distefano' => 'cup_distefano',
    'cup_garrinca' => 'cup_garrinca',
    'cup_ru' => 'cup_ru',
    'cup_pt' => 'cup_pt',
    'cup_nl' => 'cup_nl',
    'cup_ua' => 'cup_ua',
    'cup_es' => 'cup_es',
    'cup_it' => 'cup_it',
    'cup_de' => 'cup_de',
    'cup_fr' => 'cup_fr',
    'cup_po' => 'cup_po',
    'afc_super_cup' => 'afc_super_cup_game',
    'cup_avs' => 'cup_avs',
    'cup_az' => 'cup_az',
    'cup_iran' => 'cup_iran',
    'cup_kaz' => 'cup_kaz',
    'cup_kyr' => 'cup_kyr',
    'cup_taj' => 'cup_taj',
    'cup_tur' => 'cup_tur',
    'cup_uzb' => 'cup_uzb',
    'afs' => 'afs_game'
);

// Обработка обычных турниров
if (isset($tournamentTables[$game['chemp']])) {
    updateMatchResult($tournamentTables[$game['chemp']], $game, $rezult, $pen1, $pen2, $id);
}

// Обработка Лиги Европы
if ($game['chemp'] == 'msch' && $game['msch_holat'] != 'ok') {
    // Обновляем результат матча
    mysql_query("UPDATE `msch_game` SET 
        `rez1` = '" . mysql_real_escape_string($rezult[0]) . "',
        `rez2` = '" . mysql_real_escape_string($rezult[1]) . "',
        `id_report` = '" . mysql_real_escape_string($id) . "'
        WHERE id = '" . mysql_real_escape_string($game['id_match']) . "' 
        LIMIT 1");

    // Получаем данные команд
    $lrr1 = mysql_fetch_array(mysql_query("SELECT * FROM `msch_table` WHERE union_id = '" . mysql_real_escape_string($game['union_team1']) . "' LIMIT 1"));
    $lrr2 = mysql_fetch_array(mysql_query("SELECT * FROM `msch_table` WHERE union_id = '" . mysql_real_escape_string($game['union_team2']) . "' LIMIT 1"));

    // Получаем данные о матче в турнире
    $uni1 = mysql_fetch_array(mysql_query("SELECT * FROM `msch_union_game` WHERE union_id1 = '" . mysql_real_escape_string($game['union_team1']) . "' AND union_tur = '" . mysql_real_escape_string($game['msch_tur']) . "' LIMIT 1"));
    $uni2 = mysql_fetch_array(mysql_query("SELECT * FROM `msch_union_game` WHERE union_id2 = '" . mysql_real_escape_string($game['union_team2']) . "' AND union_tur = '" . mysql_real_escape_string($game['msch_tur']) . "' LIMIT 1"));

    // Базовые обновления для всех случаев
    $igr1 = $lrr1['igr'] + 1;
    $igr2 = $lrr2['igr'] + 1;
    $gz1 = $lrr1['gz'] + $rezult[0];
    $gz2 = $lrr2['gz'] + $rezult[1];
    $gp1 = $lrr1['gp'] + $rezult[1];
    $gp2 = $lrr2['gp'] + $rezult[0];

    // Определяем результат матча
    if ($rezult[0] > $rezult[1]) {
        // Победа первой команды
        $win1 = $lrr1['win'] + 1;
        $los2 = $lrr2['los'] + 1;
        $uniwin1 = $uni1['union_rez1'] + 1;
        $ochey1 = $lrr1['ochey'] + 3;
        $ochey2 = $lrr2['ochey'] + 0;

        mysql_query("UPDATE `msch_union_game` SET `union_rez1` = '$uniwin1' WHERE id = '{$uni1['id']}' LIMIT 1");
        mysql_query("UPDATE `msch_table` SET `igr` = '$igr1', `win` = '$win1', `gz` = '$gz1', `gp` = '$gp1', `ochey` = '$ochey1' WHERE id = '{$lrr1['id']}' LIMIT 1");
        mysql_query("UPDATE `msch_table` SET `igr` = '$igr2', `los` = '$los2', `gz` = '$gz2', `gp` = '$gp2', `ochey` = '$ochey2' WHERE id = '{$lrr2['id']}' LIMIT 1");
    } elseif ($rezult[1] > $rezult[0]) {
        // Победа второй команды
        $los1 = $lrr1['los'] + 1;
        $win2 = $lrr2['win'] + 1;
        $uniwin2 = $uni2['union_rez2'] + 1;
        $ochey1 = $lrr1['ochey'] + 0;
        $ochey2 = $lrr2['ochey'] + 3;

        mysql_query("UPDATE `msch_union_game` SET `union_rez2` = '$uniwin2' WHERE id = '{$uni2['id']}' LIMIT 1");
        mysql_query("UPDATE `msch_table` SET `igr` = '$igr1', `los` = '$los1', `gz` = '$gz1', `gp` = '$gp1', `ochey` = '$ochey1' WHERE id = '{$lrr1['id']}' LIMIT 1");
        mysql_query("UPDATE `msch_table` SET `igr` = '$igr2', `win` = '$win2', `gz` = '$gz2', `gp` = '$gp2', `ochey` = '$ochey2' WHERE id = '{$lrr2['id']}' LIMIT 1");
    } else {
        // Ничья
        $nn1 = $lrr1['nn'] + 1;
        $nn2 = $lrr2['nn'] + 1;
        $ochey1 = $lrr1['ochey'] + 1;
        $ochey2 = $lrr2['ochey'] + 1;

        mysql_query("UPDATE `msch_table` SET `igr` = '$igr1', `nn` = '$nn1', `gz` = '$gz1', `gp` = '$gp1', `ochey` = '$ochey1' WHERE id = '{$lrr1['id']}' LIMIT 1");
        mysql_query("UPDATE `msch_table` SET `igr` = '$igr2', `nn` = '$nn2', `gz` = '$gz2', `gp` = '$gp2', `ochey` = '$ochey2' WHERE id = '{$lrr2['id']}' LIMIT 1");
    }

    // Помечаем матч как завершенный
    mysql_query("UPDATE `r" . $prefix . "game` SET `msch_holat` = 'ok' WHERE id = '" . mysql_real_escape_string($game['id']) . "' LIMIT 1");
}

// Обновляем статус игры
mysql_query("UPDATE `r" . $prefix . "game` SET `step` = '1' WHERE id = '" . mysql_real_escape_string($game['id']) . "' LIMIT 1");

// Обновляем статистику судьи
$judge = mysql_fetch_array(mysql_query("SELECT * FROM `r_judge` WHERE `id` = '" . mysql_real_escape_string($game['judge']) . "' LIMIT 1"));
if ($judge) {
    $gamesCount = $judge['game'] + 1;
    mysql_query("UPDATE `r_judge` SET `game` = '$gamesCount' WHERE id = '" . mysql_real_escape_string($game['judge']) . "' LIMIT 1");
}

// Обновляем счетчик рефери для команд
if ($arr1['ref']) {
    $reff = $arr1['ref'] - 1;
    mysql_query("UPDATE `r_team` SET `ref` = '$reff' WHERE id = '" . mysql_real_escape_string($arr1['id']) . "' LIMIT 1");
}
if ($arr2['ref']) {
    $reff2 = $arr2['ref'] - 1;
    mysql_query("UPDATE `r_team` SET `ref` = '$reff2' WHERE id = '" . mysql_real_escape_string($arr2['id']) . "' LIMIT 1");
}

echo'</div>';
require_once("../incfiles/end.php");
?>

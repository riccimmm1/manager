<?php
define('_IN_JOHNCMS', 1);
$headmod = 'txt';
require_once ("../incfiles/core.php");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$union = isset($_GET['union']) ? intval($_GET['union']) : 0;
$prefix = $union ? '_union_' : '_';
$dirs = $union ? '/union/' : '/';

$q = mysql_query("SELECT * FROM `r".$prefix."game` WHERE id='" . $id . "' LIMIT 1;");
$arr = mysql_fetch_assoc($q);

if (empty($arr['id'])) {
    $textl = 'Ошибка';
    require_once ("../incfiles/head.php");
    echo display_error('Отчёт не найден');
    require_once ("../incfiles/end.php");
    exit;
}

$textl = htmlspecialchars($arr['name_team1']) . ' - ' . htmlspecialchars($arr['name_team2']) . ' ' . $arr['rez1'] . ':' . $arr['rez2'];
require_once ("../incfiles/head.php");

if (empty($arr['tactics1']) || empty($arr['tactics2'])) {
    header('Location: /game' . $dirs . $id);
    exit;
}

$realtime = time();
$mt = (($realtime - $arr['time']) * 18);
$mt = floor($mt / 60);

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

$k1 = mysql_query("SELECT * FROM `r_team` WHERE id='" . $arr['id_team1'] . "' LIMIT 1;");
$kom1 = mysql_fetch_assoc($k1);

$k2 = mysql_query("SELECT * FROM `r_team` WHERE id='" . $arr['id_team2'] . "' LIMIT 1;");
$kom2 = mysql_fetch_assoc($k2);

$c_name = 'Неизвестный кубок';
if (isset($arr['kubok'])) {
    $kubok = $arr['kubok'];
    $c_vars = get_defined_vars();
    
    if (is_numeric($kubok)) {
        $var_name = 'c_' . $kubok;
        if (isset($c_vars[$var_name])) {
            $c_name = $c_vars[$var_name];
        }
    } else {
        switch ($kubok) {
            case "cup_netto":     $c_name = 'Кубок Нетто'; break;
            case "cup_charlton":  $c_name = 'Кубок Чарльтона'; break;
            case "cup_en":        $c_name = 'Кубок Англии'; break;
            case "cup_muller":    $c_name = 'Кубок Мюллера'; break;
            case "cup_puskas":    $c_name = 'Кубок Пушкаша'; break;
            case "cup_fachetti":  $c_name = 'Кубок Факкетти'; break;
            case "cup_kopa":      $c_name = 'Кубок Копа'; break;
            case "cup_distefano": $c_name = 'Кубок Ди Стефано'; break;
            default:
                if (isset($c_vars['c_' . $kubok])) {
                    $c_name = $c_vars['c_' . $kubok];
                }
                break;
        }
    }
}
?>
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



	<?

////////////////////////1111111111111111111111111/////////////////		
		
		
		


		
switch ($arr['chemp']) {
    // Группа кубков с одинаковым выводом
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

    // Группа кубков с одинаковым выводом (fedcup вместо fedcup2)
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




/////////////////////////222222222222222222222222222222222222222

				 	$q1auy = mysqli_query($link, "SELECT * FROM `r_judge` WHERE `id`='".mysqli_real_escape_string($link, $arr['judge'])."' LIMIT 1");
$aayr = mysqli_fetch_assoc($q1auy);

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
                        <?php if (!empty($kom1['logo'])): ?>
                            <a href="/team/<?=$kom1['id']?>">
                                <img src="/manager/logo/big<?=$kom1['logo']?>" alt="">
                            </a>
                        <?php else: ?>
                            <a href="/team/<?=$kom1['id']?>">
                                <img src="/manager/logo/b_0.jpg" alt="" width="37">
                            </a>
                        <?php endif; ?>

                        <div>
                            <a href="/team/<?=$kom1['id']?>">
                                <span class="flags c_<?=$kom1['flag']?>_14" style="vertical-align: middle;"></span>
                                <?=$kom1['name']?>
                            </a><br>
                            <?php if($kom1['id_admin'] > 0):
                                $us1 = mysqli_query($link, "SELECT * FROM `users` WHERE `id`=".(int)$kom1['id_admin']." LIMIT 1");
                                $uss1 = mysqli_fetch_assoc($us1);
                                $vip_icons = [
                                    0 => '/images/ico/vip0_m.png',
                                    1 => '/images/ico/vip1_m.png',
                                    2 => '/images/ico/vip2_m.png',
                                    3 => '/images/ico/vip3_m.png'
                                ];
                                $vip_titles = [
                                    0 => 'Базовый аккаунт',
                                    1 => 'Улучшенный Премиум-аккаунт',
                                    2 => 'Улучшенный VIP-аккаунт',
                                    3 => 'Представительский Gold-аккаунт'
                                ];
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
                        <b><font size="+4"><?=$goal1?>:<?=$goal2?></font></b>
                        <div>
                            <?php
                            if($mt < 0) $smt = 'Матч еще не начался';
                            if($mt >= 0 && $mt <= 92) {
                                $smt = "<font size='+1.5'><b>$mt минута</b></font>";
                                echo '<img src="/images/ico/flash.gif" class="va" alt="минута">';
                            }
                            if($mt > 92) $smt = "<font size='-1.5'><b>$yakun</b></font>";
                            echo "<font size='+1'>$smt</font>";
                            ?>
                        </div>
                    </center>
                </td>
                
                <td width="47%">
                    <center>
                        <?php if (!empty($kom2['logo'])): ?>
                            <a href="/team/<?=$kom2['id']?>">
                                <img src="/manager/logo/big<?=$kom2['logo']?>" alt="">
                            </a>
                        <?php else: ?>
                            <a href="/team/<?=$kom2['id']?>">
                                <img src="/manager/logo/b_0.jpg" alt="" width="37px">
                            </a>
                        <?php endif; ?>

                        <div>
                            <a href="/team/<?=$kom2['id']?>"><?=$kom2['name']?></a>
                            <span class="flags c_<?=$kom2['flag']?>_14" style="vertical-align: middle;"></span><br>
                            <?php if($kom2['id_admin'] > 0):
                                $us2 = mysqli_query($link, "SELECT * FROM `users` WHERE `id`=".(int)$kom2['id_admin']." LIMIT 1");
                                $uss2 = mysqli_fetch_assoc($us2);
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
                <a class="cardview" href="/report<?=$dirs?><?=$id?>">
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
        // Обработка бомбардиров для разных типов турниров
        $cup_types = [
            'cup_en', 'cup_netto', 'cup_charlton', 'cup_muller', 'cup_puskas', 
            'cup_distefano', 'cup_fachetti', 'cup_kopa', 'cup_garrinca'
        ];
        
        if(in_array($arr['chemp'], $cup_types) && $mt >= 92 && $arr['bet'] == '0') {
            $menus = explode("\r\n", $arr['menus']);
            foreach($menus as $key => $val) {
                $menu = explode("|", $val);
                $r5 = mysqli_query($link, "SELECT * FROM `r_player` WHERE id='".mysqli_real_escape_string($link, $menu[2])."' LIMIT 1");
                if($byy = mysqli_fetch_assoc($r5)) {
                    $bomplus = $byy['bomb_fedcup'] + 1;
                    mysqli_query($link, "UPDATE `r_player` SET `bomb_fedcup`='$bomplus' WHERE id='{$byy['id']}'");
                }
            }
            mysqli_query($link, "UPDATE `r{$prefix}game` SET `bet`='1' WHERE id='{$arr['id']}'");
        }

        // Обработка других типов турниров
        $tournament_types = [
            'champ' => ['field' => 'bomb_champ', 'act_field' => 'act_champ', 'update_act' => true],
            'champ_retro' => ['field' => 'bomb_champ_retro', 'act_field' => 'act_champ_retro', 'update_act' => true],
            'cupcom' => ['field' => 'bomb_cupcom', 'act_field' => '', 'update_act' => false],
            'maradona' => ['field' => 'bomb_maradona', 'act_field' => '', 'update_act' => false],
            'liga_r' => ['field' => 'bomb_liga_r', 'act_field' => '', 'update_act' => false],
            'liberta' => ['field' => 'bomb_liberta', 'act_field' => '', 'update_act' => false],
            'liga' => ['field' => 'bomb_liga', 'act_field' => '', 'update_act' => false],
            'le' => ['field' => 'bomb_le', 'act_field' => '', 'update_act' => false],
            'vsch' => ['field' => 'bomb_vsch', 'act_field' => '', 'update_act' => false],
            'msch' => ['field' => 'bomb_msch', 'act_field' => '', 'update_act' => false]
        ];
        
        if(isset($tournament_types[$arr['chemp']]) && $mt >= 92 && $arr['bet'] == '0') {
            $config = $tournament_types[$arr['chemp']];
            $menus = explode("\r\n", $arr['menus']);
            
            foreach($menus as $key => $val) {
                $menu = explode("|", $val);
                $r5 = mysqli_query($link, "SELECT * FROM `r_player` WHERE id='".mysqli_real_escape_string($link, $menu[2])."' LIMIT 1");
                
                if($byy = mysqli_fetch_assoc($r5)) {
                    $bomplus = $byy[$config['field']] + 1;
                    $update_query = "UPDATE `r_player` SET `{$config['field']}`='$bomplus'";
                    
                    if($config['update_act'] && !empty($config['act_field'])) {
                        $update_query .= ", `{$config['act_field']}`='{$arr['kubok']}'";
                    }
                    
                    $update_query .= " WHERE id='{$byy['id']}'";
                    mysqli_query($link, $update_query);
                }
            }
            mysqli_query($link, "UPDATE `r{$prefix}game` SET `bet`='1' WHERE id='{$arr['id']}'");
        }
        ?>

        <?php
        $std11 = [];
        if($arr['id_stadium']) {
            $std_res = mysqli_query($link, "SELECT * FROM `r_stadium` WHERE `id`='".(int)$arr['id_stadium']."'");
            $std11 = mysqli_fetch_assoc($std_res);
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
                            
                            // Обработка специальных событий
                            switch ($menu[3]) {
                                case "twist_one_1": $til_txt = $txt_twist_one_1; break;
                                case "twist_one_2": $til_txt = $txt_twist_one_2; break;
                                case "twist_one_3": $til_txt = $txt_twist_one_3; break;
                                case "twist_one_4": $til_txt = $txt_twist_one_4; break;
                                case "twist_one_5": $til_txt = $txt_twist_one_5; break;
                                case "twist_two_1": $til_txt = $txt_twist_two_1; break;
                                case "twist_two_2": $til_txt = $txt_twist_two_2; break;
                                case "twist_two_3": $til_txt = $txt_twist_two_3; break;
                                case "twist_two_4": $til_txt = $txt_twist_two_4; break;
                                case "twist_two_5": $til_txt = $txt_twist_two_5; break;
                                case "finish_one_1": $til_txt = $txt_finish_one_1; break;
                                case "finish_one_2": $til_txt = $txt_finish_one_2; break;
                                case "finish_one_3": $til_txt = $txt_finish_one_3; break;
                                case "finish_one_4": $til_txt = $txt_finish_one_4; break;
                                case "finish_one_5": $til_txt = $txt_finish_one_5; break;
                                case "finish_one_6": $til_txt = $txt_finish_one_6; break;
                                case "finish_two_1": $til_txt = $txt_finish_two_1; break;
                                case "finish_two_2": $til_txt = $txt_finish_two_2; break;
                                case "finish_two_3": $til_txt = $txt_finish_two_3; break;
                                case "finish_two_4": $til_txt = $txt_finish_two_4; break;
                                case "finish_two_5": $til_txt = $txt_finish_two_5; break;
                                case "finish_two_6": $til_txt = $txt_finish_two_6; break;
                                case "finish_two_7": $til_txt = $txt_finish_two_7; break;
                                case "finish_two_8": $til_txt = $txt_finish_two_8; break;
                                case "finish_two_9": $til_txt = $txt_finish_two_9; break;
                                case "twist_three": $til_txt = $twist_three_1; break;
                                case "twist_four": $til_txt = $twist_four_1; break;
                                case "finish_three": $til_txt = $finish_three_1; break;
                                case "finish_four": $til_txt = $finish_four_1; break;
                                
                                case "play_for_25":
                                    $tj = mysqli_query($link, "SELECT * FROM `r_team` WHERE `id`='".(int)$menu[4]."' LIMIT 1");
                                    $teja = mysqli_fetch_assoc($tj);
                                    $teja_img = !empty($teja['logo']) 
                                        ? '<img src="/manager/logo/small'.$teja['id'].'.jpeg" alt="">'
                                        : '<img src="/manager/logo/smallnologo.jpg" alt="">';
                                    $til_txt = $teja_img.' <a href="/team/'.$teja['id'].'"><b>'.$teja['name'].'</b></a> '.$txt_play_for_25;
                                    break;
                            }
                            ?>
                            <tr>
                                <td width="5%">
                                    <?php if(!empty($menu[1])): ?>
                                        <img src="/images/txt/<?=$theme=="wap"?'s':'m'?>_<?=$menu[1]?>.gif" alt="">
                                    <?php endif; ?>
                                </td>
                                <td width="5%"><b><?=intval($menu[0])?>'</b></td>
                                <td><?=$til_txt?></td>
                            </tr>
                        <?php endif;
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
<?

	

if ($mt > 92 && $arr['step'] == '0') {
    // Получение данных о матче
    $g = mysql_query("SELECT * FROM `r" . $prefix . "game` WHERE id = '" . $id . "' LIMIT 1");
    $game = mysql_fetch_array($g);
    
    // Получение данных команд
    $q1 = mysql_query("SELECT * FROM `r_team` WHERE id = '" . $game['id_team1'] . "' LIMIT 1");
    $arr1 = mysql_fetch_array($q1);
    
    $q2 = mysql_query("SELECT * FROM `r_team` WHERE id = '" . $game['id_team2'] . "' LIMIT 1");
    $arr2 = mysql_fetch_array($q2);
    
    $rezult = [$goal1, $goal2];
    $pen1 = $pen2 = 0;

    // Обработка пенальти при ничье
    if ($rezult[0] == $rezult[1]) {
        $penalties = [
            "11:10", "10:9", "8:7", "7:6", "6:5", "5:3", "5:4", "4:2", "4:3", "3:2",
            "3:5", "4:5", "2:4", "3:4", "2:3", "10:11", "9:10", "7:8", "6:7", "5:6"
        ];
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

// ОБРАБОТКА РЕФЕРИ - УМЕНЬШЕНИЕ СЧЕТЧИКА РЕФЕРИ У КОМАНД
    if ($arr1['ref'] > 0) {
        $new_ref1 = $arr1['ref'] - 1;
        mysql_query("UPDATE `r_team` SET `ref` = '$new_ref1' WHERE id = '" . $arr1['id'] . "'");
    }
    if ($arr2['ref'] > 0) {
        $new_ref2 = $arr2['ref'] - 1;
        mysql_query("UPDATE `r_team` SET `ref` = '$new_ref2' WHERE id = '" . $arr2['id'] . "'");
    }
    
    // Обработка турнирных данных
    $chemp = $game['chemp'];
    
    // Обновление таблиц чемпионата
    if ($chemp == 'champ_retro' || $chemp == 'champ') {
        $table = ($chemp == 'champ_retro') ? 'champ_table' : 'champ_table';
        $update_table = 'champ_game';
        
        // Обновление записи матча
        mysql_query("UPDATE `$update_table` 
            SET `rez1` = '" . $rezult[0] . "', 
                `rez2` = '" . $rezult[1] . "', 
                `id_report` = '" . $id . "' 
            WHERE id = '" . $game['id_match'] . "' 
            LIMIT 1");

        // Обновление турнирной таблицы
        $this->updateChampTable($table, $game, $rezult);
    }
    // Обработка Лиги чемпионов
    elseif ($chemp == 'liga' || $chemp == 'liga_r' || $chemp == 'liga_r2') {
        $tables = [
            'liga' => ['game' => 'liga_game', 'group' => 'liga_group'],
            'liga_r' => ['game' => 'liga_game_r', 'group' => 'liga_group_r'],
            'liga_r2' => ['game' => 'liga_game_r2000', 'group' => 'liga_group_r2000']
        ];
        
        $config = $tables[$chemp];
        mysql_query("UPDATE `{$config['game']}` 
            SET `rez1` = '" . $rezult[0] . "', 
                `rez2` = '" . $rezult[1] . "', 
                `id_report` = '" . $id . "' 
            WHERE id = '" . $game['id_match'] . "' 
            LIMIT 1");

        // Для группового этапа
        if (isset($game['etap']) && $game['etap'] == 'gr') {
            $this->updateGroupTable($config['group'], $game, $rezult);
        }
    }
    // Обработка кубков
    else {
        $cupTables = [
            'brend' => 'b_cupgame',
            'cup' => 'r_cupgame',
            'z_cup' => 'z_cupgame',
            'liberta' => 'liberta_game',
            'le' => 'le_game',
            'kuefa2' => 'le_game_2000',
            'afc_chl' => 'afc_chl_game',
            'afc_cup' => 'afc_cup_game',
            'asiachamp' => 'asiachamp_game',
            'unchamp' => 'union_champ_game',
            'cupcom' => 'cupcom_game',
            'afc_cupcom' => 'afc_cupcom_game',
            'maradona' => 'maradona_game',
            'continent' => 'continent_game',
            'super_cup' => 'super_cup_game',
            'super_cup2' => 'super_cup_game_2000',
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
            'afs' => 'afs_game',
            'msch' => 'msch_game'
        ];
        
        if (isset($cupTables[$chemp])) {
            $table = $cupTables[$chemp];
            $pen_update = ($chemp == 'msch' || $chemp == 'le' || $chemp == 'kuefa2') ? "" : 
                ", `pen1` = '" . $pen1 . "', `pen2` = '" . $pen2 . "'";
            
            mysql_query("UPDATE `$table` 
                SET `rez1` = '" . $rezult[0] . "', 
                    `rez2` = '" . $rezult[1] . "' 
                    $pen_update, 
                    `id_report` = '" . $id . "' 
                WHERE id = '" . $game['id_match'] . "' 
                LIMIT 1");
        }
    }

    // Финализация матча
    mysql_query("UPDATE `r" . $prefix . "game` SET `step` = '1' WHERE id = '" . $game['id'] . "' LIMIT 1");
    
    // Обновление статистики судьи
    $q_judge = mysql_query("SELECT * FROM `r_judge` WHERE `id` = '" . $game['judge'] . "' LIMIT 1");
    $judge_data = mysql_fetch_array($q_judge);
    $new_games = $judge_data['game'] + 1;
    mysql_query("UPDATE `r_judge` SET `game` = '" . $new_games . "' WHERE id = '" . $game['judge'] . "' LIMIT 1");
}

// Вспомогательная функция для обновления турнирной таблицы чемпионата
function updateChampTable($table, $game, $rezult) {
    $l1 = mysql_query("SELECT * FROM `$table` WHERE id_team = '" . $game['id_team1'] . "' LIMIT 1");
    $lrr1 = mysql_fetch_array($l1);
    
    $l2 = mysql_query("SELECT * FROM `$table` WHERE id_team = '" . $game['id_team2'] . "' LIMIT 1");
    $lrr2 = mysql_fetch_array($l2);

    if ($rezult[0] > $rezult[1]) {
        // Обновление для команды 1 (победа)
        $fields1 = [
            'igr' => $lrr1['igr'] + 1,
            'win' => $lrr1['win'] + 1,
            'gz' => $lrr1['gz'] + $rezult[0],
            'gp' => $lrr1['gp'] + $rezult[1],
            'raz' => $lrr1['raz'] + ($rezult[0] - $rezult[1]),
            'ochey' => $lrr1['ochey'] + 3
        ];
        
        // Обновление для команды 2 (поражение)
        $fields2 = [
            'igr' => $lrr2['igr'] + 1,
            'los' => $lrr2['los'] + 1,
            'gz' => $lrr2['gz'] + $rezult[1],
            'gp' => $lrr2['gp'] + $rezult[0],
            'raz' => $lrr2['raz'] + ($rezult[1] - $rezult[0])
        ];
    } 
    elseif ($rezult[1] > $rezult[0]) {
        // Обновление для команды 1 (поражение)
        $fields1 = [
            'igr' => $lrr1['igr'] + 1,
            'los' => $lrr1['los'] + 1,
            'gz' => $lrr1['gz'] + $rezult[0],
            'gp' => $lrr1['gp'] + $rezult[1],
            'raz' => $lrr1['raz'] + ($rezult[0] - $rezult[1])
        ];
        
        // Обновление для команды 2 (победа)
        $fields2 = [
            'igr' => $lrr2['igr'] + 1,
            'win' => $lrr2['win'] + 1,
            'gz' => $lrr2['gz'] + $rezult[1],
            'gp' => $lrr2['gp'] + $rezult[0],
            'raz' => $lrr2['raz'] + ($rezult[1] - $rezult[0]),
            'ochey' => $lrr2['ochey'] + 3
        ];
    } 
    else {
        // Обновление для ничьей
        $fields1 = [
            'igr' => $lrr1['igr'] + 1,
            'nn' => $lrr1['nn'] + 1,
            'gz' => $lrr1['gz'] + $rezult[0],
            'gp' => $lrr1['gp'] + $rezult[1],
            'raz' => $lrr1['raz'] + ($rezult[0] - $rezult[1]),
            'ochey' => $lrr1['ochey'] + 1
        ];
        
        $fields2 = [
            'igr' => $lrr2['igr'] + 1,
            'nn' => $lrr2['nn'] + 1,
            'gz' => $lrr2['gz'] + $rezult[1],
            'gp' => $lrr2['gp'] + $rezult[0],
            'raz' => $lrr2['raz'] + ($rezult[1] - $rezult[0]),
            'ochey' => $lrr2['ochey'] + 1
        ];
    }

    // Формирование SQL-запросов
    $update1 = "UPDATE `$table` SET ";
    foreach ($fields1 as $field => $value) {
        $update1 .= "`$field` = '$value', ";
    }
    $update1 = rtrim($update1, ', ') . " WHERE id = '" . $lrr1['id'] . "' LIMIT 1";
    
    $update2 = "UPDATE `$table` SET ";
    foreach ($fields2 as $field => $value) {
        $update2 .= "`$field` = '$value', ";
    }
    $update2 = rtrim($update2, ', ') . " WHERE id = '" . $lrr2['id'] . "' LIMIT 1";

    mysql_query($update1);
    mysql_query($update2);
}

// Вспомогательная функция для обновления групповых таблиц
function updateGroupTable($table, $game, $rezult) {
    $l1 = mysql_query("SELECT * FROM `$table` WHERE id_team = '" . $game['id_team1'] . "' LIMIT 1");
    $lrr1 = mysql_fetch_array($l1);
    
    $l2 = mysql_query("SELECT * FROM `$table` WHERE id_team = '" . $game['id_team2'] . "' LIMIT 1");
    $lrr2 = mysql_fetch_array($l2);

    if ($rezult[0] > $rezult[1]) {
        $fields1 = [
            'igr' => $lrr1['igr'] + 1,
            'win' => $lrr1['win'] + 1,
            'gz' => $lrr1['gz'] + $rezult[0],
            'gp' => $lrr1['gp'] + $rezult[1],
            'ochey' => $lrr1['ochey'] + 3
        ];
        
        $fields2 = [
            'igr' => $lrr2['igr'] + 1,
            'los' => $lrr2['los'] + 1,
            'gz' => $lrr2['gz'] + $rezult[1],
            'gp' => $lrr2['gp'] + $rezult[0]
        ];
    } 
    elseif ($rezult[1] > $rezult[0]) {
        $fields1 = [
            'igr' => $lrr1['igr'] + 1,
            'los' => $lrr1['los'] + 1,
            'gz' => $lrr1['gz'] + $rezult[0],
            'gp' => $lrr1['gp'] + $rezult[1]
        ];
        
        $fields2 = [
            'igr' => $lrr2['igr'] + 1,
            'win' => $lrr2['win'] + 1,
            'gz' => $lrr2['gz'] + $rezult[1],
            'gp' => $lrr2['gp'] + $rezult[0],
            'ochey' => $lrr2['ochey'] + 3
        ];
    } 
    else {
        $fields1 = [
            'igr' => $lrr1['igr'] + 1,
            'nn' => $lrr1['nn'] + 1,
            'gz' => $lrr1['gz'] + $rezult[0],
            'gp' => $lrr1['gp'] + $rezult[1],
            'ochey' => $lrr1['ochey'] + 1
        ];
        
        $fields2 = [
            'igr' => $lrr2['igr'] + 1,
            'nn' => $lrr2['nn'] + 1,
            'gz' => $lrr2['gz'] + $rezult[1],
            'gp' => $lrr2['gp'] + $rezult[0],
            'ochey' => $lrr2['ochey'] + 1
        ];
    }

    $update1 = "UPDATE `$table` SET ";
    foreach ($fields1 as $field => $value) {
        $update1 .= "`$field` = '$value', ";
    }
    $update1 = rtrim($update1, ', ') . " WHERE id = '" . $lrr1['id'] . "' LIMIT 1";
    
    $update2 = "UPDATE `$table` SET ";
    foreach ($fields2 as $field => $value) {
        $update2 .= "`$field` = '$value', ";
    }
    $update2 = rtrim($update2, ', ') . " WHERE id = '" . $lrr2['id'] . "' LIMIT 1";

    mysql_query($update1);
    mysql_query($update2);
}




 
 
 
 
 
 
require_once ("../incfiles/end.php");
//require_once ("end.php");
?>

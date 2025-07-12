<?php
define('_IN_JOHNCMS', 1);
$headmod = 'report';
require_once("../incfiles/core.php");

$textl = 'Игра';
$prefix = !empty($_GET['union']) ? '_union_' : '_';
$issetun = !empty($_GET['union']) ? '&amp;union=isset' : '';
$dirs = !empty($_GET['union']) ? '/union/' : '/';

// Получаем ID матча из запроса
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Защита от SQL-инъекций
$id = mysql_real_escape_string($id);

// Получаем данные матча
$q = mysql_query("SELECT * FROM `r".$prefix."game` WHERE id='$id' LIMIT 1");
if (!$q) {
    die('Ошибка запроса: ' . mysql_error());
}

$arr = mysql_fetch_array($q);

// Проверка времени матча
if (isset($arr['time']) && isset($realtime)) {
    $mt = ($realtime - $arr['time']) * 18;
    $mt = floor($mt / 60);
    if ($mt <= 93) {
        header("Location: /txt".$dirs.$id);
        exit;
    }
}

// Проверка наличия тактик
if (empty($arr['tactics1']) || empty($arr['tactics2'])) {
    header('Location: /game'.$dirs.$id);
    exit;
}

// Проверка существования матча
if (empty($arr['id'])) {
    echo display_error('Отчёт не найден');
    require_once("../incfiles/end.php");
    exit;
}

// Получаем данные команд
$q1 = mysql_query("SELECT * FROM `r_team` WHERE id='".$arr['id_team1']."' LIMIT 1");
$arr7 = mysql_fetch_array($q1);
$q2 = mysql_query("SELECT * FROM `r_team` WHERE id='".$arr['id_team2']."' LIMIT 1");
$arr77 = mysql_fetch_array($q2);

// Устанавливаем заголовок страницы
$textl = $arr7['name'] . ' - ' . $arr77['name'] . ' ' . $arr['rez1'] . ':' . $arr['rez2'];
require_once("../incfiles/head.php");

// Функция для определения названия кубка
function getCupName($kubok) {
    $cupNames = array(
        "1" => $c_1, "2" => $c_2, "3" => $c_3, "4" => $c_4, "5" => $c_5,
        "6" => $c_6, "7" => $c_7, "8" => $c_8, "9" => $c_9, "10" => $c_10,
        "11" => $c_11, "12" => $c_12, "13" => $c_13, "14" => $c_14, "15" => $c_15,
        "16" => $c_16, "17" => $c_17, "18" => $c_18, "19" => $c_19, "20" => $c_20,
        "21" => $c_21, "22" => $c_22, "23" => $c_23, "24" => $c_24, "25" => $c_25,
        "26" => $c_26, "27" => $c_27, "28" => $c_28, "29" => $c_29, "30" => $c_30,
        "31" => $c_31, "32" => $c_32, "33" => $c_33, "34" => $c_34, "35" => $c_35,
        "36" => $c_36, "37" => $c_37, "38" => $c_38, "39" => $c_39, "40" => $c_40,
        "41" => $c_41, "42" => $c_42, "43" => $c_43, "44" => $c_44, "45" => $c_45,
        "46" => $c_46, "47" => $c_47, "48" => $c_48, "49" => $c_49, "50" => $c_50,
        "cup_netto" => 'Кубок Нетто',
        "cup_charlton" => 'Кубок Чарльтона',
        "cup_muller" => 'Кубок Мюллера',
        "cup_puskas" => 'Кубок Пушкаша',
        "cup_fachetti" => 'Кубок Факкетти',
        "cup_kopa" => 'Кубок Копа',
        "cup_distefano" => 'Кубок Ди Стефано'
    );
    
    return isset($cupNames[$kubok]) ? $cupNames[$kubok] : '';
}

$c_name = getCupName($arr['kubok']);

// Отображение заголовка в зависимости от типа турнира
switch ($arr['chemp']) {
    case "super_cup":
    case "super_cup2":
        echo '<link rel="stylesheet" href="/theme/cups/super_cup.css" type="text/css" />';
        echo '<div class="phdr_le" style="text-align:left"><font color="white">'.$arr['kubok_nomi'].'</font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
        echo '<div class="phdr_le" style="text-align:left"><center><a href="/'.($arr['chemp'] == "super_cup" ? "super_cup" : "super_cup2").'/"><b>'.$arr['kubok_nomi'].'</b></a></center></div>';
        echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/cup/b_super_cup.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
        
    case "cup_en":
    case "cup_ru":
    case "cup_de":
    case "cup_pt":
    case "cup_es":
    case "cup_it":
    case "cup_fr":
    case "cup_nl":
        $country = str_replace("cup_", "", $arr['chemp']);
        echo '<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act='.$country.'">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
        echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/cup/b_'.$arr['id_kubok'].'.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
        
    case "cup_netto":
    case "cup_charlton":
    case "cup_muller":
    case "cup_puskas":
    case "cup_fachetti":
    case "cup_kopa":
    case "cup_distefano":
        $cup = str_replace("cup_", "", $arr['chemp']);
        echo '<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
        echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act='.$cup.'">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
        echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/cup/b_'.$arr['id_kubok'].'.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
        
    case "frend":
        echo '<a href="/friendly/" class="cardview x-pt-3 x-block-center x-rounded x-bg-cover x-onhover-wrapper" style="background-image: url(/images/cup/friendly.png);width: 75px;height: 75px;overflow: visible;" title="Перейти в кубок"></a>';
        echo '<div class="gmenu"><center><a href="/friendly/"><b>Товарищеский матч</b></a></center></div>';
        break;
        
    case "z_cup":
        echo '<div class="gmenu"><center><a href="/cup3/'.$arr['id_kubok'].'"><b>'.$arr['kubok_nomi'].'</b></a></center></div>';
        echo '<div class="gmenu"><center><img src="/images/cup/b_'.$arr['kubok'].'.png" alt="Кубок"/></center></div>';
        break;
        
    case "cup":
        echo '<div class="gmenu"><center><a href="/cup/'.$arr['id_kubok'].'"><b>'.$c_name.'</b></a></center></div>';
        break;
        
    case "unchamp":
        echo '<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />';
        echo '<div class="phdr_lk"><font color="white">'.$arr['kubok_nomi'].'</font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
        echo '<div class="phdr_lk"><center><a href="/union_champ/index.php?id='.$arr['id_kubok'].'"><b>'.$arr['kubok_nomi'].'</b></a></center></div>';
        echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/union/logo/cup'.$arr['id_kubok'].'.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
        
    case "champ":
        echo '<div class="phdr">Чемпионат<b class="rlink">'.date("d.m.Y H:i", $arr['time']).'</b></div>';
        echo '<div class="gmenu"><center><a href="/champ00/index.php?act='.$arr['kubok'].'"><b>'.$arr['kubok_nomi'].'</b></a></center></div>';
        echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/cup/b_00'.$arr['kubok'].'.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
        
    case "champ_retro":
        echo '<div class="gmenu"><center><a href="/champ_retro/index.php?act='.$arr['id_kubok'].'"><b>'.$arr['kubok_nomi'].'</b></a></center></div>';
        break;
        
    case "liga_r":
    case "liga_r2":
        echo '<link rel="stylesheet" href="/theme/cups/lc.css" type="text/css" />';
        echo '<div class="phdr_lc"><center><a href="/'.$arr['id_kubok'].'/"><b>'.$arr['kubok_nomi'].'</b></a></center></div>';
        echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
        
    case "le":
    case "kuefa2":
        echo '<link rel="stylesheet" href="/theme/cups/le.css" type="text/css" />';
        echo '<div class="phdr_le"><font color="white"><a href="/'.$arr['id_kubok'].'/"><b>'.$arr['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
        echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/cup/b_le.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
        
    case "maradona":
        echo '<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />';
        echo '<div class="gmenu"><center><a href="/'.$arr['id_kubok'].'/"><b></b></a></center></div>';
        echo '<div xmlns="http://www.w3.org/1999/xhtml" class="phdr_lk">'.$arr['kubok_nomi'].'</div>';
        echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/cup/b_cupcom.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
        
    case "lk":
        echo '<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />';
        echo '<div class="gmenu"><center><a href="/'.$arr['id_kubok'].'/"><b>'.$arr['kubok_nomi'].'</b></a></center></div>';
        echo '<div xmlns="http://www.w3.org/1999/xhtml" class="phdr_lk">Кубок</div>';
        echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        break;
        
    default:
        echo '<div class="gmenu"><center><img src="/images/cup/b_'.$arr['kubok'].'.png" alt="Кубок"/></center></div>';
        break;
}

echo '<div class="gmenu" style="text-align: center"><div style="font-weight: bold">'.date("d.m.Y H:i", $arr['time']).'</div>';

// Получаем данные судьи
$q1auy = mysql_query("SELECT * FROM `r_judge` WHERE `id`='".$arr['judge']."' LIMIT 1");
$aayr = mysql_fetch_array($q1auy);

echo '
<div class="game22">
    <div>
        <b><img src="/images/gen4/whistle.png" class="va" alt=""> Главный арбитр матча</b>
    </div>
    <div>
        <a href="/judge/index.php?id='.$aayr['id'].'"><span class="flags c_'.$aayr['flag'].'_18" style="vertical-align: middle;" title="'.$aayr['flag'].'"></span> '.$aayr['name'].'</a>
    </div>
</div>';

// Определяем размер стадиона
$stadium = 1;
if ($kom1['stadium'] > 74999) $stadium = 16;
elseif ($kom1['stadium'] > 69999) $stadium = 15;
elseif ($kom1['stadium'] > 64999) $stadium = 14;
elseif ($kom1['stadium'] > 59999) $stadium = 13;
elseif ($kom1['stadium'] > 54999) $stadium = 12;
elseif ($kom1['stadium'] > 49999) $stadium = 11;
elseif ($kom1['stadium'] > 44999) $stadium = 10;
elseif ($kom1['stadium'] > 39999) $stadium = 9;
elseif ($kom1['stadium'] > 34999) $stadium = 8;
elseif ($kom1['stadium'] > 29999) $stadium = 7;
elseif ($kom1['stadium'] > 24999) $stadium = 6;
elseif ($kom1['stadium'] > 19999) $stadium = 5;
elseif ($kom1['stadium'] > 14999) $stadium = 4;
elseif ($kom1['stadium'] > 9999) $stadium = 3;
elseif ($kom1['stadium'] > 4999) $stadium = 2;

// Отображение команд и счета
echo '<div style="display: flex; justify-content: space-around;">
    <a href="/team/'.$kom1['id'].'" class="x-color-black x-hover" style="align-items: center;display: flex;flex-direction: column;justify-content: center;flex-basis: 0;flex-grow: 1;">';
    
    if (!empty($kom1['logo'])) {
        echo '<img src="/manager/logo/big'.$kom1['logo'].'" alt="Logo"/>';
    } else {
        echo '<img src="/manager/logo/b_0.jpg" alt="Logo" width="37" />';
    }
    
    echo '<div class="x-py-2">'.$kom1['name'].'<br>';
    
    if ($kom1['id_admin'] > 0) {
        $us1 = mysql_query("SELECT * FROM `users` WHERE `id`=".$kom1['id_admin']." LIMIT 1");
        $uss1 = mysql_fetch_array($us1);
        
        if ($uss1['vip'] == 0) {
            echo '<span style="opacity:0.4"><img src="/images/ico/vip0_m.png" title="Базовый аккаунт" style="width: 12px;border: none;vertical-align: middle;">'.$uss1['name'].'</span>';
        } elseif ($uss1['vip'] == 1) {
            echo '<span style="opacity:0.4"><img src="/images/ico/vip1_m.png" title="Улучшенный Премиум-аккаунт" style="width: 12px;border: none;vertical-align: middle;">'.$uss1['name'].'</span>';
        } elseif ($uss1['vip'] == 2) {
            echo '<span style="opacity:0.4"><img src="/images/ico/vip2_m.png" title="Улучшенный VIP-аккаунт" style="width: 12px;border: none;vertical-align: middle;">'.$uss1['name'].'</span>';
        } elseif ($uss1['vip'] == 3) {
            echo '<span style="opacity:0.4"><img src="/images/ico/vip3_m.png" title="Представительский Gold-аккаунт" style="width: 12px;border: none;vertical-align: middle;">'.$uss1['name'].'</span>';
        }
    }
    
    echo '</div>
    </a>
    <div style="align-items: center;display: flex;flex-direction: column;justify-content: center;">
        <div class="x-font-150 x-color-red"><div class="x-font-bold">';
        
        $nat1 = $arr['rez1'] + $arr['per1'];
        $nat2 = $arr['rez2'] + $arr['per2'];
        
        if ($arr['rez1'] != '' || $arr['rez2'] != '') {
            echo '<td><font size="+3"><b>'.$arr['rez1'].'</b>:<b>'.$arr['rez2'].'</b></font>';
            
            if ($arr['rez1'] == $arr['rez2'] && in_array($arr['chemp'], array('cup', 'b_cup', 'z_cup', 'cup_continent', 'super_cup', 'super_cup2', 'cupcom', 'cup_netto', 'cup_charlton', 'cup_muller', 'cup_puskas', 'cup_fachetti', 'cup_kopa', 'cup_distefano', 'cup_garrinca', 'cup_ru', 'cup_en', 'cup_de', 'cup_pt', 'cup_es', 'cup_it', 'cup_fr', 'cup_nl'))) {
                if ($arr['pen1'] || $arr['pen2']) {
                    echo '<br/> (п. '.$arr['pen1'].':'.$arr['pen2'].')';
                }
            }
            
            if ($arr['per1'] || $arr['per2']) {
                if ($arr['per1'] == $arr['per2']) {
                    if ($arr['pen1'] || $arr['pen2']) {
                        echo '<br/> (пен. '.$arr['pen1'].':'.$arr['pen2'].')';
                    }
                }
            }
        }
        
        echo '</div><div class="x-font-75"></div>
        </div>
    </div>
    <a href="/team/'.$kom2['id'].'" class="x-color-black x-hover" style="align-items: center;display: flex;flex-direction: column;justify-content: center;flex-basis: 0;flex-grow: 1;">';
    
    if (!empty($kom2['logo'])) {
        echo '<img src="/manager/logo/big'.$kom2['logo'].'" alt="Logo"/>';
    } else {
        echo '<img src="/manager/logo/b_0.jpg" alt="Logo" width="37" />';
    }
    
    echo '<div class="x-py-2">'.$kom2['name'].'<br>';
    
    if ($kom2['id_admin'] > 0) {
        $us2 = mysql_query("SELECT * FROM `users` WHERE `id`=".$kom2['id_admin']." LIMIT 1");
        $uss2 = mysql_fetch_array($us2);
        
        if ($uss2['vip'] == 0) {
            echo '<span style="opacity:0.4"><img src="/images/ico/vip0_m.png" title="Базовый аккаунт" style="width: 12px;border: none;vertical-align: middle;">'.$uss2['name'].'</span>';
        } elseif ($uss2['vip'] == 1) {
            echo '<span style="opacity:0.4"><img src="/images/ico/vip1_m.png" title="Улучшенный Премиум-аккаунт" style="width: 12px;border: none;vertical-align: middle;">'.$uss2['name'].'</span>';
        } elseif ($uss2['vip'] == 2) {
            echo '<span style="opacity:0.4"><img src="/images/ico/vip2_m.png" title="Улучшенный VIP-аккаунт" style="width: 12px;border: none;vertical-align: middle;">'.$uss2['name'].'</span>';
        } elseif ($uss2['vip'] == 3) {
            echo '<span style="opacity:0.4"><img src="/images/ico/vip3_m.png" title="Представительский Gold-аккаунт" style="width: 12px;border: none;vertical-align: middle;">'.$uss2['name'].'</span>';
        }
    }
    
    echo '</div></a>
</div>';

// Отображение событий матча (голы, карточки)
if ($arr['teh_end'] != 1) {
    $menus = explode("\r\n", $arr['menus']);
    $menus1 = explode("\r\n", $arr['menus1']);
    $menus2 = explode("\r\n", $arr['menus2']);
    
    asort($menus);
    asort($menus1);
    asort($menus2);
    
    echo '<div class="textcols"><right>
        <div class="textcols-item">';
    
    // Голы первой команды
    foreach ($menus as $key => $val) {
        $menu = explode("|", $val);
        $lox1 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$menu[2]."'");
        $loxx1 = mysql_fetch_array($lox1);
        
        if ($loxx1['team'] == $kom1['id']) {
            echo '<table>
                <tbody><tr>                        
                <td>'.$menu[0].'’ <img src="/images/g.gif" alt="" style="vertical-align: middle;"></td>
                <td class="x-text-center"><a href="/player/'.$menu[2].'">'.$menu[3].' '.$menu[4].'</a></td>
                </tr></tbody></table>';
        }
    }
    
    // Желтые карточки первой команды
    if ($arr['chemp'] != 'frend') {
        foreach ($menus1 as $key => $val) {
            $menu = explode("|", $val);
            $lox1 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$menu[2]."'");
            $loxx1 = mysql_fetch_array($lox1);
            
            if ($loxx1['team'] == $kom1['id']) {
                echo '<table>
                    <tbody><tr>                        
                    <td>'.$menu[0].'’ <img src="/images/yc.png" alt="Желтая" style="vertical-align: middle;"></td>
                    <td class="x-text-center"><a href="/player/'.$menu[2].'">'.$menu[3].' '.$menu[4].'</a></td>
                    </tr></tbody></table>';
            }
        }
        
        // Красные карточки первой команды
        foreach ($menus2 as $key => $val) {
            $menu = explode("|", $val);
            $lox1 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$menu[2]."'");
            $loxx1 = mysql_fetch_array($lox1);
            
            if ($loxx1['team'] == $kom1['id']) {
                echo '<table>
                    <tbody><tr>                        
                    <td>'.$menu[0].'’ <img src="/images/rc.png" alt="Красная" style="vertical-align: middle;"></td>
                    <td class="x-text-center"><a href="/player/'.$menu[2].'">'.$menu[3].' '.$menu[4].'</a></td>
                    </tr></tbody></table>';
            }
        }
    }
    
    echo '</div>
        <div class="textcols-item">';
    
    // Голы второй команды
    foreach ($menus as $key => $val) {
        $menu = explode("|", $val);
        $lox2 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$menu[2]."'");
        $loxx2 = mysql_fetch_array($lox2);
        
        if ($loxx2['team'] == $kom2['id']) {
            echo '<table>
                <tbody><tr>        
                <td>'.$menu[0].'’ <img src="/images/g.gif" alt="" style="vertical-align: middle;"></td>
                <td class="x-text-left"><a href="/player/'.$menu[2].'">'.$menu[3].' '.$menu[4].'</a></td>
                </tr></tbody></table>';
        }
    }
    
    // Желтые карточки второй команды
    if ($arr['chemp'] != 'frend') {
        foreach ($menus1 as $key => $val) {
            $menu = explode("|", $val);
            $lox2 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$menu[2]."'");
            $loxx2 = mysql_fetch_array($lox2);
            
            if ($loxx2['team'] == $kom2['id']) {
                echo '<table>
                    <tbody><tr>        
                    <td>'.$menu[0].'’ <img src="/images/yc.png" alt="Желтая карточка" style="vertical-align: middle;"></td>
                    <td class="x-text-left"><a href="/player/'.$menu[2].'">'.$menu[3].' '.$menu[4].'</a></td>
                    </tr></tbody></table>';
            }
        }
        
        // Красные карточки второй команды
        foreach ($menus2 as $key => $val) {
            $menu = explode("|", $val);
            $lox2 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$menu[2]."'");
            $loxx2 = mysql_fetch_array($lox2);
            
            if ($loxx2['team'] == $kom2['id']) {
                echo '<table>
                    <tbody><tr>        
                    <td>'.$menu[0].'’ <img src="/images/rc.png" alt="Красная карточка" style="vertical-align: middle;"></td>
                    <td class="x-text-left"><a href="/player/'.$menu[2].'">'.$menu[3].' '.$menu[4].'</a></td>
                    </tr></tbody></table>';
            }
        }
    }
    
    echo '</div></right>
    </div>';
}

// Кнопки переключения между составом и статистикой
echo '<div style="display: flex; text-align: center; width: 100%; justify-content: center; align-items: center;">
    <div class="tab-p but head_button" type="button" id="addteam">Состав</div>
    <div class="tab-p but head_button" type="button" id="h2h">H2H</div>
</div>';

// Скрипт для переключения между вкладками
echo '<script>
$(function() {
    $(".but").on("click", function(e) {
        e.preventDefault();
        $(".content").hide();
        $("#"+this.id+"div").show();
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

.textcols {
    white-space: nowrap;
}

.textcols-item {
    display: inline-block;
    width: 30%;
    margin: 0 auto;
    vertical-align: top;
}

.textcols .textcols-item:first-child {
    display: inline-block;
    width: 55%;
    margin: 0 auto;
    vertical-align: top;
}

@media (min-width: 120px) and (max-width: 639px) {
    /* Мобильные стили */
}

@media (min-width: 640px) {
    #content {
        width: 45%;
        padding: 3% 4%;
        float: left;
    }
    
    #sidebar {
        width: 45%;
        float: right;
    }
    
    #content2 {
        width: 45%;
        padding: 3% 4%;
        float: left;
    }
    
    #sidebar2 {
        width: 45%;
        float: right;
    }
}
</style>';

// Получаем данные стадиона
$std11 = mysql_query("SELECT * FROM `r_stadium` WHERE `id`='".$arr['id_stadium']."'");
$std11 = mysql_fetch_array($std11);

// Отображение стадиона
if ($arr['id_stadium']) {
    if ($std11['std']) {
        echo '<div class="gmenu"><center><img src="/images/stadium/'.$arr['id_stadium'].'.jpg" alt="'.$std11['name'].'"/>';
    } else {
        echo '<div class="gmenu"><center><img src="/images/stadium/stadium.jpg" alt="'.$std11['name'].'"/>';
    }
} else {
    echo '<center><img src="/images/stadium/stadium.jpg" alt=""/>';
}

echo '<div class="error" style="max-width: 480px;">'.$arr['zritel'].' Зрителей на Стадионе <a href="/buildings/stadium.php?id='.$std11['id'].'"><b>'.$std11['name'].'</b></a></center></div>';

// Техническое поражение
if ($arr['teh_end'] == 1) {
    echo '<div class="info">В матче зафиксировано техническое поражение одной из команд. Матч отменён, победителем признана команда которая не нарушила регламент.</div>';
} else {
    // Кнопка просмотра трансляции
    echo '<div class="cardview-wrapper" bis_skin_checked="1">
        <a class="cardview" href="/txt'.$dirs.$id.'">
            <div class="left px50" bis_skin_checked="1"><i class="font-icon font-icon-whistle"></i></div>
            <div class="right px50 arrow" bis_skin_checked="1">
                <div class="text" bis_skin_checked="1">Посмотреть трансляцию</div>
            </div>
        </a>
    </div>';
}

// Вкладка с составом команд
echo '<div id="addteamdiv" class="content">
    <div class="cardview-wrapper3" bis_skin_checked="1">
        <table id="content" class="t-table x-text-center" style="margin: 0 auto;">
            <tr class="whiteheader"><th colspan="3"><b>'.$kom1['name'].'</b><th>Опыт</th></tr>';
            
            $players1 = explode("\r\n", $arr['players1']);
            $all1 = sizeof($players1);
            
            if ($all1) {
                for ($i = 0; $i < ($all1 - 1); $i++) {
                    $play1 = explode("|", $players1[$i]);
                    $reqs1 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$play1[1]."'");
                    $p1 = mysql_fetch_array($reqs1);
                    
                    $bg_color = "#FFF7E7";
                    if ($datauser['black'] == 0) {
                        if ($p1['line'] == '1') $bg_color = "#F5FFEF";
                        elseif ($p1['line'] == '2') $bg_color = "#E2FFD2";
                        elseif ($p1['line'] == '3') $bg_color = "#ccf3b5";
                        elseif ($p1['line'] == '4') $bg_color = "#b0ea8f";
                    }
                    
                    echo '<tr style="background:'.$bg_color.';">
                        <td>'.$play1[0].'</td>
                        <td style="text-align: left;"><a href="/player/'.$play1[1].'" style="width: 100%; display: block;">';
                        
                    $player = mysql_fetch_array(mysql_query("SELECT * FROM `r_player` WHERE id='".$play1[1]."' LIMIT 1"));
                    
                    if ($player['photo']) {
                        echo '<img src="/images/players/'.$player['photo'].'" width="25px" style="margin:-3px 0px;" alt=""/>';
                    } elseif ($player['line'] == '1') {
                        echo '<img src="/images/players/gk.png" width="25px" style="margin:-3px 0px;" alt=""/>';
                    } else {
                        echo '<img src="/images/players/cm.png" width="25px" style="margin:-3px 0px;" alt=""/>';
                    }
                    
                    echo $play1[2];
                    
                    // Отображение желтых карточек в зависимости от турнира
                    $game22 = mysql_fetch_array(mysql_query("SELECT * FROM `r".$prefix."game` WHERE id='$id' LIMIT 1"));
                    switch ($game22['chemp']) {
                        case "champ_retro":
                            if ($player['yc']) echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$player['yc'].'</div>';
                            break;
                            
                        case "unchamp":
                            if ($player['yc_unchamp']) echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$player['yc_unchamp'].'</div>';
                            break;
                            
                        case "liga_r":
                            if ($player['yc_liga_r']) echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$player['yc_liga_r'].'</div>';
                            break;
                            
                        case "le":
                            if ($player['yc_le']) echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$player['yc_le'].'</div>';
                            break;
                    }
                    
                    echo '</a></td>
                        <td style="font-weight: bold;"><th>'.$play1[3].'</td></th>
                    </tr>';
                }
            }
            
            echo '</table>
        <table id="sidebar" class="t-table x-text-center" style="margin: 0 auto;">
            <tr class="whiteheader"><th colspan="3"><b>'.$kom2['name'].'</b><th>Опыт</th></tr>';
            
            $players2 = explode("\r\n", $arr['players2']);
            $all2 = sizeof($players2);
            
            if ($all2) {
                for ($i = 0; $i < ($all2 - 1); $i++) {
                    $play2 = explode("|", $players2[$i]);
                    $reqs2 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$play2[1]."'");
                    $p2 = mysql_fetch_array($reqs2);
                    
                    $bg_color = "#FFF7E7";
                    if ($datauser['black'] == 0) {
                        if ($p2['line'] == '1') $bg_color = "#F5FFEF";
                        elseif ($p2['line'] == '2') $bg_color = "#E2FFD2";
                        elseif ($p2['line'] == '3') $bg_color = "#ccf3b5";
                        elseif ($p2['line'] == '4') $bg_color = "#b0ea8f";
                    }
                    
                    echo '<tr style="background:'.$bg_color.';">
                        <td>'.$play2[0].'</td>
                        <td style="text-align: left;"><a href="/player/'.$play2[1].'" style="width: 100%; display: block;">';
                        
                    $player = mysql_fetch_array(mysql_query("SELECT * FROM `r_player` WHERE id='".$play2[1]."' LIMIT 1"));
                    
                    if ($player['photo']) {
                        echo '<img src="/images/players/'.$player['photo'].'" width="25px" style="margin:-3px 0px;" alt=""/>';
                    } elseif ($player['line'] == '1') {
                        echo '<img src="/images/players/gk.png" width="25px" style="margin:-3px 0px;" alt=""/>';
                    } else {
                        echo '<img src="/images/players/cm.png" width="25px" style="margin:-3px 0px;" alt=""/>';
                    }
                    
                    echo $play2[2];
                    
                    // Отображение желтых карточек в зависимости от турнира
                    $game22 = mysql_fetch_array(mysql_query("SELECT * FROM `r".$prefix."game` WHERE id='$id' LIMIT 1"));
                    switch ($game22['chemp']) {
                        case "champ_retro":
                            if ($player['yc']) echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$player['yc'].'</div>';
                            break;
                            
                        case "unchamp":
                            if ($player['yc_unchamp']) echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$player['yc_unchamp'].'</div>';
                            break;
                            
                        case "liga_r":
                            if ($player['yc_liga_r']) echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$player['yc_liga_r'].'</div>';
                            break;
                            
                        case "le":
                            if ($player['yc_le']) echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$player['yc_le'].'</div>';
                            break;
                    }
                    
                    echo '</a></td>
                        <td style="font-weight: bold;"><th>'.$play2[3].'</td></th>
                    </tr>';
                }
            }
            
            echo '</table>
    </div>
</div>';

// Вкладка с историей матчей (H2H)
echo '<div id="h2hdiv" class="content">
    <div class="phdr">Последние игры: '.$arr7['name'].'</div>
    <div class="c">
        <table id="example">
            <tbody>';
            
    $q = mysql_query("SELECT * FROM `r_game` WHERE `id_team1`='".$arr7['id']."' OR `id_team2`='".$arr7['id']."' ORDER BY time DESC LIMIT 5");
    
    if (mysql_num_rows($q) <= 0) {
        echo '<div class="content_empty" bis_skin_checked="1">
                <img src="/images/no_report.png" alt="x" style="vertical-align:middle">
                <p>Последних 15 матчей не было</p>
            </div>';
    } else {
        $i = 0;
        while ($res = mysql_fetch_array($q)) {
            $k1 = mysql_query("SELECT * FROM `r_team` WHERE id='".$res['id_team1']."' LIMIT 1");
            $kom1 = mysql_fetch_array($k1);
            
            $k2 = mysql_query("SELECT * FROM `r_team` WHERE id='".$res['id_team2']."' LIMIT 1");
            $kom2 = mysql_fetch_array($k2);
            
            echo ($i % 2 == 0) ? '<tr class="oddrows">' : '<tr class="evenrows">';
            echo '<td width="20%" align="center">';
            echo (date("d.m.y", $res['time']) == date("d.m.y", $realtime)) ? '<span style="color:#A9A9A9;"><span class="today">Сегодня</span></span>' : date("d.m.y", $res['time']);
            echo '</td>
                <td>
                    <span class="flags c_'.$kom1['flag'].'_14" title="'.$kom1['flag'].'"></span> 
                    <a href="/team/'.$res['id_team1'].'">'.($res['rez1'] > $res['rez2'] ? '<b>'.$kom1['name'].'</b>' : $kom1['name']).'</a>
                    - 
                    <span class="flags c_'.$kom2['flag'].'_14" title="'.$kom2['flag'].'"></span> 
                    <a href="/team/'.$res['id_team2'].'">'.($res['rez2'] > $res['rez1'] ? '<b>'.$kom2['name'].'</b>' : $kom2['name']).'</a>
                </td>
                <td width="15%">
                    <center>';
                    
            if (!empty($res['rez1']) || !empty($res['rez2']) || $res['rez1'] == '0' || $res['rez2'] == '0') {
                echo '<a href="/report/'.$res['id'].'"><font color="green"><b>'.$res['rez1'].':'.$res['rez2'].'</b></font></a>';
            } else {
                echo '<a href="/game/'.$res['id'].'"><font color="green"><b>?:?</b></font></a>';
            }
            
            echo '</center>
                </td>
                <td align="center">';
                
            if ($res['rez2'] > $res['rez1']) {
                echo '<div style="border-radius: 5px;min-width: 20px;min-height: 20px;width: 20px;height: 20px;background-color: #DD2729;text-align: center;"><span style="line-height: 22px;font-weight: bold;text-align: center; border-radius: 2px; color: white;">П</span></div>';
            } elseif ($res['rez1'] > $res['rez2']) {
                echo '<div style="border-radius: 5px;min-width: 20px;min-height: 20px;width: 20px;height: 20px;background-color: #15BB16;text-align: center;"><span style="width: 20px;height: 20px;min-width: 20px;min-height: 20px;line-height: 22px;font-weight: bold;text-align: center; border-radius: 2px; color: white;">В</span></div>';
            } elseif ($res['rez1'] == $res['rez2']) {
                echo '<div style="border-radius: 5px;min-width: 20px;min-height: 20px;width: 20px;height: 20px;background-color: #F4A62E;text-align: center;"><span style="min-width: 20px;min-height: 20px;width: 20px;height: 20px;line-height: 22px;font-weight: bold;text-align: center; border-radius: 2px; color: white;">Н</span></div>';
            }
            
            echo '</td>
            </tr>';
            
            $i++;
        }
    }
    
    echo '</tbody>
        </table>
    </div>
    
    <div class="phdr">Последние игры: '.$arr77['name'].'</div>
    <div class="c">
        <table id="example">
            <tbody>';
            
    $q = mysql_query("SELECT * FROM `r_game` WHERE `id_team1`='".$arr77['id']."' OR `id_team2`='".$arr77['id']."' ORDER BY time DESC LIMIT 5");
    
    if (mysql_num_rows($q) <= 0) {
        echo '<div class="content_empty" bis_skin_checked="1">
                <img src="/images/no_report.png" alt="x" style="vertical-align:middle">
                <p>Последних 15 матчей не было</p>
            </div>';
    } else {
        $i = 0;
        while ($res = mysql_fetch_array($q)) {
            $k1 = mysql_query("SELECT * FROM `r_team` WHERE id='".$res['id_team1']."' LIMIT 1");
            $kom1 = mysql_fetch_array($k1);
            
            $k2 = mysql_query("SELECT * FROM `r_team` WHERE id='".$res['id_team2']."' LIMIT 1");
            $kom2 = mysql_fetch_array($k2);
            
            echo ($i % 2 == 0) ? '<tr class="oddrows">' : '<tr class="evenrows">';
            echo '<td width="20%" align="center">';
            echo (date("d.m.y", $res['time']) == date("d.m.y", $realtime)) ? '<span style="color:#A9A9A9;"><span class="today">Сегодня</span></span>' : date("d.m.y", $res['time']);
            echo '</td>
                <td>
                    <span class="flags c_'.$kom2['flag'].'_14" title="'.$kom2['flag'].'"></span> 
                    <a href="/team/'.$res['id_team2'].'">'.($res['rez2'] > $res['rez1'] ? '<b>'.$kom2['name'].'</b>' : $kom2['name']).'</a>
                    - 
                    <span class="flags c_'.$kom1['flag'].'_14" title="'.$kom1['flag'].'"></span> 
                    <a href="/team/'.$res['id_team1'].'">'.($res['rez1'] > $res['rez2'] ? '<b>'.$kom1['name'].'</b>' : $kom1['name']).'</a>
                </td>
                <td width="15%">
                    <center>';
                    
            if (!empty($res['rez1']) || !empty($res['rez2']) || $res['rez1'] == '0' || $res['rez2'] == '0') {
                echo '<a href="/report/'.$res['id'].'"><font color="green"><b>'.$res['rez2'].':'.$res['rez1'].'</b></font></a>';
            } else {
                echo '<a href="/game/'.$res['id'].'"><font color="green"><b>?:?</b></font></a>';
            }
            
            echo '</center>
                </td>
                <td align="center">';
                
            if ($res['rez1'] > $res['rez2']) {
                echo '<div style="border-radius: 5px;min-width: 20px;min-height: 20px;width: 20px;height: 20px;background-color: #DD2729;text-align: center;"><span style="line-height: 22px;font-weight: bold;text-align: center; border-radius: 2px; color: white;">П</span></div>';
            } elseif ($res['rez2'] > $res['rez1']) {
                echo '<div style="border-radius: 5px;min-width: 20px;min-height: 20px;width: 20px;height: 20px;background-color: #15BB16;text-align: center;"><span style="width: 20px;height: 20px;min-width: 20px;min-height: 20px;line-height: 22px;font-weight: bold;text-align: center; border-radius: 2px; color: white;">В</span></div>';
            } elseif ($res['rez1'] == $res['rez2']) {
                echo '<div style="border-radius: 5px;min-width: 20px;min-height: 20px;width: 20px;height: 20px;background-color: #F4A62E;text-align: center;"><span style="min-width: 20px;min-height: 20px;width: 20px;height: 20px;line-height: 22px;font-weight: bold;text-align: center; border-radius: 2px; color: white;">Н</span></div>';
            }
            
            echo '</td>
            </tr>';
            
            $i++;
        }
    }
    
    echo '</tbody>
        </table>
    </div>
    
    <div class="phdr">Очные встречи: '.$arr7['name'].'-'.$arr77['name'].'</div>
    <div class="c">
        <table id="example">
            <tbody>';
            
    $qqo1 = mysql_query("SELECT * FROM `r_game` WHERE (`id_team1`='".$arr7['id']."' AND `id_team2`='".$arr77['id']."' AND (`rez1`!='' OR `rez2`!='')) OR (`id_team2`='".$arr7['id']."' AND `id_team1`='".$arr77['id']."' AND (`rez1`!='' OR `rez2`!='')) ORDER BY time DESC LIMIT 5");
    $totalfsss = mysql_num_rows($qqo1);
    
    if ($totalfsss) {
        $i = 0;
        while ($res = mysql_fetch_array($qqo1)) {
            $k1 = mysql_query("SELECT * FROM `r_team` WHERE id='".$res['id_team1']."' LIMIT 1");
            $kom1 = mysql_fetch_array($k1);
            
            $k2 = mysql_query("SELECT * FROM `r_team` WHERE id='".$res['id_team2']."' LIMIT 1");
            $kom2 = mysql_fetch_array($k2);
            
            echo ($i % 2 == 0) ? '<tr class="oddrows">' : '<tr class="evenrows">';
            echo '<td width="20%" align="center">';
            echo (date("d.m.y", $res['time']) == date("d.m.y", $realtime)) ? '<span style="color:#A9A9A9;"><span class="today">Сегодня</span></span>' : date("d.m.y", $res['time']);
            echo '</td>
                <td>
                    <span class="flags c_'.$kom1['flag'].'_14" title="'.$kom1['flag'].'"></span>
                    <a href="/team/'.$res['id_team1'].'">'.($res['rez1'] > $res['rez2'] ? '<b>'.$kom1['name'].'</b>' : ''.$kom1['name'].'').'</a>
                    - 
                    <span class="flags c_'.$kom2['flag'].'_14" title="'.$kom2['flag'].'"></span>
                    <a href="/team/'.$res['id_team2'].'">'.($res['rez2'] > $res['rez1'] ? '<b>'.$kom2['name'].'</b>' : ''.$kom2['name'].'').'</a>
                </td>
                <td width="15%">
                    <center>';
                    
            if (!empty($res['rez1']) || !empty($res['rez2']) || $res['rez1'] == '0' || $res['rez2'] == '0') {
                echo '<a href="/report/'.$res['id'].'"><font color="green"><b>'.$res['rez1'].':'.$res['rez2'].'</b></font></a>';
            } else {
                echo '<a href="/game/'.$res['id'].'"><font color="green"><b>?:?</b></font></a>';
            }
            
            echo '</center>
                </td>
            </tr>';
            
            $i++;
        }
    } else {
        echo '<div class="game-ui__history">
                <div style="font-size:140%;">
                    История противостояния
                    <span class="green">'.$arr7['name'].' - '.$arr77['name'].'</span>
                </div>
                <div id="history-prematch" style="margin-bottom:20px;"><br>Данная статистика доступна для владельцев <a href="/vip.php?action=compare&amp;type=1"><img src="/images/ico/vip1.png" title="Улучшенный Премиум-аккаунт" style="width: 40px;border: none;vertical-align: middle;"></a></div>
            </div>';
    }
    
    echo '</tbody>
        </table>
    </div>
</div>';

// Статистика игры
$bb = mysql_query("SELECT * FROM `news_2` WHERE `tid`='$id' ORDER BY `time` DESC");
if (mysql_num_rows($bb) > 0) {
    echo '<div class="gmenu">Статистика игры:</div>';
    
    $i = 0;
    while ($bb1 = mysql_fetch_assoc($bb)) {
        echo (ceil(ceil($i / 2) - ($i / 2)) == 0) ? '<div class="list1">' : '<div class="list2">';
        $menu = explode("|", $bb1['news']);
        echo '<img src="/imgages/txt/m_'.$menu[1].'.gif" alt=""/> '.$menu[2].'</div>';
        $i++;
    }
}

require_once("../incfiles/end.php");
?>

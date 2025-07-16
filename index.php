<?php
define('_IN_JOHNCMS', 1);
$headmod = 'game';
$textl = 'Игра ' . (isset($arr['kubok_nomi']) ? $arr['kubok_nomi'] : '');

require_once("../incfiles/core.php");
require_once("../incfiles/head.php");
require_once("../game/func_game.php");

// Инициализация переменных
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$act = isset($_GET['act']) ? $_GET['act'] : '';
$datauser = isset($datauser) ? $datauser : [];
$realtime = time(); // Добавлена инициализация

$prefix = !empty($_GET['union']) ? '_union_' : '_';
$issetun = !empty($_GET['union']) ? '&amp;union=isset' : '';
$dirs = !empty($_GET['union']) ? '/union/' : '/';

// Запрос к базе с использованием MySQLi
// Стало (используем параметризованные запросы):
$stmt = $db->prepare("SELECT * FROM `r{$prefix}game` WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$g = $stmt->get_result();
$game = $g ? mysqli_fetch_array($g) : [];

// JavaScript для переключения вкладок
?>
<script>
$(function() {
  $(".but").on("click", function(e) {
    e.preventDefault();
    $(".content").hide();
    $("#" + this.id + "div").show();
  });
});
</script>
<style>
.content { display:none }

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

div.team_name2 {
    white-space: nowrap;
    max-width: 50px;
    overflow: hidden;
    text-overflow: ellipsis;
}

#pagewrap {
   width: 720px;
   margin: 0 auto;
}
@media screen and (max-width: 980px) {
   #pagewrap {
      width: 95%;
   }
}
@media screen and (max-width: 650px) {
  #pagewrap {
      width: 70%;
}}
   
@media screen and (max-width: 480px) {
   #pagewrap {
      width: 55%;
}}
   

.game-ui__history {
    background: var(--game-history);
    border-top: 2px solid var(--primary-color-border);
    padding: 8px;
    overflow: hidden;
}
</style>

<?php

// Основная логика
if (empty($game)) {
    echo '<div class="rmenu">Игра не найдена</div>';
    require_once("../incfiles/end.php");
    exit;
}

// Перемещено выше: проверка счета матча ДО вывода судьи
if (!empty($game['rez1']) || !empty($game['rez2']) || $game['rez1'] == '0' || $game['rez2'] == '0') {
    header('location: /report'.$dirs.''.$id);
    exit;
}

// Проверка отмены игры
if (empty($game['id']) || empty($game['id_team1']) || empty($game['id_team2'])) {
    echo '<div class="cardview-wrapper x-overlay" id="errorMsg">
        <div class="cardview">
            <div class="x-row">
                <div class="x-col-1 x-vh-center x-font-250 x-color-white x-bg-green">
                    <i class="font-icon">!</i>
                </div>
                <div class="x-col-5 x-font-bold x-p-3">
                    Игра отменена<div class="x-pt-3">
                    <a class="mbtn mbtn-green" onclick="toggleVisibility(\'errorMsg\');">Закрыть</a>
                </div>
                </div>
            </div>
        </div>
    </div>';
    require_once ("../incfiles/end.php");
    exit;
}

$team1_id = (int)$game['id_team1'];
$team2_id = (int)$game['id_team2'];

// Функция автозаполнения состава
function complite_team($db, $team_id) {
    $team = [];
    $sql = mysqli_query($db, "SELECT * FROM `r_team` WHERE `id` = '" . (int)$team_id . "' LIMIT 1");
    if ($sql && mysqli_num_rows($sql)) {
        $team = mysqli_fetch_assoc($sql);
        
        for ($i = 1; $i <= 11; $i++) {
            if (empty($team['i' . $i])) {
                $line_condition = "";
                if ($i == 1) $line_condition = " AND `line` = '1'";
                elseif ($i >= 2 && $i <= 4) $line_condition = " AND `line` = '2'";
                elseif ($i >= 5 && $i <= 9) $line_condition = " AND `line` = '3'";
                elseif ($i >= 10 && $i <= 11) $line_condition = " AND `line` = '4'";
                
                $player_query = mysqli_query($db, 
                    "SELECT `id` FROM `r_player` 
                    WHERE `team` = '" . (int)$team_id . "' 
                    AND `sostav` = '0' 
                    $line_condition 
                    ORDER BY `rm` DESC LIMIT 1"
                );
                
                if (!$player_query || !mysqli_num_rows($player_query)) {
                    $player_query = mysqli_query($db, 
                        "SELECT `id` FROM `r_player` 
                        WHERE `team` = '" . (int)$team_id . "' 
                        AND `sostav` = '0' 
                        AND `line` != '1' 
                        ORDER BY `rm` LIMIT 1"
                    );
                }
                
                if ($player_query && mysqli_num_rows($player_query)) {
                    $player = mysqli_fetch_assoc($player_query);
                    if ($player) {
                        mysqli_query($db, "UPDATE `r_team` SET `i$i` = '" . (int)$player['id'] . "' WHERE `id` = '" . (int)$team_id . "' LIMIT 1");
                        mysqli_query($db, "UPDATE `r_team` SET `i$i` = '' WHERE `id` != '" . (int)$team_id . "' AND `i$i` = '" . (int)$player['id'] . "'");
                        mysqli_query($db, "UPDATE `r_player` SET `sostav` = '1' WHERE `id` = '" . (int)$player['id'] . "' LIMIT 1");
                    }
                }
            }
        }
    }
    return $team;
}

// Заполняем составы команд
$team1 = complite_team($db, $team1_id);
$team2 = complite_team($db, $team2_id);

// Получаем данные команд для вывода
$kom1 = $team1;
$kom2 = $team2;

$q1auy = mysqli_query($db, "SELECT * FROM `r_judge` WHERE `id`='".mysqli_real_escape_string($db, $game['judge'])."' LIMIT 1");
$aayr = $q1auy ? mysqli_fetch_array($q1auy) : [];
            
if ($aayr) {
    echo '
    <div class="game22 game-ui__referee">
        <div>
            <b><img src="/images/gen4/whistle.png" class="va" alt=""> Главный арбитр матча</b>
        </div>
        <div>
            <a href="/judge/index.php?id='.$aayr['id'].'"><span class="flags c_'.$aayr['flag'].'_18" style="vertical-align: middle;" title="'.$aayr['flag'].'"></span> '.$aayr['name'].'</a>
        </div>
    </div>';
}

// Восстановление физики и морали для команд
function restore_team_condition($db, $team_id, $realtime) {
    $req = mysqli_query($db, "SELECT * FROM `r_player` WHERE `team`='".(int)$team_id."'");
    while ($arr = mysqli_fetch_array($req)) {
        if ($arr['fiz'] < 100 || $arr['mor'] != '0') {
            $rrr = ceil(($realtime - $arr['time'])/900);
            
            $fiza = $arr['fiz'] + $rrr;
            if ($fiza > 100) $fiza = 100;
            
            if ($arr['mor'] < 0) {
                $mor = $arr['mor'] + $rrr;
                if ($mor > 0) $mor = 0;
            } elseif ($arr['mor'] > 0) {
                $mor = $arr['mor'] - $rrr;
                if ($mor < 0) $mor = 0;
            } else {
                $mor = 0;
            }
            
            $rmm = ceil($arr['mas']/100*$fiza);
            mysqli_query($db, "UPDATE `r_player` SET `fiz`='" . $fiza . "', `mor`='" . $mor . "', `rm`='" . $rmm . "' WHERE id='" . $arr['id'] . "' LIMIT 1");
        }
        
        if (($realtime - $arr['time']) > 30*3600*20) {
            $voz = $arr['voz']+1;
            mysqli_query($db, "UPDATE `r_player` SET `time`='" . $realtime . "', `voz`='" . $voz . "' WHERE id='" . $arr['id'] . "' LIMIT 1");
        }
    }
}

// Проверка времени восстановления
if ($game['time'] < ($realtime - 900)) {
    restore_team_condition($db, $game['id_team1'], $realtime);
    restore_team_condition($db, $game['id_team2'], $realtime);
}

if ($act == "add") {
    if ($datauser['manager2'] == $game['id_team1']) {
        mysqli_query($db, "UPDATE `r".$prefix."game` SET `go1`='1' WHERE id='" . (int)$id . "' LIMIT 1");
    } elseif ($datauser['manager2'] == $game['id_team2']) {
        mysqli_query($db, "UPDATE `r".$prefix."game` SET `go2`='1' WHERE id='" . (int)$id . "' LIMIT 1");
    }
    
    header('location: /game'.$dirs.''.$id);
    exit;
}

// Если нет подтверждения от команд
if ($game['go1'] != 1 || $game['go2'] != 1) {
    if ($game['time'] > $realtime) {
        $ostime = $game['time']-$realtime;
        $q1 = mysqli_query($db, "SELECT * FROM `r_team` WHERE id='" . (int)$game['id_team1'] . "' LIMIT 1");
        $arr1 = $q1 ? mysqli_fetch_array($q1) : [];
        
        $q2 = mysqli_query($db, "SELECT * FROM `r_team` WHERE id='" . (int)$game['id_team2'] . "' LIMIT 1");
        $arr2 = $q2 ? mysqli_fetch_array($q2) : [];
        
        $k1 = mysqli_query($db, "SELECT * FROM `r_team` WHERE id='" . (int)$game['id_team1'] . "' LIMIT 1");
        $kom1 = $k1 ? mysqli_fetch_array($k1) : [];
        
        $k2 = mysqli_query($db, "SELECT * FROM `r_team` WHERE id='" . (int)$game['id_team2'] . "' LIMIT 1");
        $kom2 = $k2 ? mysqli_fetch_array($k2) : [];
        
        // Определение уровня стадиона
        $stadium_levels = [
            75000 => 16,
            70000 => 15,
            65000 => 14,
            60000 => 13,
            55000 => 12,
            50000 => 11,
            45000 => 10,
            40000 => 9,
            35000 => 8,
            30000 => 7,
            25000 => 6,
            20000 => 5,
            15000 => 4,
            10000 => 3,
            5000 => 2,
            0 => 1
        ];
        
        $stadium = 1;
        foreach ($stadium_levels as $capacity => $level) {
            if ($kom['stadium'] > $capacity) {
                $stadium = $level;
                break;
            }
        }
        
        // Определение названия кубка
        $cup_names = [
            '1' => 'Кубок чемпионов',
            '2' => 'Кубок обладателей кубков',
            '3' => 'Кубок УЕФА',
            '4' => 'Суперкубок Европы',
            '5' => 'Межконтинентальный кубок',
            '6' => 'Кубок чемпионов Азии',
            '7' => 'Кубок чемпионов Африки',
            '8' => 'Кубок чемпионов Северной Америки',
            '9' => 'Кубок чемпионов Южной Америки',
            '10' => 'Кубок Либертадорес',
            '11' => 'Кубок Америки',
            '12' => 'Кубок чемпионов Океании',
            '13' => 'Золотой кубок КОНКАКАФ',
            '14' => 'Кубок африканских наций',
            '15' => 'Кубок Азии',
            '16' => 'Кубок наций Океании',
            '17' => 'Золотой кубок',
            '18' => 'Кубок конфедераций',
            '19' => 'Чемпионат мира',
            '20' => 'Чемпионат Европы',
            '21' => 'Чемпионат Азии',
            '22' => 'Чемпионат Африки',
            '23' => 'Чемпионат Северной Америки',
            '24' => 'Чемпионат Южной Америки',
            '25' => 'Чемпионат Океании',
            '26' => 'Кубок чемпионов СНГ',
            '27' => 'Кубок чемпионов Балтии',
            '28' => 'Кубок чемпионов Скандинавии',
            '29' => 'Кубок чемпионов Бенилюкса',
            '30' => 'Кубок чемпионов Балкан',
            '31' => 'Кубок чемпионов Центральной Европы',
            '32' => 'Кубок чемпионов Восточной Европы',
            '33' => 'Кубок чемпионов Западной Европы',
            '34' => 'Кубок чемпионов Южной Европы',
            '35' => 'Кубок чемпионов Северной Европы',
            '36' => 'Кубок чемпионов Средиземноморья',
            '37' => 'Кубок чемпионов Балтийского моря',
            '38' => 'Кубок чемпионов Северного моря',
            '39' => 'Кубок чемпионов Атлантики',
            '40' => 'Кубок чемпионов Альп',
            '41' => 'Кубок чемпионов Пиренеев',
            '42' => 'Кубок чемпионов Карпат',
            '43' => 'Кубок чемпионов Апеннин',
            '44' => 'Кубок чемпионов Британских островов',
            '45' => 'Кубок чемпионов Иберии',
            '46' => 'Кубок чемпионов Скандинавии и Балтии',
            '47' => 'Кубок чемпионов Центральной Америки',
            '48' => 'Кубок чемпионов Карибского бассейна',
            '49' => 'Кубок чемпионов Анд',
            '50' => 'Кубок чемпионов Гвиан',
            '60' => 'Кубок Интертото',
            '61' => 'Суперкубок Англии',
            '62' => 'Суперкубок Испании',
            '63' => 'Суперкубок Италии',
            '64' => 'Суперкубок Германии',
            '65' => 'Суперкубок Франции',
            '66' => 'Суперкубок Португалии',
            '67' => 'Суперкубок Нидерландов',
            '68' => 'Суперкубок Бельгии',
            '69' => 'Суперкубок Шотландии',
            '70' => 'Суперкубок Турции',
            '71' => 'Суперкубок Греции',
            '72' => 'Суперкубок России',
            '73' => 'Суперкубок Украины',
            '74' => 'Суперкубок Беларуси',
            '75' => 'Суперкубок Польши',
            '76' => 'Суперкубок Чехии',
            '77' => 'Суперкубок Австрии',
            '78' => 'Суперкубок Швейцарии',
            '79' => 'Суперкубок Швеции',
            '80' => 'Суперкубок Норвегии',
            '81' => 'Суперкубок Дании',
            '82' => 'Суперкубок Финляндии',
            '83' => 'Суперкубок Румынии',
            '84' => 'Суперкубок Болгарии',
            '85' => 'Суперкубок Сербии',
            '86' => 'Суперкубок Хорватии',
            '87' => 'Суперкубок Словении',
            '88' => 'Суперкубок Словакии',
            '89' => 'Суперкубок Венгрии',
            '90' => 'Суперкубок Боснии',
            '91' => 'Суперкубок Черногории',
            '92' => 'Суперкубок Македонии',
            '93' => 'Суперкубок Албании',
            '94' => 'Суперкубок Литвы',
            '95' => 'Суперкубок Латвии',
            '96' => 'Суперкубок Эстонии',
            '97' => 'Суперкубок Молдовы',
            '98' => 'Суперкубок Грузии',
            '99' => 'Суперкубок Армении',
            '100' => 'Суперкубок Азербайджана',
            '101' => 'Суперкубок Казахстана',
            '102' => 'Суперкубок Узбекистана',
            '103' => 'Суперкубок Туркменистана',
            '104' => 'Суперкубок Кыргызстана',
            '105' => 'Суперкубок Таджикистана',
            '106' => 'Суперкубок Ирана',
            '107' => 'Суперкубок Ирака',
            '108' => 'Суперкубок Саудовской Аравии',
            '109' => 'Кубок вызова',
            '150' => 'Кубок легенд',
            '151' => 'Кубок ветеранов',
            '152' => 'Кубок молодёжи',
            '153' => 'Кубок надежд',
            '154' => 'Кубок будущего',
            '155' => 'Кубок талантов',
            '156' => 'Кубок звёзд',
            '157' => 'Кубок мастеров',
            '158' => 'Кубок профессионалов',
            '159' => 'Кубок любителей',
            '160' => 'Кубок новичков',
            'cup_netto' => 'Кубок Нетто',
            'cup_charlton' => 'Кубок Чарльтона',
            'cup_en' => 'Кубок Англии',
            'cup_muller' => 'Кубок Мюллера',
            'cup_puskas' => 'Кубок Пушкаша',
            'cup_fachetti' => 'Кубок Факкетти',
            'cup_kopa' => 'Кубок Копа',
            'cup_distefano' => 'Кубок Ди Стефано'
        ];
        
        $c_name = isset($cup_names[$game['kubok']]) ? $cup_names[$game['kubok']] : 'Неизвестный кубок';
        
        // Общие стили и структура для большинства случаев
        function renderCommonCup($game, $type, $link, $css, $imgPrefix = 'b_') {
            echo '<link rel="stylesheet" href="/theme/cups/' . $css . '.css" type="text/css" />';
            echo '<div class="phdr_' . $type . '" style="text-align:left"><font color="white"><a href="' . $link . '">' . $game['kubok_nomi'] . '</a></font><b class="rlink"><font color="white">' . date("d.m.Y H:i", $game['time']) . '</b></font></div>';
            echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
            echo '<img src="/images/cup/' . $imgPrefix . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
        }

        // Специальные случаи
        switch ($game['chemp']) {
            // Национальные кубки
            case "cup_en":
            case "cup_ru":
            case "cup_de":
            case "cup_pt":
            case "cup_es":
            case "cup_it":
            case "cup_fr":
            case "cup_nl":
                $act = str_replace('cup_', '', $game['chemp']);
                echo '<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
                echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=' . $act . '">' . $game['kubok_nomi'] . '</a></font><b class="rlink"><font color="white">' . date("d.m.Y H:i", $game['time']) . '</b></font></div>';
                echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
                echo '<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
                break;
                
            // Исторические кубки
            case "cup_netto":
            case "cup_charlton":
            case "cup_muller":
            case "cup_puskas":
            case "cup_fachetti":
            case "cup_kopa":
            case "cup_distefano":
            case "cup_garrinca":
                $act = str_replace('cup_', '', $game['chemp']);
                renderCommonCup($game, 'cup', '/fedcup/fed.php?act=' . $act, 'cup');
                break;
                
            case "maradona":
                echo '<link rel="stylesheet" href="/theme/cups/maradona.css" type="text/css" />';
                echo '<div class="gmenu"><center><a href="/' . $game['id_kubok'] . '"><b></b></a></center> </div>';
                echo '<div xmlns="http://www.w3.org/1999/xhtml" class="phdr_lk" style="text-align:center">' . $game['kubok_nomi'] . '</div>';
                echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/cup/b_maradona.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
                break;
                
            case "unchamp":
                echo '<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />';
                echo '<div class="phdr_lk" style="text-align:left"><font color="white"><a href="/' . $game['id_kubok'] . '">' . $game['kubok_nomi'] . '</a></font><b class="rlink"><font color="white">' . date("d.m.Y H:i", $game['time']) . '</b></font></div>';
                echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
                echo '<img src="/union/logo/cup' . $game['id_kubok'] . '.jpg" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
                break;
                
            case "champ":
                echo '<div class="phdr">Чемпионат<b class="rlink">' . date("d.m.Y H:i", $game['time']) . '</b></div>';
                echo '<div class="gmenu"><center><a href="/champ00/index.php?act=' . $game['kubok'] . '"><b>' . $game['kubok_nomi'] . '</b></a></center> </div>';
                echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
                echo '<img src="/images/cup/b_00' . $game['kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
                break;
                
            case "champ_retro":
                echo '<div class="phdr" style="text-align:left">Чемпионат<b class="rlink">' . date("d.m.Y H:i", $game['time']) . '</b></div>';
                echo '<div class="gmenu"><center><a href="/champ/index.php?act=' . $game['id_kubok'] . '"><b>' . $game['kubok_nomi'] . '</b></a></center> </div>';
                echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
                echo '<img src="/images/cup/b_00' . $game['kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
                break;
                
            case "cup":
                echo '<div class="phdr" style="text-align:left"><a href="/cup/' . $game['id_kubok'] . '">' . $c_name . '</a><b class="rlink">' . date("d.m.Y H:i", $game['time']) . '</b></div>';
                break;
                
            case "brend":
                echo '<div class="phdr" style="text-align:left"><a href="/brendcup/' . $game['id_kubok'] . '">' . $c_name . '</a><b class="rlink">' . date("d.m.Y H:i", $game['time']) . '</b></div>';
                break;
                
            case "liga_r":
            case "liga_r2":
                renderCommonCup($game, 'lc', '/' . $game['id_kubok'], 'lc');
                echo '<img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
                break;
                
            case "liberta":
                echo '<link rel="stylesheet" href="/theme/cups/liberta.css" type="text/css" />';
                echo '<div class="phdr_le"><font color="white"><a href="/' . $arr['id_kubok'] . '/"><b>' . $arr['kubok_nomi'] . '</b></a></font><b class="rlink"><font color="white">' . date("d.m.Y H:i", $arr['time']) . '</b></font></div>';
                echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
                echo '<img src="/images/cup/b_liberta.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
                break;
                
            case "le":
            case "super_cup":
            case "super_cup2":
                $type = ($game['chemp'] == 'le') ? 'le' : 'super_cup';
                $link = ($game['chemp'] == 'super_cup2') ? '/super_cup2/' : (($game['chemp'] == 'super_cup') ? '/super_cup/' : '/' . $game['id_kubok']);
                
                echo '<link rel="stylesheet" href="/theme/cups/' . $type . '.css" type="text/css" />';
                echo '<div class="phdr_' . $type . '" style="text-align:left"><font color="white">' . $game['kubok_nomi'] . '</font><b class="rlink"><font color="white">' . date("d.m.Y H:i", $game['time']) . '</b></font></div>';
                echo '<div class="phdr_' . $type . '" style="text-align:left"><center><a href="' . $link . '"><b>' . $game['kubok_nomi'] . '</b></a></center> </div>';
                echo '<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">';
                echo '<img src="/images/cup/b_' . $type . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
                break;
                
            case "lk":
                renderCommonCup($game, 'lk', '/' . $game['id_kubok'], 'lk');
                echo '<img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
                break;
                
            default:
                echo '<div class="phdr">Матч<b class="rlink">' . date("d.m.Y H:i", $game['time']) . '</b></div>';
                echo '<div class="gmenu"><center><a href="/cup/' . $game['id_kubok'] . '"><b>' . $c_name . '</b></a></center> </div>';
                break;
        }

        echo '<br>';

        echo '<table id="pallet"><tr>';

        // Team 1
        echo '<td width="50%"><center>';
        echo '<a href="/team/' . $kom1['id'] . '">';
        echo !empty($kom1['logo']) 
            ? '<img src="/manager/logo/big' . $kom1['logo'] . '" alt=""/>'
            : '<img src="/manager/logo/b_0.jpg" alt=""/>';
        echo '</a>';

        echo "<div class=''>";
        echo "<a href='/team/" . $kom1['id'] . "'><span class='flags c_" . $kom1['flag'] . "_14' style='vertical-align: middle;' title='" . $kom1['flag'] . "'></span> " . $kom1['name'] . "</a><br>";

        if ($kom1['id_admin'] > 0) {
            $us1 = mysqli_query($db, "SELECT * FROM `users` WHERE `id`=" . (int)$kom1['id_admin'] . " LIMIT 1");
            $uss1 = $us1 ? mysqli_fetch_array($us1) : [];
            if ($uss1) {
                $vipImages = [
                    0 => ['src' => 'vip0_m.png', 'title' => 'Базовый аккаунт', 'type' => 0],
                    1 => ['src' => 'vip1_m.png', 'title' => 'Улучшенный Премиум-аккаунт', 'type' => 1],
                    2 => ['src' => 'vip2_m.png', 'title' => 'Улучшенный VIP-аккаунт', 'type' => 2],
                    3 => ['src' => 'vip3_m.png', 'title' => 'Представительский Gold-аккаунт', 'type' => 3]
                ];
                $vip = isset($uss1['vip']) ? $uss1['vip'] : 0;
                if (isset($vipImages[$vip])) {
                    $img = $vipImages[$vip];
                    echo '<span style="opacity:0.4"><a href="/vip.php?action=compare&amp;type=' . $img['type'] . '">';
                    echo '<img src="/images/ico/' . $img['src'] . '" title="' . $img['title'] . '" style="width: 12px;border: none;vertical-align: middle;">';
                    echo $uss1['name'] . '</span></a>';
                }
            }
        }
        echo "</div></center></td>";

        // Team 2
        echo '<td><center>';
        echo '<a href="/team/' . $kom2['id'] . '">';
        echo !empty($kom2['logo']) 
            ? '<img src="/manager/logo/big' . $kom2['logo'] . '" alt=""/>'
            : '<img src="/manager/logo/b_0.jpg" alt=""/>';
        echo '</a>';

        echo "<div class=''><a href='/team/" . $kom2['id'] . "'>" . $kom2['name'] . " <span class='flags c_" . $kom2['flag'] . "_14' style='vertical-align: middle;' title='" . $kom2['flag'] . "'></span></a><br>";

        if ($kom2['id_admin'] > 0) {
            $us2 = mysqli_query($db, "SELECT * FROM `users` WHERE `id`=" . (int)$kom2['id_admin'] . " LIMIT 1");
            $uss2 = $us2 ? mysqli_fetch_array($us2) : [];
            if ($uss2) {
                $vipImages = [
                    0 => ['src' => 'vip0_m.png', 'title' => 'Базовый аккаунт', 'type' => 0],
                    1 => ['src' => 'vip1_m.png', 'title' => 'Улучшенный Премиум-аккаунт', 'type' => 1],
                    2 => ['src' => 'vip2_m.png', 'title' => 'Улучшенный VIP-аккаунт', 'type' => 2],
                    3 => ['src' => 'vip3_m.png', 'title' => 'Представительский Gold-аккаунт', 'type' => 3]
                ];
                $vip = isset($uss2['vip']) ? $uss2['vip'] : 0;
                if (isset($vipImages[$vip])) {
                    $img = $vipImages[$vip];
                    echo '<span style="opacity:0.4"><a href="/vip.php?action=compare&amp;type=' . $img['type'] . '">';
                    echo '<img src="/images/ico/' . $img['src'] . '" title="' . $img['title'] . '" style="width: 12px;border: none;vertical-align: middle;">';
                    echo $uss2['name'] . '</span></a>';
                }
            }
        }
        echo "</center></td>";

        echo '</tr></table>';

        echo '<div style="display: flex; text-align: center; width: 100%; justify-content: center; align-items: center;">
                <div class="tab-p but head_button" type="button" id="addteam">Расстановка</div>
                <div class="tab-p but head_button" type="button" id="sostav">Составы</div>
                <div class="tab-p but head_button" type="button" id="h2h">H2H</div>
                <div class="tab-p but head_button" type="button" id="bets">Ставки</div>
                <div class="tab-p but head_button" type="button" id="information">Информация</div>
              </div>';

        echo '<div id="addteamdiv" class="content">';
        echo '<div class="phdr" style="text-align:center">Стартовый состав</div>';
        echo '<div id="pagewrap"><div style="display: flex; justify-content: space-around;">';

        // Функция для генерации ячейки игрока
        function generatePlayerCell($player, $form, $position_title, $game, $is_goalkeeper = false) {
            if (empty($player)) return '<td></td>';
            
            $output = '<td><a href="/player/'.$player['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока ('.$position_title.')">';
            
            // Форма игрока
            if ($is_goalkeeper) {
                $output .= '<img src="/images/forma/59.gif" alt=""><br>';
            } else {
                $output .= '<img src="/images/forma/'.$form.'.gif" alt=""><br>';
            }
            
            // Иконка пропуска матча
            if (!empty($player['utime'])) {
                $output .= '<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';
            }
            
            // Отображение желтых карточек
            $yc_count = 0;
            $title_text = '';
            if (!empty($game['chemp'])) {
                switch ($game['chemp']) {
                    case "champ_retro": 
                        $yc_count = isset($player['yc']) ? (int)$player['yc'] : 0;
                        $title_text = 'в Чемпионате';
                        break;
                    case "unchamp": 
                        $yc_count = isset($player['yc_unchamp']) ? (int)$player['yc_unchamp'] : 0;
                        $title_text = '';
                        break;
                    case "liga_r": 
                        $yc_count = isset($player['yc_liga_r']) ? (int)$player['yc_liga_r'] : 0;
                        $title_text = 'в КЕЧ';
                        break;
                    case "le": 
                        $yc_count = isset($player['yc_le']) ? (int)$player['yc_le'] : 0;
                        $title_text = 'в Кубке УЕФА';
                        break;
                }
            }
            
            if ($yc_count > 0) {
                $title = $title_text ? "Кол-во НЕ сгоревших желтых карточек $title_text" : "Кол-во НЕ сгоревших желтых карточек";
                $output .= '<div class="player-cards-1" title="'.$title.'">'.$yc_count.'</div>';
            }
            
            // Имя игрока
            $output .= '<div class="team_name2">';
            if (!empty($player['utime'])) {
                $output .= '<span class="__fio2">'.full_name_to_short($player['name']).'</span>';
            } else {
                $output .= '<span class="schema_plname">'.full_name_to_short($player['name']).'</span>';
            }
            $output .= '</a></div></td>';
            
            return $output;
        }

        // Запрос данных команды
        $team_id = isset($arr1['id']) ? (int)$arr1['id'] : 0;
        $team_query = mysqli_query($db, "SELECT * FROM `r_team` WHERE id='$team_id' LIMIT 1");
        $kom = $team_query ? mysqli_fetch_assoc($team_query) : [];

        // Проверка существования команды
        if (!$kom) {
            echo "Команда не найдена";
            exit;
        }

        // Запрос данных игроков
        $players = [];
        for ($i = 1; $i <= 11; $i++) {
            $player_id = isset($kom['i'.$i]) ? (int)$kom['i'.$i] : 0;
            if ($player_id) {
                $result = mysqli_query($db, "SELECT * FROM `r_player` WHERE id='$player_id'");
                $players[$i] = $result ? mysqli_fetch_assoc($result) : [];
            } else {
                $players[$i] = [];
            }
        }

        // Схема по умолчанию
        $schema = isset($arr1['shema']) ? mysqli_real_escape_string($db, $arr1['shema']) : '4-3-3';

        echo '<table class="schema_table2" border="0">';

        switch ($schema) {
            case "4-3-3":
                echo '<tbody>
                    <tr style="height:35%"><td colspan="4">
                        <table border="0" style="height:100%; width:100%">
                        <tbody><tr>
                            <td style="padding-top:10%">'.generatePlayerCell($players[9], $kom['forma'], 'Lf', $game).'</td>
                            <td style="padding-top:10%">'.generatePlayerCell($players[11], $kom['forma'], 'Cf', $game).'</td>
                            <td style="padding-top:10%">'.generatePlayerCell($players[10], $kom['forma'], 'Rf', $game).'</td>
                        </tr></tbody></table>
                    </td></tr>
                    
                    <tr style="height:30%"><td colspan="4">
                        <table border="0" style="height:100%; width:100%">
                        <tbody><tr>
                            <td style="padding-top:0%">'.generatePlayerCell($players[6], $kom['forma'], 'Cm', $game).'</td>
                            <td style="padding-top:0%">'.generatePlayerCell($players[7], $kom['forma'], 'Rm', $game).'</td>
                            <td style="padding-top:0%">'.generatePlayerCell($players[8], $kom['forma'], 'Cm', $game).'</td>
                        </tr></tbody></table>
                    </td></tr>
                    
                    <tr style="height:20%">
                        '.generatePlayerCell($players[2], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[3], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[4], $kom['forma'], 'Rd', $game).'
                        '.generatePlayerCell($players[5], $kom['forma'], 'Rd', $game).'
                    </tr>
                    
                    <tr style="height:15%"><td colspan="4">'.generatePlayerCell($players[1], $kom['forma'], 'Gk', $game, true).'</td></tr>
                </tbody>';
                break;

            case "3-4-3":
                echo '<tbody>
                    <tr style="height:35%">
                        '.generatePlayerCell($players[9], $kom['forma'], 'Lf', $game).'
                        '.generatePlayerCell($players[11], $kom['forma'], 'Cf', $game).'
                        '.generatePlayerCell($players[10], $kom['forma'], 'Rf', $game).'
                    </tr>
                    
                    <tr style="height:30%">
                        <td rowspan="2">'.generatePlayerCell($players[6], $kom['forma'], 'Cm', $game).'</td>
                        '.generatePlayerCell($players[7], $kom['forma'], 'Cm', $game).'
                        <td rowspan="2">'.generatePlayerCell($players[8], $kom['forma'], 'Rm', $game).'</td>
                    </tr>
                    <tr style="height:5%">
                        '.generatePlayerCell($players[5], $kom['forma'], 'Rd', $game).'
                    </tr>
                    
                    <tr style="height:15%">
                        '.generatePlayerCell($players[2], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[3], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[4], $kom['forma'], 'Rd', $game).'
                    </tr>
                    
                    <tr style="height:15%"><td colspan="3">'.generatePlayerCell($players[1], $kom['forma'], 'Gk', $game, true).'</td></tr>
                </tbody>';
                break;

            case "2-5-3":
                echo '<tbody>
                    <tr style="height:30%"><td colspan="4">
                        <table border="0" style="height:100%; width:100%">
                        <tbody><tr>
                            '.generatePlayerCell($players[9], $kom['forma'], 'Lf', $game).'
                            '.generatePlayerCell($players[11], $kom['forma'], 'Cf', $game).'
                            '.generatePlayerCell($players[10], $kom['forma'], 'Rf', $game).'
                        </tr></tbody></table>
                    </td></tr>
                    
                    <tr style="height:40%">
                        <td rowspan="2">'.generatePlayerCell($players[8], $kom['forma'], 'Rm', $game).'</td>
                        '.generatePlayerCell($players[7], $kom['forma'], 'Cm', $game).'
                        '.generatePlayerCell($players[6], $kom['forma'], 'Cm', $game).'
                        <td rowspan="2">'.generatePlayerCell($players[4], $kom['forma'], 'Rd', $game).'</td>
                    </tr>
                    <tr style="height:5%">
                        '.generatePlayerCell($players[5], $kom['forma'], 'Rd', $game).'
                    </tr>
                    
                    <tr style="height:15%"><td colspan="4">
                        <table border="0" style="height:100%; width:100%">
                        <tbody><tr>
                            '.generatePlayerCell($players[2], $kom['forma'], 'Ld', $game).'
                            '.generatePlayerCell($players[3], $kom['forma'], 'Ld', $game).'
                        </tr></tbody></table>
                    </td></tr>
                    
                    <tr style="height:15%"><td colspan="4">'.generatePlayerCell($players[1], $kom['forma'], 'Gk', $game, true).'</td></tr>
                </tbody>';
                break;

            case "5-3-2":
                echo '<tbody>
                    <tr style="height:40%"><td colspan="4" style="">
                        <table border="0" style="height:100%; width:100%">
                        <tbody><tr>
                            '.generatePlayerCell($players[11], $kom['forma'], 'Cf', $game).'
                            '.generatePlayerCell($players[9], $kom['forma'], 'Lf', $game).'
                        </tr>
                        <tr>
                            '.generatePlayerCell($players[6], $kom['forma'], 'Cm', $game).'
                            '.generatePlayerCell($players[8], $kom['forma'], 'Rm', $game).'
                        </tr></tbody></table>
                    </td></tr>
                    
                    <tr style="height:15%"><td colspan="4">'.generatePlayerCell($players[7], $kom['forma'], 'Cm', $game).'</td></tr>
                    
                    <tr style="height:15%">
                        '.generatePlayerCell($players[2], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[3], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[5], $kom['forma'], 'Rd', $game).'
                        '.generatePlayerCell($players[4], $kom['forma'], 'Rd', $game).'
                    </tr>
                    
                    <tr style="height:15%"><td colspan="4">'.generatePlayerCell($players[10], $kom['forma'], 'Rf', $game).'</td></tr>
                    
                    <tr style="height:15%"><td colspan="4">'.generatePlayerCell($players[1], $kom['forma'], 'Gk', $game, true).'</td></tr>
                </tbody>';
                break;

            case "4-4-2":
                echo '<tbody>
                    <tr style="height:40%"><td colspan="4" style="">
                        <table border="0" style="height:100%; width:100%">
                        <tbody><tr>
                            '.generatePlayerCell($players[11], $kom['forma'], 'Cf', $game).'
                            '.generatePlayerCell($players[9], $kom['forma'], 'Lf', $game).'
                        </tr></tbody></table>
                    </td></tr>
                    
                    <tr style="height:10%">
                        <td rowspan="2">'.generatePlayerCell($players[6], $kom['forma'], 'Cm', $game).'</td>
                        '.generatePlayerCell($players[7], $kom['forma'], 'Cm', $game).'
                        <td rowspan="2">'.generatePlayerCell($players[8], $kom['forma'], 'Rm', $game).'</td>
                    </tr>
                    <tr style="height:10%">
                        '.generatePlayerCell($players[10], $kom['forma'], 'Rf', $game).'
                    </tr>
                    
                    <tr style="height:30%">
                        '.generatePlayerCell($players[2], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[3], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[5], $kom['forma'], 'Rd', $game).'
                        '.generatePlayerCell($players[4], $kom['forma'], 'Rd', $game).'
                    </tr>
                    
                    <tr style="height:10%"><td colspan="4">'.generatePlayerCell($players[1], $kom['forma'], 'Gk', $game, true).'</td></tr>
                </tbody>';
                break;

            case "3-5-2":
                echo '<tbody>
                    <tr style="height:25%"><td colspan="4" style="">
                        <table border="0" style="height:100%; width:100%">
                        <tbody><tr>
                            '.generatePlayerCell($players[11], $kom['forma'], 'Cf', $game).'
                            '.generatePlayerCell($players[9], $kom['forma'], 'Lf', $game).'
                        </tr></tbody></table>
                    </td></tr>
                    
                    <tr>
                        <td rowspan="3" valign="bottom">'.generatePlayerCell($players[6], $kom['forma'], 'Cm', $game).'</td>
                        '.generatePlayerCell($players[8], $kom['forma'], 'Rm', $game).'
                        <td rowspan="3" valign="bottom">'.generatePlayerCell($players[10], $kom['forma'], 'Rf', $game).'</td>
                    </tr>
                    <tr>
                        '.generatePlayerCell($players[7], $kom['forma'], 'Cm', $game).'
                    </tr>
                    <tr>
                        '.generatePlayerCell($players[5], $kom['forma'], 'Rd', $game).'
                    </tr>
                    
                    <tr style="height:15%"><td colspan="4">
                        <table border="0" style="height:100%; width:100%">
                        <tbody><tr>
                            '.generatePlayerCell($players[2], $kom['forma'], 'Ld', $game).'
                            '.generatePlayerCell($players[3], $kom['forma'], 'Ld', $game).'
                            '.generatePlayerCell($players[4], $kom['forma'], 'Rd', $game).'
                        </tr></tbody></table>
                    </td></tr>
                    
                    <tr style="height:10%"><td colspan="4">'.generatePlayerCell($players[1], $kom['forma'], 'Gk', $game, true).'</td></tr>
                </tbody>';
                break;

            case "5-4-1":
                echo '<tbody>
                    <tr style="height:40%"><td colspan="4">'.generatePlayerCell($players[11], $kom['forma'], 'Cf', $game).'</td></tr>
                    
                    <tr style="height:15%">
                        <td rowspan="2">'.generatePlayerCell($players[6], $kom['forma'], 'Cm', $game).'</td>
                        '.generatePlayerCell($players[7], $kom['forma'], 'Cm', $game).'
                        <td rowspan="2">'.generatePlayerCell($players[8], $kom['forma'], 'Rm', $game).'</td>
                    </tr>
                    <tr style="height:5%">
                        '.generatePlayerCell($players[9], $kom['forma'], 'Lf', $game).'
                    </tr>
                    
                    <tr style="height:15%">
                        '.generatePlayerCell($players[2], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[3], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[5], $kom['forma'], 'Rd', $game).'
                        '.generatePlayerCell($players[4], $kom['forma'], 'Rd', $game).'
                    </tr>
                    
                    <tr style="height:15%"><td colspan="4">'.generatePlayerCell($players[10], $kom['forma'], 'Rf', $game).'</td></tr>
                    
                    <tr style="height:10%"><td colspan="4">'.generatePlayerCell($players[1], $kom['forma'], 'Gk', $game, true).'</td></tr>
                </tbody>';
                break;

            case "4-5-1":
                echo '<tbody>
                    <tr style="height:35%"><td colspan="4" style="">'.generatePlayerCell($players[11], $kom['forma'], 'Cf', $game).'</td></tr>
                    
                    <tr style="height:10%"><td colspan="4" style="">'.generatePlayerCell($players[6], $kom['forma'], 'Cm', $game).'</td></tr>
                    
                    <tr style="height:10%">
                        <td rowspan="2">'.generatePlayerCell($players[8], $kom['forma'], 'Rm', $game).'</td>
                        '.generatePlayerCell($players[7], $kom['forma'], 'Cm', $game).'
                        <td rowspan="2">'.generatePlayerCell($players[9], $kom['forma'], 'Lf', $game).'</td>
                    </tr>
                    <tr style="height:10%">
                        '.generatePlayerCell($players[10], $kom['forma'], 'Rf', $game).'
                    </tr>
                    
                    <tr style="height:20%">
                        '.generatePlayerCell($players[2], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[3], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[5], $kom['forma'], 'Rd', $game).'
                        '.generatePlayerCell($players[4], $kom['forma'], 'Rd', $game).'
                    </tr>
                    
                    <tr style="height:15%"><td colspan="4">'.generatePlayerCell($players[1], $kom['forma'], 'Gk', $game, true).'</td></tr>
                </tbody>';
                break;

            case "6-3-1":
                echo '<tbody>
                    <tr style="height:45%"><td colspan="4">'.generatePlayerCell($players[11], $kom['forma'], 'Cf', $game).'</td></tr>
                    
                    <tr style="height:25%">
                        '.generatePlayerCell($players[6], $kom['forma'], 'Cm', $game).'
                        '.generatePlayerCell($players[7], $kom['forma'], 'Cm', $game).'
                        '.generatePlayerCell($players[8], $kom['forma'], 'Rm', $game).'
                    </tr>
                    
                    <tr style="height:10%">
                        '.generatePlayerCell($players[2], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[3], $kom['forma'], 'Ld', $game).'
                        '.generatePlayerCell($players[4], $kom['forma'], 'Rd', $game).'
                        '.generatePlayerCell($players[5], $kom['forma'], 'Rd', $game).'
                    </tr>
                    
                    <tr style="height:20%"><td colspan="4">'.generatePlayerCell($players[1], $kom['forma'], 'Gk', $game, true).'</td></tr>
                </tbody>';
                break;

            default:
                echo '<tbody><tr><td colspan="4">Неизвестная схема: '.htmlspecialchars($schema).'</td></tr></tbody>';
        }

        echo '</table></div></div></div>';

        echo '<div id="betsdiv" class="content">';
        echo '<link rel="stylesheet" href="/game/bets.css" type="text/css" />';

        // Проверка авторизации и параметра
        if (empty($datauser['id'])) {
            header("Location: #");
            exit;
        }

        if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
            header("Location: #");
            exit;
        }
        $id = (int)$_GET['id'];

        // Получаем данные матча
        $game = mysqli_fetch_assoc(mysqli_query($db, 
            "SELECT * FROM r_game WHERE id_match = '$id' LIMIT 1"
        ));

        // Получаем баланс пользователя
        $ratRes = mysqli_fetch_assoc(mysqli_query($db, 
            "SELECT money FROM r_team WHERE id = {$datauser['manager2']}"
        ));
        $balance = isset($ratRes['money']) ? $ratRes['money'] : 0;

        // Проверка доступности ставок
        $game5 = mysqli_fetch_assoc(mysqli_query($db, 
            "SELECT * FROM t_games 
            WHERE champ = '{$game['chemp']}' 
            AND id_match = {$game['id_match']}"
        ));

        if (!$game5) {
            echo '<div class="rmenu">На этот матч нет ставок</div>';
        } else {
            $betCloseTime = $game['time'] - 60;
            
            if ($betCloseTime > $realtime) {
                // Обработка отправки формы
                $error = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $winner = isset($_POST['winner']) ? (int)$_POST['winner'] : 0;
                    $mil = isset($_POST['mil']) ? (int)$_POST['mil'] : 0;
                    
                    if ($winner && $mil >= 10 && $mil <= $balance) {
                        mysqli_query($db, "START TRANSACTION");
                        
                        // Добавляем ставку
                        $insert = mysqli_query($db, 
                            "INSERT INTO t_mils VALUES (
                                0, 
                                '$id', 
                                '$user_id', 
                                '$mil', 
                                '$winner'
                            )"
                        );
                        
                        if ($insert) {
                            // Обновляем баланс
                            mysqli_query($db, 
                                "UPDATE r_team 
                                SET money = money - $mil 
                                WHERE id = {$datauser['manager2']}"
                            );
                            
                            // Формируем текст ставки
                            $teams = explode('|', $game5['teams']);
                            $betTypes = [
                                1 => $teams[0] . ' <b>П1</b>',
                                2 => $teams[1] . ' <b>П2</b>',
                                3 => $teams[0].'-'.$teams[1].' <b>Ничья</b>'
                            ];
                            $betText = isset($betTypes[$winner]) ? $betTypes[$winner] : 'Неизвестная ставка';
                            
                            // Добавляем запись в историю
                            mysqli_query($db, 
                                "INSERT INTO news SET
                                time = '$realtime',
                                money = '-$mil',
                                text = 'Ставка $betText',
                                team_id = '{$datauser['manager2']}'"
                            );
                            
                            mysqli_query($db, "COMMIT");
                            header("Location: ".$_SERVER['REQUEST_URI']);
                            exit;
                        } else {
                            $error = 'Ошибка при сохранении ставки';
                        }
                    } else {
                        $error = 'Некорректная сумма или выбор ставки';
                    }
                }
                
                // Формируем коэффициенты
                $teams = explode('|', $game5['teams']);
                $coefs = explode('|', $game5['coefs']);
                
                echo '<div class="phdr" style="text-align:center">Сделать ставку</div>';
                
                if ($balance < 10) {
                    echo '<div class="rmenu">Недостаточно средств. Минимум: 10 <img src="/images/m_game3.gif" class="money"></div>';
                } else {
                    if ($error) {
                        echo '<div class="rmenu">'.$error.'</div>';
                    }
                    
                    echo '<div class="menu"><form method="POST">';
                    echo '<div class="market-group-box--z23Vvd">
                        <div class="header--2fWAgi _expanded--2iUfDc">
                            <div class="section--5JAm4a">
                                <div class="text-new--2WAqa8">Исход матча (основное время)</div>
                            </div>
                        </div>
                        <div class="cardview x-text-center">
                            <div class="section--5JAm4a _horizontal--18WrKP">
                                <div class="grid--6A7cHV">
                                    <div class="row-common--33mLED">
                                        <div class="cell-wrap--LHnTwg">
                                            <div class="cardview x-text-center" style="display: flex; flex: 1 1 0%; position: relative; height: 30px;">
                                                <label>
                                                    <div class="factor-td--3ZZULU cell-state-normal--iYJc0x">
                                                        <div class="t--4zyb4K text-state-normal--1L40o3">
                                                            <input type="radio" name="winner" value="1" required> '.$teams[0].'
                                                        </div>
                                                        <div class="v--1iHcVX value-state-normal--4JL4xN">'.$coefs[0].'</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="cell-wrap--LHnTwg">
                                            <div style="display: flex; flex: 1 1 0%; position: relative; height: 30px;">
                                                <label>
                                                    <div class="factor-td--3ZZULU cell-state-normal--iYJc0x">
                                                        <div class="t--4zyb4K text-state-normal--1L40o3">
                                                            <input type="radio" name="winner" value="3"> Ничья
                                                        </div>
                                                        <div class="v--1iHcVX value-state-normal--4JL4xN">'.$coefs[2].'</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="cell-wrap--LHnTwg">
                                            <div style="display: flex; flex: 1 1 0%; position: relative; height: 30px;">
                                                <label>
                                                    <div class="factor-td--3ZZULU cell-state-normal--iYJc0x">
                                                        <div class="t--4zyb4K text-state-normal--1L40o3">
                                                            <input type="radio" name="winner" value="2"> '.$teams[1].'
                                                        </div>
                                                        <div class="v--1iHcVX value-state-normal--4JL4xN">'.$coefs[1].'</div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                    
                    echo '<br><label>Ставка: 
                        <input type="number" name="mil" value="10" min="10" max="'.$balance.'" required>
                        (10-'.$balance.')</label><br>';
                    echo '<input type="submit" name="submit" value="Поставить">';
                    echo '</form></div>';
                }
            } else {
                echo '<div class="rmenu">Приём ставок окончен</div>';
            }
            
            // История ставок
            $betsQuery = mysqli_query($db, 
                "SELECT * FROM t_mils 
                WHERE user = '$user_id' 
                AND refid = {$game['id']}"
            );
            $allMils = $betsQuery ? mysqli_num_rows($betsQuery) : 0;
            
            echo '<center><div class="gmenu">Всего ставок: '.$allMils.'</div>';
            
            if ($betsQuery) {
                while ($bet = mysqli_fetch_assoc($betsQuery)) {
                    $betTypes = [
                        1 => 'П1',
                        2 => 'П2',
                        3 => 'Ничья'
                    ];
                    $betType = isset($betTypes[$bet['winner']]) ? $betTypes[$bet['winner']] : 'Неизвестно';
                    
                    echo '<tr class="coupon__table-row--6OoyA1">
                        <td class="coupon__table-col--3p8NRM" colspan="2">
                            <span>'.$game['name_team1'].' – '.$game['name_team2'].'</span>
                        </td>
                        <td class="coupon__table-col--3p8NRM _type_stake--4UOCA4">
                            <span><b>'.$betType.'</b></span>
                            '.$bet['mil'].' <img src="/images/m_game3.gif" class="money">
                        </td>
                        <td class="coupon__table-col--3p8NRM coupon__table-status--77SBfz">
                            <span class="coupon__table-stake--PyBpdc">';
                    
                    // Коэффициент в зависимости от типа ставки
                    if ($bet['winner'] == 1) echo $coefs[0];
                    elseif ($bet['winner'] == 2) echo $coefs[1];
                    else echo $coefs[2];
                    
                    echo '</span>
                        </td>
                    </tr><br>';
                }
            }
            echo '</center>';
        }

        echo '</div><div id="h2hdiv" class="content">';

        // Подключение файла с историей
        require_once("../game/history3.php");

        echo '</div>';

        echo '<div id="informationdiv" class="content">';

        ///////////////information games

        ///////////////stadion
        $std11 = mysqli_query($db, "SELECT * FROM `r_stadium` WHERE `id`='".mysqli_real_escape_string($db, $game['id_stadium'])."' LIMIT 1");
        $std11 = $std11 ? mysqli_fetch_array($std11) : [];

        if($game['id_stadium']) {
            echo '<div class="game-ui__history">
                    <div style="float: left; margin-right: 40px;">';
            
            if($std11['std']) {
                echo '<img src="/images/stadium/'.$game['id_stadium'].'.jpg" style="width: 480px; height: 240px; border: 1px solid var(--primary-color-border); margin-top:7px;" alt="">';
            } else {
                echo '<img src="/images/stadium/stadium.jpg" style="width: 480px; height: 240px; border: 1px solid var(--primary-color-border); margin-top:7px;" alt="">';
            }
            
            echo '</div>
                  <div style="font-size:140%;margin-top:20px;">Место проведения матча</div>
                  <div style="font-size:170%;">'.$std11['name'].'</div>
                  <div style="font-size:160%;color:green;">'.$game['zritel'].' зрителей</div>';
            
            if($game['chemp'] == '!frend') {
                echo '<div>город '.$std11['city'].'</div>';
            }
            
            echo '</div>';
        }
        ///////////////stadion

        $j1 = mysqli_query($db, "SELECT * FROM `r_team` WHERE id='".mysqli_real_escape_string($db, $game['id_team1'])."' LIMIT 1");
        $jam1 = $j1 ? mysqli_fetch_array($j1) : [];

        $j2 = mysqli_query($db, "SELECT * FROM `r_team` WHERE id='".mysqli_real_escape_string($db, $game['id_team2'])."' LIMIT 1");
        $jam2 = $j2 ? mysqli_fetch_array($j2) : [];

        ///////////////information games
        echo '</div>';

        echo '<div id="sostavdiv" class="content">';
        echo '<div class="phdr orangebk"><center><b>Состав</b></center></div>';

        echo '<table id="example" class="t-table">';
        echo '<tr bgcolor="40B832" align="center" class="whiteheader">';
        echo '<td><b>'.$jam1['name'].'</b></td><td><b>'.$jam2['name'].'</b></td></tr>';
        echo '<tr>';

        // Team 1 players
        $rq = mysqli_query($db, "SELECT * FROM `r_player` WHERE `team`='".$jam1['id']."' 
                      AND (`id`='".$jam1['i1']."' OR `id`='".$jam1['i2']."' OR `id`='".$jam1['i3']."' 
                      OR `id`='".$jam1['i4']."' OR `id`='".$jam1['i5']."' OR `id`='".$jam1['i6']."' 
                      OR `id`='".$jam1['i7']."' OR `id`='".$jam1['i8']."' OR `id`='".$jam1['i9']."' 
                      OR `id`='".$jam1['i10']."' OR `id`='".$jam1['i11']."') 
                      AND `sostav`!='4' ORDER BY line ASC, poz ASC");

        echo '<td width="50%">';
        if ($rq) {
            while ($parr1 = mysqli_fetch_array($rq)) {
                $bgColor = '';
                if ($datauser['black'] == 0) {
                    // Replace match() with switch (PHP 5.x compatible)
                    switch ($parr1['line']) {
                        case 1: $bgColor = '#fff7e7'; break;
                        case 2: $bgColor = '#f7ffef'; break;
                        case 3: $bgColor = '#e7f7ff'; break;
                        case 4: $bgColor = '#ffefef'; break;
                        default: $bgColor = ''; break;
                    }
                } else {
                    switch ($parr1['line']) {
                        case 1: $bgColor = '#434343'; break;
                        case 2: $bgColor = '#363636'; break;
                        case 3: $bgColor = '#262525'; break;
                        case 4: $bgColor = '#1e1e1e'; break;
                        default: $bgColor = ''; break;
                    }
                }
                
                echo '<div style="background-color:' . $bgColor . '" class="gmenu2">';
                echo '<span class="flags c_' . $parr1['flag'] . '_18" style="vertical-align: middle;" title="' . $parr1['flag'] . '"></span> ';
                echo '<b>' . $parr1['poz'] . '</b> ';
                echo '<a href="/player/' . $parr1['id'] . '">' . $parr1['name'] . '</a> ';
                
                // Display cards based on tournament type
                switch ($game['chemp']) {
                    case "champ_retro":
                        if ($parr1['yc'] > 0) {
                            echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">' . $parr1['yc'] . '</div>';
                        }
                        break;
                    case "unchamp":
                        if ($parr1['yc_unchamp'] > 0) {
                            echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Союзном Чемпионате">' . $parr1['yc_unchamp'] . '</div>';
                        }
                        break;
                    case "liga_r":
                        if ($parr1['yc_liga_r'] > 0) {
                            echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Ретро Кубке Чемпионов">' . $parr1['yc_liga_r'] . '</div>';
                        }
                        break;
                    case "le":
                        if ($parr1['yc_le'] > 0) {
                            echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">' . $parr1['yc_le'] . '</div>';
                        }
                        break;
                }
                
                if ($parr1['rc'] > 0) {
                    echo '<div class="player-cards-2" title="Кол-во НЕ сгоревших красных карточек">' . $parr1['rc'] . '</div>';
                }
                
                echo '</div>';
            }
        }
        echo '</td>';

        // Team 2 players
        $rq2 = mysqli_query($db, "SELECT * FROM `r_player` WHERE `team`='".$jam2['id']."' 
                       AND (`id`='".$jam2['i1']."' OR `id`='".$jam2['i2']."' OR `id`='".$jam2['i3']."' 
                       OR `id`='".$jam2['i4']."' OR `id`='".$jam2['i5']."' OR `id`='".$jam2['i6']."' 
                       OR `id`='".$jam2['i7']."' OR `id`='".$jam2['i8']."' OR `id`='".$jam2['i9']."' 
                       OR `id`='".$jam2['i10']."' OR `id`='".$jam2['i11']."') 
                       AND `sostav`!='4' ORDER BY line ASC, poz ASC");

        echo '<td width="50%">';
        if ($rq2) {
            while ($parr2 = mysqli_fetch_array($rq2)) {
                $bgColor = '';
                if($datauser['black'] == 0) {
                    // Replaced match() with switch-case
                    switch ($parr2['line']) {
                        case 1: $bgColor = '#fff7e7'; break;
                        case 2: $bgColor = '#f7ffef'; break;
                        case 3: $bgColor = '#e7f7ff'; break;
                        case 4: $bgColor = '#ffefef'; break;
                        default: $bgColor = ''; break;
                    }
                } else {
                    switch ($parr2['line']) {
                        case 1: $bgColor = '#434343'; break;
                        case 2: $bgColor = '#363636'; break;
                        case 3: $bgColor = '#262525'; break;
                        case 4: $bgColor = '#1e1e1e'; break;
                        default: $bgColor = ''; break;
                    }
                }
                
                echo '<div style="background-color:'.$bgColor.'" class="gmenu2">';
                echo '<span class="flags c_'.$parr2['flag'].'_18" style="vertical-align: middle;" title="'.$parr2['flag'].'"></span> ';
                echo '<b>'.$parr2['poz'].'</b> <a href="/player/'.$parr2['id'].'">'.$parr2['name'] . ' ';
                
                switch ($game['chemp']) {
                    case "champ_retro":
                        if($parr2['yc'] > 0) {
                            echo '<div class="player-cards-1" title="Кол-во желтых карточек">'.$parr2['yc'].'</div>';
                        }
                        break;
                    case "unchamp":
                        if($parr2['yc_unchamp'] > 0) {
                            echo '<div class="player-cards-1" title="Кол-во желтых карточек">'.$parr2['yc_unchamp'].'</div>';
                        }
                        break;
                    case "liga_r":
                        if($parr2['yc_liga_r'] > 0) {
                            echo '<div class="player-cards-1" title="Кол-во желтых карточек">'.$parr2['yc_liga_r'].'</div>';
                        }
                        break;
                    case "le":
                        if($parr2['yc_le'] > 0) {
                            echo '<div class="player-cards-1" title="Кол-во желтых карточек">'.$parr2['yc_le'].'</div>';
                        }
                        break;
                }
                
                if($parr2['rc'] > 0) {
                    echo '<div class="player-cards-2" title="Кол-во красных карточек">'.$parr2['rc'].'</div>';
                }
                
                echo '</a>';
                echo '</div>';
            }
        }
        echo '</td>';

        echo '</tr></table>';
        echo '</div>';

        echo '<meta http-equiv="refresh" content="60;url=/game'.$dirs.$id.'"/>';
        echo '<center><div class="info">Матч начнется через: '.date("i:s", $ostime).'</div></center>';

        if ($datauser['manager2'] == $game['id_team1'] || $datauser['manager2'] == $game['id_team2']) {
            echo '<br/><center><form action="/team/sostav.php"><input type="submit" title="Нажмите для изменения состава" name="submit" value="Изменить состав"/></form>';
            echo '<form action="/team/tactic.php"><input type="submit" title="Нажмите для изменения тактики" name="submit" value="Изменить тактику"/></form></center><br/>';
        }

        ////////////////////////Убираем игрока с дисквалификацией из состава/////////////////////
        if ($game['chemp'] == 'champ_retro') {
            // Team 1
            $test1 = mysqli_query($db, "SELECT * FROM `r_player` WHERE (`id`='".$jam1['i1']."' OR `id`='".$jam1['i2']."' OR `id`='".$jam1['i3']."' 
                             OR `id`='".$jam1['i4']."' OR `id`='".$jam1['i5']."' OR `id`='".$jam1['i6']."' 
                             OR `id`='".$jam1['i7']."' OR `id`='".$jam1['i8']."' OR `id`='".$jam1['i9']."' 
                             OR `id`='".$jam1['i10']."' OR `id`='".$jam1['i11']."') 
                             AND `team`='".$jam1['id']."' LIMIT 11");
            
            if ($test1) {
                while ($pidr = mysqli_fetch_array($test1)) {
                    if ($pidr['utime'] > 0) {
                        mysqli_query($db, "UPDATE `r_player` SET `sostav`='4' WHERE `id`='".$pidr['id']."'");
                        for($i = 1; $i <= 11; $i++) {
                            mysqli_query($db, "UPDATE `r_team` SET `i".$i."`='' WHERE `i".$i."`='".$pidr['id']."'");
                        }
                        echo '<div class ="error">Мы убрали '.$pidr['name'].' из состава. У него дисквалификация</div>';
                    }
                }
            }
            
            // Team 2
            $test2 = mysqli_query($db, "SELECT * FROM `r_player` WHERE (`id`='".$jam2['i1']."' OR `id`='".$jam2['i2']."' OR `id`='".$jam2['i3']."' 
                             OR `id`='".$jam2['i4']."' OR `id`='".$jam2['i5']."' OR `id`='".$jam2['i6']."' 
                             OR `id`='".$jam2['i7']."' OR `id`='".$jam2['i8']."' OR `id`='".$jam2['i9']."' 
                             OR `id`='".$jam2['i10']."' OR `id`='".$jam2['i11']."') 
                             AND `team`='".$jam2['id']."' LIMIT 11");
            
            if ($test2) {
                while ($pidr2 = mysqli_fetch_array($test2)) {
                    if ($pidr2['utime'] > 0) {
                        mysqli_query($db, "UPDATE `r_player` SET `sostav`='4' WHERE `id`='".$pidr2['id']."'");
                        for($i = 1; $i <= 11; $i++) {
                            mysqli_query($db, "UPDATE `r_team` SET `i".$i."`='' WHERE `i".$i."`='".$pidr2['id']."'");
                        }
                        echo '<div class ="error">Мы убрали '.$pidr2['name'].' из состава. У него дисквалификация</div>';
                    }
                }
            }
        }
        ////////////////////////Убираем игрока с дисквалификацией из состава/////////////////////

        //////////////////////autosostav

        // Team 1
        $result = mysqli_query($db, "SELECT * FROM `r_player` WHERE (`id`='".$jam1['i1']."' OR `id`='".$jam1['i2']."' OR `id`='".$jam1['i3']."' 
                          OR `id`='".$jam1['i4']."' OR `id`='".$jam1['i5']."' OR `id`='".$jam1['i6']."' 
                          OR `id`='".$jam1['i7']."' OR `id`='".$jam1['i8']."' OR `id`='".$jam1['i9']."' 
                          OR `id`='".$jam1['i10']."' OR `id`='".$jam1['i11']."') 
                          AND `team`='".$jam1['id']."' AND `sostav`!='4'");
        $myrow = $result ? mysqli_fetch_row($result) : [];

        if ($myrow && count($myrow) < 11) {
            echo $jam1['name'].' У вас меньше 11 игроков<br>';
            
            $sql = mysqli_query($db, "SELECT * FROM `r_team` WHERE `id`='".$game['id_team1']."' LIMIT 1");
            if(mysqli_num_rows($sql)) {
                $team = mysqli_fetch_assoc($sql);
                for($i = 1; $i <= 11; $i++) {
                    if(!$team['i'.$i]) {
                        if($i == 1) {
                            $sql = mysqli_query($db, "SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' AND `line`='1' AND `sostav`='0' ORDER BY `rm` DESC LIMIT 1");
                        } elseif($i >= 2 && $i <= 4) {
                            $sql = mysqli_query($db, "SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' AND `line`='2' AND `sostav`='0' ORDER BY `rm` DESC LIMIT 1");
                        } elseif($i >= 5 && $i <= 9) {
                            $sql = mysqli_query($db, "SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' AND `line`='3' AND `sostav`='0' ORDER BY `rm` DESC LIMIT 1");
                        } elseif($i >= 10 && $i <= 11) {
                            $sql = mysqli_query($db, "SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' AND `line`='4' AND `sostav`='0' ORDER BY `rm` DESC LIMIT 1");
                        }
                        
                        if(!mysqli_num_rows($sql)) {
                            $sql = mysqli_query($db, "SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' AND `sostav`='0' AND `line`!='1' ORDER BY `rm` LIMIT 1");
                        }
                        
                        if(mysqli_num_rows($sql)) {
                            $player = mysqli_fetch_assoc($sql);
                            mysqli_query($db, "UPDATE `r_team` SET `i$i`='".$player['id']."' WHERE `id`='".$game['id_team1']."' LIMIT 1");
                            mysqli_query($db, "UPDATE `r_player` SET `sostav`='1' WHERE `id`='".$player['id']."' LIMIT 1");
                        }
                    }
                }
            }
        }

        // Team 2
        $result2 = mysqli_query($db, "SELECT * FROM `r_player` WHERE (`id`='".$jam2['i1']."' OR `id`='".$jam2['i2']."' OR `id`='".$jam2['i3']."' 
                           OR `id`='".$jam2['i4']."' OR `id`='".$jam2['i5']."' OR `id`='".$jam2['i6']."' 
                           OR `id`='".$jam2['i7']."' OR `id`='".$jam2['i8']."' OR `id`='".$jam2['i9']."' 
                           OR `id`='".$jam2['i10']."' OR `id`='".$jam2['i11']."') 
                           AND `team`='".$jam2['id']."' AND `sostav`!='4'");
        $myrow2 = $result2 ? mysqli_fetch_row($result2) : [];

        if ($myrow2 && count($myrow2) < 11) {
            echo $jam2['name'].' У вас меньше 11 игроков<br>';
            
            $sql = mysqli_query($db, "SELECT * FROM `r_team` WHERE `id`='".$game['id_team2']."' LIMIT 1");
            if(mysqli_num_rows($sql)) {
                $team = mysqli_fetch_assoc($sql);
                for($i = 1; $i <= 11; $i++) {
                    if(!$team['i'.$i]) {
                        if($i == 1) {
                            $sql = mysqli_query($db, "SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' AND `line`='1' AND `sostav`='0' ORDER BY `rm` DESC LIMIT 1");
                        } elseif($i >= 2 && $i <= 4) {
                            $sql = mysqli_query($db, "SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' AND `line`='2' AND `sostav`='0' ORDER BY `rm` DESC LIMIT 1");
                        } elseif($i >= 5 && $i <= 9) {
                            $sql = mysqli_query($db, "SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' AND `line`='3' AND `sostav`='0' ORDER BY `rm` DESC LIMIT 1");
                        } elseif($i >= 10 && $i <= 11) {
                            $sql = mysqli_query($db, "SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' AND `line`='4' AND `sostav`='0' ORDER BY `rm` DESC LIMIT 1");
                        }
                        
                        if(!mysqli_num_rows($sql)) {
                            $sql = mysqli_query($db, "SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' AND `sostav`='0' AND `line`!='1' ORDER BY `rm` LIMIT 1");
                        }
                        
                        if(mysqli_num_rows($sql)) {
                            $player = mysqli_fetch_assoc($sql);
                            mysqli_query($db, "UPDATE `r_team` SET `i$i`='".$player['id']."' WHERE `id`='".$game['id_team2']."' LIMIT 1");
                            mysqli_query($db, "UPDATE `r_player` SET `sostav`='1' WHERE `id`='".$player['id']."' LIMIT 1");
                        }
                    }
                }
            }
        }

      //  ++$kmess;
      //  ++$stgame;
      //  ++$i;
        require_once ("../incfiles/end.php");
        exit;
    }
} else {
    // Игра подтверждена
    echo '<div class="rmenu">Обе команды подтвердили участие. Матч начнется '.date('d.m.Y H:i', $game['time']).'.</div>';

    // Вывод состава команд
    echo '<div class="phdr orangebk"><center><b>Состав</b></center></div>';
    echo '<div class="gmenu"><table width="100%"><tr><td width="50%" valign="top">';

    echo '<b>'.$kom1['name'].'</b><br>';
    $players1 = mysqli_query($db, "SELECT * FROM `r_player` WHERE `team`='".$kom1['id']."' AND `sostav`='1' ORDER BY `line` ASC, `poz` ASC");
    if ($players1) {
        while ($player = mysqli_fetch_assoc($players1)) {
            echo $player['poz'] . '. <a href="/player/'.$player['id'].'">'.$player['name'].'</a><br>';
        }
    }

    echo '</td><td width="50%" valign="top">';

    echo '<b>'.$kom2['name'].'</b><br>';
    $players2 = mysqli_query($db, "SELECT * FROM `r_player` WHERE `team`='".$kom2['id']."' AND `sostav`='1' ORDER BY `line` ASC, `poz` ASC");
    if ($players2) {
        while ($player = mysqli_fetch_assoc($players2)) {
            echo $player['poz'] . '. <a href="/player/'.$player['id'].'">'.$player['name'].'</a><br>';
        }
    }

    echo '</td></tr></table></div>';
}

// Таймер обновления страницы
if ($game['time'] > $realtime) {
    $ostime = $game['time'] - $realtime;
    echo '<meta http-equiv="refresh" content="60;url=/game'.$dirs.$id.'"/>';
    echo '<center><div class="info">Матч начнется через: '.date("i:s", $ostime).'</div></center>';
} else {
    echo '<center><div class="info">Матч скоро начнется</div></center>';
}

// Проверка дисквалификаций (только для champ_retro)
if ($game['chemp'] == 'champ_retro') {
    $test1 = mysqli_query($db, "SELECT * FROM `r_player` WHERE (`id`='".$kom1['i1']."' OR `id`='".$kom1['i2']."' OR `id`='".$kom1['i3']."' 
                     OR `id`='".$kom1['i4']."' OR `id`='".$kom1['i5']."' OR `id`='".$kom1['i6']."' 
                     OR `id`='".$kom1['i7']."' OR `id`='".$kom1['i8']."' OR `id`='".$kom1['i9']."' 
                     OR `id`='".$kom1['i10']."' OR `id`='".$kom1['i11']."') 
                     AND `team`='".$kom1['id']."' LIMIT 11");
    
    if ($test1) {
        while ($pidr = mysqli_fetch_array($test1)) {
            if ($pidr['utime'] > 0) {
                mysqli_query($db, "UPDATE `r_player` SET `sostav`='4' WHERE `id`='".$pidr['id']."'");
                for($i = 1; $i <= 11; $i++) {
                    mysqli_query($db, "UPDATE `r_team` SET `i".$i."`='' WHERE `i".$i."`='".$pidr['id']."'");
                }
                echo '<div class ="error">Мы убрали '.$pidr['name'].' из состава. У него дисквалификация</div>';
            }
        }
    }
    
    $test2 = mysqli_query($db, "SELECT * FROM `r_player` WHERE (`id`='".$kom2['i1']."' OR `id`='".$kom2['i2']."' OR `id`='".$kom2['i3']."' 
                     OR `id`='".$kom2['i4']."' OR `id`='".$kom2['i5']."' OR `id`='".$kom2['i6']."' 
                     OR `id`='".$kom2['i7']."' OR `id`='".$kom2['i8']."' OR `id`='".$kom2['i9']."' 
                     OR `id`='".$kom2['i10']."' OR `id`='".$kom2['i11']."') 
                     AND `team`='".$kom2['id']."' LIMIT 11");
    
    if ($test2) {
        while ($pidr2 = mysqli_fetch_array($test2)) {
            if ($pidr2['utime'] > 0) {
                mysqli_query($db, "UPDATE `r_player` SET `sostav`='4' WHERE `id`='".$pidr2['id']."'");
                for($i = 1; $i <= 11; $i++) {
                    mysqli_query($db, "UPDATE `r_team` SET `i".$i."`='' WHERE `i".$i."`='".$pidr2['id']."'");
                }
                echo '<div class ="error">Мы убрали '.$pidr2['name'].' из состава. У него дисквалификация</div>';
            }
        }
    }
}

require_once ("../incfiles/end.php");
exit;
?>

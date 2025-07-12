<?php
define('_IN_JOHNCMS', 1);
$headmod = 'game';
require_once("../incfiles/core.php");
require_once("../incfiles/head.php");
require_once("../game/func_game.php");

// Получаем ID игры из запроса
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Определяем параметры для разных типов игр (обычная/юнион)
$prefix = !empty($_GET['union']) ? '_union_' : '_';
$issetun = !empty($_GET['union']) ? '&amp;union=isset' : '';
$dirs = !empty($_GET['union']) ? '/union/' : '/';

// Получаем данные о матче
$game = mysql_fetch_assoc(mysql_query("SELECT * FROM `r".$prefix."game` WHERE id = '".$id."' LIMIT 1"));

// Устанавливаем заголовок страницы
$textl = 'Игра '.htmlspecialchars($game['kubok_nomi']);

// Проверяем, завершена ли уже игра
if (!empty($game['rez1']) || !empty($game['rez2']) || $game['rez1'] == '0' || $game['rez2'] == '0') {
    header('Location: /report'.$dirs.$id);
    exit;
}

// Проверяем валидность данных игры
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
    require_once("../incfiles/end.php");
    exit;
}

/**
 * Функция для автоматического заполнения состава команды
 * @param int $team_id ID команды
 * @param int $game_id ID игры (для исключения дублирования)
 */
function complete_team($team_id, $game_id) {
    $team = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_team` WHERE `id`='".$team_id."' LIMIT 1"));
    
    if (!$team) return;
    
    for ($i = 1; $i <= 11; $i++) {
        if (empty($team['i'.$i])) {
            // Определяем позицию для игрока
            if ($i == 1) {
                $line = 1; // Вратарь
            } elseif ($i >= 2 && $i <= 4) {
                $line = 2; // Защита
            } elseif ($i >= 5 && $i <= 9) {
                $line = 3; // Полузащита
            } else {
                $line = 4; // Нападение
            }
            
            // Ищем подходящего игрока
            $sql = "SELECT `id` FROM `r_player` WHERE `team`='".$team_id."' AND `line`='".$line."' AND `sostav`='0' ORDER BY `rm` DESC LIMIT 1";
            $player = mysql_fetch_assoc(mysql_query($sql));
            
            // Если не нашли по позиции, берем любого
            if (!$player) {
                $sql = "SELECT `id` FROM `r_player` WHERE `team`='".$team_id."' AND `sostav`='0' AND `line`!='1' ORDER BY `rm` LIMIT 1";
                $player = mysql_fetch_assoc(mysql_query($sql));
            }
            
            if ($player) {
                // Обновляем состав
                mysql_query("UPDATE `r_team` SET `i".$i."`='".$player['id']."' WHERE `id`='".$team_id."' LIMIT 1");
                mysql_query("UPDATE `r_team` SET `i".$i."`='' WHERE `id`!='".$game_id."' LIMIT 1");
                mysql_query("UPDATE `r_player` SET `sostav`='1' WHERE `id`='".$player['id']."' LIMIT 1");
                mysql_query("UPDATE `r_player` SET `sostav`='0' WHERE `id`!='".$player['id']."' LIMIT 1");
            }
        }
    }
}

// Автозаполнение составов обеих команд
complete_team($game['id_team1'], $id);
complete_team($game['id_team2'], $id);

// Обработка подтверждения игры
if (isset($_GET['act']) && $_GET['act'] == "add") {
    if ($datauser['manager2'] == $game['id_team1']) {
        mysql_query("UPDATE `r".$prefix."game` SET `go1`='1' WHERE id='".$id."' LIMIT 1");
    } elseif ($datauser['manager2'] == $game['id_team2']) {
        mysql_query("UPDATE `r".$prefix."game` SET `go2`='1' WHERE id='".$id."' LIMIT 1");
    }
    header('Location: /game'.$dirs.$id);
    exit;
}

// Восстановление физической формы игроков
function restore_players($team_id) {
    global $realtime;
    
    $players = mysql_query("SELECT * FROM `r_player` WHERE `team`='".$team_id."'");
    while ($player = mysql_fetch_assoc($players)) {
        $update = array();
        
        // Восстановление физической формы и морали
        if ($player['fiz'] < 100 || $player['mor'] != '0') {
            $rrr = ceil(($realtime - $player['time'])/900); // Базовое восстановление
            
            // Физическая форма
            $fiza = $player['fiz'] + $rrr;
            if ($fiza > 100) $fiza = 100;
            $update['fiz'] = $fiza;
            
            // Мораль
            if ($player['mor'] < 0) {
                $mor = $player['mor'] + $rrr;
                if ($mor > 0) $mor = 0;
            } elseif ($player['mor'] > 0) {
                $mor = $player['mor'] - $rrr;
                if ($mor < 0) $mor = 0;
            } else {
                $mor = 0;
            }
            $update['mor'] = $mor;
            
            // Расчет рейтинга
            $update['rm'] = ceil($player['mas']/100*$fiza);
        }
        
        // Старение игроков
        if (($realtime - $player['time']) > 30*3600*20) {
            $update['time'] = $realtime;
            $update['voz'] = $player['voz'] + 1;
        }
        
        // Обновление данных игрока
        if (!empty($update)) {
            $set = array();
            foreach ($update as $field => $value) {
                $set[] = "`".$field."`='".$value."'";
            }
            mysql_query("UPDATE `r_player` SET ".implode(', ', $set)." WHERE id='".$player['id']."' LIMIT 1");
        }
    }
}

// Восстановление игроков обеих команд
if ($game['time'] < ($realtime - 900)) {
    restore_players($game['id_team1']);
    restore_players($game['id_team2']);
}

// Получаем информацию о судье
$judge = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_judge` WHERE `id`='".$game['judge']."' LIMIT 1"));
?>

<script>
$(function() {
    $(".but").on("click", function(e) {
        e.preventDefault();
        $(".content").hide();
        $("#"+this.id+"div").show();
    });
});
</script>

<style>
.content { display: none }

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
    }
}

@media screen and (max-width: 480px) {
    #pagewrap {
        width: 55%;
    }
}

.game-ui__history {
    background: var(--game-history);
    border-top: 2px solid var(--primary-color-border);
    padding: 8px;
    overflow: hidden;
}
</style>

<div class="game22 game-ui__referee">
    <div>
        <b><img src="/images/gen4/whistle.png" class="va" alt=""> Главный арбитр матча</b>
    </div>
    <div>
        <a href="/judge/index.php?id=<?php echo $judge['id']; ?>">
            <span class="flags c_<?php echo $judge['flag']; ?>_18" style="vertical-align: middle;" title="<?php echo $judge['flag']; ?>"></span> 
            <?php echo htmlspecialchars($judge['name']); ?>
        </a>
    </div>
</div>

<?php
// Если нет подтверждения от обеих команд
if ($game['go1'] != 1 || $game['go2'] != 1) {
    if ($game['time'] > $realtime) {
        $ostime = $game['time'] - $realtime;
        $team1 = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_team` WHERE id='".$game['id_team1']."' LIMIT 1"));
        $team2 = mysql_fetch_assoc(mysql_query("SELECT * FROM `r_team` WHERE id='".$game['id_team2']."' LIMIT 1"));
        
        // Здесь должен быть вывод формы подтверждения игры



       // Форма подтверждения игры
echo '<div class="game-confirmation">';
echo '<div class="game-confirmation__header">';
echo '<h3>Подтверждение участия в матче</h3>';
echo '<div class="game-time">До начала матча осталось: ' . gmdate("H:i:s", $ostime) . '</div>';
echo '</div>';

echo '<div class="game-teams">';
echo '<div class="game-team">';
echo '<div class="team-logo"><img src="/images/teams/' . $team1['id'] . '.png" alt="' . htmlspecialchars($team1['name']) . '"></div>';
echo '<div class="team-name">' . htmlspecialchars($team1['name']) . '</div>';
echo '<div class="team-confirm">' . ($game['go1'] == 1 ? '<span class="confirmed">✓ Подтверждено</span>' : '<span class="not-confirmed">Ожидание</span>') . '</div>';
echo '</div>';

echo '<div class="game-vs">VS</div>';

echo '<div class="game-team">';
echo '<div class="team-logo"><img src="/images/teams/' . $team2['id'] . '.png" alt="' . htmlspecialchars($team2['name']) . '"></div>';
echo '<div class="team-name">' . htmlspecialchars($team2['name']) . '</div>';
echo '<div class="team-confirm">' . ($game['go2'] == 1 ? '<span class="confirmed">✓ Подтверждено</span>' : '<span class="not-confirmed">Ожидание</span>') . '</div>';
echo '</div>';
echo '</div>';

// Кнопка подтверждения (показывается только для менеджера соответствующей команды)
if (($datauser['manager2'] == $game['id_team1'] && $game['go1'] != 1) || 
    ($datauser['manager2'] == $game['id_team2'] && $game['go2'] != 1)) {
    echo '<div class="confirm-button">';
    echo '<a href="?id=' . $id . '&amp;act=add' . $issetun . '" class="mbtn mbtn-green">Подтвердить участие</a>';
    echo '</div>';
}

echo '<div class="game-notice">';
echo '<p>Обе команды должны подтвердить участие в матче. Если одна из команд не подтвердит участие за 15 минут до начала, матч будет отменен автоматически.</p>';
echo '</div>';

echo '</div>'; // закрытие game-confirmation

// Стили для формы подтверждения
echo '<style>
.game-confirmation {
    background: var(--game-history);
    border: 1px solid var(--primary-color-border);
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
}

.game-confirmation__header {
    text-align: center;
    margin-bottom: 15px;
}

.game-confirmation__header h3 {
    margin: 0 0 5px 0;
    color: var(--primary-text);
}

.game-time {
    color: var(--secondary-text);
    font-size: 14px;
}

.game-teams {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
}

.game-team {
    text-align: center;
    flex: 1;
}

.team-logo img {
    max-width: 80px;
    max-height: 80px;
}

.team-name {
    font-weight: bold;
    margin: 5px 0;
}

.team-confirm .confirmed {
    color: #4CAF50;
    font-weight: bold;
}

.team-confirm .not-confirmed {
    color: #FF9800;
}

.game-vs {
    font-size: 24px;
    font-weight: bold;
    margin: 0 20px;
    color: var(--primary-text);
}

.confirm-button {
    text-align: center;
    margin: 20px 0;
}

.game-notice {
    font-size: 13px;
    color: var(--secondary-text);
    text-align: center;
    margin-top: 15px;
}
</style>';
    }
}












// Получаем данные команд
$k1 = mysql_query("SELECT * FROM `r_team` WHERE id='".$game['id_team1']."' LIMIT 1");
$kom1 = mysql_fetch_assoc($k1);

$k2 = mysql_query("SELECT * FROM `r_team` WHERE id='".$game['id_team2']."' LIMIT 1");
$kom2 = mysql_fetch_assoc($k2);

// Автозаполнение составов
complete_team($kom1['id'], $game['id_team1']);
complete_team($kom2['id'], $game['id_team2']);

// Определяем уровень стадиона
$stadium_levels = [
    75000 => 16, 70000 => 15, 65000 => 14, 60000 => 13, 55000 => 12,
    50000 => 11, 45000 => 10, 40000 => 9, 35000 => 8, 30000 => 7,
    25000 => 6, 20000 => 5, 15000 => 4, 10000 => 3, 5000 => 2
];

$stadium = 1;
foreach ($stadium_levels as $capacity => $level) {
    if ($kom1['stadium'] > $capacity) {
        $stadium = $level;
        break;
    }
}

// Определяем название кубка
$cup_names = [
    // Числовые кубки
    "1" => $c_1, "2" => $c_2, "3" => $c_3, "4" => $c_4, "5" => $c_5,
    "6" => $c_6, "7" => $c_7, "8" => $c_8, "9" => $c_9, "10" => $c_10,
    // ... все остальные числовые кубки ...
    "cup_netto" => "Кубок Нетто",
    "cup_charlton" => "Кубок Чарльтона",
    "cup_en" => "Кубок Англии",
    "cup_muller" => "Кубок Мюллера",
    "cup_puskas" => "Кубок Пушкаша",
    "cup_fachetti" => "Кубок Факкетти",
    "cup_kopa" => "Кубок Копа",
    "cup_distefano" => "Кубок Ди Стефано",
    "cup_garrinca" => "Кубок Гарринчи",
    // ... все остальные специальные кубки ...
];

$c_name = isset($cup_names[$game['kubok']]) ? $cup_names[$game['kubok']] : '';

// Шаблоны для разных типов турниров
$tournament_templates = [
    "cup_en" => [
        "css" => "/theme/cups/cup.css",
        "header_class" => "phdr_cup",
        "link" => "/fedcup2/fed.php?act=en",
        "img" => "b_".$game['id_kubok'].".png",
        "img_height" => 64
    ],
    "cup_ru" => [
        "css" => "/theme/cups/cup.css",
        "header_class" => "phdr_cup",
        "link" => "/fedcup2/fed.php?act=ru",
        "img" => "b_".$game['id_kubok'].".png",
        "img_height" => 64
    ],
    // ... шаблоны для других федеральных кубков ...
    "cup_netto" => [
        "css" => "/theme/cups/cup.css",
        "header_class" => "phdr_cup",
        "link" => "/fedcup/fed.php?act=netto",
        "img" => "b_".$game['id_kubok'].".png",
        "img_height" => 64
    ],
    "cup_charlton" => [
        "css" => "/theme/cups/cup.css",
        "header_class" => "phdr_cup",
        "link" => "/fedcup/fed.php?act=charlton",
        "img" => "b_".$game['id_kubok'].".png",
        "img_height" => 64
    ],
    // ... шаблоны для других специальных кубков ...
    "maradona" => [
        "css" => "/theme/cups/maradona.css",
        "header_class" => "phdr_lk",
        "link" => "/".$game['id_kubok'],
        "img" => "b_maradona.png",
        "img_height" => 64
    ],
    "unchamp" => [
        "css" => "/theme/cups/lk.css",
        "header_class" => "phdr_lk",
        "link" => "/".$game['id_kubok'],
        "img" => "cup".$game['id_kubok'].".jpg",
        "img_height" => 64
    ],
    "champ" => [
        "css" => "",
        "header_class" => "phdr",
        "link" => "/champ00/index.php?act=".$game['kubok'],
        "img" => "b_00".$game['kubok'].".png",
        "img_height" => 64
    ],
    "champ_retro" => [
        "css" => "",
        "header_class" => "phdr",
        "link" => "/champ/index.php?act=".$game['id_kubok'],
        "img" => "b_00".$game['kubok'].".png",
        "img_height" => 64
    ],
    "cup" => [
        "css" => "",
        "header_class" => "phdr",
        "link" => "/cup/".$game['id_kubok'],
        "img" => "",
        "img_height" => 0
    ],
    "brend" => [
        "css" => "",
        "header_class" => "phdr",
        "link" => "/brendcup/".$game['id_kubok'],
        "img" => "",
        "img_height" => 0
    ],
    "liga_r" => [
        "css" => "/theme/cups/lc.css",
        "header_class" => "phdr_lc",
        "link" => "/".$game['id_kubok'],
        "img" => "lc2.png",
        "img_height" => 64
    ],
    "liga_r2" => [
        "css" => "/theme/cups/lc.css",
        "header_class" => "phdr_lc",
        "link" => "/".$game['id_kubok'],
        "img" => "lc2.png",
        "img_height" => 64
    ],
    "liberta" => [
        "css" => "/theme/cups/liberta.css",
        "header_class" => "phdr_le",
        "link" => "/".$game['id_kubok']."/",
        "img" => "b_liberta.png",
        "img_height" => 64
    ],
    "le" => [
        "css" => "/theme/cups/le.css",
        "header_class" => "phdr_le",
        "link" => "/".$game['id_kubok'],
        "img" => "b_le.png",
        "img_height" => 64
    ],
    "super_cup" => [
        "css" => "/theme/cups/super_cup.css",
        "header_class" => "phdr_le",
        "link" => "/super_cup/",
        "img" => "b_super_cup.png",
        "img_height" => 64
    ],
    "super_cup2" => [
        "css" => "/theme/cups/super_cup.css",
        "header_class" => "phdr_le",
        "link" => "/super_cup2/",
        "img" => "b_super_cup.png",
        "img_height" => 64
    ],
    "lk" => [
        "css" => "/theme/cups/lk.css",
        "header_class" => "phdr_lk",
        "link" => "/".$game['id_kubok'],
        "img" => "lc2.png",
        "img_height" => 64
    ]
];

// Выводим информацию о турнире
if (isset($tournament_templates[$game['chemp']])) {
    $t = $tournament_templates[$game['chemp']];
    
    if (!empty($t['css'])) {
        echo '<link rel="stylesheet" href="'.$t['css'].'" type="text/css" />';
    }
    
    echo '<div class="'.$t['header_class'].'"><font color="white"><a href="'.$t['link'].'">'.$game['kubok_nomi'].'</a></font>';
    echo '<b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
    
    if (!empty($t['img'])) {
        echo '<div class="top" style="text-align: center">';
        echo '<img src="/images/cup/'.$t['img'].'" height="'.$t['img_height'].'" alt="*">';
        echo '<br><div class="text_top"><b></b></div></div>';
    }
} else {
    // Дефолтный шаблон
    echo '<div class="phdr">Матч<b class="rlink">'.date("d.m.Y H:i", $game['time']).'</b></div>';
    echo '<div class="gmenu"><center><a href="/cup/'.$game['id_kubok'].'"><b>'.$c_name.'</b></a></center></div>';
}

// Выводим информацию о командах
echo '<table id="pallet"><tr>';

// Команда 1
echo '<td width="50%"><center>';
echo '<a href="/team/'.$kom1['id'].'">';
echo '<img src="/manager/logo/'.(!empty($kom1['logo']) ? 'big'.$kom1['logo'] : 'b_0.jpg').'" alt=""/>';
echo '</a>';
echo '<div><a href="/team/'.$kom1['id'].'">';
echo '<span class="flags c_'.$kom1['flag'].'_14" style="vertical-align: middle;" title="'.$kom1['flag'].'"></span> ';
echo htmlspecialchars($kom1['name']).'</a><br>';

// Информация о менеджере команды 1
if ($kom1['id_admin'] > 0) {
    $us1 = mysql_fetch_assoc(mysql_query("SELECT * FROM `users` WHERE `id`=".$kom1['id_admin']." LIMIT 1"));
    if ($us1) {
        $vip_icons = [
            0 => ['img' => 'vip0_m.png', 'title' => 'Базовый аккаунт'],
            1 => ['img' => 'vip1_m.png', 'title' => 'Улучшенный Премиум-аккаунт'],
            2 => ['img' => 'vip2_m.png', 'title' => 'Улучшенный VIP-аккаунт'],
            3 => ['img' => 'vip3_m.png', 'title' => 'Представительский Gold-аккаунт']
        ];
        
        echo '<span style="opacity:0.4"><a href="/vip.php?action=compare&amp;type='.$us1['vip'].'">';
        echo '<img src="/images/ico/'.$vip_icons[$us1['vip']]['img'].'" title="'.$vip_icons[$us1['vip']]['title'].'" ';
        echo 'style="width: 12px;border: none;vertical-align: middle;">';
        echo htmlspecialchars($us1['name']).'</span></a>';
    }
}
echo '</div></center></td>';

// Команда 2
echo '<td><center>';
echo '<a href="/team/'.$kom2['id'].'">';
echo '<img src="/manager/logo/'.(!empty($kom2['logo']) ? 'big'.$kom2['logo'] : 'b_0.jpg').'" alt=""/>';
echo '</a>';
echo '<div><a href="/team/'.$kom2['id'].'">';
echo htmlspecialchars($kom2['name']).' ';
echo '<span class="flags c_'.$kom2['flag'].'_14" style="vertical-align: middle;" title="'.$kom2['flag'].'"></span>';
echo '</a><br>';

// Информация о менеджере команды 2
if ($kom2['id_admin'] > 0) {
    $us2 = mysql_fetch_assoc(mysql_query("SELECT * FROM `users` WHERE `id`=".$kom2['id_admin']." LIMIT 1"));
    if ($us2) {
        $vip_icons = [
            0 => ['img' => 'vip0_m.png', 'title' => 'Базовый аккаунт'],
            1 => ['img' => 'vip1_m.png', 'title' => 'Улучшенный Премиум-аккаунт'],
            2 => ['img' => 'vip2_m.png', 'title' => 'Улучшенный VIP-аккаунт'],
            3 => ['img' => 'vip3_m.png', 'title' => 'Представительский Gold-аккаунт']
        ];
        
        echo '<span style="opacity:0.4"><a href="/vip.php?action=compare&amp;type='.$us2['vip'].'">';
        echo '<img src="/images/ico/'.$vip_icons[$us2['vip']]['img'].'" title="'.$vip_icons[$us2['vip']]['title'].'" ';
        echo 'style="width: 12px;border: none;vertical-align: middle;">';
        echo htmlspecialchars($us2['name']).'</span></a>';
    }
}
echo '</div></center></td>';

echo '</tr></table>';

// Выводим табы для переключения между разделами
echo '<div style="display: flex; text-align: center; width: 100%; justify-content: center; align-items: center;">';
echo '<div class="tab-p but head_button" type="button" id="addteam">Расстановка</div>';
echo '<div class="tab-p but head_button" type="button" id="sostav">Составы</div>';
echo '<div class="tab-p but head_button" type="button" id="h2h">H2H</div>';
echo '<div class="tab-p but head_button" type="button" id="bets">Ставки</div>';
echo '<div class="tab-p but head_button" type="button" id="information">Информация</div>';
echo '</div>';

// Форма подтверждения игры (если нужно)
if ($game['go1'] != 1 || $game['go2'] != 1) {
    if ($game['time'] > $realtime) {
        $ostime = $game['time'] - $realtime;
        
        echo '<div class="game-confirmation">';
        echo '<div class="game-confirmation__header">';
        echo '<h3>Подтверждение участия в матче</h3>';
        echo '<div class="game-time">До начала матча осталось: '.gmdate("H:i:s", $ostime).'</div>';
        echo '</div>';
        
        echo '<div class="game-teams">';
        echo '<div class="game-team">';
        echo '<div class="team-logo"><img src="/images/teams/'.$kom1['id'].'.png" alt="'.htmlspecialchars($kom1['name']).'"></div>';
        echo '<div class="team-name">'.htmlspecialchars($kom1['name']).'</div>';
        echo '<div class="team-confirm">'.($game['go1'] == 1 ? '<span class="confirmed">✓ Подтверждено</span>' : '<span class="not-confirmed">Ожидание</span>').'</div>';
        echo '</div>';
        
        echo '<div class="game-vs">VS</div>';
        
        echo '<div class="game-team">';
        echo '<div class="team-logo"><img src="/images/teams/'.$kom2['id'].'.png" alt="'.htmlspecialchars($kom2['name']).'"></div>';
        echo '<div class="team-name">'.htmlspecialchars($kom2['name']).'</div>';
        echo '<div class="team-confirm">'.($game['go2'] == 1 ? '<span class="confirmed">✓ Подтверждено</span>' : '<span class="not-confirmed">Ожидание</span>').'</div>';
        echo '</div>';
        echo '</div>';
        
        // Кнопка подтверждения (показывается только для менеджера соответствующей команды)
        if (($datauser['manager2'] == $game['id_team1'] && $game['go1'] != 1) || 
            ($datauser['manager2'] == $game['id_team2'] && $game['go2'] != 1)) {
            echo '<div class="confirm-button">';
            echo '<a href="?id='.$id.'&amp;act=add'.$issetun.'" class="mbtn mbtn-green">Подтвердить участие</a>';
            echo '</div>';
        }
        
        echo '<div class="game-notice">';
        echo '<p>Обе команды должны подтвердить участие в матче. Если одна из команд не подтвердит участие за 15 минут до начала, матч будет отменен автоматически.</p>';
        echo '</div>';
        echo '</div>';
    }
}

// Выводим раздел с расстановкой
echo '<div id="addteamdiv" class="content">';
echo '<div class="phdr" style="text-align:center">Стартовый состав</div>';
echo '<div id="pagewrap"><div style="display: flex; justify-content: space-around;">';
// Здесь будет вывод расстановки команд
echo '</div></div>';
echo '</div>';

// Скрипт для переключения между табами
echo '<script>
$(function() {
    $(".but").on("click", function(e) {
        e.preventDefault();
        $(".content").hide();
        $("#"+this.id+"div").show();
    });
});
</script>';

	//////////////////////////tactics1


/**
 * Функция для безопасного получения данных игрока
 * 
 * @param int $playerId ID игрока
 * @return array|false Данные игрока или false если не найден
 */
function getPlayerData($playerId) {
    if (empty($playerId)) return false;
    
    $query = "SELECT * FROM `r_player` WHERE `id` = '" . mysql_real_escape_string($playerId) . "'";
    $result = mysql_query($query);
    
    return ($result && mysql_num_rows($result) > 0) ? mysql_fetch_assoc($result) : false;
}

/**
 * Функция для отображения карточки игрока
 * 
 * @param array $player Данные игрока
 * @param array $team Данные команды
 * @param string $position Позиция игрока
 * @param string $title Заголовок для ссылки
 * @param bool $isGoalkeeper Флаг вратаря
 * @param array $game Данные игры
 * @return string HTML код карточки игрока
 */
function renderPlayer($player, $team, $position, $title, $isGoalkeeper = false, $game = array()) {
    if (!$player) return '';
    
    $output = '<a href="/player/'.$player['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока ('.$title.')">';
    $output .= '<img src="/images/forma/'.($isGoalkeeper ? '59.gif' : $team['forma'].'.gif').'" alt=""><br>';
    
    // Отображение желтой карточки за пропуск матча
    if (!empty($player['utime'])) {
        $output .= '<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';
    }
    
    // Отображение не сгоревших желтых карточек
    $cardTypes = array(
        'champ_retro' => 'yc',
        'unchamp' => 'yc_unchamp',
        'liga_r' => 'yc_liga_r',
        'le' => 'yc_le'
    );
    
    if (!empty($game['chemp']) && isset($cardTypes[$game['chemp']])) {
        $cardType = $cardTypes[$game['chemp']];
        if (!empty($player[$cardType])) {
            $titleText = '';
            switch ($game['chemp']) {
                case 'champ_retro': $titleText = 'Кол-во НЕ сгоревших желтых карточек в Чемпионате'; break;
                case 'unchamp': $titleText = 'Кол-во НЕ сгоревших желтых карточек'; break;
                case 'liga_r': $titleText = 'Кол-во НЕ сгоревших желтых карточек в КЕЧ'; break;
                case 'le': $titleText = 'Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА'; break;
            }
            $output .= '<div class="player-cards-1" title="'.$titleText.'">'.$player[$cardType].'</div>';
        }
    }
    
    $output .= '<div class="team_name2">';
    $output .= !empty($player['utime']) ? '<span class="__fio2">' : '<span class="schema_plname">';
    $output .= full_name_to_short($player['name']).'</span>';
    $output .= '</div></a>';
    
    return $output;
}

/**
 * Получает данные команды по ID
 * 
 * @param int $teamId ID команды
 * @return array|false Данные команды или false если не найдена
 */
function getTeamData($teamId) {
    if (empty($teamId)) return false;
    
    $query = "SELECT * FROM `r_team` WHERE id = '" . mysql_real_escape_string($teamId) . "' LIMIT 1";
    $result = mysql_query($query);
    
    return ($result && mysql_num_rows($result) > 0) ? mysql_fetch_assoc($result) : false;
}

/**
 * Отображает тактическую схему команды
 * 
 * @param array $team Данные команды
 * @param array $players Массив данных игроков
 * @param string $schema Название схемы
 * @param array $game Данные игры
 */
function renderTacticalScheme($team, $players, $schema, $game = array()) {
    echo '<table class="schema_table2" border="0">';
    
    switch ($schema) {
        case "4-3-3":
            echo '<tbody>
                <tr style="height:35%"><td colspan="4">
                    <table border="0" style="height:100%; width:100%">
                        <tbody>
                            <tr>
                                <td style="padding-top:10%">'.renderPlayer($players[9], $team, 'Lf', 'Lf', false, $game).'</td>
                                <td style="padding-top:10%">'.renderPlayer($players[11], $team, 'Cf', 'Cf', false, $game).'</td>
                                <td style="padding-top:10%">'.renderPlayer($players[10], $team, 'Rf', 'Rf', false, $game).'</td>
                            </tr>
                        </tbody>
                    </table>
                </td></tr>
                
                <tr style="height:30%"><td colspan="4">
                    <table border="0" style="height:100%; width:100%">
                        <tbody>
                            <tr>
                                <td style="padding-top:0%">'.renderPlayer($players[6], $team, 'Cm', 'Cm', false, $game).'</td>
                                <td style="padding-top:0%">'.renderPlayer($players[7], $team, 'Rm', 'Rm', false, $game).'</td>
                                <td style="padding-top:0%">'.renderPlayer($players[8], $team, 'Cm', 'Cm', false, $game).'</td>
                            </tr>
                        </tbody>
                    </table>
                </td></tr>
                
                <tr style="height:20%">
                    <td>'.renderPlayer($players[2], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[3], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[4], $team, 'Rd', 'Rd', false, $game).'</td>
                    <td>'.renderPlayer($players[5], $team, 'Rd', 'Rd', false, $game).'</td>
                </tr>
                
                <tr style="height:15%">
                    <td colspan="4">'.renderPlayer($players[1], $team, 'Gk', 'Gk', true, $game).'</td>
                </tr>
            </tbody>';
            break;
            
        case "3-4-3":
            echo '<tbody>
                <tr style="height:35%">
                    <td style="padding-top:0%">'.renderPlayer($players[9], $team, 'Lf', 'Lf', false, $game).'</td>
                    <td>'.renderPlayer($players[11], $team, 'Cf', 'Cf', false, $game).'</td>
                    <td style="padding-top:0%">'.renderPlayer($players[10], $team, 'Rf', 'Rf', false, $game).'</td>
                </tr>
                
                <tr style="height:30%">
                    <td style="padding-top:0%" rowspan="2">'.renderPlayer($players[6], $team, 'Cm', 'Cm', false, $game).'</td>
                    <td style="padding-top:0%">'.renderPlayer($players[7], $team, 'Cm', 'Cm', false, $game).'</td>
                    <td style="padding-top:0%" rowspan="2">'.renderPlayer($players[8], $team, 'Rm', 'Rm', false, $game).'</td>
                </tr>
                
                <tr style="height:5%">
                    <td>'.renderPlayer($players[5], $team, 'Rd', 'Rd', false, $game).'</td>
                </tr>
                
                <tr style="height:15%">
                    <td>'.renderPlayer($players[2], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[3], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[4], $team, 'Rd', 'Rd', false, $game).'</td>
                </tr>
                
                <tr style="height:15%">
                    <td colspan="3">'.renderPlayer($players[1], $team, 'Gk', 'Gk', true, $game).'</td>
                </tr>
            </tbody>';
            break;
            
        case "2-5-3":
            echo '<tbody>
                <tr style="height:30%"><td colspan="4">
                    <table border="0" style="height:100%; width:100%">
                        <tbody>
                            <tr>
                                <td style="padding-top:10%">'.renderPlayer($players[9], $team, 'Lf', 'Lf', false, $game).'</td>
                                <td style="padding-top:10%">'.renderPlayer($players[11], $team, 'Cf', 'Cf', false, $game).'</td>
                                <td style="padding-top:10%">'.renderPlayer($players[10], $team, 'Rf', 'Rf', false, $game).'</td>
                            </tr>
                        </tbody>
                    </table>
                </td></tr>
                
                <tr style="height:40%">
                    <td style="padding-top:0%" rowspan="2">'.renderPlayer($players[8], $team, 'Rm', 'Rm', false, $game).'</td>
                    <td style="padding-top:0%">'.renderPlayer($players[7], $team, 'Cm', 'Cm', false, $game).'</td>
                    <td style="padding-top:0%">'.renderPlayer($players[6], $team, 'Cm', 'Cm', false, $game).'</td>
                    <td style="padding-top:0%" rowspan="2">'.renderPlayer($players[4], $team, 'Rd', 'Rd', false, $game).'</td>
                </tr>
                
                <tr style="height:5%">
                    <td colspan="2">'.renderPlayer($players[5], $team, 'Rd', 'Rd', false, $game).'</td>
                </tr>
                
                <tr style="height:15%"><td colspan="4">
                    <table border="0" style="height:100%; width:100%">
                        <tbody>
                            <tr>
                                <td>'.renderPlayer($players[2], $team, 'Ld', 'Ld', false, $game).'</td>
                                <td>'.renderPlayer($players[3], $team, 'Ld', 'Ld', false, $game).'</td>
                            </tr>
                        </tbody>
                    </table>
                </td></tr>
                
                <tr style="height:15%">
                    <td colspan="4">'.renderPlayer($players[1], $team, 'Gk', 'Gk', true, $game).'</td>
                </tr>
            </tbody>';
            break;
            
        case "5-3-2":
            echo '<tbody>
                <tr style="height:40%"><td colspan="4">
                    <table border="0" style="height:100%; width:100%">
                        <tbody>
                            <tr>
                                <td>'.renderPlayer($players[11], $team, 'Cf', 'Cf', false, $game).'</td>
                                <td>'.renderPlayer($players[9], $team, 'Lf', 'Lf', false, $game).'</td>
                            </tr>
                            <tr>
                                <td>'.renderPlayer($players[6], $team, 'Cm', 'Cm', false, $game).'</td>
                                <td>'.renderPlayer($players[8], $team, 'Rm', 'Rm', false, $game).'</td>
                            </tr>
                        </tbody>
                    </table>
                </td></tr>
                
                <tr style="height:15%">
                    <td colspan="4">'.renderPlayer($players[7], $team, 'Cm', 'Cm', false, $game).'</td>
                </tr>
                
                <tr style="height:15%">
                    <td>'.renderPlayer($players[2], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[3], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[5], $team, 'Rd', 'Rd', false, $game).'</td>
                    <td>'.renderPlayer($players[4], $team, 'Rd', 'Rd', false, $game).'</td>
                </tr>
                
                <tr style="height:15%">
                    <td colspan="4">'.renderPlayer($players[10], $team, 'Rf', 'Rf', false, $game).'</td>
                </tr>
                
                <tr style="height:15%">
                    <td colspan="4">'.renderPlayer($players[1], $team, 'Gk', 'Gk', true, $game).'</td>
                </tr>
            </tbody>';
            break;
            
        case "4-4-2":
            echo '<tbody>
                <tr style="height:40%"><td colspan="4">
                    <table border="0" style="height:100%; width:100%">
                        <tbody>
                            <tr>
                                <td>'.renderPlayer($players[11], $team, 'Cf', 'Cf', false, $game).'</td>
                                <td>'.renderPlayer($players[9], $team, 'Lf', 'Lf', false, $game).'</td>
                            </tr>
                        </tbody>
                    </table>
                </td></tr>
                
                <tr style="height:10%">
                    <td rowspan="2">'.renderPlayer($players[6], $team, 'Cm', 'Cm', false, $game).'</td>
                    <td colspan="2">'.renderPlayer($players[7], $team, 'Cm', 'Cm', false, $game).'</td>
                    <td rowspan="2">'.renderPlayer($players[8], $team, 'Rm', 'Rm', false, $game).'</td>
                </tr>
                
                <tr style="height:10%">
                    <td colspan="2">'.renderPlayer($players[10], $team, 'Rf', 'Rf', false, $game).'</td>
                </tr>
                
                <tr style="height:30%">
                    <td>'.renderPlayer($players[2], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[3], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[5], $team, 'Rd', 'Rd', false, $game).'</td>
                    <td>'.renderPlayer($players[4], $team, 'Rd', 'Rd', false, $game).'</td>
                </tr>
                
                <tr style="height:10%">
                    <td colspan="4">'.renderPlayer($players[1], $team, 'Gk', 'Gk', true, $game).'</td>
                </tr>
            </tbody>';
            break;
            
        case "3-5-2":
            echo '<tbody>
                <tr style="height:25%"><td colspan="4">
                    <table border="0" style="height:100%; width:100%">
                        <tbody>
                            <tr>
                                <td>'.renderPlayer($players[11], $team, 'Cf', 'Cf', false, $game).'</td>
                                <td>'.renderPlayer($players[9], $team, 'Lf', 'Lf', false, $game).'</td>
                            </tr>
                        </tbody>
                    </table>
                </td></tr>
                
                <tr>
                    <td rowspan="3" valign="bottom">'.renderPlayer($players[6], $team, 'Cm', 'Cm', false, $game).'</td>
                    <td colspan="2">'.renderPlayer($players[8], $team, 'Rm', 'Rm', false, $game).'</td>
                    <td rowspan="3" valign="bottom">'.renderPlayer($players[10], $team, 'Rf', 'Rf', false, $game).'</td>
                </tr>
                
                <tr>
                    <td colspan="2">'.renderPlayer($players[7], $team, 'Cm', 'Cm', false, $game).'</td>
                </tr>
                
                <tr>
                    <td colspan="2">'.renderPlayer($players[5], $team, 'Rd', 'Rd', false, $game).'</td>
                </tr>
                
                <tr style="height:15%"><td colspan="4">
                    <table border="0" style="height:100%; width:100%">
                        <tbody>
                            <tr>
                                <td>'.renderPlayer($players[2], $team, 'Ld', 'Ld', false, $game).'</td>
                                <td>'.renderPlayer($players[3], $team, 'Ld', 'Ld', false, $game).'</td>
                                <td style="">'.renderPlayer($players[4], $team, 'Rd', 'Rd', false, $game).'</td>
                            </tr>
                        </tbody>
                    </table>
                </td></tr>
                
                <tr style="height:10%">
                    <td colspan="4">'.renderPlayer($players[1], $team, 'Gk', 'Gk', true, $game).'</td>
                </tr>
            </tbody>';
            break;
            
        case "5-4-1":
            echo '<tbody>
                <tr style="height:40%">
                    <td colspan="4">'.renderPlayer($players[11], $team, 'Cf', 'Cf', false, $game).'</td>
                </tr>
                
                <tr style="height:15%">
                    <td rowspan="2">'.renderPlayer($players[6], $team, 'Cm', 'Cm', false, $game).'</td>
                    <td colspan="2">'.renderPlayer($players[7], $team, 'Cm', 'Cm', false, $game).'</td>
                    <td rowspan="2">'.renderPlayer($players[8], $team, 'Rm', 'Rm', false, $game).'</td>
                </tr>
                
                <tr style="height:5%">
                    <td colspan="2">'.renderPlayer($players[9], $team, 'Lf', 'Lf', false, $game).'</td>
                </tr>
                
                <tr style="height:15%">
                    <td>'.renderPlayer($players[2], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[3], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[5], $team, 'Rd', 'Rd', false, $game).'</td>
                    <td>'.renderPlayer($players[4], $team, 'Rd', 'Rd', false, $game).'</td>
                </tr>
                
                <tr style="height:15%">
                    <td colspan="4">'.renderPlayer($players[10], $team, 'Rf', 'Rf', false, $game).'</td>
                </tr>
                
                <tr style="height:10%">
                    <td colspan="4">'.renderPlayer($players[1], $team, 'Gk', 'Gk', true, $game).'</td>
                </tr>
            </tbody>';
            break;
            
        case "4-5-1":
            echo '<tbody>
                <tr style="height:35%">
                    <td colspan="4">'.renderPlayer($players[11], $team, 'Cf', 'Cf', false, $game).'</td>
                </tr>
                
                <tr style="height:10%">
                    <td colspan="4">'.renderPlayer($players[6], $team, 'Cm', 'Cm', false, $game).'</td>
                </tr>
                
                <tr>
                    <td rowspan="2">'.renderPlayer($players[8], $team, 'Rm', 'Rm', false, $game).'</td>
                    <td colspan="2">'.renderPlayer($players[7], $team, 'Cm', 'Cm', false, $game).'</td>
                    <td rowspan="2">'.renderPlayer($players[9], $team, 'Lf', 'Lf', false, $game).'</td>
                </tr>
                
                <tr style="height:10%">
                    <td colspan="2">'.renderPlayer($players[10], $team, 'Rf', 'Rf', false, $game).'</td>
                </tr>
                
                <tr style="height:20%">
                    <td>'.renderPlayer($players[2], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[3], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[5], $team, 'Rd', 'Rd', false, $game).'</td>
                    <td>'.renderPlayer($players[4], $team, 'Rd', 'Rd', false, $game).'</td>
                </tr>
                
                <tr style="height:15%">
                    <td colspan="4">'.renderPlayer($players[1], $team, 'Gk', 'Gk', true, $game).'</td>
                </tr>
            </tbody>';
            break;
            
        case "6-3-1":
            echo '<tbody>
                <tr style="height:45%">
                    <td colspan="4">'.renderPlayer($players[11], $team, 'Cf', 'Cf', false, $game).'</td>
                </tr>
                
                <tr style="height:25%">
                    <td>'.renderPlayer($players[6], $team, 'Cm', 'Cm', false, $game).'</td>
                    <td colspan="2">'.renderPlayer($players[7], $team, 'Cm', 'Cm', false, $game).'</td>
                    <td>'.renderPlayer($players[8], $team, 'Rm', 'Rm', false, $game).'</td>
                </tr>
                
                <tr style="height:10%">
                    <td>'.renderPlayer($players[2], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[3], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[5], $team, 'Rd', 'Rd', false, $game).'</td>
                    <td>'.renderPlayer($players[4], $team, 'Rd', 'Rd', false, $game).'</td>
                </tr>
                
                <tr style="height:10%">
                    <td colspan="2">'.renderPlayer($players[9], $team, 'Lf', 'Lf', false, $game).'</td>
                    <td colspan="2">'.renderPlayer($players[10], $team, 'Rf', 'Rf', false, $game).'</td>
                </tr>
                
                <tr style="height:10%">
                    <td colspan="4">'.renderPlayer($players[1], $team, 'Gk', 'Gk', true, $game).'</td>
                </tr>
            </tbody>';
            break;
            
        default:
            // По умолчанию выводим схему 4-3-3
            echo '<tbody>
                <tr style="height:35%"><td colspan="4">
                    <table border="0" style="height:100%; width:100%">
                        <tbody>
                            <tr>
                                <td style="padding-top:10%">'.renderPlayer($players[9], $team, 'Lf', 'Lf', false, $game).'</td>
                                <td style="padding-top:10%">'.renderPlayer($players[11], $team, 'Cf', 'Cf', false, $game).'</td>
                                <td style="padding-top:10%">'.renderPlayer($players[10], $team, 'Rf', 'Rf', false, $game).'</td>
                            </tr>
                        </tbody>
                    </table>
                </td></tr>
                
                <tr style="height:30%"><td colspan="4">
                    <table border="0" style="height:100%; width:100%">
                        <tbody>
                            <tr>
                                <td style="padding-top:0%">'.renderPlayer($players[6], $team, 'Cm', 'Cm', false, $game).'</td>
                                <td style="padding-top:0%">'.renderPlayer($players[7], $team, 'Rm', 'Rm', false, $game).'</td>
                                <td style="padding-top:0%">'.renderPlayer($players[8], $team, 'Cm', 'Cm', false, $game).'</td>
                            </tr>
                        </tbody>
                    </table>
                </td></tr>
                
                <tr style="height:20%">
                    <td>'.renderPlayer($players[2], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[3], $team, 'Ld', 'Ld', false, $game).'</td>
                    <td>'.renderPlayer($players[4], $team, 'Rd', 'Rd', false, $game).'</td>
                    <td>'.renderPlayer($players[5], $team, 'Rd', 'Rd', false, $game).'</td>
                </tr>
                
                <tr style="height:15%">
                    <td colspan="4">'.renderPlayer($players[1], $team, 'Gk', 'Gk', true, $game).'</td>
                </tr>
            </tbody>';
    }
    
    echo '</table><br>';
}

// ====================== Тактика первой команды ======================
if (!empty($arr1['id'])) {
    $team1 = getTeamData($arr1['id']);
    if ($team1) {
        // Получаем данные всех игроков команды
        $players1 = array();
        for ($i = 1; $i <= 11; $i++) {
            $players1[$i] = getPlayerData($team1['i'.$i]);
        }
        
        // Определяем схему (по умолчанию 4-3-3)
        $schema1 = isset($arr1['shema']) ? $arr1['shema'] : '4-3-3';
        
        // Выводим тактику первой команды
        renderTacticalScheme($team1, $players1, $schema1, $game);
    }
}

// ====================== Тактика второй команды ======================
if (!empty($arr2['id'])) {
    $team2 = getTeamData($arr2['id']);
    if ($team2) {
        // Получаем данные всех игроков команды
        $players2 = array();
        for ($i = 1; $i <= 11; $i++) {
            $players2[$i] = getPlayerData($team2['i'.$i]);
        }
        
        // Определяем схему (по умолчанию 4-3-3)
        $schema2 = isset($arr2['shema']) ? $arr2['shema'] : '4-3-3';
        
        // Выводим тактику второй команды
        renderTacticalScheme($team2, $players2, $schema2, $game);
    }
}

//////////////////////////tactics2

		echo'</div>';
echo"</div>";

echo'</div>';echo '<div id="betsdiv" class="content">';
// Bets games section
echo '<link rel="stylesheet" href="/game/bets.css" type="text/css" />';

// Validate user and input
if (!$user_id) {
    header("Location: #");
    exit;
}

if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: #");
    exit;
}

$id = (int)$_GET['id'];

// Get user's money
$ratRes = mysql_fetch_array(mysql_query("SELECT `money` FROM `r_team` WHERE `id` = $datauser[manager2] LIMIT 1;"));
$rat = $ratRes['money'];

// Get game data
$game6 = mysql_fetch_array(mysql_query("SELECT * FROM `r_game` WHERE `id_match` = '$id' LIMIT 1;"));
$game5 = mysql_fetch_array(mysql_query("SELECT * FROM `t_games` WHERE `champ`='".$game6['chemp']."' AND `id_match` = ".$game6['id_match']." LIMIT 1;"));

if (!$game5) {
    echo display_error('На этот матч нет ставок');
} else {
    $betCloseTime = $game6['time'] - 60;
    
    if ($betCloseTime > $realtime) {
        $teams = explode('|', $game5['teams']); 
        $teamsCount = count($teams);
        $coefs = explode('|', $game5['coefs']);

        echo '<div class="phdr" style="text-align:center">Сделать ставку</div>';
        
        if ($ratRes['money'] >= 10) {
            if (isset($_POST['submit'])) {
                $winner = false;
                if (!empty($_POST['winner']) && ctype_digit($_POST['winner'])) {
                    $winner = (int)$_POST['winner'];
                }
                
                $mil = false;
                if (!empty($_POST['mil']) && ctype_digit($_POST['mil']) && $_POST['mil'] >= 10 && $_POST['mil'] <= $ratRes['money']) {
                    $mil = (int)$_POST['mil'];
                }

                if ($winner && $mil) {
                    $query = mysql_query("INSERT INTO `t_mils` VALUES(0, '$id', '$user_id', '$mil', '$winner');");
                    
                    if ($query) {
                        if ($winner == 1) {
                            $betText = $teams[0].' <b>П1</b>';
                        } elseif ($winner == 2) {
                            $betText = $teams[1].' <b>П2</b>';
                        } else {
                            $betText = $teams[0].'-'.$teams[1].' <b>Ничья</b>';
                        }
                        
                        mysql_query("UPDATE `r_team` SET `money` = (`money` - $mil) WHERE `id` = $datauser[manager2];");
                        mysql_query("INSERT INTO `news` SET
                            `time` = '$realtime',
                            `money` = '-$mil',
                            `text` = 'Ставка $betText',
                            `team_id` = '".$kom['id']."';");
                        
                        header("Location: #");
                        exit;
                    } else {
                        echo '<div class="rmenu">Произошла ошибка. Приносим вам свои извинения.</div>';
                    }
                } else {
                    echo '<div class="rmenu">Вы заполнили не все поля либо заполнили их не верно</div>';
                }
            }
            
            // Bet form
            echo '<div class="menu">';
            echo '<form action="?id='.$id.'" method="POST">';
            echo '
            <div>
               <div class="market-group-box--z23Vvd">
                  <div class="header--2fWAgi _expanded--2iUfDc">
                     <div class="section--5JAm4a">
                        <div class="star--43zWzf _off--w7ZWgi"></div>
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
                                           <div class="t--4zyb4K text-state-normal--1L40o3"><input type="radio" name="winner" value="1"/>'.$teams[0].'</div>
                                           <div class="v--1iHcVX value-state-normal--4JL4xN">'.$coefs[0].'</div>
                                        </div>
                                    </label>
                                 </div>
                              </div>
                              <div class="cell-wrap--LHnTwg">
                                 <div style="display: flex; flex: 1 1 0%; position: relative; height: 30px;">
                                    <label>
                                        <div class="factor-td--3ZZULU cell-state-normal--iYJc0x">
                                           <div class="t--4zyb4K text-state-normal--1L40o3"><input type="radio" name="winner" value="'.($teamsCount + 1).'"/>Ничья</div>
                                           <div class="v--1iHcVX value-state-normal--4JL4xN">'.$coefs[$teamsCount].'</div>
                                        </div>
                                    </label>
                                 </div>
                              </div>
                              <div class="cell-wrap--LHnTwg">
                                 <div style="display: flex; flex: 1 1 0%; position: relative; height: 30px;">
                                    <label>
                                        <div class="factor-td--3ZZULU cell-state-normal--iYJc0x">
                                           <div class="t--4zyb4K text-state-normal--1L40o3"><input type="radio" name="winner" value="2"/>'.$teams[1].'</div>
                                           <div class="v--1iHcVX value-state-normal--4JL4xN">'.$coefs[1].'</div>
                                        </div>
                                    </label>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>';
            
            echo '<br/><label>Ставка: <input type="text" name="mil" value="10" maxlength="'.strlen($ratRes['money']).'"/>(10-'.$ratRes['money'].')</label><br/>';
            echo '<input type="submit" name="submit" value="Поставить"/>';
            echo '</form>';
            echo '</div>';
        } else {
            echo '<div class="rmenu">У вас не достаточно денег для ставки! Минимальная ставка: 10 <img src="/images/m_game3.gif" class="money"></div>';
        }
    } else {
        echo '<div class="rmenu">Приём ставок окончен</div>';
    }
    
    // Show user's bets history
    $allMils = mysql_num_rows(mysql_query("SELECT * FROM `t_mils` WHERE `user`='$user_id' AND `refid` = ".$game6['id'].";"));
    $g65 = mysql_query("SELECT * FROM `t_mils` WHERE `user`='$user_id' AND `refid` = ".$game6['id'].";");
    
    echo '<center>';
    echo '<div class="gmenu">Всего ставок: '.$allMils.'</div>';
    
    while ($gamerrr = mysql_fetch_array($g65)) {
        echo '
        <tr class="coupon__table-row--6OoyA1">
           <td class="coupon__table-col--3p8NRM" colspan="2">
              <span class="coupon__sport-icon--4VDiOV _use_color_settings--SQwskl" style="background-image: url(&quot;//origin.bk6bba-resources.com/ContentCommon/Logotypes/SportKinds/new-design/white_new/1-football.svg&quot;);"></span>
              <span>'.$game6['name_team1'].' – '.$game6['name_team2'].'</span>
           </td>
           <td class="coupon__table-col--3p8NRM _type_stake--4UOCA4">
              <span><b>';
              
        if ($gamerrr['winner'] == 1) {
            echo 'П1';
        } elseif ($gamerrr['winner'] == 2) {
            echo 'П2';
        } else {
            echo 'Ничья';
        }
        
        echo '</b></span>';
        echo ' '.$gamerrr['mil'].' <img src="/images/m_game3.gif" class="money">';
        echo '</td>
           <td class="coupon__table-col--3p8NRM coupon__table-status--77SBfz _type_factor-value--5U80LG _type_label--4DkPHs _status_lose--h4Xx4x" title="Пари не сыграло. (1:0)">
              <span class="coupon__table-stake--PyBpdc">';
              
        if ($gamerrr['winner'] == 1) {
            echo $coefs[0];
        } elseif ($gamerrr['winner'] == 2) {
            echo $coefs[1];
        } else {
            echo $coefs[2];
        }
        
        echo '</span>';
        echo '</td>
        </tr>
        <br>';
    }
    echo '</center>';
    echo '<br>';
}

echo '</div>';
echo '<div id="h2hdiv" class="content">';
// History games section
require_once("../game/history3.php");
echo '</div>';
echo '<div id="informationdiv" class="content">';
// Information games section

// Stadium info
$std11 = mysql_fetch_array(mysql_query("SELECT * FROM `r_stadium` WHERE `id`='".$game6['id_stadium']."' LIMIT 1;"));
if ($game6['id_stadium']) {
    echo '<div class="game-ui__history">
        <div style="float: left; margin-right: 40px;">';
    
    if ($std11['std']) {
        echo '<img src="/images/stadium/'.$game6['id_stadium'].'.jpg" style="width: 480px; height: 240px; border: 1px solid var(--primary-color-border); margin-top:7px;" alt="">';
    } else {
        echo '<img src="/images/stadium/stadium.jpg" style="width: 480px; height: 240px; border: 1px solid var(--primary-color-border); margin-top:7px;" alt="">';
    }
    
    echo '</div>
        <div style="font-size:140%;margin-top:20px;">Место проведения матча</div>
        <div style="font-size:170%;">'.$std11['name'].'</div>
        <div style="font-size:160%;color:green;">'.$game6['zritel'].' зрителей</div>';
    
    if ($game6['chemp'] == '!frend') {
        echo '<div>город '.$std11['city'].'</div>';
    }
    
    echo '</div>';
}

// Team info
$jam1 = mysql_fetch_array(mysql_query("SELECT * FROM `r_team` WHERE id='".$game6['id_team1']."' LIMIT 1;"));
$jam2 = mysql_fetch_array(mysql_query("SELECT * FROM `r_team` WHERE id='".$game6['id_team2']."' LIMIT 1;"));

echo '</div>';

// Team lineups
echo '<div id="sostavdiv" class="content">';
echo '<div class="phdr orangebk"><center><b>Состав</b></center></div>';
echo '<table id="example" class="t-table">';
echo '<tr bgcolor="40B832" align="center" class="whiteheader">';
echo '<td><b>'.$jam1['name'].'</b></td><td><b>'.$jam2['name'].'</b></td></tr>';
echo '<tr>';

// Home team lineup
$rq = mysql_query("SELECT * FROM `r_player` WHERE `team`='".$jam1['id']."' AND (`id`='".$jam1['i1']."' OR `id`='".$jam1['i2']."' OR `id`='".$jam1['i3']."' OR `id`='".$jam1['i4']."' OR `id`='".$jam1['i5']."' OR `id`='".$jam1['i6']."' OR `id`='".$jam1['i7']."' OR `id`='".$jam1['i8']."' OR `id`='".$jam1['i9']."' OR `id`='".$jam1['i10']."' OR `id`='".$jam1['i11']."') AND `sostav`!='4' ORDER BY line ASC, poz ASC;");

echo '<td width="50%">';
$d = 1;
while ($parr1 = mysql_fetch_array($rq)) {
    $bgColor = '';
    if ($datauser['black'] == 0) {
        switch ($parr1['line']) {
            case 1: $bgColor = '#fff7e7'; break;
            case 2: $bgColor = '#f7ffef'; break;
            case 3: $bgColor = '#e7f7ff'; break;
            case 4: $bgColor = '#ffefef'; break;
        }
    } else {
        switch ($parr1['line']) {
            case 1: $bgColor = '#434343'; break;
            case 2: $bgColor = '#363636'; break;
            case 3: $bgColor = '#262525'; break;
            case 4: $bgColor = '#1e1e1e'; break;
        }
    }
    
    echo '<div style="background-color:'.$bgColor.'" class="gmenu2">';
    echo '<span class="flags c_'.$parr1['flag'].'_18" style="vertical-align: middle;" title="'.$parr1['flag'].'"></span> ';
    echo '<b>'.$parr1['poz'].'</b> ';
    echo '<a href="/player/'.$parr1['id'].'">'.$parr1['name'].' ';
    
    switch ($game6['chemp']) {
        case "champ_retro":
            if ($parr1['yc'] > 0) {
                echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$parr1['yc'].'</div>';
            }
            break;
        case "unchamp":
            if ($parr1['yc_unchamp'] > 0) {
                echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Союзном Чемпионате">'.$parr1['yc_unchamp'].'</div>';
            }
            break;
        case "liga_r":
            if ($parr1['yc_liga_r'] > 0) {
                echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Ретро Кубке Чемпионов">'.$parr1['yc_liga_r'].'</div>';
            }
            break;
        case "le":
            if ($parr1['yc_le'] > 0) {
                echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$parr1['yc_le'].'</div>';
            }
            break;
    }
    
    if ($parr1['rc'] > 0) {
        echo '<div class="player-cards-2" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$parr1['rc'].'</div>';
    }
    
    echo '</a>';
    echo '</div>';
    ++$d;
}
echo '</td>';

// Away team lineup
$rq2 = mysql_query("SELECT * FROM `r_player` WHERE `team`='".$jam2['id']."' AND (`id`='".$jam2['i1']."' OR `id`='".$jam2['i2']."' OR `id`='".$jam2['i3']."' OR `id`='".$jam2['i4']."' OR `id`='".$jam2['i5']."' OR `id`='".$jam2['i6']."' OR `id`='".$jam2['i7']."' OR `id`='".$jam2['i8']."' OR `id`='".$jam2['i9']."' OR `id`='".$jam2['i10']."' OR `id`='".$jam2['i11']."') AND `sostav`!='4' ORDER BY line ASC, poz ASC;");
echo '<td width="50%">';
$d = 1;
while ($parr2 = mysql_fetch_array($rq2)) {
    $bgColor = '';
    if ($datauser['black'] == 0) {
        switch ($parr2['line']) {
            case 1: $bgColor = '#fff7e7'; break;
            case 2: $bgColor = '#f7ffef'; break;
            case 3: $bgColor = '#e7f7ff'; break;
            case 4: $bgColor = '#ffefef'; break;
        }
    } else {
        switch ($parr2['line']) {
            case 1: $bgColor = '#434343'; break;
            case 2: $bgColor = '#363636'; break;
            case 3: $bgColor = '#262525'; break;
            case 4: $bgColor = '#1e1e1e'; break;
        }
    }
    
    echo '<div style="background-color:'.$bgColor.'" class="gmenu2">';
    echo '<span class="flags c_'.$parr2['flag'].'_18" style="vertical-align: middle;" title="'.$parr2['flag'].'"></span> ';
    echo '<b>'.$parr2['poz'].'</b> <a href="/player/'.$parr2['id'].'">'.$parr2['name'].' ';
    
    switch ($game6['chemp']) {
        case "champ_retro":
            if ($parr2['yc'] > 0) {
                echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$parr2['yc'].'</div>';
            }
            break;
        case "unchamp":
            if ($parr2['yc_unchamp'] > 0) {
                echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Союзном Чемпионате">'.$parr2['yc_unchamp'].'</div>';
            }
            break;
        case "liga_r":
            if ($parr2['yc_liga_r'] > 0) {
                echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Ретро Кубке Чемпионов">'.$parr2['yc_liga_r'].'</div>';
            }
            break;
        case "le":
            if ($parr2['yc_le'] > 0) {
                echo '<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$parr2['yc_le'].'</div>';
            }
            break;
    }
    
    if ($parr2['rc'] > 0) {
        echo '<div class="player-cards-2" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$parr2['rc'].'</div>';
    }
    
    echo '</a>';
    echo '</div>';
    ++$d;
}
echo '</td>';

echo '</tr></table>';
echo '</div>';

// Auto-refresh and match start time
echo '<meta http-equiv="refresh" content="60;url=/game'.$dirs.$id.'"/>';
echo '<center><div class="info">Матч начнется через: '.date("i:s", $ostime).'</div></center>';

// Team management links for managers
if ($datauser['manager2'] == $game6['id_team1'] || $datauser['manager2'] == $game6['id_team2']) {
    echo '<br/><center>
        <form action="/team/sostav.php"><input type="submit" title="Нажмите для изменения состава" name="submit" value="Изменить состав"/></form>
        <form action="/team/tactic.php"><input type="submit" title="Нажмите для изменения тактики" name="submit" value="Изменить тактику"/></form>
        </center><br/>';
}

// Remove disqualified players from lineup (for champ_retro only)
if ($game6['chemp'] == 'champ_retro') {
    // Home team
    $test1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$jam1['i1']."' OR `id`='".$jam1['i2']."' OR `id`='".$jam1['i3']."' OR `id`='".$jam1['i4']."' OR `id`='".$jam1['i5']."' OR `id`='".$jam1['i6']."' OR `id`='".$jam1['i7']."' OR `id`='".$jam1['i8']."' OR `id`='".$jam1['i9']."' OR `id`='".$jam1['i10']."' OR `id`='".$jam1['i11']."') AND `team`='".$jam1['id']."' LIMIT 11");
    
    while ($pidr = mysql_fetch_array($test1)) {
        if ($pidr['utime'] > 0) {
            mysql_query("UPDATE `r_player` SET `sostav`='4' WHERE `id`='".$pidr['id']."';");
            for ($i = 1; $i <= 11; $i++) {
                mysql_query("UPDATE `r_team` SET `i$i`='' WHERE `i$i`='".$pidr['id']."' AND `id`='".$jam1['id']."' LIMIT 1;");
            }
            echo '<div class="error">Мы убрали '.$pidr['name'].' из состава. У него дисквалификация</div>';
        }
    }
    
    // Away team
    $test2 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$jam2['i1']."' OR `id`='".$jam2['i2']."' OR `id`='".$jam2['i3']."' OR `id`='".$jam2['i4']."' OR `id`='".$jam2['i5']."' OR `id`='".$jam2['i6']."' OR `id`='".$jam2['i7']."' OR `id`='".$jam2['i8']."' OR `id`='".$jam2['i9']."' OR `id`='".$jam2['i10']."' OR `id`='".$jam2['i11']."') AND `team`='".$jam2['id']."' LIMIT 11");
    
    while ($pidr2 = mysql_fetch_array($test2)) {
        if ($pidr2['utime'] > 0) {
            mysql_query("UPDATE `r_player` SET `sostav`='4' WHERE `id`='".$pidr2['id']."';");
            for ($i = 1; $i <= 11; $i++) {
                mysql_query("UPDATE `r_team` SET `i$i`='' WHERE `i$i`='".$pidr2['id']."' AND `id`='".$jam2['id']."' LIMIT 1;");
            }
            echo '<div class="error">Мы убрали '.$pidr2['name'].' из состава. У него дисквалификация</div>';
        }
    }
}

// Auto lineup for teams with less than 11 players
// Home team
$result = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$jam1['i1']."' OR `id`='".$jam1['i2']."' OR `id`='".$jam1['i3']."' OR `id`='".$jam1['i4']."' OR `id`='".$jam1['i5']."' OR `id`='".$jam1['i6']."' OR `id`='".$jam1['i7']."' OR `id`='".$jam1['i8']."' OR `id`='".$jam1['i9']."' OR `id`='".$jam1['i10']."' OR `id`='".$jam1['i11']."') AND `team`='".$jam1['id']."' AND `sostav`!='4'");
$playerCount = mysql_num_rows($result);

if ($playerCount < 11) {
    echo $jam1['name'].' У вас меньше 11 игроков<br>';
    
    $sql = mysql_query("SELECT * FROM `r_team` WHERE `id`='".$game6['id_team1']."' LIMIT 1");
    if (mysql_num_rows($sql)) {
        $team = mysql_fetch_assoc($sql);
        for ($i = 1; $i <= 11; $i++) {
            if (!$team['i'.$i]) {
                $line = 0;
                if ($i == 1) {
                    $line = 1;
                } elseif ($i >= 2 && $i <= 4) {
                    $line = 2;
                } elseif ($i >= 5 && $i <= 9) {
                    $line = 3;
                } elseif ($i >= 10) {
                    $line = 4;
                }
                
                $sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game6['id_team1']."' AND `line`='$line' AND `sostav`='0' ORDER BY `rm` DESC LIMIT 1");
                
                if (!mysql_num_rows($sql)) {
                    $sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game6['id_team1']."' AND `sostav`='0' AND `line`!='1' ORDER BY `rm` LIMIT 1");
                }
                
                if (mysql_num_rows($sql)) {
                    $player = mysql_fetch_assoc($sql);
                    mysql_query("UPDATE `r_team` SET `i$i`='".$player['id']."' WHERE `id`='".$game6['id_team1']."' LIMIT 1");
                    mysql_query("UPDATE `r_player` SET `sostav`='1' WHERE `id`='".$player['id']."' LIMIT 1");
                }
            }
        }
    }
}

// Away team
$result2 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$jam2['i1']."' OR `id`='".$jam2['i2']."' OR `id`='".$jam2['i3']."' OR `id`='".$jam2['i4']."' OR `id`='".$jam2['i5']."' OR `id`='".$jam2['i6']."' OR `id`='".$jam2['i7']."' OR `id`='".$jam2['i8']."' OR `id`='".$jam2['i9']."' OR `id`='".$jam2['i10']."' OR `id`='".$jam2['i11']."') AND `team`='".$jam2['id']."' AND `sostav`!='4'");
$playerCount2 = mysql_num_rows($result2);

if ($playerCount2 < 11) {
    echo $jam2['name'].' У вас меньше 11 игроков<br>';
    
    $sql = mysql_query("SELECT * FROM `r_team` WHERE `id`='".$game6['id_team2']."' LIMIT 1");
    if (mysql_num_rows($sql)) {
        $team = mysql_fetch_assoc($sql);
        for ($i = 1; $i <= 11; $i++) {
            if (!$team['i'.$i]) {
                $line = 0;
                if ($i == 1) {
                    $line = 1;
                } elseif ($i >= 2 && $i <= 4) {
                    $line = 2;
                } elseif ($i >= 5 && $i <= 9) {
                    $line = 3;
                } elseif ($i >= 10) {
                    $line = 4;
                }
                
                $sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game6['id_team2']."' AND `line`='$line' AND `sostav`='0' ORDER BY `rm` DESC LIMIT 1");
                
                if (!mysql_num_rows($sql)) {
                    $sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game6['id_team2']."' AND `sostav`='0' AND `line`!='1' ORDER BY `rm` LIMIT 1");
                }
                
                if (mysql_num_rows($sql)) {
                    $player = mysql_fetch_assoc($sql);
                    mysql_query("UPDATE `r_team` SET `i$i`='".$player['id']."' WHERE `id`='".$game6['id_team2']."' LIMIT 1");
                    mysql_query("UPDATE `r_player` SET `sostav`='1' WHERE `id`='".$player['id']."' LIMIT 1");
                }
            }
        }
    }
}

++$kmess;
++$stgame;
++$i;
require_once("../incfiles/end.php");
exit;
// Функция для получения текста события
function func_text($type, $team, $minute, $player_name) {
    $events = [
        'twist_one' => "[$minute'] Начало матча. Команда $player_name начинает игру.",
        'twist_two' => "[$minute'] Команда $player_name усиливает давление.",
        'finish_one' => "[$minute'] Первый тайм завершен.",
        'finish_two' => "[$minute'] Матч завершен.",
        'fiks' => "[$minute'] Финальный свисток.",
        'goal' => "[$minute'] Гол! Игрок $player_name забивает гол.",
        'goal1' => "[$minute'] Гол! Игрок $player_name забивает за первую команду.",
        'goal2' => "[$minute'] Гол! Игрок $player_name забивает за вторую команду.",
        'yellow' => "[$minute'] Желтая карточка для $player_name.",
        'red1' => "[$minute'] Красная карточка! Игрок $player_name удален.",
        'red2' => "[$minute'] Красная карточка! Игрок $player_name удален.",
        'crest' => "[$minute'] Травма! Игрок $player_name покидает поле."
    ];
    return $events[$type] ?? "[$minute'] Неизвестное событие";
}

// Основной класс для обработки матча
class MatchProcessor {
    private $game;
    private $team1;
    private $team2;
    private $text = '';
    private $menus = '';
    private $players1 = '';
    private $players2 = '';
    
    public function __construct($game_id) {
        $this->loadGameData($game_id);
    }
    
    private function loadGameData($game_id) {
        $game_query = mysql_query("SELECT * FROM `r_game` WHERE id='$game_id' LIMIT 1");
        $this->game = mysql_fetch_array($game_query);
        
        $team1_query = mysql_query("SELECT * FROM `r_team` WHERE id='".$this->game['id_team1']."' LIMIT 1");
        $this->team1 = mysql_fetch_array($team1_query);
        
        $team2_query = mysql_query("SELECT * FROM `r_team` WHERE id='".$this->game['id_team2']."' LIMIT 1");
        $this->team2 = mysql_fetch_array($team2_query);
    }
    
    public function process() {
        if (!$this->checkTeams()) {
            return false;
        }
        
        $this->generateMatchText();
        
        $advantages = $this->calculateAdvantages();
        $team1_power = $this->calculateTeamPower($this->team1, $advantages['team1']);
        $team2_power = $this->calculateTeamPower($this->team2, $advantages['team2']);
        
        $result = $this->determineMatchResult($team1_power, $team2_power);
        $rezult = explode(':', $result['score']);
        
        $this->generateMatchEvents($team1_power, $team2_power);
        
        if ($rezult[0] > 0) {
            $this->handleGoals($this->team1, $rezult[0], 1);
        }
        
        if ($rezult[1] > 0) {
            $this->handleGoals($this->team2, $rezult[1], 2);
        }
        
        $this->processPlayers($rezult);
        $this->updateMatchData($team1_power, $team2_power, $rezult, $result);
        
        return true;
    }
    
    private function checkTeams() {
        $count1 = $this->getPlayersCount($this->team1);
        $count2 = $this->getPlayersCount($this->team2);
        
        if ($count1 < 7 || $count2 < 7) {
            $this->handleTechnicalDefeat($count1, $count2);
            return false;
        }
        
        return true;
    }
    
    private function getPlayersCount($team) {
        $count = 0;
        for ($i = 1; $i <= 11; $i++) {
            if (!empty($team['i'.$i])) $count++;
        }
        return $count;
    }
    
    private function handleTechnicalDefeat($count1, $count2) {
        $tech_score = ($count1 < 7) ? "0:3" : "3:0";
        mysql_query("UPDATE `r_game` SET `teh_end`='1', `rez1`='".explode(':', $tech_score)[0]."', 
                   `rez2`='".explode(':', $tech_score)[1]."' WHERE id='".$this->game['id']."'");
    }
    
    private function generateMatchText() {
        if (empty($this->game['tactics1']) && empty($this->game['tactics2'])) {
            $this->text .= func_text('twist_one', 1, '01', $this->team1['name']).'\r\n';
            $this->text .= func_text('twist_two', 1, '46', $this->team1['name']).'\r\n';
            $this->text .= func_text('finish_one', 1, '45', $this->team1['name']).'\r\n';
            $this->text .= func_text('finish_two', 1, '90', $this->team1['name']).'\r\n';
            $this->text .= func_text('fiks', 1, '93', $this->team1['name']).'\r\n';
        }
    }
    
    private function calculateAdvantages() {
        $advantages = ['team1' => 0, 'team2' => 0];
        
        // Стратегические преимущества
        $strategy_matrix = [
            [0, 3, 2], [1, 0, 2], [2, 1, 2], [3, 2, 2],
            [3, 0, -2], [0, 1, -2], [1, 2, -2], [2, 3, -2]
        ];
        
        foreach ($strategy_matrix as $adv) {
            if ($this->team1['strat'] == $adv[0] && $this->team2['strat'] == $adv[1]) {
                $advantages['team1'] += $adv[2];
                break;
            } elseif ($this->team2['strat'] == $adv[0] && $this->team1['strat'] == $adv[1]) {
                $advantages['team2'] += $adv[2];
                break;
            }
        }
        
        // Преимущества в пасах
        $pass_matrix = [
            [0, 1, 2], [1, 2, 2], [2, 0, 2],
            [1, 0, -2], [2, 1, -2], [0, 2, -2]
        ];
        
        foreach ($pass_matrix as $adv) {
            if ($this->team1['pas'] == $adv[0] && $this->team2['pas'] == $adv[1]) {
                $advantages['team1'] += $adv[2];
                break;
            } elseif ($this->team2['pas'] == $adv[0] && $this->team1['pas'] == $adv[1]) {
                $advantages['team2'] += $adv[2];
                break;
            }
        }
        
        // Тактические преимущества
        $tactic_matrix = [
            [100, 10, 3], [90, 10, 1], [100, 20, 1], [90, 20, 3],
            [80, 60, 3], [80, 50, 1], [70, 60, 1], [70, 50, 3],
            [60, 40, 3], [60, 30, 1], [50, 40, 1], [50, 30, 3],
            [40, 90, 3], [30, 100, 3], [40, 100, 1], [30, 90, 1],
            [20, 70, 3], [10, 80, 3], [20, 80, 1], [10, 70, 1],
            [10, 100, -3], [10, 90, -1], [20, 100, -1], [20, 90, -3],
            [60, 80, -3], [50, 80, -1], [60, 70, -1], [50, 70, -3],
            [40, 60, -3], [30, 60, -1], [40, 50, -1], [30, 50, -3],
            [90, 40, -3], [100, 30, -3], [100, 40, -1], [90, 30, -1],
            [70, 20, -3], [80, 10, -3], [80, 20, -1], [70, 10, -1]
        ];
        
        foreach ($tactic_matrix as $adv) {
            if ($this->team1['tactic'] == $adv[0] && $this->team2['tactic'] == $adv[1]) {
                $advantages['team1'] += $adv[2];
                break;
            } elseif ($this->team2['tactic'] == $adv[0] && $this->team1['tactic'] == $adv[1]) {
                $advantages['team2'] += $adv[2];
                break;
            }
        }
        
        // Бонус за подкуп судьи
        if (!empty($this->team1['ref'])) {
            $advantages['team1'] += 10;
        }
        if (!empty($this->team2['ref'])) {
            $advantages['team2'] += 10;
        }
        
        return $advantages;
    }
    
    private function calculateTeamPower($team, $advantage) {
        $total_power = 0;
        
        // Расчет силы игроков
        for ($i = 1; $i <= 11; $i++) {
            if (!empty($team['i'.$i])) {
                $player = $this->getPlayer($team['i'.$i]);
                $power = $player['mas'] * $player['fiz'] / 100;
                $total_power += $power;
            }
        }
        
        // Оптимальность схемы
        $scheme_bonus = $this->calculateSchemeBonus($team);
        $total_power *= $scheme_bonus;
        
        // Учет преимуществ
        $total_power = $total_power * (1 + $advantage / 100);
        
        return round($total_power);
    }
    
    private function getPlayer($player_id) {
        $query = mysql_query("SELECT * FROM `r_player` WHERE id='$player_id' LIMIT 1");
        return mysql_fetch_array($query);
    }
    
    private function calculateSchemeBonus($team) {
        // Определение оптимального количества игроков по линиям для схемы
        $schemes = [
            '4-3-3' => ['G' => 1, 'D' => 4, 'M' => 3, 'F' => 3],
            '3-4-3' => ['G' => 1, 'D' => 3, 'M' => 4, 'F' => 3],
            '2-5-3' => ['G' => 1, 'D' => 2, 'M' => 5, 'F' => 3],
            '5-3-2' => ['G' => 1, 'D' => 5, 'M' => 3, 'F' => 2],
            '4-4-2' => ['G' => 1, 'D' => 4, 'M' => 4, 'F' => 2],
            '3-5-2' => ['G' => 1, 'D' => 3, 'M' => 5, 'F' => 2],
            '6-3-1' => ['G' => 1, 'D' => 6, 'M' => 3, 'F' => 1],
            '5-4-1' => ['G' => 1, 'D' => 5, 'M' => 4, 'F' => 1],
            '4-5-1' => ['G' => 1, 'D' => 4, 'M' => 5, 'F' => 1]
        ];
        
        if (!isset($schemes[$team['shema']])) {
            return 1;
        }
        
        $optimal = $schemes[$team['shema']];
        $actual = ['G' => 0, 'D' => 0, 'M' => 0, 'F' => 0];
        
        // Подсчет реального количества игроков по линиям
        for ($i = 1; $i <= 11; $i++) {
            if (!empty($team['i'.$i])) {
                $player = $this->getPlayer($team['i'.$i]);
                switch ($player['line']) {
                    case 1: $actual['G']++; break;
                    case 2: $actual['D']++; break;
                    case 3: $actual['M']++; break;
                    case 4: $actual['F']++; break;
                }
            }
        }
        
        // Расчет коэффициента оптимальности
        $ratios = [];
        foreach ($optimal as $line => $count) {
            if ($count == 0 || $actual[$line] == 0) {
                $ratios[$line] = 0;
            } elseif ($count <= $actual[$line]) {
                $ratios[$line] = $count / $actual[$line];
            } else {
                $ratios[$line] = $actual[$line] / $count;
            }
        }
        
        // Средний коэффициент по всем линиям
        $average_ratio = array_sum($ratios) / count($ratios);
        return max(0.5, min(1.5, $average_ratio)); // Ограничиваем диапазон
    }
    
    private function determineMatchResult($power1, $power2) {
        $difference = $power1 - $power2;
        
        if ($difference > 850) {
            $scores = ["6:1", "7:2", "10:2", "9:1", "8:1"];
        } elseif ($difference > 550) {
            $scores = ["6:0", "7:1", "5:0", "5:1"];
        } elseif ($difference > 450) {
            $scores = ["4:0", "4:1", "3:0", "3:2", "3:1", "2:1", "0:0", "1:1"];
        } elseif ($difference > 300) {
            $scores = ["3:0", "3:0", "4:1", "3:1", "2:1", "0:0", "1:1"];
        } elseif ($difference > 200) {
            $scores = ["2:0", "2:0", "1:0", "3:1", "0:1", "1:2", "0:0", "1:1", "2:2"];
        } elseif ($difference < -850) {
            $scores = ["1:6", "2:7", "2:10", "1:8", "1:9"];
        } elseif ($difference < -550) {
            $scores = ["0:6", "1:7", "0:5", "1:5"];
        } elseif ($difference < -450) {
            $scores = ["0:4", "1:4", "0:3", "2:3", "1:3", "1:2", "0:0", "1:1"];
        } elseif ($difference < -300) {
            $scores = ["0:3", "0:3", "1:4", "1:3", "1:2", "0:0", "1:1"];
        } elseif ($difference < -200) {
            $scores = ["0:2", "0:2", "0:1", "1:3", "1:0", "2:1", "2:1", "0:0", "1:1", "2:2"];
        } else {
            $scores = ["0:1", "0:0", "1:1", "1:2", "2:3", "1:0", "2:1"];
        }
        
        $random_key = array_rand($scores);
        $score = $scores[$random_key];
        
        // Проверка на ничью в плей-офф
        $penalty = null;
        if (explode(':', $score)[0] == explode(':', $score)[1] && 
            in_array($this->game['gr'], ['1/8', '1/4', '1/2', '1/1'])) {
            $penalties = ["5:3", "5:4", "4:2", "4:3", "3:2", "3:5", "4:5", "2:4", "3:4", "2:3"];
            $random_key = array_rand($penalties);
            $penalty = $penalties[$random_key];
        }
        
        return [
            'score' => $score,
            'penalty' => $penalty
        ];
    }
    
    private function generateMatchEvents($power1, $power2) {
        $probability = $power1 / ($power1 + $power2) * 100;
        
        for ($minute = 2; $minute <= 91; $minute += rand(3, 7)) {
            $rand = rand(0, 100);
            if ($minute < 10) $minute_str = '0'.$minute;
            else $minute_str = $minute;
            
            if ($probability > $rand) {
                $this->text .= func_text('play', 1, $minute_str, '').'\r\n';
            } else {
                $this->text .= func_text('play', 2, $minute_str, '').'\r\n';
            }
        }
    }
    
    private function handleGoals($team, $goals_count, $team_num) {
        for ($i = 0; $i < $goals_count; $i++) {
            $minute = rand(10, 90);
            $goal_type = ($team_num == 1) ? 'goal1' : 'goal2';
            
            // Выбор случайного игрока для гола
            $players = [];
            for ($j = 1; $j <= 11; $j++) {
                if (!empty($team['i'.$j])) {
                    $player = $this->getPlayer($team['i'.$j]);
                    $players[] = $player;
                }
            }
            
            if (!empty($players)) {
                $scorer = $players[array_rand($players)];
                $this->text .= func_text($goal_type, $team_num, $minute, $scorer['name']).'\r\n';
                $this->menus .= $minute.'|goal|'.$scorer['id'].'|'.$scorer['name'].'\r\n';
                
                // Обновляем статистику игрока
                mysql_query("UPDATE `r_player` SET `goal`=`goal`+1 WHERE id='".$scorer['id']."'");
            }
        }
    }
    
    private function processPlayers($rezult) {
        $this->processTeamPlayers($this->team1, $rezult[0], $rezult[1], 1);
        $this->processTeamPlayers($this->team2, $rezult[1], $rezult[0], 2);
    }
    
    private function processTeamPlayers($team, $goals, $goals_against, $team_num) {
        for ($i = 1; $i <= 11; $i++) {
            if (!empty($team['i'.$i])) {
                $player = $this->getPlayer($team['i'.$i]);
                $this->updatePlayerStats($player, $goals, $goals_against, $team_num);
            }
        }
    }
    
    private function updatePlayerStats($player, $goals, $goals_against, $team_num) {
        // Уменьшение физики
        $fiz_loss = rand(7, 18);
        $new_fiz = max(0, $player['fiz'] - $fiz_loss);
        
        // Обновление морали
        if ($goals > $goals_against) {
            $new_mor = $player['mor'] + 3;
        } elseif ($goals < $goals_against) {
            $new_mor = $player['mor'] - 3;
        } else {
            $new_mor = $player['mor'];
        }
        
        // Увеличение опыта
        $exp_gain = $this->calculateExpGain($player, $team_num);
        $new_exp = $player['oput'] + $exp_gain;
        
        // Обновление игрока
        mysql_query("UPDATE `r_player` SET 
            `fiz`='$new_fiz', 
            `mor`='$new_mor', 
            `oput`='$new_exp', 
            `game`=`game`+1 
            WHERE id='".$player['id']."'");
        
        // Формирование строки для отображения
        $player_line = $player['poz'].'|'.$player['id'].'|'.$player['name'];
        if ($new_fiz <= 0) {
            $player_line .= ' <img src="/images/trav.gif" alt=""/>';
        }
        $player_line .= '|+'.$exp_gain.'\r\n';
        
        if ($team_num == 1) {
            $this->players1 .= $player_line;
        } else {
            $this->players2 .= $player_line;
        }
        
        // Обработка травм
        if (rand(1, 100) == 1 && $new_fiz <= 0) {
            $this->handleInjury($player, $team_num);
        }
        
        // Обработка карточек
        $this->handleCards($player, $team_num);
    }
    
    private function calculateExpGain($player, $team_num) {
        $team = ($team_num == 1) ? $this->team1 : $this->team2;
        
        // Коэффициенты тренера и вратаря
        $trener_coeff = 1;
        switch ($team['trener']) {
            case 1: $trener_coeff = 2; break;
            case 2: $trener_coeff = 3; break;
            case 3: $trener_coeff = 4; break;
            case 4: $trener_coeff = 5; break;
        }
        
        $vrat_coeff = 1;
        switch ($team['vrat']) {
            case 1: $vrat_coeff = 2; break;
            case 2: $vrat_coeff = 3; break;
            case 3: $vrat_coeff = 4; break;
            case 4: $vrat_coeff = 5; break;
        }
        
        // Базовый прирост опыта
        $base_exp = $player['tal'];
        
        // Умножаем на коэффициент в зависимости от позиции
        if ($player['line'] == 1) { // Вратарь
            return round($base_exp * $vrat_coeff);
        } else {
            return round($base_exp * $trener_coeff);
        }
    }
    
    private function handleInjury($player, $team_num) {
        $minute = rand(10, 90);
        $this->text .= func_text('crest', $team_num, $minute, $player['name']).'\r\n';
        $this->menus .= $minute.'|injury|'.$player['id'].'|'.$player['name'].'\r\n';
        
        mysql_query("UPDATE `r_player` SET `sostav`='3', `btime`='".(time() + 172800)."' 
                   WHERE id='".$player['id']."'");
    }
    
    private function handleCards($player, $team_num) {
        // Желтые карточки
        if (rand(1, 100) <= 5) {
            $minute = rand(10, 90);
            $this->text .= func_text('yellow', $team_num, $minute, $player['name']).'\r\n';
            $this->menus .= $minute.'|yellow|'.$player['id'].'|'.$player['name'].'\r\n';
            
            mysql_query("UPDATE `r_player` SET `yc`=`yc`+1 WHERE id='".$player['id']."'");
            
            // Проверка на удаление за 2 желтые карточки
            $player = $this->getPlayer($player['id']);
            if ($player['yc'] >= 2) {
                $this->handleRedCard($player, $team_num, true);
            }
        }
        
        // Прямые красные карточки
        if (rand(1, 100) == 1) {
            $this->handleRedCard($player, $team_num, false);
        }
    }
    
    private function handleRedCard($player, $team_num, $from_yellow) {
        $minute = rand(10, 90);
        $card_type = ($team_num == 1) ? 'red1' : 'red2';
        $this->text .= func_text($card_type, $team_num, $minute, $player['name']).'\r\n';
        $this->menus .= $minute.'|red|'.$player['id'].'|'.$player['name'].'\r\n';
        
        if ($from_yellow) {
            mysql_query("UPDATE `r_player` SET `yc`=`yc`-2, `rc`=`rc`+1, `sostav`='4', `utime`='2' 
                       WHERE id='".$player['id']."'");
        } else {
            mysql_query("UPDATE `r_player` SET `rc`=`rc`+1, `sostav`='4', `utime`='2' 
                       WHERE id='".$player['id']."'");
        }
        
        // Удаляем игрока из состава команды
        for ($i = 1; $i <= 11; $i++) {
            if (!empty($this->{'team'.$team_num}['i'.$i]) && 
                $this->{'team'.$team_num}['i'.$i] == $player['id']) {
                mysql_query("UPDATE `r_team` SET `i$i`='' WHERE id='".$this->{'team'.$team_num}['id']."'");
                break;
            }
        }
    }
    
    private function updateMatchData($team1_power, $team2_power, $rezult, $result) {
        $tactics1 = implode('|', [
            $this->team1['shema'],
            $this->team1['pas'],
            $this->team1['strat'],
            $this->team1['tactic'],
            $this->team1['pres'],
            $team1_power
        ]);
        
        $tactics2 = implode('|', [
            $this->team2['shema'],
            $this->team2['pas'],
            $this->team2['strat'],
            $this->team2['tactic'],
            $this->team2['pres'],
            $team2_power
        ]);
        
        $pen1 = $pen2 = '';
        if ($result['penalty']) {
            $pen = explode(':', $result['penalty']);
            $pen1 = $pen[0];
            $pen2 = $pen[1];
        }
        
        mysql_query("UPDATE `r_game` SET
            `players1`='".mysql_real_escape_string($this->players1)."',
            `players2`='".mysql_real_escape_string($this->players2)."',
            `tactics1`='".mysql_real_escape_string($tactics1)."',
            `tactics2`='".mysql_real_escape_string($tactics2)."',
            `menus`='".mysql_real_escape_string($this->menus)."',
            `text`='".mysql_real_escape_string($this->text)."',
            `rez1`='".$rezult[0]."',
            `rez2`='".$rezult[1]."',
            `pen1`='".$pen1."',
            `pen2`='".$pen2."'
            WHERE id='".$this->game['id']."'");
        
        // Обновление статистики команд
        $this->updateTeamStats($this->team1['id'], $rezult[0], $rezult[1]);
        $this->updateTeamStats($this->team2['id'], $rezult[1], $rezult[0]);
    }
    
    private function updateTeamStats($team_id, $goals_for, $goals_against) {
        $update_fields = [
            'game' => '`game`=`game`+1',
            'goal' => '`goal`=`goal`+'.$goals_for,
            'miss' => '`miss`=`miss`+'.$goals_against
        ];
        
        if ($goals_for > $goals_against) {
            $update_fields['win'] = '`win`=`win`+1';
            $update_fields['och'] = '`och`=`och`+3';
        } elseif ($goals_for == $goals_against) {
            $update_fields['nich'] = '`nich`=`nich`+1';
            $update_fields['och'] = '`och`=`och`+1';
        } else {
            $update_fields['por'] = '`por`=`por`+1';
        }
        
        $update_query = "UPDATE `r_team` SET ".implode(', ', $update_fields)." WHERE id='$team_id'";
        mysql_query($update_query);
    }
}

// Обработка матча
$match = new MatchProcessor($game_id);
if ($match->process()) {
    header("Location: /report".$game_id);
} else {
    header("Location: /txt".$game_id);
}
exit;







header('location: /txt'.$dirs.''.$id);
require_once ("../incfiles/end.php");
?>

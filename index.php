<?php
define('_IN_JOHNCMS', 1);
$headmod = 'mainpage';
$textl = 'Менеджер';
require_once('../incfiles/core.php');

require_once('../incfiles/head.php');
require_once('../incfiles/ban.php');
require_once('../incfiles/code/fakt.php');

// Инициализация переменных
$user_id = (int)$datauser['id'];
$realtime = time();

/**
 * Функция вывода сообщения
 */
function show_message($text, $color) {
    echo '<div class="cardview-wrapper x-overlay" id="errorMsg">
        <div class="cardview">
            <div class="x-row">
                <div class="x-col-1 x-vh-center x-color-white x-bg-' . htmlspecialchars($color) . '">
                    <i class="font-icon">!</i>
                </div>
                <div class="x-col-5 x-font-bold x-p-3">
                    ' . htmlspecialchars($text) . '
                    <div class="x-pt-3">
                        <a class="mbtn mbtn-' . htmlspecialchars($color) . '" onclick="toggleVisibility(\'errorMsg\');">Закрыть</a>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}

/**
 * Функция редиректа назад
 */
function redirect_back() {
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    header("Location: " . $referer);
    exit;
}

// Оптимизация стилей
$text_color = ($datauser['black'] == 0) ? '#fff' : '#282828';
$styles = '

.team-add {
    text-align: center;
    border: 1px dashed ' . $text_color . ';
    color: ' . $text_color . ';
    cursor: pointer;
    padding: 3px;
    opacity: 0.3;
}
.team-add:hover {
    opacity: 1;
}';

echo "<style>{$styles}</style>";

// Обработка действий
if (isset($_GET['mod'])) {
    $mod = mysql_real_escape_string($_GET['mod']);
    if ($mod == "team" || $mod == "mtj") {
        // Проверяем, есть ли у пользователя команда в выбранном режиме
        if (($mod == "team" && !empty($datauser['manager2'])) || 
            ($mod == "mtj" && !empty($datauser['mtj']))) {
            mysql_query("UPDATE `users` SET `club` = '$mod' WHERE `id` = '$user_id'");
            header('Location: /fm/index.php');
            exit;
        } else {
            // Если команды нет, перенаправляем на страницу создания
            header('Location: /store_teams' . ($mod == "mtj" ? '2' : '') . '.php');
            exit;
        }
    }

    switch ($mod) {
        case "ads":
            if ($datauser['rubl'] >= 10) {
                mysql_query("UPDATE `users` SET `rubl` = `rubl` - 10, `ads` = '0' WHERE `id` = '$user_id'");
                show_message('Вы отключили рекламу', 'green');
            } else {
                show_message('У тебя не хватает денег!', 'red');
            }
            redirect_back();
            break;
            
        case "hints":
            $set_user['hints'] = 0;
            $set_user_serialized = serialize($set_user);
            mysql_query("UPDATE `users` SET `set_user` = '" . mysql_real_escape_string($set_user_serialized) . "' WHERE `id` = '$user_id' LIMIT 1");
            show_message('Вы отключили подсказки', 'green');
            header("refresh:1;url=".$_SERVER['HTTP_REFERER']);
            break;
            
        case "ajax":
            if (!isset($set_user)) $set_user = array();
            $field = ($mod == "hints") ? "hints" : "ajax";
            $set_user_serialized = mysql_real_escape_string(serialize($set_user));
            mysql_query("UPDATE `users` SET `set_user` = '$set_user_serialized' WHERE `id` = '$user_id'");
            show_message('Вы отключили ' . ($mod == "hints" ? 'подсказки' : 'уведомления'), 'green');
            redirect_back();
            break;
            
        case "mir80":
            mysql_query("UPDATE `users` SET `mir` = 'retro80' WHERE `id` = '$user_id'");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
            
        case "mir2000":
            mysql_query("UPDATE `users` SET `mir` = 'retro2000' WHERE `id` = '$user_id'");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
            
        case "mirvirt":
            mysql_query("UPDATE `users` SET `mir` = 'virt' WHERE `id` = '$user_id'");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
            
        case "team":
            mysql_query("UPDATE `users` SET `club` = 'team' WHERE `id` = '$user_id'");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
            
        case "mtj":
            mysql_query("UPDATE `users` SET `club` = 'mtj' WHERE `id` = '$user_id'");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
    }
}

// Основная логика - проверяем есть ли у пользователя команда
if ($datauser['id']) {
    // Проверяем, есть ли у пользователя команда и является ли он менеджером
    if ($datauser['club'] == 'team' && !empty($datauser['manager2'])) {
        // Пользователь имеет команду - показываем основной интерфейс
        $manager_id = (int)$datauser['manager2'];
        $qk = mysql_query("SELECT * FROM `r_team` WHERE id = '$manager_id' LIMIT 1");
        
        if (mysql_num_rows($qk)) {
            $kom = mysql_fetch_array($qk);
        
            // Уровни команды
            $oput_levels = array(
                1 => 100, 2 => 300, 3 => 800, 4 => 2000, 5 => 4000,
                6 => 7500, 7 => 11000, 8 => 18000, 9 => 25000, 10 => 30000,
                11 => 40000, 12 => 37000, 13 => 45000, 14 => 55000, 15 => 65000,
                16 => 77000, 17 => 90000, 18 => 105000, 19 => 125000, 20 => 155000,
                21 => 1900000
            );

            $oput = isset($oput_levels[$kom['level']]) ? $oput_levels[$kom['level']] : 0;
            
            if ($kom['oput'] >= $oput) {
                $addlevel = $kom['level'] + 1;
                mysql_query("UPDATE `r_team` SET `level` = '$addlevel' WHERE id = '$manager_id'");
                
                // Отправка уведомления
                $u = mysql_query("SELECT `name` FROM `users` WHERE `id` = '" . (int)$kom['id_admin'] . "' LIMIT 1");
                if (mysql_num_rows($u)) {
                    $user = mysql_fetch_assoc($u);
                    $message = "Поздравляем! \r\n\r\n Ваша команда [color=green] {$kom['name']} [/color] получила [color=green][b]{$addlevel}-й Уровень[/b][/color]";
                    
                    mysql_query("INSERT INTO `privat` SET
                        `user` = '" . mysql_real_escape_string($user['name']) . "',
                        `user_id` = '1098',
                        `text` = '" . mysql_real_escape_string($message) . "',
                        `time` = '$realtime',
                        `author` = 'system',
                        `type` = 'in',
                        `chit` = 'no',
                        `temka` = 'Новый уровень'");
                }
            }

            // Получаем информацию об игроках команды
            $req = mysql_query("SELECT * FROM `r_player` WHERE `team` = '" . $kom['id'] . "' ORDER BY line ASC, poz ASC");
            $total = mysql_num_rows($req);
            $allfizkom = 0;
            while ($arr = mysql_fetch_array($req)) {
                $allfizkom += $arr['fiz'];
            }

            // Получаем информацию о составе команды
            $player_ids = array_filter(array(
                $kom['i1'], $kom['i2'], $kom['i3'], $kom['i4'], $kom['i5'], 
                $kom['i6'], $kom['i7'], $kom['i8'], $kom['i9'], $kom['i10'], $kom['i11']
            ));
            
            $fizsos = 0;
            $fizkom = 0;
            if (!empty($player_ids)) {
                $ids_str = implode("','", array_map('intval', $player_ids));
                $r = mysql_query("SELECT * FROM `r_player` WHERE `id` IN ('$ids_str') AND `team` = '" . $kom['id'] . "' LIMIT 11");

                $allfizsos = 0;
                while ($e = mysql_fetch_assoc($r)) {
                    $allfizsos += $e['fiz'];
                }

                $fizsos = round($allfizsos / 11);
                $fizkom = round($allfizkom / max($total, 1));
            }

            // Определяем аватар пользователя
            $avatar_path = "../files/avatar/" . $datauser['id'] . ".png";
            $img = file_exists($avatar_path) 
                ? '/files/avatar/' . $datauser['id'] . '.png' 
                : '/images/no_avatar.png';

            // Вывод сообщения об изменении языка
            if (isset($_GET['ok'])) {
                echo '<div class="pravmenu"><b><font color="red"><center>' . htmlspecialchars($lng_til['til_ozgartir']) . '</center></font></b></div>';
            }
            ?>

            <!-- Основной HTML интерфейс -->
            <div class="gmenu m_info">
                <div class="m_avatar_block game-tour-holder">
                    <div class="m_avatar" style="background-image: url(<?= htmlspecialchars($img) ?>);">
                        <a href="/avatar.php"><img src="/images/rounder.png" alt="Avatar"/></a>
                    </div>
                </div>

                <div class="m_team_block">
                    <div class="m_team_name_block">
                        <div class="m_team_name">
                            <span class="flags c_<?= htmlspecialchars($kom['flag']) ?>_18" style="vertical-align: middle;" title="<?= htmlspecialchars($kom['flag']) ?>"></span> 
                            <?= htmlspecialchars($kom['name']) ?>
                        </div>
                        <div class="m_team_ligue">
                <?= isset($kom['strana']) && isset($championships[$kom['strana']]) ? htmlspecialchars($championships[$kom['strana']]) : ''; ?>
            </div>
        </div>
    </div>

                   
<div class="m_avatar_block2">
        <?php
        if ($datauser['club'] == 'team') {
            if (empty($datauser['manager2'])) {
                mysql_query("UPDATE `users` SET `club` = 'team' WHERE `id` = '$user_id'");
                header('Location: /fm/index.php');
                exit;
            }
            
            $qk5 = mysql_query("SELECT * FROM `r_team` WHERE id_admin = '$user_id' LIMIT 1");
            $kom = mysql_fetch_assoc($qk5);
            $qkw = mysql_query("SELECT * FROM `mtj` WHERE id = '" . $datauser['mtj'] . "' LIMIT 1");
            $mtj = mysql_fetch_assoc($qkw);
            
            $team_logo = empty($kom['logo']) ? '/manager/logo/b_0.jpg' : '/manager/logo/big' . $kom['logo'];
            
            if (!$datauser['manager2']) {
                echo '<a class="button1" href="/store_teams.php"><img style="width:56px" src="/images/menu2/team1.png" title="Добавить команду"><span class="fmantext"><div class="team_name7">Добавить команду</div></span></a>';
            } else {
                echo '<a class="button1" href="/fm/?mod=team"><img style="width:56px" src="' . htmlspecialchars($team_logo) . '" title="Перейти в управление клубом ' . htmlspecialchars($kom['name']) . '"><span class="fmantext"><div class="team_name7">' . htmlspecialchars($kom['name']) . '</div></span></a>';
            }
            
            if (!$datauser['mtj']) {
                echo '<a class="button1" href="/store_teams2.php"><img style="width:56px" src="/images/menu2/team2.png" title="Добавить команду"><span class="fmantext"><div class="team_name7">Добавить команду</div></span></a>';
            } else {
                echo '<a class="button1" href="/fm/?mod=mtj"><img style="width:56px" src="/manager/mtj/big' . htmlspecialchars($mtj['logo']) . '" title="Перейти в управление клубом ' . htmlspecialchars($mtj['name']) . '"><span class="fmantext"><div class="team_name7">' . htmlspecialchars($mtj['name']) . '</div></span></a>';
            }
        }
        ?>
    </div>

                <div class="m_team_logo">
                    <?php if ($kom['retro'] == 0): ?>
                        <a href="/team/logo.php?act=up_logo&amp;id=<?= (int)$kom['id'] ?>"></a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($kom['sponsor']) && $kom['sponsor'] != '0'): ?>
                <?php 
                $arr9 = mysql_fetch_array(mysql_query("SELECT * FROM `sponsor` WHERE id = '" . $kom['sponsor'] . "'"));
                $lost = isset($kom['lost']) ? (int)$kom['lost'] : 0;
                $limit = isset($arr9['limit']) ? (int)$arr9['limit'] : 1;
                $width = ($lost / max($limit, 1)) * 100;
                ?>
                <table class="m_team_level_table" style="">
                    <tr>
                        <td>
                            <div class="m_team_level_num game-tour-holder">
                                <a href="/sponsor/">Спонсор <?= htmlspecialchars(isset($arr9['name']) ? $arr9['name'] : '') ?></a>
                            </div>
                        </td>
                        <td style="width: 100%;" class="tooltip">
                            <span class="tooltip-text tooltip-top">Лимит поражений: <?= $lost ?>/<?= $limit ?></span>
                            <div class="m_team_level_pbar2 game-tour-holder">
                                <div style="width: <?= $width ?>%;"></div>
                            </div>
                        </td>
                    </tr>
                </table>
            <?php endif; ?>

            <div class="phdr m_team_level" style="overflow: visible;">
                <div class="m_time_wrap">
                    <div id="m_time" onclick="setShowHide(this.id); return false;" class="m_time" style="display: none;">
                        <?= date("d.m.Y H:i:s", $realtime) ?>
                    </div>
                </div>
                
                <table class="m_team_level_table" style="">
                    <tr>
                        <td>
                            <div class="m_team_level_num game-tour-holder">
                                <?= htmlspecialchars(isset($uroven) ? $uroven : 'Уровень') ?> <?= isset($kom['level']) ? (int)$kom['level'] : 0 ?>
                            </div>
                        </td>
                        <td style="width: 100%;" class="tooltip">
                            <span class="tooltip-text tooltip-top">Прогресс: <?= isset($kom['oput']) ? (int)$kom['oput'] : 0 ?>/<?= isset($oput) ? (int)$oput : 0 ?></span>
                            <div class="m_team_level_pbar game-tour-holder">
                                <div style="width: <?= isset($kom['oput']) && isset($oput) ? ($kom['oput'] / max($oput, 1)) * 100 : 0; ?>%;"></div>
                            </div>
                        </td>
                        <td><i class="fi fi-clock x-color-dg"></i></td>
                        <td>
                            <div class="m_time_short game-tour-holder" onclick="setShowHide('m_time'); return false;">
                                <?= date("H:i", $realtime) ?>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="m_team_level_table" style="margin: 6px;">
                    <tr>
                        <td>
                            <div class="m_team_stat">
                                <i class="fi fi-rocket x-color-yellow"></i>
                                <small><?= htmlspecialchars(isset($opyt) ? $opyt : 'Опыт') ?> <b><?= isset($kom['oput']) ? (int)$kom['oput'] : 0 ?></b></small>
                            </div>
                        </td>
                        <td>
                            <div class="m_team_stat">
                                <i class="fi fi-users x-color-red"></i>
                                <small><?= htmlspecialchars(isset($fans) ? $fans : 'Фанаты') ?> <b><?= isset($kom['fans']) ? (int)$kom['fans'] : 0 ?></b></small>
                            </div>
                        </td>
                        <td>
                            <div class="m_team_stat">
                                <i class="fi fi-flag x-color-dg"></i>
                                <small><?= htmlspecialchars(isset($slava) ? $slava : 'Слава') ?> <b><?= isset($kom['slava']) ? (int)$kom['slava'] : 0 ?></b></small>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <?php
            // Обработка скрытия позиции
            if (isset($_GET['act']) && $_GET['act'] == "top_hide") {
                if (!isset($set_user)) $set_user = array();
                $set_user['matchoftheday'] = 0;
                $set_user_serialized = mysql_real_escape_string(serialize($set_user));
                mysql_query("UPDATE `users` SET `set_user` = '$set_user_serialized' WHERE `id` = '$user_id' LIMIT 1");
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }

            // Админ-панель
            if ($user_id == 1094) {
                echo '<div class="info"><a href="/fm/admin.php">Админ панель</a></div>';
                echo '<div class="info"><a href="/admin.php">Админ панель2</a></div>';
            }

            // Приглашение игроков
            if (isset($kom['strana']) && $kom['strana'] == 'mir') {
                echo '<div class="info"><a href="/team/mtj.php"><font color="red">Пригласить игроков</font></a></div>';
            }

            /**
             * Проверка и выдача бонуса
             */
            function checkAndGiveBonus($user_id, $datauser) {
                global $realtime;
                $ip = mysql_real_escape_string($datauser['ip']);
                $quc = mysql_query("SELECT 1 FROM `umedal` WHERE `ip`='$ip' LIMIT 1");
                $cont = mysql_num_rows($quc);
                $qun = mysql_query("SELECT 1 FROM `umedal` WHERE `user`='".(int)$user_id."' LIMIT 1");
                $um = mysql_num_rows($qun);

                if(isset($_GET['rubl']) && $cont == 0 && $um == 0) {
                    $new_rubl = $datauser['rubl'] + 1;
                    mysql_query("UPDATE `users` SET `rubl` = '".(int)$new_rubl."' WHERE `id`='".(int)$user_id."' LIMIT 1");
                    
                    mysql_query("INSERT INTO `umedal` SET 
                        `ip` = '$ip',
                        `user` = '".(int)$user_id."'");
                    
                    header("Location: /fm/");
                    exit;
                }

                if($cont == 0 && $um == 0) {
                    echo '<ul class="m_main_menu">
                        <center><li class="m_main_menu_item">
                            <a href="index.php?rubl"><font color="red">Получить 1 <img src="/images/butcer.png" alt=""></font></a>
                        </li></center>
                    </ul>';
                }
            }

            /**
             * Проверка набора в чемпионат
             */
            function checkChampRegistration($datauser, $kom) {
                global $realtime;
                $manager_id = (int)$datauser['manager2'];
                $q = mysql_query("SELECT 1 FROM `champ_bilet` WHERE id_team='$manager_id' LIMIT 1");
                $total = mysql_num_rows($q);

                $q57 = mysql_query("SELECT 1 FROM `champ_table` WHERE id_team='$manager_id' LIMIT 1");
                $total_table = mysql_num_rows($q57);

                if($total == 0 && $total_table == 0) {
                    $path = ($kom['retro'] == 2000) ? '/champ00/nabor.php' : '/champ/nabor.php';
                    
                    echo '<div id="m_notification">
                        <table style="width: 100%; padding: 6px;" class="x-bg-color-hover x-border-bottom x-border-lg-2">
                            <tbody><tr>
                            <span class="x-badge">набор</span>
                            <td style="width: 60px;"><a href="'.$path.'"><img src="/images/icon/cup.jpeg" alt="!" class="x-rounded" style="width: 50px;"></a></td>
                            <td>
                                <a href="'.$path.'" class="x-color-black x-d-block" title="Нажмите, чтобы перейти...">
                                <span class="x-font-150"><font color="red">'.$uch.'</font></span>
                                </a>
                            </td>
                            <td style="width: 25px; text-align: center;">
                                <i class="fi fi-cup x-color-yellow x-font-125"></i>
                                <div class="x-color-dg x-p-2 x-font-150">'.number_format(100000, 0, ',', ' ') . ' <img src="/images/m_game3.gif" class="money"></div>
                            </td>
                            </tr>
                        </tbody></table>
                    </div>';
                }
            }

            // Основной код
            checkAndGiveBonus($user_id, $datauser);
            checkChampRegistration($datauser, $kom);

            /**
             * Отображение кубков
             */
            function displayCupNotification($table_prefix, $min_level, $max_level, $liga, $base_path, $cup_images) {
                global $kom, $realtime, $nabor1s, $nabor1ss;
                
                if($kom['level'] >= $min_level && $kom['level'] <= $max_level) {
                    $query = "SELECT * FROM `{$table_prefix}_cup` WHERE `ot`>='$min_level' AND `do`<='$max_level'";
                    if($liga) $query .= " AND `liga`='$liga'";
                    $query .= " ORDER BY time DESC LIMIT 1";
                    
                    $req = mysql_query($query);
                    if($arr = mysql_fetch_array($req)) {
                        $b = mysql_query("SELECT 1 FROM `{$table_prefix}_bilet` WHERE id_cup = '".(int)$arr['id']."' LIMIT 8");
                        $totalbilet = mysql_num_rows($b);
                        
                        if($totalbilet < 8) {
                            $cup_id = $arr['id_cup'];
                            $c_name = isset($GLOBALS["c_$cup_id"]) ? $GLOBALS["c_$cup_id"] : $arr['name'];
                            
                            echo '<div id="m_notification">
                                <table style="width: 100%; padding: 6px;" class="x-bg-color-hover x-border-bottom x-border-lg-2">
                                    <tbody><tr>
                                    <td style="width: 60px;"><a href="'.$base_path.'/cup.php?id='.(int)$arr['id'].'">
                                        <img src="/images/cup/b_'.$cup_id.'.png" alt="!" class="x-rounded" style="width: 50px;"></a></td>
                                    <td>
                                        <a href="'.$base_path.'/cup.php?id='.(int)$arr['id'].'" class="x-color-black x-d-block" title="Нажмите, чтобы перейти...">
                                        <span class="x-font-150">'.htmlspecialchars($c_name).'</span>
                                        <span class="x-d-block x-py-1">'.htmlspecialchars($nabor1s).'</span>
                                        <span class="x-color-dg">'.htmlspecialchars($nabor1ss).'</span>
                                        </a>
                                    </td>
                                    <td style="width: 25px; text-align: center;">
                                        <i class="fi fi-cup x-color-yellow x-font-125"></i>
                                        <div class="x-color-dg x-p-2 x-font-150">'.number_format_short($arr['priz']).' <img src="/images/m_game3.gif" class="money"></div>
                                    </td>
                                    </tr>
                                </tbody></table>
                            </div>';
                        }
                    }
                }
            }

            // Регулярные кубки
            displayCupNotification('r', 1, 2, 1, '/tournament', true);
            displayCupNotification('r', 2, 2, 2, '/tournament', true);
            displayCupNotification('r', 3, 3, 3, '/tournament', true);
            displayCupNotification('r', 4, 4, 4, '/tournament', true);
            displayCupNotification('r', 5, 15, 5, '/tournament', true);

            // Брендовые кубки
            displayCupNotification('b', 1, 1, 0, '/brendcup', true);
            displayCupNotification('b', 2, 2, 0, '/brendcup', true);
            displayCupNotification('b', 3, 15, 0, '/brendcup', true);

            // Коммерческие кубки
            displayCupNotification('z', 1, 1, 0, '/commercup', true);
            displayCupNotification('z', 2, 2, 0, '/commercup', true);
            displayCupNotification('z', 3, 3, 0, '/commercup', true);
            displayCupNotification('z', 4, 4, 0, '/commercup', true);
            displayCupNotification('z', 5, 15, 0, '/commercup', true);
            ?>

            <style>
            input[type="button1"]:hover, input[type~="button1"]:hover, a.button1:hover {
                color: #fff;
                text-align: center;
                outline: medium none;
                background: -moz-linear-gradient(top,#8dc893,#31a424);
                background: -webkit-gradient(linear,left top,left bottom,from(#31a424),to(#31a424));
                background: -o-linear-gradient(top,#8dc893,#31a424);
                box-shadow: inset 0px 1px 0px 0px #31a424;
                -webkit-box-shadow: inset 0 1px 0 0 #31a424;
                font-weight: bold;
                font-size: 11px;
                background-color: #F2B54B;
                border: 0px solid #fff;
                text-decoration: none;
                cursor: pointer;
                margin: 3px;
                white-space: nowrap;
            }
            input[type="button1"], input[type~="button1"], a.button1 {
                color: #fff;
                text-align: center;
                outline: medium none;
                background: -moz-linear-gradient(top,#8dc893,#31a424);
                background: -webkit-gradient(linear,left top,left bottom,from(#8dc893),to(#31a424));
                background: -o-linear-gradient(top,#8dc893,#31a424);
                box-shadow: inset 0px 1px 0px 0px #31a424;
                -webkit-box-shadow: inset 0 1px 0 0 #31a424;
                font-weight: bold;
                padding: 5px 20px 30px 20px;
                font-size: 11px;
                background-color: #F2B54B;
                border: 0px solid #fff;
                text-decoration: none;
                cursor: pointer;
                position: relative;
                margin: 3px;
                white-space: nowrap;
            }

            .button1 {
                display: inline-block;
                color: white;
                font-weight: bold;
                padding: 3px;
                font-size: 10px;
                background-color: #8dc893;
                border: 1px solid #31a424;
                border-radius: 3px;
            }

            .menu {
                background: #fff url(images/menu.gif) repeat-x bottom;
                border-bottom: 1px solid #d6d6d6;
                color: #222;
                margin: 0;
                margin-bottom: 1px;
                padding: 9px;
            }

            .fmantext {
                background: #fff;
                padding: 5px 3px;
                font-size: 10px;
                color: #000;
                border: 1px solid #999999;
                text-align: center;
                position: absolute;
                left: 0;
                bottom: 0;
                width: 91.9%;
                outline: medium none;
                opacity: 0.9;
            }

            @media (min-width: 640px) {
                #content3 {
                    width: 70%;
                    float: left;
                }
                #sidebar3 {
                    width: 30%;
                    float: right;
                }
            }

            div.team_name7 {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .counts {
                float: right;
                background: #E3E8EA;
                padding: 1px 1px;
                border-radius: 3px;
                font-size: 10px;
                font-weight: bold;
                color: #e0321e;
                box-shadow: inset 0 1px 1px rgb(0 0 0 / 10%);
            }
            
            </style>

            <?php 
            echo '<div class="gmenu" id="content3">';

            // Club section
            echo '<div class="phdr game-tour-holder"><i class="fi fi-pos x-color-dg"></i> <b>'.htmlspecialchars($club).'</b></div>';
            echo '<div class="c" align="center">
                    <a class="button1" href="/team/'.$kom['id'].'"><img style="width:56px" src="/images/menu2/team.png"><span class="fmantext"><div class="team_name7">'.htmlspecialchars($komanda).'</div></span></a>
                    <a class="button1" href="/team/sostav.php"><img style="width:56px" src="/images/menu2/template.png"><span class="fmantext"><div class="team_name7">'.htmlspecialchars($sostav).'</div></span></a>
                    <a class="button1" href="/team/train.php"><img style="width:56px" src="/images/menu2/trening.png"><span class="fmantext"><div class="team_name7">'.htmlspecialchars($trener).'<span class="counts" style="color:red;">28</span></div></span></a>
                    <a class="button1" href="/team/train.php?act=tren"><img style="width:56px" src="/images/menu2/trening_buy.png"><span class="fmantext"><div class="team_name7">'.htmlspecialchars($tren1).'</div></span></a>
                    <a class="button1" href="/team/tactic.php"><img style="width:56px" src="/images/menu2/transfer.png"><span class="fmantext"><div class="team_name7">'.htmlspecialchars($taktika).'</div></span></a>
                  </div>';

            // Matches section
            $totalij = mysql_result(mysql_query("SELECT COUNT(*) FROM `taklif_ijara` WHERE beruvchi='".$datauser['manager2']."'"), 0);

            echo '<div class="phdr game-tour-holder"><i class="fi fi-cup x-color-dg"></i> <b>'.htmlspecialchars($match).'</b></div>';
            echo '<div class="c" align="center">
                    <a class="button1" href="/friendly/"><img style="width:56px" src="/images/menu2/tov.png"><span class="fmantext"><div class="team_name7">'.htmlspecialchars($frendly2).'</div></span></a>';

            // Determine tournament ID based on team level
            $tid = ($kom['level'] <= 3) ? $kom['level'] : ($kom['level'] == 4 ? 4 : 5);
            $bid = ($kom['level'] <= 3) ? 1 : 2;

            echo '<a class="button1" href="/tournament/index.php?id='.$tid.'"><img style="width:56px" src="/images/menu2/tournament.png"><span class="fmantext"><div class="team_name7">'.htmlspecialchars($r_kuboklar).'</div></span></a>
                  <a class="button1" href="/champ/"><img style="width:56px" src="/images/menu2/chemp.png"><span class="fmantext"><div class="team_name7">'.htmlspecialchars($champ).'</div></span></a>';

            if($datauser['mir'] == 'retro80') {
                echo '<a class="button1" href="/maradona/"><img style="width:56px" src="/images/menu2/ligan.png"><span class="fmantext"><div class="team_name7">'.htmlspecialchars($maradona_cup2).'</div></span></a>';
            }

            if($datauser['mir'] == 'retro80') {
                echo '<a class="button1" href="/fedcup/"><img style="width:56px" src="/images/menu2/nation.png"><span class="fmantext"><div class="team_name7">'.htmlspecialchars($fedcups2).'</div></span></a>';
            } elseif($datauser['mir'] == 'retro2000') {
                echo '<a class="button1" href="/fedcup2/"><img style="width:56px" src="/images/menu2/nation.png"><span class="fmantext"><div class="team_name7">'.htmlspecialchars($fedcups2).'</div></span></a>';
            }
            $tototal = mysql_num_rows(mysql_query("SELECT * FROM `r_frend` WHERE id_team2='".$datauser['manager2']."' AND id_team1>'0'"));

            echo '<a class="button1" href="/evrocups/"><img style="width:56px" src="/images/menu2/cup_uefa_lc_80.png"><span class="fmantext"><div class="team_name7">'.$eurocup.'</div></span></a>
                  <a class="button1" href="/union/vsch.php"><img style="width:56px" src="/images/menu2/sncup.png"><span class="fmantext"><div class="team_name7">'.$vsch.'</div></span></a>
                  <a class="button1" href="/paynetcup/index.php"><img style="width:56px" src="/images/menu2/cuppriz.png"><span class="fmantext"><div class="team_name7">'.$paynetcup.'</div></span></a>
                  <a class="button1" href="/friendly/to.php"><img style="width:56px" src="/images/menu2/playnow.png"><span class="fmantext"><div class="team_name7">'.$frend_to2.' <span class="counts">'.$total.'</span></div></span></a>
                  <a class="button1" href="/turnir/gold.php"><img style="width:56px" src="/images/menu2/archzal.png"><span class="fmantext"><div class="team_name7">'.$gold.'</div></span></a>
                  <a class="button1" href="/history/'.$kom['id'].'"><img style="width:56px" src="/images/menu2/history.png"><span class="fmantext"><div class="team_name7">'.$arhivmatch.'</div></span></a>
                  </div>';

            // Communication section
            $chatonltime = $realtime - 300;
            $chatonline_u = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `lastdate` > $chatonltime AND `place` LIKE 'chat%'"), 0);
            unset($_SESSION['fsort_id'], $_SESSION['fsort_users']);

            $onltime = $realtime - 300;
            $online_u2 = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `lastdate` > $onltime AND `place` LIKE 'forum%'"), 0);

            echo '<div class="phdr game-tour-holder"><i class="fi fi-users x-color-dg"></i> <b>'.$obsheniya.'</b></div>';
            echo '<div class="c" align="center">
                    <a class="button1" href="/forum/"><img style="width:56px" src="/images/menu2/forum.png"><span class="fmantext"><div class="team_name7">'.$forum.($online_u2 >= 1 ? '<span class="counts">'.$online_u2.'</span>' : '').'</div></span></a>
                    <a class="button1" href="/chat.php"><img style="width:56px" src="/images/menu2/chat.png"><span class="fmantext"><div class="team_name7">'.$chat.($chatonline_u >= 1 ? '<span class="counts">'.$chatonline_u.'</span>' : '').'</div></span></a>
                    <a class="button1" href="/union/"><img style="width:56px" src="/images/menu2/union.png"><span class="fmantext"><div class="team_name7">'.$soyuz.'</div></span></a>
                  </div>';

            // Transfer section
            echo '<div class="phdr game-tour-holder"><i class="fi fi-basket x-color-dg"></i> <b>'.$transfer.'</b></div>';
            echo '<div class="c" align="center">';

            $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `r_player` WHERE `retro` ='".$datauser['mir']."' AND `t_money` != '0'"), 0);
            $totalreal = mysql_result(mysql_query("SELECT COUNT(*) FROM `r_player_real` WHERE id != '0'"), 0);
            $men_tak = mysql_num_rows(mysql_query("SELECT * FROM `taklif` WHERE `id` AND `oluvchi`='".$datauser['manager2']."'"));
            $totalleg = mysql_result(mysql_query("SELECT COUNT(*) FROM `legend` WHERE id != '0'"), 0);

            echo '<a class="button1" href="/transfer/"><img style="width:56px" src="/images/menu2/transfer.png"><span class="fmantext"><div class="team_name7">'.$transfer.'<span class="counts">'.$total.'</span></div></span></a>
                  <a class="button1" href="/player/bookmark.php"><img style="width:56px" src="/images/menu2/agent.png"><span class="fmantext"><div class="team_name7">'.$bookmark2.'</div></span></a>
                  <a class="button1" href="/fm/magazin.php"><img style="width:56px" src="/images/menu2/buy_pay.png"><span class="fmantext"><div class="team_name7"><b>'.$magazin.'</b></div></span></a>
                  <a class="button1" href="/player/shop.php"><img style="width:56px" src="/images/menu2/mne.png"><span class="fmantext"><div class="team_name7">'.$realplayer2.'<span class="counts">'.$totalreal.'</span></div></span></a>
                  <a class="button1" href="/transfer/taklif.php"><img style="width:56px" src="/images/menu2/my.png"><span class="fmantext"><div class="team_name7">Покупки<span class="counts">'.$men_tak.'</span></div></span></a>
                  <a class="button1" href="/ijara/"><img style="width:56px" src="/images/menu2/who_arend.png"><span class="fmantext"><div class="team_name7">'.$ijr.'<span class="counts">'.$totalij.'</span></div></span></a>';

            if(!$kom['retro']) {
                echo '<a class="button1" href="/agent/"><img style="width:56px" src="/images/menu2/whereplay.png"><span class="fmantext"><div class="team_name7">'.$agent.'</div></span></a>';
            }

            echo '<a class="button1" href="/player/search.php"><img style="width:56px" src="/images/menu2/search.png"><span class="fmantext"><div class="team_name7">'.$poiskplayer.'</div></span></a>
                  <a class="button1" href="/team/search.php"><img style="width:56px" src="/images/menu2/search2.png"><span class="fmantext"><div class="team_name7">'.$poiskclub.'</div></span></a>
                  </div>';

            // Office section
            echo '<div class="phdr game-tour-holder"><i class="fi fi-attach x-color-dg"></i> <b>'.$ofis.'</b></div>';
            echo '<div class="c" align="center">
                    <a class="button1" href="../serv_manager.php?act=menu&amp;id='.$kom['id'].'"><img style="width:56px" src="/images/menu2/settings.png"><span class="fmantext"><div class="team_name7">'.$servis.'</div></span></a>
                    <a class="button1" href="/str/zadanie.php"><img style="width:56px" src="/images/menu2/task.png"><span class="fmantext"><div class="team_name7">'.$topshiriqlar2.'</div></span></a>
                    <a class="button1" href="/staff/"><img style="width:56px" src="/images/menu2/staff.png"><span class="fmantext"><div class="team_name7">'.$personal.'</div></span></a>
                    <a class="button1" href="/buildings/baza.php"><img style="width:56px" src="/images/menu2/baza.png"><span class="fmantext"><div class="team_name7">'.$baza.'</div></span></a>
                    <a class="button1" href="/buildings/stadium.php?id='.$kom['id'].'"><img style="width:56px" src="/images/menu2/std.png"><span class="fmantext"><div class="team_name7">'.$stadion.'</div></span></a>
                    <a class="button1" href="/totalizator/"><img style="width:56px" src="/images/menu2/bet.png"><span class="fmantext"><div class="team_name7">'.$turnir1x2.'</div></span></a>
                    <a class="button1" href="/rating/"><img style="width:56px" src="/images/menu2/uefa.png"><span class="fmantext"><div class="team_name7">'.$rating.'</div></span></a>
                    <a class="button1" href="/referal.php"><img style="width:56px" src="/images/menu2/rait.png"><span class="fmantext"><div class="team_name7">'.$referal.'</div></span></a>';

            // Calculate trophies count
            $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `r_priz` WHERE win = '".$datauser['manager2']."'"), 0);
            $total55 = mysql_result(mysql_query("SELECT COUNT(*) FROM `r_priz` WHERE win = '".$datauser['mtj']."'"), 0);
            $total56 = mysql_result(mysql_query("SELECT COUNT(*) FROM `r_priz_player` WHERE `id_cup`='goldglow' AND team = '".$datauser['manager2']."'"), 0);
            $total57 = mysql_result(mysql_query("SELECT COUNT(*) FROM `r_priz_player` WHERE `id_cup`='goldball' AND team = '".$datauser['manager2']."'"), 0);
            $total58 = mysql_result(mysql_query("SELECT COUNT(*) FROM `r_priz_player` WHERE `id_cup`='goldbutsa' AND team = '".$datauser['manager2']."'"), 0);
            $ttt = $total + $total55 + $total56 + $total57 + $total58;

            echo '<a class="button1" href="/team/trophies.php"><img style="width:56px" src="/images/menu2/raitplayer.png"><span class="fmantext"><div class="team_name7">'.$trofey.'<span class="counts">'.$ttt.'</span></div></span></a>
                  <a class="button1" href="/team/news.php?id='.$kom['id'].'"><img style="width:56px" src="/images/menu2/newsman.png"><span class="fmantext"><div class="team_name7">'.$news.'</div></span></a>';

            if($kom['mir'] == 'retro80' || $kom['mir'] == 'retro2000') {
                echo '<a class="button1" href="/str/cont1.php?act=delman&amp;id='.$kom['id'].'"><img style="width:56px" src="/images/menu2/del.png"><span class="fmantext"><div class="team_name7">'.$uvol.'</div></span></a>';
            } else {
                echo '<a class="button1" href="/str/cont1.php?act=delmans&amp;id='.$kom['id'].'"><img style="width:56px" src="/images/menu2/del.png"><span class="fmantext"><div class="team_name7">'.$uvol.'</div></span></a>';
            }

            echo '</div></div>';

            // Sidebar chat
            echo '<aside class="gmenu" id="sidebar3">';
            require('../chat2.php');
            echo '</aside>';

            echo '<table class="team_table_pad" id="generallist"><tbody><tr></tr></tbody></table></table>';

        } else {
            // Команда не найдена в базе
            echo '<div class="phdr"><center><b>Ваша команда не найдена</b></center></div>';
            echo '<div class="menu"><a href="/store_teams.php">Добавить новую команду</a></div>';
        }
    } else {
        // Пользователь не имеет команды - показываем интерфейс для выбора/создания команды
        echo '<div class="phdr"><center><b>Добро пожаловать в футбольный менеджер</b></center></div>';

        // Проверяем, есть ли у пользователя команда в другом режиме
        $has_team = false;
        $qkws = mysql_query("SELECT * FROM `r_team` WHERE id='".$datauser['manager2']."' LIMIT 1");
        if (mysql_num_rows($qkws)) {
            $mtjs = mysql_fetch_array($qkws);
            $has_team = true;
        }
        
        $qkw = mysql_query("SELECT * FROM `mtj` WHERE id='".$datauser['mtj']."' LIMIT 1");
        if (mysql_num_rows($qkw)) {
            $mtj = mysql_fetch_array($qkw);
            $has_team = true;
        }

        echo '<div class="lt3">';
        if(!$datauser['manager2']) {
            echo '<a href="/store_teams.php"><div class="team-add">добавить команду<br><span style="font-size: 18px;">1</span></div></a>';
        } else {
            echo '<a href="/fm/?mod=team"><div class="b">
                    <img src="/manager/logo/big'.$mtjs['logo'].'" style="width: 37px; height: 37px;" title="Перейти в управление клубом '.$mtjs['name'].'" alt="'.$mtjs['name'].'">
                  </div></a>';
        }
        
        if(!$datauser['mtj']) {
            echo '<a href="/store_teams2.php"><div class="team-add">добавить команду<br><span style="font-size: 18px;">2</span></div></a>';
        } else {
            echo '<a href="/fm/?mod=mtj"><div class="b">
                    <img src="/manager/mtj/big'.$mtj['logo'].'" style="width: 37px; height: 37px;" title="Перейти в управление клубом '.$mtj['name'].'" alt="'.$mtj['name'].'">
                  </div></a>';
        }
        echo '</div>';

        // Выбор мира игры
        switch ($datauser['mir']) {
            case "virt":
                echo '<a href="?mod=mir80"><div class="x-my-3 x-text-center x-font-bold" bis_skin_checked="1"><span class="x-bc-li1 x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Ретро Мир 80-х</a></span>
                      <a href="?mod=mir2000"><span class="x-bg-bronse x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Ретро Мир 00-х</a></span>
                      </div>';
                
                echo '<div class="phdr"><center><b>Выберите уникальное название Вашей команды</b></center></div>
                      <div style="max-width:280px; margin:0px auto; padding-top:20px;" bis_skin_checked="1">
                      <div class="gmenu"><div class="row-input"><center><form action="/addteam.php" method="post">
                      <b></b>Название команды:<br/><input type="text" name="nameteam" value=""/><br/>
                      <b>Страна команды:</b><br/>
                      <select name="flag">
                        <option value="ru">Россия</option>
                        <option value="ua">Украина</option>
                        <option value="en">Англия</option>
                        <option value="it">Италия</option>
                        <option value="sp">Испания</option>
                        <option value="ge">Германия</option>
                        <option value="fr">Франция</option>
                        <option value="nl">Голландия</option>
                      </select><br/>
                      <b>Выбор мира:</b><br/>
                      <select name="mir">
                        <option value="1">1 мир</option>
                        <option value="2">2 мир</option>
                        <option value="3">3 мир</option>
                      </select><br/>
                      <input type="submit" title="Нажмите чтобы начать игру" name="submit" value="Создать"/></form></center>
                      </div></div></div>';
                
                echo '<div class="cardview" bis_skin_checked="1">
                      <div class="x-row" bis_skin_checked="1">
                        <div class="x-col-1 x-vh-center x-font-250 x-color-white x-bg-green" bis_skin_checked="1">
                          <i class="font-icon font-icon-whistle"></i>
                        </div>
                        <div class="x-col-5 x-p-3" bis_skin_checked="1">
                          <div class="" bis_skin_checked="1">Название может содержать либо только символы русского и украинского алфавитов, или только английского длиной не более 20 символов!</div>
                        </div>
                      </div>
                      </div>';
                break;
                
            case "retro80":
                echo '<a href="?mod=mir80"><div class="x-my-3 x-text-center x-font-bold" bis_skin_checked="1"><span class="x-bc-li1 x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Ретро Мир 80-х</a></span>
                      <a href="?mod=mir2000"><span class="x-bg-bronse x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Ретро Мир 00-х</a></span>
                      </div>';
                
                echo '<img src="/images/mir/retro80.jpg" style="width: 100%; height: 50%;">
                      <p><b>Что тебя ожидает?</b></p>
                      <ul><li>Сотни ретро-клубов 80-х из самых ведущих футбольных стран!</li></ul>
                      <ul><li>Больше 50-ти ретро-сборных 80-х!</li></ul>
                      <ul><li>Тысячи ретро игроков 80-х, которых можно прокачивать!</li></ul>
                      <ul><li>Ретро Чемпионат Европы. И Чемпионат Мира!</li></ul>
                      <ul><li>Кубок Европейских Чемпионов, Кубок УЕФА, Кубок Интертото и многие ретро турниры!</li></ul>
                      <a href="/auction.php"><div class="x-my-3 x-text-center x-font-bold" bis_skin_checked="1"><span class="x-bg-orange x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Аукцион!!!</a></span>
                      <a href="/store_teams.php"><span class="x-bg-green x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Магазин!!!</a></span></div>';
                break;
                
            case "retro2000":
                echo '<a href="?mod=mir80"><div class="x-my-3 x-text-center x-font-bold" bis_skin_checked="1"><span class="x-bc-li1 x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Ретро Мир 80-х</a></span>
                      <a href="?mod=mir2000"><span class="x-bg-bronse x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Ретро Мир 00-х</a></span>
                      </div>';
                
                echo '<img src="/images/mir/retro2000.jpg" style="width: 100%; height: 50%;">
                      <p><b>Что тебя ожидает?</b></p>
                      <ul><li>Сотни ретро-клубов 00-х из самых ведущих футбольных стран!</li></ul>
                      <ul><li>Больше 50-ти ретро-сборных 00-х!</li></ul>
                      <ul><li>Тысячи ретро игроков 00-х, которых можно прокачивать!</li></ul>
                      <ul><li>Ретро Чемпионат Европы. И Чемпионат Мира!</li></ul>
                      <ul><li>Лига Чемпионов, Кубок УЕФА, Кубок Интертото и многие ретро турниры!</li></ul>
                      <a href="/auction.php"><div class="x-my-3 x-text-center x-font-bold" bis_skin_checked="1"><span class="x-bg-orange x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Аукцион!!!</a></span>
                      <a href="/store_teams.php"><span class="x-bg-green x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Магазин!!!</a></span></div>';
                break;
                
            default:
                echo '<a href="?mod=mir80"><div class="x-my-3 x-text-center x-font-bold" bis_skin_checked="1"><span class="x-bc-li1 x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Ретро Мир 80-х</a></span></div>
                      <a href="?mod=mir2000"><div class="x-my-3 x-text-center x-font-bold" bis_skin_checked="1"><span class="x-bg-bronse x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Ретро Мир 00-х</a></span></div>
                      <a href="/auction.php"><div class="x-my-3 x-text-center x-font-bold" bis_skin_checked="1"><span class="x-bg-orange x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Аукцион!!!</a></span></div>
                      <a href="/store_teams.php"><div class="x-my-3 x-text-center x-font-bold" bis_skin_checked="1"><span class="x-bg-green x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Магазин!!!</a></span></div>';
                break;
        }
    }
} else {
    // Пользователь не авторизован
    echo '<div class="phdr"><center><b>Для доступа к менеджеру необходимо авторизоваться</b></center></div>';
    echo '<div class="menu"><a href="/login.php">Войти</a> | <a href="/registration.php">Зарегистрироваться</a></div>';
}

// Если у пользователя есть сборная, перенаправляем на соответствующую страницу
// На этот:
if (!empty($datauser['mtj']) && $datauser['club'] == 'mtj') {
    header('Location: /fm/index2.php');
    exit;
}

require_once('../incfiles/end.php');
?>
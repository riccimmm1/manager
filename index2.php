<?php
define('_IN_JOHNCMS', 1);
$headmod = 'mainpage';
$textl = 'Менеджер';
require_once('../incfiles/core.php');
require_once('../incfiles/head.php');
require_once('../incfiles/ban.php');
require_once('../incfiles/code/fakt.php');

// Обработка переключения между клубом и сборной
if (isset($_GET['mod'])) {
    $mod = $_GET['mod'];
    if ($mod == 'team' || $mod == 'mtj') {
        mysql_query("UPDATE `users` SET `club` = '$mod' WHERE `id` = '$user_id'");
        
        // Проверяем, есть ли у пользователя команда в выбранном режиме
        if ($mod == 'team' && empty($datauser['manager2'])) {
            // Если переключаемся на клуб, но клуба нет - перенаправляем на создание
            header('Location: /store_teams.php');
            exit;
        } elseif ($mod == 'mtj' && empty($datauser['mtj'])) {
            // Если переключаемся на сборную, но сборной нет - перенаправляем на создание
            header('Location: /store_teams2.php');
            exit;
        }
        
        header('Location: /fm/index.php');
        exit;
    }
}

// Проверка авторизации пользователя
if (!$datauser['id']) {
    require_once('../incfiles/slider.php');
    echo '<div class="phdr"><b>Онлайн Футбольный Менеджер</b></div>';
    echo '<div class="gmenu"><center><img src="/images/fman.jpeg" alt="16"/></center></div>';
    echo '<div class="gmenu"> <font color="darkgreen"><b>FOOT24</b></font> - Бесплатная онлайн игра в жанре футбольного менеджера. Вы создаёте футбольный клуб и управляете им. Соревнуйтесь с тысячью реальных игроков, вступайте в кубковые турниры и национальные чемпионаты. Станьте лидером!</div>';
    echo '<table id="pallet"><tr>';
    echo '<div class="info">';
    echo '<center> <td><form> <a href="/registration.php" class="redbutton"> Регистрация</a></form></td></center>';
    echo '</div>';
    echo '</tr></table>';
    echo '<div class="phdr"><b>Факт из футбола</b></div>';
    echo '<div class="c orangebk">' . $fakt['fakt'] . '</div>';
    echo '<div class="phdr"><b>Как играть?</b></div>';
    echo '<div class="m_main_menu_item">';
    echo '<div class="m_main_menu_item"> <a href="/football_manager/rules.php">Правила</a> </div> ';
    echo '<div class="m_main_menu_item"> <a href="/football_manager/help.php">Справка</a> </div> ';
    header('Location: /login.php');
    require_once('../incfiles/end.php');
    exit;
}

// Если пользователь должен быть в разделе клуба
// На:
if ($datauser['club'] == 'team' || empty($datauser['mtj'])) {
    header('Location: /fm/index.php');
    exit;
}

// Если пользователь в команде mtj
if ($datauser['club'] == 'mtj' && !empty($datauser['mtj'])) {
    // Проверка наличия команды у пользователя
  /*  if (empty($datauser['mtj'])) {
        mysql_query("UPDATE `users` SET `club` = 'team' WHERE `id` = '" . intval($user_id) . "'");
        header('Location: /fm/index.php');
        exit;
    }*/

    // Получение данных команды
    $team_id = intval($datauser['mtj']);
    $qk = mysql_query("SELECT * FROM `mtj` WHERE id = '$team_id' LIMIT 1");
    
    if (mysql_num_rows($qk) == 0) {
        header('Location: /fm/index.php');
        exit;
    }
    
    $kom777 = mysql_fetch_assoc($qk);

    // Уровни и опыт
    $levels = array(
        1 => 100, 2 => 300, 3 => 800, 4 => 2000, 5 => 4000,
        6 => 7500, 7 => 11000, 8 => 18000, 9 => 25000, 10 => 30000,
        11 => 40000, 12 => 37000, 13 => 45000, 14 => 55000, 15 => 65000,
        16 => 77000, 17 => 90000, 18 => 105000, 19 => 125000, 20 => 155000,
        21 => 1900000
    );
    
    $oput = isset($levels[$kom777['level']]) ? $levels[$kom777['level']] : 0;

    // Проверка повышения уровня
    if ($kom777['oput'] >= $oput && $oput > 0) {
        $addlevel = $kom777['level'] + 1;
        mysql_query("UPDATE `mtj` SET `level` = '$addlevel' WHERE id = '$team_id'");
        
        $u = mysql_query("SELECT `name` FROM `users` WHERE `id` = '" . intval($kom777['id_admin']) . "' LIMIT 1");
        $user = mysql_fetch_assoc($u);

        $message = "Поздравляем! \r\n\r\n Ваша команда [color=green] " . $kom777['name'] . " [/color] получила [color=green][b]" . $addlevel . "-й Уровень[/b][/color]";
        $message = mysql_real_escape_string($message);
        
        mysql_query("INSERT INTO `privat` SET
            `user` = '" . mysql_real_escape_string($user['name']) . "',
            `user_id` = '1098',
            `text` = '$message',
            `time` = '" . time() . "',
            `author` = 'system',
            `type` = 'in',
            `chit` = 'no',
            `temka` = 'Новый уровень',
            `otvet` = '0',
            `me` = '0',
            `cont` = '0',
            `ignor` = '0',
            `attach` = '0'");
    }

    // Расчет физической формы команды
    $req = mysql_query("SELECT * FROM `r_player` WHERE `mtj` = '$team_id' ORDER BY line ASC, poz ASC");
    $total = mysql_num_rows($req);
    $allfizkom777 = 0;
    
    while ($arr = mysql_fetch_assoc($req)) {
        $allfizkom777 += $arr['fiz'];
    }

    $player_ids = array_filter([
        $kom777['i1'], $kom777['i2'], $kom777['i3'], $kom777['i4'], $kom777['i5'],
        $kom777['i6'], $kom777['i7'], $kom777['i8'], $kom777['i9'], $kom777['i10'], $kom777['i11']
    ]);
    
    if (!empty($player_ids)) {
        $ids_str = implode("','", array_map('intval', $player_ids));
        $r = mysql_query("SELECT * FROM `r_player` WHERE `id` IN ('$ids_str') AND `mtj` = '$team_id' LIMIT 11");
        
        $allfizsos = 0;
        while ($e = mysql_fetch_assoc($r)) {
            $allfizsos += $e['fiz'];
        }
        
        $fizsos = round($allfizsos / 11);
        $fizkom777 = round($allfizkom777 / $total);
    } else {
        $fizsos = 0;
        $fizkom777 = 0;
    }

    // Аватар пользователя
    $img = file_exists("../files/avatar/" . $datauser['id'] . ".png") 
        ? '/files/avatar/' . $datauser['id'] . '.png' 
        : '/images/no_avatar.png';

    // Вывод HTML
    ?>
    <?php if (isset($_GET['ok'])): ?>
        <div class="pravmenu"><b><font color="red"><center><?php echo htmlspecialchars($lng_til['til_ozgartir']); ?></center></font></b></div>
    <?php endif; ?>

    <div class="gmenu m_info">
        <div class="m_avatar_block game-tour-holder">
            <div class="m_avatar" style="background-image: url(<?php echo htmlspecialchars($img); ?>);">
                <a href="/avatar.php"><img src="/images/rounder.png" alt="Avatar"/></a>
            </div>
        </div>

        <div class="m_team_block">
            <table><tr><td></td></tr></table>
            
            <div class="m_team_block">
                <div class="m_team_name_block">
                    <div class="m_team_name">
                        <td>
                            <span class="flags c_<?php echo htmlspecialchars($kom777['flag']); ?>_18" style="vertical-align: middle;" title="<?php echo htmlspecialchars($kom777['flag']); ?>"></span> 
                            <?php echo htmlspecialchars($kom777['name']); ?>
                        </td>
                        <div class="m_team_ligue">
                            <?php 
                            switch($kom777['strana']) {
                                case "yashin": echo "УЕФА (Европа)"; break;
                                case "pele": echo "ФИФА (Старый мир)"; break;
                                default: echo "Неизвестная лига";
                            }
                            ?>
                        </div>
                    </div>

                    <div class="m_avatar_block2">
                        <?php
                        if ($datauser['club'] == 'mtj') {
                            if (empty($datauser['mtj'])) {
                                mysql_query("UPDATE `users` SET `club` = 'mtj' WHERE `id` = '" . intval($user_id) . "'");
                                header('Location: /fm/index.php');
                                exit;
                            }
                            $qk5 = mysql_query("SELECT * FROM `r_team` WHERE id_admin = '$user_id' LIMIT 1");
                            $kom = mysql_fetch_assoc($qk5);
                            $qkw = mysql_query("SELECT * FROM `mtj` WHERE id = '$team_id' LIMIT 1");
                            $mtj = mysql_fetch_assoc($qkw);
                            
                            $www = empty($kom['logo']) ? '/manager/logo/b_0.jpg' : '/manager/logo/big' . $kom['logo'];
                            
                            if (!$datauser['manager2']) {
                                echo '<a class="button1" href="/store_teams.php"><img style="width:56px" src="/images/menu2/team1.png" title="Добавить команду"><span class="fmantext"><div class="team_name7">Добавить команду</div></span></a>';
                            } else {
                                echo '<a class="button1" href="/fm/?mod=team"><img style="width:56px" src="' . htmlspecialchars($www) . '" title="Перейти в управление клубом ' . htmlspecialchars($kom['name']) . '"><span class="fmantext"><div class="team_name7">' . htmlspecialchars($kom['name']) . '</div></span></a>';
                            }
                            
                            if (!$datauser['mtj']) {
                                echo '<a class="button1" href="/store_teams2.php"><img style="width:56px" src="/images/menu2/team2.png" title="Добавить команду"><span class="fmantext"><div class="team_name7">Добавить команду</div></span></a>';
                            } else {
                                echo '<a class="button1" href="/fm/?mod=mtj"><img style="width:56px" src="/manager/mtj/big' . htmlspecialchars($mtj['logo']) . '" title="Перейти в управление клубом ' . htmlspecialchars($mtj['name']) . '"><span class="fmantext"><div class="team_name7">' . htmlspecialchars($mtj['name']) . '</div></span></a>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="m_team_logo">
            <?php if ($kom777['retro'] == 0): ?>
                <a href="/mtj/logo.php?act=up_logo&amp;id=<?php echo intval($kom777['id']); ?>"></a>
            <?php endif; ?>
        </div>
    </div>

    <style>
.team-add {
    text-align: center;
    border: 1px dashed #fff;
    color: #fff;
    CURSOR: pointer;
    padding: 3px;
    opacity: 0.3;
}
.team-add:hover {
    opacity: 1;
}
.head-ui__search-form {
    display: inline-block;
    float: right;
    margin-right: 50px;
    margin-top: 16px;
    opacity: 0.2;
    padding: 3px;
    position: absolute;
    margin-left: 40px;
}
.lt3 {
    margin-right: 40px;
    padding: 6px;
    background-image: url(/images/42576d0faf80.png);
    border-radius: 8px 8px 0 0;
    float: right;
    display: inline-block;
}
.lt3 div {
    padding: 3px;
    float: left;
    overflow: hidden;
    margin-right: 3px;
    width: 46px;
    height: 46px;
    text-align: center;
    font-size: 8px;
    CURSOR: pointer;
}
.lt3 div.b {
    padding: 3px;
    float: left;
    overflow: hidden;
    margin-right: 3px;
    width: 46px;
    height: 46px;
    text-align: center;
    font-size: 8px;
    border: 2px solid #71ff5e;
    background-image: url(/images/active.png);
    border-radius: 3px;
    CURSOR: pointer;
}
.lt3 div {
    padding: 3px;
    float: left;
    overflow: hidden;
    margin-right: 3px;
    width: 46px;
    height: 46px;
    text-align: center;
    font-size: 8px;
    CURSOR: pointer;
}
</style>

    <div class="phdr m_team_level" style="overflow: visible;">
        <div class="m_time_wrap">
            <div id="m_time" onclick="setShowHide(this.id); return false;" class="m_time" style="display: none;">
                <?php echo date("d.m.Y H:i:s", time()); ?>
            </div>
        </div>
        
        <table class="m_team_level_table" style="">
            <tr>
                <td><div class="m_team_level_num game-tour-holder"><?php echo htmlspecialchars($uroven) . ' ' . intval($kom777['level']); ?></div></td>
                <td style="width: 100%;" class="tooltip">
                    <span class="tooltip-text tooltip-top">Прогресс: <?php echo intval($kom777['oput']); ?>/<?php echo intval($oput); ?></span>
                    <div class="m_team_level_pbar game-tour-holder">
                        <div style="width: <?php echo ($kom777['oput'] / $oput * 100); ?>%;"></div>
                    </div>
                </td>
                <td><i class="fi fi-clock x-color-dg"></i></td>
                <td>
                    <div class="m_time_short game-tour-holder" onclick="setShowHide('m_time'); return false;">
                        <?php echo date("H:i", time()); ?>
                    </div>
                </td>
            </tr>
        </table>

        <table class="m_team_level_table" style="margin: 6px;">
            <tr>
                <td><div class="m_team_stat"><i class="fi fi-rocket x-color-yellow"></i><small><?php echo htmlspecialchars($opyt) . ' <b>' . intval($kom777['oput']) . '</b>'; ?></small></div></td>
                <td><div class="m_team_stat"><i class="fi fi-users x-color-red"></i><small><?php echo htmlspecialchars($fans) . ' <b>' . intval($kom777['fans']) . '</b>'; ?></small></div></td>
                <td><div class="m_team_stat"><i class="fi fi-flag x-color-dg"></i><small><?php echo htmlspecialchars($slava) . ' <b>' . intval($kom777['slava']) . '</b>'; ?></small></div></td>
            </tr>
        </table>
    </div>

    <?php
    // Админ панель
    if ($user_id == 1094) {
        echo '<div class="info"><a href="/fm/admin.php">Админ панель</a></div>';
        echo '<div class="info"><a href="/admin.php">Админ панель2</a></div>';
    }

    // Информация о команде
    if (!empty($datauser['mtj'])) {
        $upm = mysql_query("SELECT * FROM `mtj` WHERE `id` = '$team_id'");
        $us_mtj = mysql_fetch_assoc($upm);
        $mtj_frend = mysql_query("SELECT * FROM `r_frend_mtj` WHERE id_team1 = '$team_id' AND id_team2 > '0'");
        $ftr = mysql_num_rows($mtj_frend);
        $m_mtj_frend = mysql_query("SELECT * FROM `r_frend_mtj` WHERE id_team2 = '$team_id' AND id_team2 > '0'");
        $m_ftr = mysql_num_rows($m_mtj_frend);
        
        $mat = '';
        switch ($us_mtj['materic']) {
            case "uefa1": $mat = $m_uefa1; break;
            case "uefa": $mat = $m_uefa; break;
            case "konmebol": $mat = $m_konmebol; break;
            case "konkakaf": $mat = $m_konkakaf; break;
            case "kaf": $mat = $m_kaf; break;
            case "afk": $mat = $m_afk; break;
            case "ofk": $mat = $m_ofk; break;
            default: $mat = "Неизвестный регион";
        }
    }

    // Бонус за посещение
    $ip = mysql_real_escape_string($datauser['ip']);
    $quc = mysql_query("SELECT * FROM `umedal` WHERE `ip` = '$ip'");
    $сont = mysql_num_rows($quc);
    $qun = mysql_query("SELECT * FROM `umedal` WHERE `user` = '" . intval($user_id) . "'");
    $um = mysql_num_rows($qun);

    if (isset($_GET['rubl']) && $сont == 0 && $um == 0) {
        mysql_query("UPDATE `users` SET `rubl` = `rubl` + 1 WHERE `id` = '" . intval($user_id) . "' LIMIT 1");
        mysql_query("INSERT INTO `umedal` SET `ip` = '$ip', `user` = '" . intval($user_id) . "'");
        header("Location: /fm/");
        exit;
    }

    if ($сont == 0 && $um == 0) {
        echo '<ul class="m_main_menu">';
        echo '<center><li class="m_main_menu_item"><a href="index.php?rubl"><font color="red">Получить 1 <img src="/images/butcer.png" alt=""></font></a></li></center>';
        echo '</ul>';
    }
    ?>

    <style>
input[type="button1"]:hover, input[type~="button1"]:hover, a.button1:hover {
    color: #fff;
    text-align: center;
    outline: medium none;
    background: -moz-linear-gradient(top,#fab902,#f29b08);
    background: -webkit-gradient(linear,left top,left bottom,from(#fab902),to(#f29b08));
    background: -o-linear-gradient(top,#fab902,#f29b08);
    box-shadow: inset 0px 1px 0px 0px #fdbe26;
    -webkit-box-shadow: inset 0 1px 0 0 #fdbe26;
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
    background: -moz-linear-gradient(top,#F5A607,#F29A09);
    background: -webkit-gradient(linear,left top,left bottom,from(#F5A607),to(#F29A09));
    background: -o-linear-gradient(top,#F5A607,#F29A09);
    box-shadow: inset 0px 1px 0px 0px #fdbe26;
    -webkit-box-shadow: inset 0 1px 0 0 #fdbe26;
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
    background-color: #F5A607;
    border: 1px solid #D58903;
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
    font-size: 11px;
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
        width: 68%;
        float: left;
    }
    #sidebar3 {
        width: 27%;
        float: right;
    }
}

div.team_name7 {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>

    <?php
    echo '<div class="phdr"><strong>' . htmlspecialchars($club) . '</strong></div>';
    echo '<div class="gmenu" align="center">';
    echo '<a class="button1" href="/mtj/' . intval($kom777['id']) . '"><img style="width:56px" src="/images/menu/list.png"><span class="fmantext">' . htmlspecialchars($komanda) . '</span></a>';
    echo '<a class="button1" href="/mtj/tactic.php"><img style="width:56px" src="/images/menu/tactic.png"><span class="fmantext">' . htmlspecialchars($taktika) . '</span></a>';
    echo '<a class="button1" href="/mtj/sostav.php"><img style="width:56px" src="/images/menu/shema.png"><span class="fmantext">' . htmlspecialchars($sostav) . '</span></a>';
    echo '<a class="button1" href="/mtj/train.php"><img style="width:56px" src="/images/menu/train.png"><span class="fmantext">' . htmlspecialchars($trener) . '<span class="counts" style="color:red;">21</span></span></a>';
    echo '<a class="button1" href="train.php"><img style="width:56px" src="/images/menu/trening_buy.png"><span class="fmantext">Прокачка</span></a>';
    echo '</div>';

    echo '<div class="phdr"><strong>ОФИС</strong></div>';
    echo '<div class="c" align="center">';
    echo '<a class="button1" href="/mtj/news.php?id=' . intval($kom777['id']) . '"><img style="width:56px" src="/images/menu/finance.png"><span class="fmantext">' . htmlspecialchars($news) . '</span></a>';
    echo '<a class="button1" href="../serv_manager2.php?act=menu&amp;id=' . intval($kom777['id']) . '"><img style="width:56px" src="/images/menu/buildings.png"><span class="fmantext"><b>' . htmlspecialchars($servis) . '</b></span></a>';
    echo '<a class="button1" href="/buildings/stadium_sb.php?id=' . intval($kom777['id']) . '"><img style="width:56px" src="/images/menu/stad.png"><span class="fmantext"><b>Стадион</b></span></a>';
    echo '<a class="button1" href="/totalizator/"><img style="width:56px" src="/images/menu/lager.png"><span class="fmantext"><b>' . htmlspecialchars($turnir1x2) . '</b></span></a>';
    echo '<a class="button1" href="/mtj/player_retro.php?act=' . htmlspecialchars($kom777['flag']) . '"><img style="width:56px" src="/images/menu/personal.png"><span class="fmantext">Пригласить<br> игроков</span></a>';
    echo '</div>';

    echo '<div class="phdr"><strong>СОРЕВНОВАНИЯ</strong></div>';
    echo '<div class="c" align="center">';
    echo '<a class="button1" href="/friendly_mtj/"><img style="width:56px" src="/images/menu/tovar.png"><span class="fmantext">' . htmlspecialchars($frendly2) . '</span></a>';

    $qkw = mysql_query("SELECT * FROM `mtj` WHERE id = '$team_id' LIMIT 1");
    $mtj = mysql_fetch_assoc($qkw);

    if ($mtj['retro'] == '80') {
        echo '<a class="button1" href="/pele/"><img style="width:56px" src="/images/cup/b_pele.png"><span class="fmantext">' . htmlspecialchars($kch_pele) . '</span></a>';
        echo '<a class="button1" href="/euro/"><img style="width:56px" src="/images/menu/euro.png"><span class="fmantext">' . htmlspecialchars($kch_euro2) . '</span></a>';
        echo '<a class="button1" href="/wc/"><img style="width:56px" src="/images/menu/worldchamp.png"><span class="fmantext">' . htmlspecialchars($mtj_jch2) . '</span></a>';
    } elseif ($mtj['retro'] == '2000') {
        echo '<a class="button1" href="/euro2000/"><img style="width:56px" src="/images/menu/79.png"><span class="fmantext">' . htmlspecialchars($kch_euro2) . '</span></a>';
        echo '<a class="button1" href="/wc2000/"><img style="width:56px" src="/images/menu/78.png"><span class="fmantext">' . htmlspecialchars($mtj_jch2) . '</span></a>';
    }

    echo '<a class="button1" href="/mtj_history/' . intval($kom777['id']) . '"><img style="width:56px" src="/images/menu/arch.png"><span class="fmantext">' . htmlspecialchars($arhivmatch) . '</span></a>';
    echo '</div>';

    echo '<div class="phdr"><strong>ПРОЧЕЕ</strong></div>';
    echo '<div class="c" align="center">';
    echo '<a class="button1" href="/mtj/rating.php"><img style="width:56px" src="/images/menu/fifa.png"><span class="fmantext">' . htmlspecialchars($mtjrating) . '</span></a>';
    echo '<a class="button1" href="/mtj/mtj.php"><img style="width:56px" src="/images/menu/rteam.png"><span class="fmantext">' . htmlspecialchars($sbornye2) . '</span></a>';
    
    $req = mysql_query("SELECT COUNT(*) FROM `r_priz` WHERE win = '$team_id'");
    $total = mysql_result($req, 0);
    $req55 = mysql_query("SELECT COUNT(*) FROM `r_priz` WHERE win = '$team_id'");
    $total55 = mysql_result($req55, 0);
    $req56 = mysql_query("SELECT COUNT(*) FROM `r_priz_player` WHERE `id_cup`='goldglow' AND team = '$team_id'");
    $total56 = mysql_result($req56, 0);
    $req57 = mysql_query("SELECT COUNT(*) FROM `r_priz_player` WHERE `id_cup`='goldball' AND team = '$team_id'");
    $total57 = mysql_result($req57, 0);
    $req58 = mysql_query("SELECT COUNT(*) FROM `r_priz_player` WHERE `id_cup`='goldbutsa' AND team = '$team_id'");
    $total58 = mysql_result($req58, 0);
    
    $ttt = $total + $total55 + $total56 + $total57 + $total58;
    echo '<a class="button1" href="/team/trophies.php"><img style="width:56px" src="/images/menu/rplayer.png"><span class="fmantext">' . htmlspecialchars($trofey) . '</span></a>';
    
    echo '<a class="button1" href="/union/"><img style="width:56px" src="/union/logo/big1.jpg"><span class="fmantext">' . htmlspecialchars($soyuz) . '</span></a>';
    echo '<a class="button1" href="/player/search.php"><img style="width:56px" src="/images/menu/search.png"><span class="fmantext">' . htmlspecialchars($poiskplayer) . '</span></a>';
    
    $chatonltime = time() - 300;
    $chatonline_u = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `lastdate` > $chatonltime AND `place` LIKE 'chat%'"), 0);
    unset($_SESSION['fsort_id']);
    unset($_SESSION['fsort_users']);
    
    echo '<a class="button1" href="/chat.php"><img style="width:56px" src="/images/menu/news.png"><span class="fmantext">';
    if ($chatonline_u >= 1) {
        echo '<b>' . ($user_id ? htmlspecialchars($chaton) : htmlspecialchars($chaton)) . '&nbsp;' . intval($chatonline_u) . '</b>';
    }
    echo '</span></a>';
    
    if ($kom['retro'] == 2000 || $kom['retro'] == 80) {
        echo '<a class="button1" href="/str/cont1.php?act=delman2&amp;id=' . intval($kom777['id']) . '"><img style="width:56px" src="/images/menu/uvol.png"><span class="fmantext">' . htmlspecialchars($uvol) . '</span></a>';
    }
    echo '</div>';
} else {
    // Если есть клуб, но пользователь не в том разделе
    if (!empty($datauser['manager2'])) {
        mysql_query("UPDATE `users` SET `club` = 'team' WHERE `id` = '$user_id'");
        header('Location: /fm/index.php');
        exit;
    } else {
        // Форма создания команды для новых пользователей
        echo '<div class="phdr"><center><b>Выберите уникальное название Вашей команды</b></center></div>';
        echo '<div style="max-width:280px; margin:0px auto; padding-top:20px;">';
        echo '<div class="gmenu"><div class="row-input"><center><form action="/addteam.php" method="post">';
        echo '<b></b>Название команды:<br/><input type="text" name="nameteam" value=""/><br/>';
        echo '<b>Страна команды:</b><br/>
        <select name="flag">
        <option value="ru">Россия</option>
        <option value="ua">Украина</option>
        <option value="en">Англия</option>
        <option value="it">Италия</option>
        <option value="sp">Испания</option>
        <option value="ge">Германия</option>
        <option value="fr">Франция</option>
        </select><br/>';
        echo '<b>Выбор мира:</b><br/>
        <select name="mir">
        <option value="1">1 мир</option>
        <option value="2">2 мир</option>
        <option value="3">3 мир</option>
        </select><br/>';
        echo '<input type="submit" title="Нажмите чтобы начать игру" name="submit" value="Создать"/></form></center>';
        echo '</div></div></div>';
        
        echo '<div class="cardview">
            <div class="x-row">
                <div class="x-col-1 x-vh-center x-font-250 x-color-white x-bg-green">
                    <i class="font-icon font-icon-whistle"></i>
                </div>
                <div class="x-col-5 x-p-3">
                    <div>Название может содержать либо только символы русского и украинского алфавитов, или только английского длиной не более 20 символов!</div>
                </div>
            </div>
        </div>';
        
        echo '<a href="/auction2.php"><div class="x-my-3 x-text-center x-font-bold"><span class="x-bg-orange x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Аукцион!!!</span></div></a>';
        echo '<a href="/store_teams.php"><div class="x-my-3 x-text-center x-font-bold"><span class="x-bg-green x-px-3 x-py-2 x-font-75 x-color-white x-rounded-3"><i class="fi fi-user"></i> Магазин!!!</span></div></a>';
    }
}
/*
<div class="team-switcher">
    <a href="?mod=team" class="<?= $datauser['club'] == 'team' ? 'active' : '' ?>">Клуб</a>
    <a href="?mod=mtj" class="<?= $datauser['club'] == 'mtj' ? 'active' : '' ?>">Сборная</a>
</div>
*/
require_once('../incfiles/end.php');
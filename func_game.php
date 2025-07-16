<?php
//defined('_IN_JOHNCMS') or die('Error: restricted access');
error_reporting(E_ALL);
ini_set('display_errors', 1);
function func_text($type, $kom, $minuta, $name) {
    global $arr1, $arr2;

    // Определяем команды и игроков
    if ($kom == 1) {
        $jid1 = $arr1['id'];
        $jid2 = $arr2['id'];
        $komm1 = $arr1['name'];
        $komm2 = $arr2['name'];
        $kid1 = $arr1;
        $kid2 = $arr2;
    } else {
        $jid1 = $arr2['id'];
        $jid2 = $arr1['id'];
        $komm1 = $arr2['name'];
        $komm2 = $arr1['name'];
        $kid1 = $arr2;
        $kid2 = $arr1;
    }

    // Стили для вывода
    $styles = '<style>
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
    
    echo $styles;

    // Обработка различных типов событий
    switch ($type) {
        case 'twist_one':
            $messages = [
                '01|twist_one|Первый тайм начинается.|twist_one_1',
                '01|twist_one|Только-только стартовал матч.|twist_one_2',
                '01|twist_one|Сегодняшний матч только что начался.|twist_one_3',
                '01|twist_one|Первая половина встречи началась.|twist_one_4',
                '01|twist_one|Судья дает свисток, и мы начинаем.|twist_one_5'
            ];
            $str = $messages[array_rand($messages)];
            break;

        case 'twist_two':
            $messages = [
                '46|twist_two|Судья своим свистком открывает второй тайм встречи.|twist_two_1',
                '46|twist_two|Перерыв закончен, и вторая половина игры вот-вот начнется!|twist_two_2',
                '46|twist_two|Игра возобновляется.|twist_two_3',
                '46|twist_two|Мы начинаем второй тайм.|twist_two_4',
                '46|twist_two|Судья дает сигнал к началу второго тайма.|twist_two_5'
            ];
            $str = $messages[array_rand($messages)];
            break;

        case 'twist_three':
            $str = '91|twist_three|Первый тайм дополнительного времени начинается.|twist_three_1';
            break;

        case 'twist_four':
            $str = '106|twist_four|Судья своим свистком открывает последний тайм этой встречи.|twist_four_1';
            break;

        case 'fiks':
            $str = '94|fiks| ';
            break;

        case 'fiks_two':
            $str = '122|fiks_two| ';
            break;

        case 'finish_one':
            $messages = [
                '45.0|finish_one|Судья свистит, и команды идут в раздевалки. Закончился первый тайм.|finish_one_1',
                '45.0|finish_one|Вот и все к перерыву. Игроки направляются в подтрибунное помещение.|finish_one_2',
                '45.0|finish_one|Больше ничего не случится в первой половине встречи.|finish_one_3',
                '45.0|finish_one|Судья дал сигнал к концу первого тайма.|finish_one_4',
                '45.0|finish_one|Конец первого тайма.|finish_one_5',
                '45.0|finish_one|Перерыв.|finish_one_6'
            ];
            $str = $messages[array_rand($messages)];
            break;

        case 'finish_two':
            $messages = [
                '92|finish_two|Рефери дает свисток, означающий конец сегодняшнего матча.|finish_two_1',
                '93|finish_two|Конец игры.|finish_two_2',
                '93|finish_two|Судья поглядел на часы и решил закончить матч.|finish_two_3',
                '93|finish_two|Матч только что завершился.|finish_two_4',
                '93|finish_two|Рефери сверяется с часами и свистит, показывая, что матч закончен.|finish_two_5',
                '93|finish_two|Арбитр свистит, показывая, что матч подошел к концу.|finish_two_6',
                '93|finish_two|Вот и все на сегодня. Игра окончена.|finish_two_7',
                '93|finish_two|Арбитр свистит, показывая, что матч подошел к концу.|finish_two_8',
                '93|finish_two|Игра закончена, судья дал свисток к концу матча.|finish_two_9'
            ];
            $str = $messages[array_rand($messages)];
            break;

        case 'finish_three':
            $str = '105.0|finish_three|Судья свистит, и команды идут в раздевалки. Закончился первый тайм дополнительного времени.|finish_three_1';
            break;

        case 'finish_four':
            $str = '122|finish_four|Рефери дает свисток, означающий конец сегодняшнего матча.|finish_four_1';
            break;

        case 'goal1':
        case 'goal2':
            // Обработка голов
            $is_home_team = ($type == 'goal1');
            $team = $is_home_team ? $arr1 : $arr2;
            $opponent_team = $is_home_team ? $arr2 : $arr1;
            
            // Выбор игрока, который сделал передачу
            $shans = rand(1, 100);
            $line_condition = '';
            if ($shans > 25) {
                $line_condition = "AND `line`='3'";
            } elseif ($shans < 25) {
                $line_condition = "AND `line`='4'";
            } else {
                $line_condition = "AND `line`='2'";
            }
            
            $player_ids = implode("','", array_filter([
                $team['i1'], $team['i2'], $team['i3'], $team['i4'], $team['i5'],
                $team['i6'], $team['i7'], $team['i8'], $team['i9'], $team['i10'], $team['i11']
            ]));
            
            $query = "SELECT * FROM `r_player` WHERE `id` IN ('$player_ids') $line_condition AND `name`!='$name' ORDER BY RAND() LIMIT 1";
            $z = mysql_query($query);
            $np = mysql_fetch_array($z);
            
            // Выбор вратаря соперника
            $opponent_ids = implode("','", array_filter([
                $opponent_team['i1'], $opponent_team['i2'], $opponent_team['i3'], $opponent_team['i4'], $opponent_team['i5'],
                $opponent_team['i6'], $opponent_team['i7'], $opponent_team['i8'], $opponent_team['i9'], $opponent_team['i10'], $opponent_team['i11']
            ]));
            
            $query = "SELECT * FROM `r_player` WHERE `id` IN ('$opponent_ids') AND `line`='1' ORDER BY RAND() LIMIT 1";
            $zz = mysql_query($query);
            $gk = mysql_fetch_array($zz);
            
            $metr = rand(8, 25);
            $messages = [
                "$minuta|$type|<b style='color:#304F9E'>И мяч в сетке ворот! {$np['name']} показал классную технику и отлично навесил в штрафную, где $name ($komm1) смог забить отличный гол. Он нанес удар точно в правый угол ворот.</b>",
                "$minuta|$type|<b style='color:#304F9E'>$name заработал пенальти. $name точно пробил в нижний угол ворот с пенальти. Вратарь никак не мог спасти тут.</b>",
                "$minuta|$type|<b style='color:#304F9E'>$name ($komm1) с пенальти точно пробил в левый от себя угол.</b>",
                "$minuta|$type|<b style='color:#304F9E'>Отлично выполненный штрафной удар! {$np['name']} исполнил стандарт, и $name ($komm1) на углу вратарской сыграл головой. Точный удар прямо в нижний угол ворот.</b>",
                "$minuta|$type|<b style='color:#304F9E'>$name ($komm1) не оставляет шансов вратарю резким ударом в правый угол.</b>",
                "$minuta|$type|<b style='color:#304F9E'>ГОЛ $name! $name ($komm1) получил передачу, которую сделал {$np['name']} и нанес идеальный удар по воротам.</b>",
                "$minuta|$type|<b style='color:#304F9E'>Мяч в сетке! {$np['name']} делает хороший пас, а $name ($komm1) был в идеальной позиции, чтобы отправить мяч в сетку.</b>",
                "$minuta|$type|<b style='color:#304F9E'>{$np['name']} делает идеальный пас, мяч хорошо обрабатывает $name ($komm1). Он наносит точный удар в девятку.</b>",
                "$minuta|$type|<b style='color:#304F9E'>ГОЛ! Из пределов штрафной $name ($komm1) бьет с отскока в нижний правый угол ворот.</b>",
                "$minuta|$type|<b style='color:#304F9E'>ГОЛ! {$np['name']} показывает отличное видение поля и отдает пас, мяч принимает $name ($komm1) и наносит точный удар по воротам.</b>",
                "$minuta|$type|<b style='color:#304F9E'>ГОЛ! $name ($komm1) получил отскок во вратарской и нанес удар головой точно под перекладину.</b>",
                "$minuta|$type|<b style='color:#304F9E'>ГОЛ! $name ($komm1) получил отличный разрезающий пас в штрафную и забил! Точным ударом низом по центру ворот.</b>",
                "$minuta|$type|<b style='color:#304F9E'>Гол! {$np['name']} на навесе с углового освободился от опеки и сделал скидку партнеру. Это был $name ($komm1) и он классно завершил.</b>",
                "$minuta|$type|<b style='color:#304F9E'>ГОЛ! Аут, который подал {$np['name']} завершился взятием ворот. $name ($komm1) точным ударом головой положил мяч в сетку.</b>",
                "$minuta|$type|<b style='color:#304F9E'>ГОЛ! $name принял мяч ($komm1), затем он нанес изумительный удар точно в нижний угол ворот.</b>",
                "$minuta|$type|<b style='color:#304F9E'>{$np['name']} исполняет угловой, а $name ($komm1) играет лучше всех головой в штрафной площади и забивает гол.</b>",
                "$minuta|$type|<b style='color:#304F9E'>ГОЛ! {$np['name']} сделал голевой пас сейчас, и $name ($komm1) с угла штрафной нанес удар в левый нижний угол ворот.</b>",
                "$minuta|$type|<b style='color:#304F9E'>ГОЛ! {$np['name']} сделал голевой пас сейчас, и $name ($komm1) с угла штрафной нанес удар в левый угол ворот.</b>",
                "$minuta|$type|<b style='color:#304F9E'>ГОЛ! {$np['name']} делает голевую передачу, $name ($komm1) легко кивнул мяч в пустые ворота после такого классного навеса.</b>",
                "$minuta|$type|<b style='color:#304F9E'>ГОЛ! $name ($komm1) добил с близкого расстояния после прилетевшего к нему отскока.</b>",
                "$minuta|$type|<b style='color:#304F9E'>ГОЛ! {$np['name']} делает отличный пас, а $name ($komm1) забивает в пустые ворота.</b>",
                "$minuta|$type|<b style='color:#304F9E'>$name ($komm1) после классного сольного прохода наносит удар по воротам с $metr метров. Вратарь оказался бессилен, мяч в сетке.</b>",
                "$minuta|$type|<b style='color:#304F9E'>$name ($komm1) отличный проход совершил и нанес удар в нижний левый угол ворот, вратарь был бессилен.</b>"
            ];
            $str = $messages[array_rand($messages)];
            
            // Обновление статистики
            mysql_query("UPDATE `r_player` SET `pas_goal`=`pas_goal`+1 WHERE `id`='{$np['id']}' LIMIT 1");
            mysql_query("UPDATE `r_player` SET `miss_goal`=`miss_goal`+1 WHERE `id`='{$gk['id']}' LIMIT 1");
            break;

        case 'crest':
            $messages = [
                "$minuta|crest|$name нуждается в помощи врачей команды.",
                "$minuta|crest|$name столкнулся головой со своим партнёром в центре поля, жуткая картина.",
                "$minuta|crest|Пауза в игре. $name получает помощь - ему свело ногу. Не готов игрок играть ещё весь матч.",
                "$minuta|crest|Практически каждую минуту фиксируются фолы, которые ещё более тормозят, и без того, медленный темп игры. $name сейчас пришлось даже прибегать к помощи врачей.."
            ];
            $str = $messages[array_rand($messages)];
            break;

        case 'play':
            // Выбор случайных игроков из обеих команд
            $team1_player_ids = implode("','", array_filter([
                $kid1['i1'], $kid1['i2'], $kid1['i3'], $kid1['i4'], $kid1['i5'],
                $kid1['i6'], $kid1['i7'], $kid1['i8'], $kid1['i9'], $kid1['i10'], $kid1['i11']
            ]));
            
            $team2_player_ids = implode("','", array_filter([
                $kid2['i1'], $kid2['i2'], $kid2['i3'], $kid2['i4'], $kid2['i5'],
                $kid2['i6'], $kid2['i7'], $kid2['i8'], $kid2['i9'], $kid2['i10'], $kid2['i11']
            ]));
            
            // Игроки команды 1
            $z1 = mysql_fetch_array(mysql_query("SELECT * FROM `r_player` WHERE `id` IN ('$team1_player_ids') AND `line`='2' ORDER BY RAND() LIMIT 1"));
            $p1 = mysql_fetch_array(mysql_query("SELECT * FROM `r_player` WHERE `id` IN ('$team1_player_ids') AND `line`='3' ORDER BY RAND() LIMIT 1"));
            $f1 = mysql_fetch_array(mysql_query("SELECT * FROM `r_player` WHERE `id` IN ('$team1_player_ids') AND `line`='4' ORDER BY RAND() LIMIT 1"));
            
            // Игроки команды 2
            $z2 = mysql_fetch_array(mysql_query("SELECT * FROM `r_player` WHERE `id` IN ('$team2_player_ids') AND `line`='2' ORDER BY RAND() LIMIT 1"));
            $p2 = mysql_fetch_array(mysql_query("SELECT * FROM `r_player` WHERE `id` IN ('$team2_player_ids') AND `line`='3' ORDER BY RAND() LIMIT 1"));
            $g2 = mysql_fetch_array(mysql_query("SELECT * FROM `r_player` WHERE `id` IN ('$team2_player_ids') AND `line`='1' ORDER BY RAND() LIMIT 1"));
            
            $messages = [
                "$minuta|fol|{$z2['name']} ($komm2) свалил соперника в подкате. $komm1 получает право на штрафной. Отличная точка для прямого удара.\r\n" . ($minuta<8?'0':'').($minuta+1)."|warning|На этот раз неудачно пробил штрафной удар {$p1['name']} ($komm1). Мяч попал в стенку.|play_for_1",
                "$minuta|good|$komm1 владеет мячом, но без острых атак, просто перепасовка.|play_for_2|$jid1",
                "$minuta|ugol|{$z1['name']} ($komm1) исполняет угловой. Но оборона соперника уверенно отводит угрозу от ворот.|ugol_3|{$z1['id']}|$jid1",
                "$minuta|fol|Игра прервана. {$z2['name']} ($komm2) нарушил правила в борьбе за мяч. Он даже не протестует против свистка, все верно заметил судья. {$arr1['name']} бьет штрафной удар.|play_for_4|{$z2['id']}|{$kid2['id']}",
                "$minuta|warning|{$f1['name']} ($komm1) попытался обострить, но мяч был выбит.|play_for_5|{$f1['id']}|$jid1",
                "$minuta|warning|{$p1['name']} ($komm1) исполнил удар близко к линии штрафной площади. Он решает навесить, но голкипер играет безупречно на выходе.|play_for_6|{$p1['id']}|$jid1",
                // ... остальные сообщения
                "$minuta|fol|Опасная игра, это был {$z2['name']} ($komm2). Судья свистнул.|play_for_59"
            ];
            $str = $messages[array_rand($messages)];
            break;

        case 'yellow':
            $messages = [
                "$minuta|yellow|$name получает предупреждение, рукой сыграл игрок, а потом ещё начал спорить с арбитром матча.",
                "$minuta|yellow|$name откровенно опасно играл в подкате против опонента. По ноге не досталось, но за намерение грубо сфолить получил карточку игрок.",
                "$minuta|yellow|Жёлтую карточку получил $name. Потерял мяч, в итоге пришлось нарушить правила, сорвав атаку соперника.",
                "$minuta|yellow|$name сорвал опасную атаку за что и получил жёлтую карточку.",
                "$minuta|yellow|Судья достает из широких штанин... Э-э-э, я имею ввиду желтую карточку. Карточку получил $name."
            ];
            $str = $messages[array_rand($messages)];
            break;

        case 'red1':
        case 'red2':
            $is_home_team = ($type == 'red1');
            $team = $is_home_team ? $arr1 : $arr2;
            
            $messages = [
                "$minuta|$type|Заслуженное удаление. Подкат был жесткий, и судья наказал заслуженно, $name ($komm1).|$type|$name",
                "$minuta|$type|Судья достает из широких штанин... Э-э-э, я имею ввиду красную карточку. Карточку получил $name."
            ];
            $str = $messages[array_rand($messages)];
            
            // Обновление статуса игрока
            $player_ids = implode("','", array_filter([
                $team['i1'], $team['i2'], $team['i3'], $team['i4'], $team['i5'],
                $team['i6'], $team['i7'], $team['i8'], $team['i9'], $team['i10'], $team['i11']
            ]));
            
            $query = "SELECT * FROM `r_player` WHERE `id` IN ('$player_ids') AND `sostav`='1' AND `line`!='1' ORDER BY RAND() LIMIT 1";
            $player = mysql_fetch_array(mysql_query($query));
            
            if ($player) {
                mysql_query("UPDATE `r_player` SET `rc`=`rc`+1, `utime`='2', `sostav`='4' WHERE `id`='{$player['id']}' LIMIT 1");
                
                // Удаление игрока из состава команды
                for ($i = 1; $i <= 11; $i++) {
                    mysql_query("UPDATE `r_team` SET `i$i`='' WHERE `i$i`='{$team['id']}'");
                }
            }
            break;

        default:
            $str = '';
            break;
    }

    return $str;
}

<?php
//defined('_IN_JOHNCMS') or die('Error: restricted access');

function func_text($type,$kom,$minuta,$name)
{
    global $arr1, $arr2;

    // Преобразование типов событий
    if ($type === 'goal') {
        $type = ($kom == 1) ? 'goal1' : 'goal2';
    } elseif ($type === 'yellow_card') {
        $type = 'yellow';
    } elseif ($type === 'red_card') {
        $type = ($kom == 1) ? 'red1' : 'red2';
    }

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



echo'<style>
   
        /* текстовая трансляция */
		
		/*
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
	
	
	
if ($type == 'twist_one')
{

$i_twist_one = rand(1,5);
switch ($i_twist_one)
{
case "1":$str = '01|twist_one|Первый тайм начинается.|twist_one_1';break;
case "2":$str = '01|twist_one|Только-только стартовал матч.|twist_one_2';break;
case "3":$str = '01|twist_one|Сегодняшний матч только что начался.|twist_one_3';break;
case "4":$str = '01|twist_one|Первая половина встречи началась.|twist_one_4';break;
case "5":$str = '01|twist_one|Судья дает свисток, и мы начинаем.|twist_one_5';break;

}

}
elseif ($type == 'twist_two')
{

$i_twist_two = rand(1,5);
switch ($i_twist_two)
{
case "1":$str = '46|twist_two|Судья своим свистком открывает второй тайм встречи.|twist_two_1';break;
case "2":$str = '46|twist_two|Перерыв закончен, и вторая половина игры вот-вот начнется!|twist_two_2';break;
case "3":$str = '46|twist_two|Игра возобновляется.|twist_two_3';break;
case "4":$str = '46|twist_two|Мы начинаем второй тайм.|twist_two_4';break;
case "5":$str = '46|twist_two|Судья дает сигнал к началу второго тайма.|twist_two_5';break;
}
}
elseif ($type == 'twist_three')
{

$i_twist_three = rand(1,1);
switch ($i_twist_three)
{
case "1":$str = '91|twist_three|Первый тайм дополнительного времени начинается.|twist_three_1';break;


}

}
elseif ($type == 'twist_four')
{

$i_twist_four = rand(1,1);
switch ($i_twist_four)
{
case "1":$str = '106|twist_four|Судья своим свистком открывает последний тайм этой встречи.|twist_four_1';break;

}
}
elseif ($type == 'fiks')
{

$i_fiks = rand(1,1);
switch ($i_fiks)
{
case "1":$str = '94|fiks| ';break;
}
}
elseif ($type == 'fiks_two')
{

$i_fiks_two = rand(1,1);
switch ($i_fiks_two)
{
case "1":$str = '122|fiks_two| ';break;
}
}


elseif ($type == 'finish_one')
{

$i_finish_one = rand(1,6);
switch ($i_finish_one)
{
case "1":$str = '45.0|finish_one|Судья свистит, и команды идут в раздевалки. Закончился первый тайм.|finish_one_1';break;
case "2":$str = '45.0|finish_one|Вот и все к перерыву. Игроки направляются в подтрибунное помещение.|finish_one_2';break;
case "3":$str = '45.0|finish_one|Больше ничего не случится в первой половине встречи.|finish_one_3';break;
case "4":$str = '45.0|finish_one|Судья дал сигнал к концу первого тайма.|finish_one_4';break;
case "5":$str = '45.0|finish_one|Конец первого тайма.|finish_one_5';break;
case "6":$str = '45.0|finish_one|Перерыв.|finish_one_6';break;
}

}
elseif ($type == 'finish_two')
{

$i_finish_two = rand(1,9);
switch ($i_finish_two)
{
case "1":$str = '92|finish_two|Рефери дает свисток, означающий конец сегодняшнего матча.|finish_two_1';break;
case "2":$str = '93|finish_two|Конец игры.|finish_two_2';break;
case "3":$str = '93|finish_two|Судья поглядел на часы и решил закончить матч.|finish_two_3';break;
case "4":$str = '93|finish_two|Матч только что завершился.|finish_two_4';break;
case "5":$str = '93|finish_two|Рефери сверяется с часами и свистит, показывая, что матч закончен.|finish_two_5';break;
case "6":$str = '93|finish_two|Арбитр свистит, показывая, что матч подошел к концу.|finish_two_6';break;
case "7":$str = '93|finish_two|Вот и все на сегодня. Игра окончена.|finish_two_7';break;
case "8":$str = '93|finish_two|Арбитр свистит, показывая, что матч подошел к концу.|finish_two_8';break;
case "9":$str = '93|finish_two|Игра закончена, судья дал свисток к концу матча.|finish_two_9';break;
}

}
elseif ($type == 'finish_three')
{

$i_finish_three = rand(1,1);
switch ($i_finish_three)
{
case "1":$str = '105.0|finish_three|Судья свистит, и команды идут в раздевалки. Закончился первый тайм дополнительного времени.|finish_three_1';break;

}

}
elseif ($type == 'finish_four')
{

$i_finish_four = rand(1,1);
switch ($i_finish_four)
{
case "1":$str = '122|finish_four|Рефери дает свисток, означающий конец сегодняшнего матча.|finish_four_1';break;

}

}
elseif ($type == 'goal1') ////Голы
{
	$shans=rand(1,100);
	if($shans > 25){
$z = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='3' AND `name`!='".$name."' ORDER BY RAND() ");
	}
	elseif($shans < 25){
	$z = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='4' AND `name`!='".$name."' ORDER BY RAND() ");
	
	}
		
		else{
		$z = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='2' AND `name`!='".$name."' ORDER BY RAND() ");

		
	}

$o = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `name`='".$name."' ORDER BY RAND() ");
$oi = mysql_fetch_array($o);


$o11 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."')  AND `role`='pen'  limit 1 ");
$oi11 = mysql_fetch_array($o11);



$np = mysql_fetch_array($z);

$zz = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='1' ORDER BY RAND() ");
$gk = mysql_fetch_array($zz);
$metr = rand(8,25);
$i_goal = rand(1,23);
switch ($i_goal)
{	



case "1":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">И мяч в сетке ворот! '.$np['name'].' показал классную технику и отлично навесил в штрафную, где '.$name.' ('.$komm1.') смог забить отличный гол. Он нанес удар точно в правый угол ворот.</b>';break;

case "2":
// if($oi11[role]=='pen'){
// $str = ''.$minuta.'|goal1_pen|<b style="color:#304F9E">'.$name.' заработал пенальти. '.$oi11['name'].' точно пробил в нижний угол ворот с пенальти. Вратарь никак не мог спасти тут.</b>';break;
 // mysql_query("UPDATE `r_player` SET  `goal`=".($oi11['goal']+1)." WHERE `id`='" . $oi11['id'] . "' LIMIT 1;");	

// }
// else{
$str = ''.$minuta.'|goal1|<b style="color:#304F9E">'.$name.' заработал пенальти. '.$name.' точно пробил в нижний угол ворот с пенальти. Вратарь никак не мог спасти тут.</b>';break;	
// }
case "3":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">'.$name.' ('.$komm1.') с пенальти точно пробил в левый от себя угол.</b>';break;
case "4":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">Отлично выполненный штрафной удар! '.$np['name'].' исполнил стандарт, и '.$name.' ('.$komm1.') на углу вратарской сыграл головой. Точный удар прямо в нижний угол ворот.</b>';break;
case "5":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">'.$name.' ('.$komm1.') не оставляет шансов вратарю резким ударом в правый угол.</b>';break;
case "6":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">ГОЛ '.$name.'! '.$name.' ('.$komm1.') получил передачу, которую сделал '.$np['name'].' и нанес идеальный удар по воротам.</b>';break;
case "7":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">Мяч в сетке! '.$np['name'].' делает хороший пас, а '.$name.' ('.$komm1.') был в идеальной позиции, чтобы отправить мяч в сетку.</b>';break;
case "8":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">'.$np['name'].' делает идеальный пас, мяч хорошо обрабатывает '.$name.' ('.$komm1.'). Он наносит точный удар в девятку.</b>';break;
case "9":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">ГОЛ! Из пределов штрафной '.$name.' ('.$komm1.') бьет с отскока в нижний правый угол ворот.</b>';break;
case "10":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">ГОЛ! '.$np['name'].' показывает отличное видение поля и отдает пас, мяч принимает '.$name.' ('.$komm1.') и наносит точный удар по воротам.</b>';break;
case "11":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">ГОЛ! '.$name.' ('.$komm1.') получил отскок во вратарской и нанес удар головой точно под перекладину.</b>';break;
case "12":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">ГОЛ! '.$name.' ('.$komm1.') получил отличный разрезающий пас в штрафную и забил! Точным ударом низом по центру ворот.</b>';break;
case "13":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">Гол! '.$np['name'].' на навесе с углового освободился от опеки и сделал скидку партнеру. Это был '.$name.' ('.$komm1.') и он классно завершил.</b>';break;
case "14":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">ГОЛ! Аут, который подал '.$np['name'].' завершился взятием ворот. '.$name.' ('.$komm1.') точным ударом головой положил мяч в сетку.</b>';break;
case "15":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">ГОЛ! '.$name.' принял мяч ('.$komm1.'), затем он нанес изумительный удар точно в нижний угол ворот.</b>';break;
case "16":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">'.$np['name'].' исполняет угловой, а '.$name.' ('.$komm1.') играет лучше всех головой в штрафной площади и забивает гол.</b>';break;
case "17":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">ГОЛ! '.$np['name'].' сделал голевой пас сейчас, и '.$name.' ('.$komm1.') с угла штрафной нанес удар в левый нижний угол ворот.</b>';break;
case "18":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">ГОЛ! '.$np['name'].' сделал голевой пас сейчас, и '.$name.' ('.$komm1.') с угла штрафной нанес удар в левый угол ворот.</b>';break;
case "19":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">ГОЛ! '.$np['name'].' делает голевую передачу, '.$name.' ('.$komm1.') легко кивнул мяч в пустые ворота после такого классного навеса.</b>';break;
case "20":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">ГОЛ! '.$name.' ('.$komm1.') добил с близкого расстояния после прилетевшего к нему отскока.</b>';break;
case "21":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">ГОЛ! '.$np['name'].' делает отличный пас, а '.$name.' ('.$komm1.') забивает в пустые ворота.</b>';break;
case "22":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">'.$name.' ('.$komm1.') после классного сольного прохода наносит удар по воротам с '.$metr.' метров. Вратарь оказался бессилен, мяч в сетке.</b>';break;
case "23":$str = ''.$minuta.'|goal1|<b style="color:#304F9E">'.$name.' ('.$komm1.') отличный проход совершил и нанес удар в нижний левый угол ворот, вратарь был бессилен.</b>';break;
 

}

 mysql_query("UPDATE `r_player` SET  `pas_goal`=".($np['pas_goal']+1)." WHERE `id`='" . $np['id'] . "' LIMIT 1;");	
  mysql_query("UPDATE `r_player` SET  `miss_goal`=".($gk['miss_goal']+1)." WHERE `id`='" . $gk['id'] . "' LIMIT 1;");	

 // mysql_query("insert into `r_goals1` SET  `pas_goal`=".($np['pas_goal']+1)." WHERE `id`='" . $np['id'] . "' LIMIT 1;");	
  // mysql_query("insert into `r_goals1` SET  `miss_goal`=".($gk['miss_goal']+1)." WHERE `id`='" . $gk['id'] . "' LIMIT 1;");	






//  mysql_query("INSERT INTO `r_goals1` (`id_player`, `team_id`, `pas_goal`) VALUES ('" . $np['id'] . "', '" . $np['team'] . "', '".($np['pas_goal']+1)."') ON DUPLICATE KEY UPDATE `pas_goal` = '".($np['pas_goal']+1)."', `id_player`='" . $np['id'] . "', `team_id` = '" . $np['team'] . "' LIMIT 1 ;");
//  mysql_query("INSERT INTO `r_goals1` (`id_player`, `team_id`, `miss_goal`) VALUES ('" . $gk['id'] . "', '" . $gk['team'] . "', '".($gk['miss_goal']+1)."') ON DUPLICATE KEY UPDATE `miss_goal` = '".($gk['miss_goal']+1)."',  `id_player`='" . $gk['id'] . "', `team_id` = '" . $gk['team'] . "' LIMIT 1;");


}
elseif ($type == 'goal2')
{
	$shans=rand(1,100);
	if($shans > 25){
$z = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='3' ORDER BY RAND() ");
	}
	elseif($shans < 25){
	$z = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='4' AND `name`!='".$name."' ORDER BY RAND() ");
	
	}
	else{
		$z = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='2' ORDER BY RAND() ");

		
	}

$o = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `name`='".$name."' ORDER BY RAND() ");
$oi = mysql_fetch_array($o);

$o22 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."')  AND `role`='pen'  limit 1 ");
$oi22 = mysql_fetch_array($o22);

	
	

$np = mysql_fetch_array($z);
$zz = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='1' ORDER BY RAND() ");
$gk = mysql_fetch_array($zz);
$metr = rand(8,25);
$i_goal = rand(1,23);
switch ($i_goal)
{
case "1":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">И мяч в сетке ворот! '.$np['name'].' показал классную технику и отлично навесил в штрафную, где '.$name.' ('.$komm1.') смог забить отличный гол. Он нанес удар точно в правый угол ворот.</b>';break;

case "2":
// if($oi22[role]=='pen'){
// $str = ''.$minuta.'|goal2_pen|<b style="color:#304F9E">'.$name.' заработал пенальти. '.$oi22['name'].' точно пробил в нижний угол ворот с пенальти. Вратарь никак не мог спасти тут.</b>';break;
 // mysql_query("UPDATE `r_player` SET  `goal`=".($oi22['goal']+1)." WHERE `id`='" . $oi22['id'] . "' LIMIT 1;");	

// }
// else{
$str = ''.$minuta.'|goal2|<b style="color:#304F9E">'.$name.' заработал пенальти. '.$name.' точно пробил в нижний угол ворот с пенальти. Вратарь никак не мог спасти тут.</b>';break;	
// }
case "3":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">'.$np['name'].' заработал пенальти. '.$name.' ('.$komm1.') с пенальти точно пробил в левый от себя угол.</b>';break;
case "4":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">Отлично выполненный штрафной удар! '.$np['name'].' исполнил стандарт, и '.$name.' ('.$komm1.') на углу вратарской сыграл головой. Точный удар прямо в нижний угол ворот.</b>';break;
case "5":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">'.$np['name'].' делает отличный пас в штрафную противника, и '.$name.' ('.$komm1.') не оставляет шансов вратарю резким ударом в правый угол.</b>';break;
case "6":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">ГОЛ '.$name.'! '.$name.' ('.$komm1.') получил передачу, которую сделал '.$np['name'].' и нанес идеальный удар по воротам.</b>';break;
case "7":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">Мяч в сетке! '.$np['name'].' делает хороший пас, а '.$name.' ('.$komm1.') был в идеальной позиции, чтобы отправить мяч в сетку.</b>';break;
case "8":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">'.$np['name'].' делает идеальный пас, мяч хорошо обрабатывает '.$name.' ('.$komm1.'). Он наносит точный удар в девятку.</b>';break;
case "9":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">ГОЛ! Из пределов штрафной '.$name.' ('.$komm1.') бьет с отскока в нижний правый угол ворот. '.$np['name'].' сделал голевой пас.</b>';break;
case "10":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">ГОЛ! '.$np['name'].' показывает отличное видение поля и отдает пас, мяч принимает '.$name.' ('.$komm1.') и наносит точный удар по воротам.</b>';break;
case "11":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">ГОЛ! '.$name.' ('.$komm1.') получил отскок во вратарской и нанес удар головой точно под перекладину.</b>';break;
case "12":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">ГОЛ! '.$name.' ('.$komm1.') получил отличный разрезающий пас в штрафную и забил! Точным ударом низом по центру ворот.</b>';break;
case "13":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">Гол! '.$np['name'].' на навесе с углового освободился от опеки и сделал скидку партнеру. Это был '.$name.' ('.$komm1.') и он классно завершил.</b>';break;
case "14":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">ГОЛ! Аут, который подал '.$np['name'].' завершился взятием ворот. '.$name.' ('.$komm1.') точным ударом головой положил мяч в сетку.</b>';break;
case "15":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">ГОЛ! '.$np['name'].' делает отличный пас прямо в ноги, мяч принял '.$name.' ('.$komm1.'), затем он нанес изумительный удар точно в нижний угол ворот.</b>';break;
case "16":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">'.$np['name'].' исполняет угловой, а '.$name.' ('.$komm1.') играет лучше всех головой в штрафной площади и забивает гол.</b>';break;
case "17":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">ГОЛ! '.$np['name'].' сделал голевой пас сейчас, и '.$name.' ('.$komm1.') с угла штрафной нанес удар в левый нижний угол ворот.</b>';break;
case "18":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">ГОЛ! '.$np['name'].' сделал голевой пас сейчас, и '.$name.' ('.$komm1.') с угла штрафной нанес удар в левый угол ворот.</b>';break;
case "19":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">ГОЛ! '.$np['name'].' делает голевую передачу, '.$name.' ('.$komm1.') легко кивнул мяч в пустые ворота после такого классного навеса.</b>';break;
case "20":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">ГОЛ! '.$name.' ('.$komm1.') добил с близкого расстояния после прилетевшего к нему отскока.</b>';break;
case "21":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">ГОЛ! '.$np['name'].' делает отличный пас, а '.$name.' ('.$komm1.') забивает в пустые ворота.</b>';break;
case "22":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">'.$name.' ('.$komm1.') после классного сольного прохода наносит удар по воротам с '.$metr.' метров. Вратарь оказался бессилен, мяч в сетке.</b>';break;
case "23":$str = ''.$minuta.'|goal2|<b style="color:#304F9E">'.$name.' ('.$komm1.') отличный проход совершил и нанес удар в нижний левый угол ворот, вратарь был бессилен.</b>';break;
//case "23":$str = ''.$minuta.'|goal2|<b>';break;
 
} 
 mysql_query("UPDATE `r_player` SET  `pas_goal`=".($np['pas_goal']+1)." WHERE `id`='" . $np['id'] . "' LIMIT 1;");	
  mysql_query("UPDATE `r_player` SET  `miss_goal`=".($gk['miss_goal']+1)." WHERE `id`='" . $gk['id'] . "' LIMIT 1;");
 // mysql_query("insert into `r_goals1` SET  `pas_goal`=".($np['pas_goal']+1)." WHERE `id`='" . $np['id'] . "' LIMIT 1;");	
  // mysql_query("insert into `r_goals1` SET  `miss_goal`=".($gk['miss_goal']+1)." WHERE `id`='" . $gk['id'] . "' LIMIT 1;");	




// mysql_query("INSERT INTO `r_goals1` (`id_player`, `team_id`, `pas_goal`) VALUES ('" . $np['id'] . "', '" . $np['team'] . "', '".($np['pas_goal']+1)."') ON DUPLICATE KEY UPDATE `pas_goal` = '".($np['pas_goal']+1)."', `id_player`='" . $np['id'] . "', `team_id` = '" . $np['team'] . "'  LIMIT 1 ;");
// mysql_query("INSERT INTO `r_goals1` (`id_player`, `team_id`, `miss_goal`) VALUES ('" . $gk['id'] . "', '" . $gk['team'] . "', '".($gk['miss_goal']+1)."') ON DUPLICATE KEY UPDATE `miss_goal` = '".($gk['miss_goal']+1)."',  `id_player`='" . $gk['id'] . "', `team_id` = '" . $gk['team'] . "'  LIMIT 1;");

}
elseif ($type == 'crest') //// травмы
{

$i_crest = rand(1,4);
switch ($i_crest)
{
case "1":$str = ''.$minuta.'|crest|'.$name.' нуждается в помощи врачей команды.';break;
case "2":$str = ''.$minuta.'|crest|'.$name.' столкнулся головой со своим партнёром в центре поля, жуткая картина.';break;
case "3":$str = ''.$minuta.'|crest|Пауза в игре. '.$name.' получает помощь - ему свело ногу. Не готов игрок играть ещё весь матч.';break;
case "4":$str = ''.$minuta.'|crest|Практически каждую минуту фиксируются фолы, которые ещё более тормозят, и без того, медленный темп игры. '.$name.' сейчас пришлось даже прибегать к помощи врачей..';break;

}





}
elseif ($type == 'play')
{

$z = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$kid1['i1']."' or `id`='".$kid1['i2']."' or `id`='".$kid1['i3']."' or `id`='".$kid1['i4']."' or `id`='".$kid1['i5']."' or `id`='".$kid1['i6']."' or `id`='".$kid1['i7']."' or `id`='".$kid1['i8']."' or `id`='".$kid1['i9']."' or `id`='".$kid1['i10']."' or `id`='".$kid1['i11']."') AND `line`='2' ORDER BY RAND() ");
$z1 = mysql_fetch_array($z);
$p = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$kid1['i1']."' or `id`='".$kid1['i2']."' or `id`='".$kid1['i3']."' or `id`='".$kid1['i4']."' or `id`='".$kid1['i5']."' or `id`='".$kid1['i6']."' or `id`='".$kid1['i7']."' or `id`='".$kid1['i8']."' or `id`='".$kid1['i9']."' or `id`='".$kid1['i10']."' or `id`='".$kid1['i11']."') AND `line`='3' ORDER BY RAND() ");
$p1 = mysql_fetch_array($p);
$f = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$kid1['i1']."' or `id`='".$kid1['i2']."' or `id`='".$kid1['i3']."' or `id`='".$kid1['i4']."' or `id`='".$kid1['i5']."' or `id`='".$kid1['i6']."' or `id`='".$kid1['i7']."' or `id`='".$kid1['i8']."' or `id`='".$kid1['i9']."' or `id`='".$kid1['i10']."' or `id`='".$kid1['i11']."') AND `line`='4' ORDER BY RAND() ");
$f1 = mysql_fetch_array($f);

		$zz = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$kid2['i1']."' or `id`='".$kid2['i2']."' or `id`='".$kid2['i3']."' or `id`='".$kid2['i4']."' or `id`='".$kid2['i5']."' or `id`='".$kid2['i6']."' or `id`='".$kid2['i7']."' or `id`='".$kid2['i8']."' or `id`='".$kid2['i9']."' or `id`='".$kid2['i10']."' or `id`='".$kid2['i11']."') AND `line`='2' ORDER BY RAND() ");
$z2 = mysql_fetch_array($zz);
$pp = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$kid2['i1']."' or `id`='".$kid2['i2']."' or `id`='".$kid2['i3']."' or `id`='".$kid2['i4']."' or `id`='".$kid2['i5']."' or `id`='".$kid2['i6']."' or `id`='".$kid2['i7']."' or `id`='".$kid2['i8']."' or `id`='".$kid2['i9']."' or `id`='".$kid2['i10']."' or `id`='".$kid2['i11']."') AND `line`='3' ORDER BY RAND() ");
$p2 = mysql_fetch_array($pp);
$ff = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$kid2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$kid2['i3']."' or `id`='".$kid2['i4']."' or `id`='".$kid2['i5']."' or `id`='".$kid2['i6']."' or `id`='".$kid2['i7']."' or `id`='".$kid2['i8']."' or `id`='".$kid2['i9']."' or `id`='".$kid2['i10']."' or `id`='".$kid2['i11']."') AND `line`='4' ORDER BY RAND() ");
$f2 = mysql_fetch_array($ff);
$gg = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$kid2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$kid2['i3']."' or `id`='".$kid2['i4']."' or `id`='".$kid2['i5']."' or `id`='".$kid2['i6']."' or `id`='".$kid2['i7']."' or `id`='".$kid2['i8']."' or `id`='".$kid2['i9']."' or `id`='".$kid2['i10']."' or `id`='".$kid2['i11']."') AND `line`='1' ORDER BY RAND() ");
$g2 = mysql_fetch_array($gg);
		
$i_play_for = rand(1,59);
switch ($i_play_for) 
{
case "1":$str = ''.$minuta.'|fol|'.$z2['name'].' ('.$kid2[name].') свалил соперника в подкате. '.$kid1[name].' получает право на штрафной. Отличная точка для прямого удара.\r\n'.($minuta<8?'0':NULL).''.($minuta+1).'|warning|На этот раз неудачно пробил штрафной удар '.$p1['name'].' ('.$kid1[name].'). Мяч попал в стенку.|play_for_1';break;
case "2":$str = ''.$minuta.'|good|'.$komm1.' владеет мячом, но без острых атак, просто перепасовка.|play_for_2|'.$jid1.'';break;
case "3":$str = ''.$minuta.'|ugol|'.$z1['name'].' ('.$komm1.') исполняет угловой. Но оборона соперника уверенно отводит угрозу от ворот.|ugol_3|'.$z1['id'].'|'.$jid1.'';break;
case "4":$str = ''.$minuta.'|fol|Игра прервана. '.$z2['name'].' ('.$kid2[name].') нарушил правила в борьбе за мяч. Он даже не протестует против свистка, все верно заметил судья. '.$arr1[name].' бьет штрафной удар.|play_for_4|'.$z2['id'].'|'.$kid2[id].'';break;
case "5":$str = ''.$minuta.'|warning|'.$f1['name'].' ('.$komm1.') попытался обострить, но мяч был выбит.|play_for_5|'.$f1['id'].'|'.$jid1.'';break;
case "6":$str = ''.$minuta.'|warning|'.$p1['name'].' ('.$komm1.') исполнил удар близко к линии штрафной площади. Он решает навесить, но голкипер играет безупречно на выходе.|play_for_6|'.$p1['id'].'|'.$jid1.'';break;
case "7":$str = ''.$minuta.'|warning|'.$p1['name'].' ('.$komm1.') совершил подачу в штрафную, а там '.$f1['name'].' приняв мяч, собирался пробить, но защитники остановили его.|play_for_7|'.$p1['id'].'|'.$jid1.'|'.$f1['id'].'';break;
case "8":$str = ''.$minuta.'|ofs|'.$f1['name'].' ('.$komm1.') прорвался к воротам, но, к сожалению, успел попасть в офсайд. Свисток прозвучал.|play_for_8|'.$f1['id'].'|'.$jid1.'';break;
case "9":$str = ''.$minuta.'|warning|'.$f1['name'].' ('.$komm1.') бежал к воротам, но был остановлен защитником.|play_for_9|'.$f1['id'].'|'.$jid1.'';break;
case "10":$str = ''.$minuta.'|warning|'.$p1['name'].' ('.$komm1.') с края штрафной нанес удар и попал в защитника.|play_for_10|'.$p1['id'].'|'.$jid1.'';break;
case "11":$str = ''.$minuta.'|warning|'.$f1['name'].' ('.$komm1.') нанес удар, получив мяч. Он пробил с края штрафной мимо ворот.|play_for_11|'.$f1['id'].'|'.$jid1.'';break;
case "12":$str = ''.$minuta.'|warning|'.$p1['name'].' ('.$komm1.') высоко навесил в штрафную, но один из защитников головой выбил мяч. Судья сигнализирует, что будет угловой, '.$komm1.' подаст его.\r\n'.($minuta<8?'0':NULL).''.($minuta+1).'|ugol|Угловой подал '.$p1['name'].' ('.$komm1.'), но защитник выбил.|play_for_12';break;
case "13":$str = ''.$minuta.'|warning|Хорошо отзащищались. '.$z1['name'].' ('.$komm1.') делал подачу в штрафную, которую, скорее всего, должен был получить '.$f1['name'].', но мяч выбили.|play_for_13';break;
case "14":$str = ''.$minuta.'|warning|'.$f1['name'].' ('.$komm1.') наносит удар с угла штрафной, но очень плохо делает это - выше.|play_for_14|'.$f1['id'].'|'.$jid1.'';break;
case "15":$str = ''.$minuta.'|ugol|Судья показывает на угловой флажок. '.$komm1.' получает право на угловой. '.$p1['name'].' ('.$komm1.') исполняет дальний пас, но отдает мяч прямо в ноги защитникам соперника.\r\n'.($minuta<8?'0':NULL).''.($minuta+1).'|ugol|Мяч вынесли из штрафной, после подачи углового, которую сделал '.$p1['name'].' ('.$komm1.').|play_for_15';break;
case "16":$str = ''.$minuta.'|warning|'.$p1['name'].' ('.$komm1.') обострял ситуацию сейчас. Он прострелил в штрафную, но защитники сыграли уверенно и выбили мяч. Мяч уходит в аут. '.$komm1.' получает право на угловой.\r\n'.($minuta<8?'0':NULL).''.($minuta+1).'|ugol|'.$p1['name'].' ('.$komm1.') подал угловой, в результате которого защитник выбил мяч головой.|play_for_16';break;
case "17":$str = ''.$minuta.'|fol|Кто-то из футболистов команды '.$komm2.' грубо нарушает правила. Судья держит все под контролем - дал свисток. '.$komm1.' получит право на штрафной удар. Они смогут что-то извлечь из него, наверняка.\r\n'.($minuta<8?'0':NULL).''.($minuta+1).'|warning|'.$p1['name'].' ('.$komm1.') бил штрафной, но попал только в стенку.|play_for_17';break;
case "18":$str = ''.$minuta.'|warning|<b>'.$f1['name'].' ('.$komm1.') принял мяч после прострела в штрафную и вторым касанием классно пробил. '.$g2['name'].' парировал этот опаснейший удар в правый угол ворот.</b>|warning_18';break;
case "19":$str = ''.$minuta.'|ugol|'.$f1['name'].' ('.$komm1.') был на краю штрафной и сделал отличный пас. Один из защитников в последний момент прервал его. Мяч улетел за поле. "Угловой", показывают судьи. '.$komm1.' исполнит его.\r\n'.($minuta<8?'0':NULL).''.($minuta+1).'|ugol|'.$p1['name'].' ('.$komm1.') исполняет угловой. Но оборона соперника уверенно отводит угрозу от ворот. Мяч покидает поле.|play_for_19';break;
// case "20":$str = ''.$minuta.'|yellow|'.$z2['name'].' ('.$komm2.') получает желтую карточку. Судья заметил грубый фол.|yellow_20|'.$z2['id'].'|'.$jid2.'';break;
case "21":$str = ''.$minuta.'|fol|'.$z2['name'].' ('.$komm2.') совершил подкат. Судья замечает, что подкат был не в мяч и свистит, но не предупреждает игрока карточкой.|play_for_21|'.$z2['id'].'|'.$jid2.'';break;
case "22":$str = ''.$minuta.'|warning|'.$p1['name'].' ('.$komm1.') отдал пас. Его получил '.$f1['name'].', но успешно вмешались защитники.|play_for_22|'.$p1['id'].'|'.$jid1.'|'.$f1['id'].'';break;
case "23":$str = ''.$minuta.'|warning|'.$f1['name'].' ('.$komm1.') ворвался в штрафную и откликнулся на подачу. Пробил по воротам, но вратарь на месте.|play_for_23|'.$f1['id'].'|'.$jid1.'';break;
case "24":$str = ''.$minuta.'|ugol|'.$f1['name'].' ('.$komm1.') расстреливал ворота с близкого расстояния, но мяч после его удара головой был выбит защитником. Командой '.$komm1.' будет исполнен угловой.\r\n'.($minuta<8?'0':NULL).''.($minuta+1).'|ugol|'.$p1['name'].' ('.$komm1.') исполняет угловой. Защита выбивает мяч подальше от ворот.|play_for_24';break;
case "25":$str = ''.$minuta.'|good|'.$komm1.' контролирует темп игры.|play_for_25|'.$jid1.'';break;
case "26":$str = ''.$minuta.'|ofs|Казалось бы отличный момент, но '.$f1['name'].' ('.$komm1.') забрался в офсайд.|play_for_26|'.$f1['id'].'|'.$jid1.'';break;
// case "27":$str = ''.$minuta.'|yellow|'.$p2['name'].' ('.$komm2.') удостоен желтой карточки. Грубо он сыграл.|yellow_27|'.$p2['id'].'|'.$jid2.'';break;
case "28":$str = ''.$minuta.'|ofs|'.$f1['name'].' ('.$komm1.') забрался в офсайд. Судья заметил это.|play_for_28|'.$f1['id'].'|'.$jid1.'';break;
case "29":$str = ''.$minuta.'|warning|После разрезающего оборону паса, '.$f1['name'].' ('.$komm1.') получил мяч в отличной позиции для удара и выстрелил. Но '.$g2['name'].' хорошо видел момент удара и отбил мяч, направленный в левый угол ворот.|play_for_29';break;
case "30":$str = ''.$minuta.'|ugol|'.$z1['name'].' ('.$komm1.') попытался отправить мяч в штрафную. Но один из защитников выбил мяч. Мяч ушел за пределы поля. '.$komm1.' получает право на угловой.\r\n'.($minuta<8?'0':NULL).''.($minuta+1).'|ugol|'.$p1['name'].' ('.$komm1.') исполняет угловой. Мяч оказался в гуще игроков обеих команд, но в итоге был вынесен подальше защитниками.|play_for_30';break;
case "31":$str = ''.$minuta.'|good|Хорошая перепасовка - '.$p1['name'].' ('.$komm1.') делает пас, а '.$f1['name'].' принимает мяч, но не в раю живем - '.$g2['name'].' читает их игру как пятиклассник азбуку и забирает мяч.|play_for_31';break;
case "32":$str = ''.$minuta.'|warning|'.$z1['name'].' ('.$komm1.') нанес отличный удар. Он получил мяч на углу штрафной и сразу пробил. Это мог бы быть трудный мяч для вратаря, но защитник заблокировал путь мячу.|play_for_32|'.$z1['id'].'|'.$jid1.'';break;
case "33":$str = ''.$minuta.'|good|'.$f1['name'].' ('.$komm1.') разворачивает игру и находит пасом своего партнера. '.$p1['name'].' продолжит атаку.|play_for_33';break;
case "34":$str = ''.$minuta.'|warning|'.$z1['name'].' ('.$komm1.') попытался сделать точную передачу, но '.$p1['name'].' не смог достать мяч - слишком сильный пас.|play_for_34';break;
case "35":$str = ''.$minuta.'|warning|'.$p1['name'].' ('.$komm1.') знал, что делать с мячом, но ему не повезло. Получив пас он пробил с 18 метров, но пробил чуть выше ворот.|play_for_35|'.$p1['id'].'|'.$jid1.'';break;
case "36":$str = ''.$minuta.'|good|'.$komm1.' контролирует мяч. Много коротких пасов, небольших комбинаций. Игроки ожидают от соперника ошибок.|play_for_36|'.$jid1.'';break;
case "37":$str = ''.$minuta.'|warning|'.$f1['name'].' ('.$komm1.') принял мяч и нанес удар издали. Но вратарь спокойно вытащил мяч из угла ворот.|play_for_37|'.$f1['id'].'|'.$jid1.'';break;
// case "38":$str = ''.$minuta.'|yellow|Заслуженный горчичник. Подкат был жесткий, и судья наказал заслуженно, '.$z2['name'].' ('.$komm2.') с предупреждением теперь.|yellow_38';break;
case "39":$str = ''.$minuta.'|good|'.$p1['name'].' ('.$komm1.') сделал неточный пас в штрафную. Был сделан перехват.|play_for_39|'.$p1['id'].'|'.$jid1.'';break;
case "40":$str = ''.$minuta.'|warning|'.$p1['name'].' ('.$komm1.') был на краю штрафной и сделал отличный пас. Один из защитников в последний момент прервал его. Аут вбросит '.$komm1.'.|play_for_40';break;
case "41":$str = ''.$minuta.'|good|'.$f1['name'].' ('.$komm1.') сделал забег в штрафную, но развернуться для удара не смог.|play_for_41|'.$f1['id'].'|'.$jid1.'';break;
case "42":$str = ''.$minuta.'|good|Игроки команды '.$komm1.' обмениваются короткими передачами.|play_for_42|'.$jid1.'';break;
case "43":$str = ''.$minuta.'|warning|'.$f1['name'].' ('.$komm1.') получил мяч после отскока и зарядил выше ворот.|play_for_43|'.$f1['id'].'|'.$jid1.'';break;
case "44":$str = ''.$minuta.'|warning|'.$p1['name'].' ('.$komm1.') почти создал голевой момент, но его попытка сыграть на партнера не прошла.|play_for_44|'.$p1['id'].'|'.$jid1.'';break;
case "45":$str = ''.$minuta.'|warning|'.$p1['name'].' ('.$komm1.') пробил из-за пределов штрафной. Опасно бил, но мяч попал в соперника.|play_for_45|'.$p1['id'].'|'.$jid1.'';break;
case "46":$str = ''.$minuta.'|warning|'.$p1['name'].' ('.$komm1.') обострял ситуацию сейчас. Он сделал неточный прострел в штрафную, и один из защитников был первый на мяче.|play_for_46|'.$p1['id'].'|'.$jid1.'';break;
case "47":$str = ''.$minuta.'|fol|'.$z2['name'].' ('.$komm1.') слишком грубо отнимал мяч, и судья был вынужден прервать игру.|play_for_47|'.$z2['id'].'|'.$jid1.'';break;
case "48":$str = ''.$minuta.'|ofs|Помощник судьи поднял флажок. '.$p1['name'].' ('.$komm1.') попал в офсайд, вот в чем дело.|play_for_48|'.$p1['id'].'|'.$jid1.'';break;
case "49":$str = ''.$minuta.'|fol|'.$p2['name'].' ('.$komm2.') боролся с соперником за мяч. '.$p2['name'].' сфолил в атаке.|play_for_49';break;
case "50":$str = ''.$minuta.'|warning|Пас сделал '.$p2['name'].' ('.$komm2.'), но '.$z1['name'].' перехватил мяч.|play_for_50|'.$p2['id'].'|'.$jid2.'|'.$z1['id'].'';break;
case "51":$str = ''.$minuta.'|warning|'.$f1['name'].' ('.$komm1.') получил мяч в штрафной, пробил, но его удар был заблокирован.|play_for_51|'.$f1['id'].'|'.$jid1.'';break;
case "52":$str = ''.$minuta.'|fol|'.$z2['name'].' ('.$komm2.') грубо сыграл, и судья увидел это нарушение - свисток. '.$komm1.' исполнит штрафной.\r\n'.($minuta<8?'0':NULL).''.($minuta+1).'|warning|'.$p2['name'].' ('.$komm2.') исполняет штрафной удар, который неточен, и защита не имеет никаких проблем с выносом.|play_for_52';break;
case "53":$str = ''.$minuta.'|fol|Нечестно сыграл '.$p2['name'].' ('.$komm2.'). Он придерживал своего оппонента.|play_for_53|'.$p2['id'].'|'.$jid2.'';break;
case "54":$str = ''.$minuta.'|warning|<b>'.$g2['name'].' отбил дальний удар, который нанес '.$p1['name'].' ('.$komm1.'), целившись под перекладину. '.$komm1.' получит право на угловой.</b>\r\n'.($minuta<8?'0':NULL).''.($minuta+1).'|ugol|'.$p1['name'].' ('.$komm1.') подавал угловой, но неудачно - никто из партнеров не смог нанести удар по воротам.|warning_54';break;
case "55":$str = ''.$minuta.'|warning|<b>'.$f1['name'].' ('.$komm1.') попытался пробить. Мяч пролетел рядом с правой штангой.</b>|warning_55|'.$f1['id'].'|'.$jid1.'';break;
case "56":$str = ''.$minuta.'|warning|<b>'.$f1['name'].' ('.$komm1.') вышел один-на-один и, не особо сближаясь, пробил. '.$g2['name'].' сумел среагировать на этот удар и отбил мяч.</b>|warning_56';break;
case "57":$str = ''.$minuta.'|warning|<b>'.$f1['name'].' ('.$komm1.') подобрал отскок и нанес удар со средней дистанции. Мяч пошел по перекладину, но '.$g2['name'].' спас свою команду от гола.</b>|warning_57';break;
case "58":$str = ''.$minuta.'|ugol|'.$p1['name'].' ('.$komm1.') попытается подать угловой так, чтобы мяч попал на голову одноклубника.\r\n'.($minuta<8?'0':NULL).''.($minuta+1).'|warning|'.$f1['name'].' ('.$komm1.') перевисел своего оппонента после подачи углового в воздухе и пробил головой в левый угол ворот. Мимо.|ugol_58';break;
case "59":$str = ''.$minuta.'|fol|Опасная игра, это был '.$z2['name'].' ('.$komm2.'). Судья свистнул.|play_for_59';break;
// case "60":$str = ''.$minuta.'|red|Заслуженное удаление. Подкат был жесткий, и судья наказал заслуженно, '.$z2['name'].' ('.$komm2.') .|red_60';break;
// case "38":$str = ''.$minuta.'||'.$komm1.' владеет в последние минуты мячом. Хорошо играют в пас футболисты.|play_for_60|'.$jid1.'';break;
// case "20":$str = ''.$minuta.'||'.$p1['name'].' ('.$komm1.') не нашел никого из партнеров по команде своим пасом и удар был заблокирован.|play_for_61|'.$p1['id'].'|'.$jid1.'';break;
// case "27":$str = ''.$minuta.'|warning|<b>Что творит вратарь?! '.$g2[name].' совершает отличный сейв! '.$f1['name'].' ('.$komm1.') легко прошел оппонента и расчехлил пушку дальним выстрелом в нижний правый угол ворот. Голкипер спас!</b>|warning_62';break;
/*case "1":$str = ''.$minuta.'||';break;
case "1":$str = ''.$minuta.'||';break;
case "1":$str = ''.$minuta.'||';break;
*/
}
}

elseif ($type == 'yellow')
{
$i_yellow = rand(1,7);
switch ($i_yellow)
{
case "1":$str = ''.$minuta.'|yellow|'.$name.' получает предупреждение, рукой сыграл игрок , а потом ещё начал спорить с арбитром матча.';break;
case "2":$str = ''.$minuta.'|yellow|'.$name.' откровенно опасно играл в подкате против опонента. По ноге не досталось, но за намерение грубо сфолить получил карточку игрок.';break;
case "3":$str = ''.$minuta.'|yellow|Жёлтую карточку получил '.$name.'. Потерял мяч, в итоге пришлось нарушить правила, сорвав атаку соперника.';break;

case "4":$str = ''.$minuta.'|yellow|'.$name.' сорвал опасную атаку за что и получил жёлтую карточку.';break;
case "5":$str = ''.$minuta.'|yellow|'.$name.' откровенно опасно играл в подкате против опонента. По ноге не досталось, но за намерение грубо сфолить получил карточку игрок.';break;
case "6":$str = ''.$minuta.'|yellow|Жёлтую карточку получил '.$name.'. Потерял мяч, в итоге пришлось нарушить правила, сорвав атаку соперника.';break;
case "7":$str = ''.$minuta.'|yellow|Судья достает из широких штанин... Э-э-э, я имею ввиду желтую карточку. Карточку получил '.$name.'. ';break;

}
}


elseif ($type == 'red1')
{
$i_red = rand(1,2);
switch ($i_red)
{
case "1":$str = ''.$minuta.'|red1|Заслуженное удаление. Подкат был жесткий, и судья наказал заслуженно, '.$name.' ('.$komm1.') .|red1|'.$name.'';break;
case "2":$str = ''.$minuta.'|red1|Судья достает из широких штанин... Э-э-э, я имею ввиду желтую карточку. Карточку получил '.$name.'. ';break;
  
  $zz5 = mysql_query("SELECT * FROM `r_player` WHERE (`name`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `sostav`='1', AND `line`!='1' ORDER BY RAND() ");
$gk5 = mysql_fetch_array($zz5);
  mysql_query("UPDATE `r_player` SET  `rc`=".($gk5['rc']+1)." and `utime`='2' and `sostav`='4' WHERE `id`='" . $gk5['id'] . "' LIMIT 1;");	
   mysql_query("update `r_team` set `i1`='' where `i1`='" . $arr1['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $arr1['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $arr1['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $arr1['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $arr1['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $arr1['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $arr1['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $arr1['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $arr1['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $arr1['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $arr1['id'] . "';");
                  
}
}
elseif ($type == 'red2')
{
$i_red = rand(1,2);
switch ($i_red)
{
case "1":$str = ''.$minuta.'|red2|Заслуженное удаление. Подкат был жесткий, и судья наказал заслуженно, '.$name.' ('.$komm2.') .|red2|'.$name.'';break;
case "2":$str = ''.$minuta.'|red2|Судья достает из широких штанин... Э-э-э, я имею ввиду желтую карточку. Карточку получил '.$name.'. ';break;
  
  $zz5 = mysql_query("SELECT * FROM `r_player` WHERE (`name`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `sostav`='1', AND `line`!='1' ORDER BY RAND() ");
$gk5 = mysql_fetch_array($zz5);
  mysql_query("UPDATE `r_player` SET  `rc`=".($gk5['rc']+1)." and `utime`='2' and `sostav`='4' WHERE `id`='" . $gk5['id'] . "' LIMIT 1;");	
   mysql_query("update `r_team` set `i1`='' where `i1`='" . $arr2['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $arr2['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $arr2['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $arr2['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $arr2['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $arr2['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $arr2['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $arr2['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $arr2['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $arr2['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $arr2['id'] . "';");
                  
}
}







 


return $str;
}





?>
<?php
define('_IN_JOHNCMS', 1);
$headmod = 'report';
require_once ("../incfiles/core.php");
$textl = 'Игра';
$prefix = !empty($_GET['union']) ? '_union_' : '_';
$issetun = !empty($_GET['union']) ? '&amp;union=isset' : '';
$dirs = !empty($_GET['union']) ? '/union/' : '/';
$textl = 'Игра';

$q = @mysql_query("select * from `r".$prefix."game` where id='" . $id . "' LIMIT 1;");
$arr = @mysql_fetch_array($q);

$mt=($realtime-$arr['time'])*18;
$mt=floor($mt/60);
if($mt<=93){header("Location:/txt".$dirs."" . $id . "");}


$q1 = @mysql_query("select * from `r_team` where id='" . $arr[id_team1] . "' LIMIT 1;");
$arr7 = @mysql_fetch_array($q1);
$q2 = @mysql_query("select * from `r_team` where id='" . $arr[id_team2] . "' LIMIT 1;");
$arr77 = @mysql_fetch_array($q2);

$textl = $arr7[name] . ' - ' . $arr77[name].' '.$arr[rez1].':'.$arr[rez2];


require_once ("../incfiles/head.php");


if($arr['tactics1']=='' or $arr['tactics2']==''){
	header('location: /game'.$dirs.''.$id);
	exit;
}
if (empty($arr[id]))
{
echo display_error('Отчёт не найден');
require_once ("../incfiles/end.php");
exit;
}


switch ($arr[kubok])
{
case "1":
$c_name = ''.$c_1.'';
break;
case "2":
$c_name = ''.$c_2.'';
break;
case "3":
$c_name = ''.$c_3.'';
break;
case "4":
$c_name = ''.$c_4.'';
break;
case "5":
$c_name = ''.$c_5.'';
break;
case "6":
$c_name = ''.$c_6.'';
break;
case "7":
$c_name = ''.$c_7.'';
break;
case "8":
$c_name = ''.$c_8.'';
break;
case "9":
$c_name = ''.$c_9.'';
break;
case "10":
$c_name = ''.$c_10.'';
break;
case "11":
$c_name = ''.$c_11.'';
break;
case "12":
$c_name = ''.$c_12.'';
break;
case "13":
$c_name = ''.$c_13.'';
break;
case "14":
$c_name = ''.$c_14.'';
break;
case "15":
$c_name = ''.$c_15.'';
break;
case "16":
$c_name = ''.$c_16.'';
break;
case "17":
$c_name = ''.$c_17.'';
break;
case "18":
$c_name = ''.$c_18.'';
break;
case "19":
$c_name = ''.$c_19.'';
break;
case "20":
$c_name = ''.$c_20.'';
break;
case "21":
$c_name = ''.$c_21.'';
break;
case "22":
$c_name = ''.$c_22.'';
break;
case "23":
$c_name = ''.$c_23.'';
break;
case "24":
$c_name = ''.$c_24.'';
break;
case "25":
$c_name = ''.$c_25.'';
break;
case "26":
$c_name = ''.$c_26.'';
break;
case "27":
$c_name = ''.$c_27.'';
break;
case "28":
$c_name = ''.$c_28.'';
break;
case "29":
$c_name = ''.$c_29.'';
break;
case "30":
$c_name = ''.$c_30.'';
break;
case "31":
$c_name = ''.$c_31.'';
break;
case "32":
$c_name = ''.$c_32.'';
break;
case "33":
$c_name = ''.$c_33.'';
break;
case "34":
$c_name = ''.$c_34.'';
break;
case "35":
$c_name = ''.$c_35.'';
break;
case "36":
$c_name = ''.$c_36.'';
break;
case "37":
$c_name = ''.$c_37.'';
break;
case "38":
$c_name = ''.$c_38.'';
break;
case "39":
$c_name = ''.$c_39.'';
break;
case "40":
$c_name = ''.$c_40.'';
break;
case "41":
$c_name = ''.$c_41.'';
break;
case "42":
$c_name = ''.$c_42.'';
break;
case "43":
$c_name = ''.$c_43.'';
break;
case "44":
$c_name = ''.$c_44.'';
break;
case "45":
$c_name = ''.$c_45.'';
break;
case "46":
$c_name = ''.$c_46.'';
break;
case "47":
$c_name = ''.$c_47.'';
break;
case "48":
$c_name = ''.$c_48.'';
break;
case "49":
$c_name = ''.$c_49.'';
break;
case "50":
$c_name = ''.$c_50.'';
break;
case "cup_netto":
										$c_name='Кубок Нетто';
										break;
										
										case "cup_charlton":
										$c_name='Кубок Чарльтона';
										break;
										
										case "cup_muller":
										$c_name='Кубок Мюллера';
										break;
										
										case "cup_puskas":
										$c_name='Кубок Пушкаша';
										break;
										
										case "cup_fachetti":
										$c_name='Кубок Факкетти';
										break;
										
										case "cup_kopa":
										$c_name='Кубок Копа';
										break;
										
										case "cup_distefano":
										$c_name='Кубок Ди Стефано';
										break;
}
// echo '<div class="phdr"><center><b>Отчет по матчу</b></center></div>';


switch ($arr[chemp])
{
	
	
case "super_cup":
		echo'<link rel="stylesheet" href="/theme/cups/super_cup.css" type="text/css" />';
echo '<div class="phdr_le"  style="text-align:left"><font color="white">'.$arr['kubok_nomi'].'</font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '<div class="phdr_le"  style="text-align:left"><center>
<a href="/super_cup/"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_super_cup.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';

	break;
	
case "super_cup2":
		echo'<link rel="stylesheet" href="/theme/cups/super_cup.css" type="text/css" />';
echo '<div class="phdr_le"  style="text-align:left"><font color="white">'.$arr['kubok_nomi'].'</font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '<div class="phdr_le"  style="text-align:left"><center>
<a href="/super_cup2/"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_super_cup.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';

	break;
	
	
		
case "cup_en":

echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=en">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;

case "cup_ru":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=ru">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;

case "cup_de":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=de">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;

case "cup_pt":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=pt">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;
case "cup_es":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=es">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;
case "cup_it":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=it">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;

case "cup_fr":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=fr">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;

case "cup_nl":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=nl">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;

	
	
	
	
	
  case "cup_netto":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=netto">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';



break;

case "cup_charlton":

echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=charlton">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;

case "cup_muller":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=muller">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;

case "cup_puskas":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=puskas">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;

case "cup_fachetti":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=fachetti">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;

case "cup_kopa":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=kopa">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;

case "cup_distefano":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=distefano">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;
case "cup_garrinca":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup/fed.php?act=garrinca">'.$arr['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;
										
	case "frend":
	echo'<a href="/friendly/" class="cardview x-pt-3 x-block-center x-rounded x-bg-cover x-onhover-wrapper" style="background-image: url(/images/cup/friendly.png);width: 75px;height: 75px;overflow: visible;" title="Перейти в кубок"></a>';
echo '<div class="gmenu"><center><a href="/friendly/"><b>Товарищеский матч</b></a></center> </div>';
break;
	case "z_cup":
echo '<div class="gmenu"><center><a href="/cup3/' . $arr['id_kubok'] . '"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';

echo'<div class="gmenu"><center><img src="/images/cup/b_'.$arr[kubok].'.png" alt="Кубок"/></center></div>';

break;
	case "cup":
echo '<div class="gmenu"><center><a href="/cup/' . $arr['id_kubok'] . '"><b>'.$c_name.'</b></a></center> </div>';
break;
case "unchamp":
echo'<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />';
echo '<div class="phdr_lk"><font color="white">'.$arr['kubok_nomi'].'</font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '<div class="phdr_lk"><center>
<a href="/union_champ/index.php?id=' . $arr['id_kubok'] . '"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/union/logo/cup' . $arr['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';

break;

	case "champ":
echo '<div class="phdr">Чемпионат<b class="rlink">'.date("d.m.Y H:i", $arr['time']).'</b></div>';
echo '<div class="gmenu"><center><a href="/champ00/index.php?act=' . $arr['kubok'] . '"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_00' . $arr['kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';

	break;
	case "champ_retro":
echo '<div class="gmenu"><center><a href="/champ_retro/index.php?act=' . $arr['id_kubok'] . '"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';
	break;
		case "liga_r":
	echo'<link rel="stylesheet" href="/theme/cups/lc.css" type="text/css" />';


echo '<div class="phdr_lc"><center>
<a href="/' . $arr['id_kubok'] . '/"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';

echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


	break;
		case "liga_r2":
	echo'<link rel="stylesheet" href="/theme/cups/lc.css" type="text/css" />';


echo '<div class="phdr_lc"><center>
<a href="/' . $arr['id_kubok'] . '/"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';

echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


	break;
		case "le":
		// echo'<link rel="stylesheet" href="/theme/cups/le.css" type="text/css" />';


// echo '<div class="gmenu"><center><a href="/' . $arr['id_kubok'] . '"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';
// echo'<div xmlns="http://www.w3.org/1999/xhtml" class="phdr_le">Кубок УЕФА</div>';
// echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/cup/b_le.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
		echo'<link rel="stylesheet" href="/theme/cups/le.css" type="text/css" />';
echo '<div class="phdr_le"><font color="white"><a href="/' . $arr['id_kubok'] . '/"><b>'.$arr['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';

echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_le.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
	break;
	
	case "kuefa2":
		// echo'<link rel="stylesheet" href="/theme/cups/le.css" type="text/css" />';


// echo '<div class="gmenu"><center><a href="/' . $arr['id_kubok'] . '"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';
// echo'<div xmlns="http://www.w3.org/1999/xhtml" class="phdr_le">Кубок УЕФА</div>';
// echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/cup/b_le.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
		echo'<link rel="stylesheet" href="/theme/cups/le.css" type="text/css" />';
echo '<div class="phdr_le"><font color="white"><a href="/' . $arr['id_kubok'] . '/"><b>'.$arr['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';

echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_le.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
	break;
	
		case "maradona":
		echo'<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />';

echo '<div class="gmenu"><center><a href="/' . $arr['id_kubok'] . '/"><b></b></a></center> </div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="phdr_lk">'.$arr['kubok_nomi'].'</div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/cup/b_cupcom.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';

	break;
	
		case "lk":
		echo'<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />';


echo '<div class="gmenu"><center><a href="/' . $arr['id_kubok'] . '/"><b>'.$arr['kubok_nomi'].'</b></a></center> </div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="phdr_lk">Кубок</div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';

	break;
	
	default:
	echo'<div class="gmenu"><center><img src="/images/cup/b_'.$arr[kubok].'.png" alt="Кубок"/></center></div>';
break;
}

echo'<div class="gmenu" style="text-align: center"><div style="font-weight: bold">'.date("d.m.Y H:i", $arr['time']).'</div>';



$k1 = @mysql_query("select * from `r_team` where id='" . $arr[id_team1] . "' LIMIT 1;");
$kom1 = @mysql_fetch_array($k1);

$k2 = @mysql_query("select * from `r_team` where id='" . $arr[id_team2] . "' LIMIT 1;");
$kom2 = @mysql_fetch_array($k2);


	 		$q1auy = @mysql_query("select * from `r_judge` WHERE `id`='".$arr[judge]."'  LIMIT 1;");
$aayr = @mysql_fetch_array($q1auy);
			
			echo'
			<div class="game22">
                        <div>
                            <b><img src="/images/gen4/whistle.png" class="va" alt=""> Главный арбитр матча</b>
                        </div>
                        <div>
                         <a href="/judge/index.php?id='.$aayr[id].'"><span class="flags c_'.$aayr[flag].'_18" style="vertical-align: middle;" title="'.$aayr[flag].'"></span> '.$aayr[name].'              </a>          </div>
                    </div>';
if($kom['stadium'] > 74999) $stadium = 16;
elseif($kom['stadium'] > 69999) $stadium = 15;
elseif($kom['stadium'] > 64999) $stadium = 14;
elseif($kom['stadium'] > 59999) $stadium = 13;
elseif($kom['stadium'] > 54999) $stadium = 12;
elseif($kom['stadium'] > 49999) $stadium = 11;
elseif($kom['stadium'] > 44999) $stadium = 10;
elseif($kom['stadium'] > 39999) $stadium = 9;
elseif($kom['stadium'] > 34999) $stadium = 8;
elseif($kom['stadium'] > 29999) $stadium = 7;
elseif($kom['stadium'] > 24999) $stadium = 6;
elseif($kom['stadium'] > 19999) $stadium = 5;
elseif($kom['stadium'] > 14999) $stadium = 4;
elseif($kom['stadium'] > 9999) $stadium = 3;
elseif($kom['stadium'] > 4999) $stadium = 2;
else  $stadium = 1;


echo'<div style="display: flex; justify-content: space-around;">
		<a href="/team/' . $kom1['id'] . '" class="x-color-black x-hover" style="align-items: center;display: flex;flex-direction: column;justify-content: center;flex-basis: 0;flex-grow: 1;">
			';
			
						if (!empty($kom1[logo]))
{
echo '<img src="/manager/logo/big' . $kom1[logo] . '" alt="Logo"/> ';
}
else
{
echo '<img src="/manager/logo/b_0.jpg" alt="Logo"  width="37" /> ';
}

//<div class="x-bg-cover x-rounded" style="background-image: url(/images/logo/b_29855.jpg?1675249959);width: 75px;height: 75px;"></div>
			echo'<div class="x-py-2">'.$kom1[name].'<br>';
					if($kom1[id_admin] > 0){
			//VIP-Статус
              
$us1 = mysql_query("SELECT * FROM `users` WHERE `id`=$kom1[id_admin] LIMIT 1;");
$uss1 = @mysql_fetch_array($us1);   
	  if ($uss1[vip] == 0) {
                 echo'<span style="opacity:0.4"><img src="/images/ico/vip0_m.png" title="Базовый аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss1[name] . '</span>';
	}
 else   if ($uss1[vip] == 1) {
                echo'<span style="opacity:0.4"><img src="/images/ico/vip1_m.png" title="Улучшенный Премиум-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss1[name] . '</span>';
		}
  else  if ($uss1[vip] == 2) {
	echo'<span style="opacity:0.4"><img src="/images/ico/vip2_m.png" title="Улучшенный VIP-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss1[name] . '</span>';
	}
  else  if ($uss1[vip] == 3) {
		 echo'<span style="opacity:0.4"><img src="/images/ico/vip3_m.png" title="Представительский Gold-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss1[name] . '</span> ';
			}
			
 }
			
			
			
			echo'</div>
		</a>
		<div style="align-items: center;display: flex;flex-direction: column;justify-content: center;">
			<div class="x-font-150 x-color-red"><div class="x-font-bold">';
			

	$nat1 = $arr[rez1]+$arr[per1];
$nat2 = $arr[rez2]+$arr[per2];	
		
			if ($arr[rez1] != '' || $arr[rez2] != '')
{
	
echo '<td> <font size="+3">
<b>'.$arr[rez1].'</b>:<b>'.$arr[rez2].'</b></font>';
if ($arr[rez1] == $arr[rez2] && $arr[chemp] == 'cup'|| $arr[chemp] == 'b_cup'|| $arr[chemp] == 'z_cup'|| $arr[chemp] == 'cup_continent'|| $arr[chemp] == 'super_cup'|| $arr[chemp] == 'super_cup2'|| $arr[chemp] == 'cupcom' || $arr[chemp] == 'cup_netto' || $arr[chemp] == 'cup_charlton' || $arr[chemp] == 'cup_muller' || $arr[chemp] == 'cup_puskas' || $arr[chemp] == 'cup_fachetti' || $arr[chemp] == 'cup_kopa' || $arr[chemp] == 'cup_distefano' || $arr[chemp] == 'cup_garrinca' || $arr[chemp] == 'cup_ru' || $arr[chemp] == 'cup_en' || $arr[chemp] == 'cup_de' || $arr[chemp] == 'cup_pt' || $arr[chemp] == 'cup_es' || $arr[chemp] == 'cup_it' || $arr[chemp] == 'cup_fr' || $arr[chemp] == 'cup_nl')
{
	if($arr[pen1] || $arr[pen2]){
	echo '<br/> (п. '.$arr[pen1].':'.$arr[pen2].')';}
}


if($arr[per1] || $arr[per2]){
			if ($arr[per1] == $arr[per2])
{
if($arr[pen1] || $arr[pen2]){
echo'<br/> (пен. '.$arr[pen1].':'.$arr[pen2].')';	}
}
}
}
			echo'	</div><div class="x-font-75"></div>
			</div>
		</div>
		<a href="/team/' . $kom2['id'] . '" class="x-color-black x-hover" style="align-items: center;display: flex;flex-direction: column;justify-content: center;flex-basis: 0;flex-grow: 1;">
			';
						
						if (!empty($kom2[logo]))
{
echo '<img src="/manager/logo/big' . $kom2[logo] . '" alt="Logo"/> ';
}
else
{
echo '<img src="/manager/logo/b_0.jpg" alt="Logo"  width="37" /> ';
}

			echo'<div class="x-py-2">'.$kom2[name].'<br>';
		if($kom2[id_admin] > 0){
			//VIP-Статус
              
$us2 = mysql_query("SELECT * FROM `users` WHERE `id`=$kom2[id_admin] LIMIT 1;");
$uss2 = @mysql_fetch_array($us2);   
	  if ($uss2[vip] == 0) {
                 echo'<span style="opacity:0.4"><img src="/images/ico/vip0_m.png" title="Базовый аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss2[name] . '</span>';
	}
 else   if ($uss2[vip] == 1) {
                echo'<span style="opacity:0.4"><img src="/images/ico/vip1_m.png" title="Улучшенный Премиум-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss2[name] . '</span>';
		}
  else  if ($uss2[vip] == 2) {
	echo'<span style="opacity:0.4"><img src="/images/ico/vip2_m.png" title="Улучшенный VIP-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss2[name] . '</span>';
	}
  else  if ($uss2[vip] == 3) {
		 echo'<span style="opacity:0.4"><img src="/images/ico/vip3_m.png" title="Представительский Gold-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss2[name] . ' </span>';
			}
			
 }	echo'</div>';	echo'</a>';
			
		echo'</div>';
	
			
			
















{


echo'<div class="textcols"><right>
	<div class="textcols-item " >';
	
		if($arr['teh_end'] != 1)
{
	$menus = explode("\r\n",$arr[menus]);
	asort($menus);
	next($menus);
 

				
				while (list($key, $val) = each($menus)) 
					
	{
		  $menu = explode("|",$menus[$key]);
		       $lox1 = mysql_query("SELECT * FROM `r_player` where `id`='" . $menu[2] . "';");
            $loxx1 = mysql_fetch_array($lox1);
			
			    $lox11 = mysql_query("SELECT * FROM `r_player` where `team`='" . $loxx1[team] .
                "';");
            $loxx11 = mysql_fetch_array($lox11);
		if($loxx1[team] == $kom1[id]){

echo'<table>
				<tbody><tr>						
	
	<td>'.$menu[0].'’ <img src="/images/g.gif" alt="" style="vertical-align: middle;"></td>
	<td class="x-text-center"><a href="/player/' . $menu[2] . '">' . $menu[3] . ' ' . $menu[4] . '</a></td>
	</tr></tr>				</tbody></table>';
	
	
	}
}
	
	
	
	
	
	
	
	
	
	
	if($arr[chemp] != 'frend'){
//////////yellow////////////////////////
	$menus1 = explode("\r\n",$arr[menus1]);
	asort($menus1);
	next($menus1);
 

				
				while (list($key, $val) = each($menus1)) 
					
	{
		  $menu = explode("|",$menus1[$key]);
		       $lox1 = mysql_query("SELECT * FROM `r_player` where `id`='" . $menu[2] . "';");
            $loxx1 = mysql_fetch_array($lox1);
			
			    $lox11 = mysql_query("SELECT * FROM `r_player` where `team`='" . $loxx1[team] .
                "';");
            $loxx11 = mysql_fetch_array($lox11);
		if($loxx1[team] == $kom1[id]){

echo'<table>
				<tbody><tr>						
	
	<td>'.$menu[0].'’ <img src="/images/yc.png" alt="Желтая" style="vertical-align: middle;"></td>
	<td class="x-text-center"><a href="/player/' . $menu[2] . '">' . $menu[3] . ' ' . $menu[4] . '</a></td>
	</tr></tr>				</tbody></table>';
	

	
	}
}

//////////yellow////////////////////////


//////////red////////////////////////
	$menus2 = explode("\r\n",$arr[menus2]);
	asort($menus2);
	next($menus2);
 

				
				while (list($key, $val) = each($menus2)) 
					
	{
		  $menu = explode("|",$menus2[$key]);
		       $lox1 = mysql_query("SELECT * FROM `r_player` where `id`='" . $menu[2] . "';");
            $loxx1 = mysql_fetch_array($lox1);
			
			    $lox11 = mysql_query("SELECT * FROM `r_player` where `team`='" . $loxx1[team] .
                "';");
            $loxx11 = mysql_fetch_array($lox11);
		if($loxx1[team] == $kom1[id]){

echo'<table>
				<tbody><tr>						
	
	<td>'.$menu[0].'’ <img src="/images/rc.png" alt="Красная" style="vertical-align: middle;"></td>
	<td class="x-text-center"><a href="/player/' . $menu[2] . '">' . $menu[3] . ' ' . $menu[4] . '</a></td>
	</tr></tr>				</tbody></table>';
	

	
	}
}

//////////red////////////////////////
	}
		 }

echo'	</div>
	
	<div class="textcols-item">';
		if($arr['teh_end'] != 1)
{
	$menus = explode("\r\n",$arr[menus]);
	asort($menus);
	next($menus);
 

				
				while (list($key, $val) = each($menus)) 
					
	{
		  $menu = explode("|",$menus[$key]);
		       $lox1 = mysql_query("SELECT * FROM `r_player` where `id`='" . $menu[2] . "';");
            $loxx1 = mysql_fetch_array($lox1);
			
			    $lox11 = mysql_query("SELECT * FROM `r_player` where `team`='" . $loxx1[team] .
                "';");
            $loxx11 = mysql_fetch_array($lox11);
		if($loxx1[team] == $kom2[id]){

echo'		<table>
				<tbody><tr>		
	
	<td>'.$menu[0].'’ <img src="/images/g.gif" alt="" style="vertical-align: middle;"></td>
	<td class="x-text-left"><a href="/player/' . $menu[2] . '">' . $menu[3] . ' ' . $menu[4] . '</a></td>
	</tr>	</tr>				</tbody></table>';
	}
}
	
	
	
		if($arr[chemp] != 'frend'){
//////////yellow////////////////////////
	$menus1 = explode("\r\n",$arr[menus1]);
	asort($menus1);
	next($menus1);
 

				
				while (list($key, $val) = each($menus1)) 
					
	{
		  $menu = explode("|",$menus1[$key]);
		       $lox2 = mysql_query("SELECT * FROM `r_player` where `id`='" . $menu[2] . "';");
            $loxx2 = mysql_fetch_array($lox2);
			
			    $lox22 = mysql_query("SELECT * FROM `r_player` where `team`='" . $loxx2[team] .
                "';");
            $loxx22 = mysql_fetch_array($lox22);
		if($loxx2[team] == $kom2[id]){

echo'		<table>
				<tbody><tr>		
	
	<td>'.$menu[0].'’ <img src="/images/yc.png" alt="Желтая карточка" style="vertical-align: middle;"></td>
	<td class="x-text-left"><a href="/player/' . $menu[2] . '">' . $menu[3] . ' ' . $menu[4] . '</a></td>
	</tr>	</tr>				</tbody></table>';
	
	

	
	}
}
	//////////yellow////////////////////////
	//////////red////////////////////////
	$menus2 = explode("\r\n",$arr[menus2]);
	asort($menus2);
	next($menus2);
 

				
				while (list($key, $val) = each($menus2)) 
					
	{
		  $menu = explode("|",$menus2[$key]);
		       $lox2 = mysql_query("SELECT * FROM `r_player` where `id`='" . $menu[2] . "';");
            $loxx2 = mysql_fetch_array($lox2);
			
			    $lox22 = mysql_query("SELECT * FROM `r_player` where `team`='" . $loxx2[team] .
                "';");
            $loxx22 = mysql_fetch_array($lox22);
		if($loxx2[team] == $kom2[id]){

echo'		<table>
				<tbody><tr>		
	
	<td>'.$menu[0].'’ <img src="/images/rc.png" alt="Красная карточка" style="vertical-align: middle;"></td>
	<td class="x-text-left"><a href="/player/' . $menu[2] . '">' . $menu[3] . ' ' . $menu[4] . '</a></td>
	</tr>	</tr>				</tbody></table>';
	
	

	
	}
}
	//////////red////////////////////////
		}
	
	
}
	echo'</div></right>
</div>';















////////////////////////////////////////////////////////////////////////////////////////////////////

	
	echo'<div style="display: flex;
    text-align: center;
    width: 100%;
    justify-content: center;
    align-items: center;">
                    <div class="tab-p but head_button" type="button" id="addteam">Состав</div>
                     <div class="tab-p but head_button" type="button" id="h2h">H2H</div>
                  
                </div>';
              






?>
<script>
$(function() {
  $(".but").on("click",function(e) {
    e.preventDefault();
    $(".content").hide();
    $("#"+this.id+"div").show();
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

@media (min-width: 120px) and (max-width: 639px) {


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
</style>
<?




$std11 = mysql_query("SELECT * FROM `r_stadium` where `id`='".$arr[id_stadium]."' ;");
            $std11 = mysql_fetch_array($std11);
			
/* if ($arr['final']){
	
	echo '<div class="gmenu"><center><img width="50%" health="50%" src="/images/stadium/198.jpg" alt="Республиканский"/></center>';
	echo '<center><div class="phdr" style="max-width: 80%;">'.$arr[zritel].' Зрителей на Стадионе <a href="/buildings/stadium.php?id=198"><b>Республиканский</b></a></center> </div>';

}
else{ */
	
if($arr[id_stadium]){
	if($std11[std]){
echo '<div class="gmenu"><center><img  src="/images/stadium/'.$arr[id_stadium].'.jpg"  alt="'.$std11[name].'"/>';
	}
else{
echo '<div class="gmenu"><center><img  src="/images/stadium/stadium.jpg"  alt="'.$std11[name].'"/>';
}	
//echo '<div class="pravmenu"><center>'.$arr[zritel].' Зрителей на Стадионе <a href="/buildings/stadium.php?id=' . $kom1['id'] . '"><b>'.$kom1['stadium_name'].'</b></a></center> </div>';

}
else{
	echo '<center><img src="/images/stadium/stadium.jpg" alt=""/>';
	
}			
/* }	 */			
// echo '<div class="pravmenu"><center>'.$arr[zritel].' Зрителей в Стадионе <a href="/buildings/stadium.php?id=' . $kom1['id'] . '"><b>'.$kom1['name'].' Arena</b></a></center> </div>';
echo '<div class="error" style="max-width: 480px;">'.$arr[zritel].' Зрителей на Стадионе <a href="/buildings/stadium.php?id=' . $std11[id].'"><b>'.$std11[name].'</b></a></center> </div>';


if($arr['teh_end'] == 1) // тех поражение
{
echo '<div class="info">В матче зафиксировано техническое поражение одной из команд. Матч отменён, победителем признана команда которая не нарушила регламент.</div>';
}
else
{/*echo'<div class="cardview-wrapper" bis_skin_checked="1">				<a class="cardview" href="/txt'.$dirs.''.$id.'">		<div class="left px50" bis_skin_checked="1"><i class="font-icon font-icon-whistle"></i></div>		<div class="right px50 arrow" bis_skin_checked="1">			<div class="text" bis_skin_checked="1">Посмотреть трансляцию</div>		</div>	</a></div>';*/
//	echo '<hr/><center><form action="/txt'.$dirs.''.$id.'"><input type="submit" title="Нажмите для просмотра трансляции" name="submit" value="Текстовая трансляция"/></form></center><hr/>';

	////////////////
	$tactics1 = explode("|",$arr[tactics1]);
	$tactics2 = explode("|",$arr[tactics2]);
	
	$players1 = explode("\r\n",$arr[players1]);
	$players2 = explode("\r\n",$arr[players2]);
	////////////////


// echo'<div class="gmenu">';
?>
<style>
.cardview-wrapper3 {
    /* background-color: #f3f3f3; */
    overflow: auto;
}
</style>
<?
///////////////////////////////////////////////////
echo'<div class="cardview-wrapper" bis_skin_checked="1">				
<a class="cardview" href="/txt'.$dirs.''.$id.'">
	<div class="left px50" bis_skin_checked="1"><i class="font-icon font-icon-whistle"></i></div>
	<div class="right px50 arrow" bis_skin_checked="1">		
	<div class="text" bis_skin_checked="1">Посмотреть трансляцию</div>	
	</div>	</a>';
	echo'</div>';
	

}


echo'<div id="addteamdiv" class="content">';
  
echo'<div class="cardview-wrapper3" bis_skin_checked="1">				

		';
	// echo'</div>';
		
// echo'<div class="x-block-center x-my-3 t-wrapper" style="max-width: 480px;">';

echo'<table id="content" class="t-table x-text-center" style="margin: 0 auto;"><tr class="whiteheader"><th colspan="3"><b>' . $kom2[name] . '</b><th>Опыт</th></th></tr>';
	



	$all1 = sizeof($players1);
	if($all1)
	{
	for($i=0; $i<($all1-1); $i++)
	{$play1 = explode("|",$players1[$i]);
		$reqs1 = mysql_query("SELECT * FROM `r_player` where `id`='".$play1[1]."'  ");
$p1 = mysql_fetch_array($reqs1);
	
	if($datauser[black] == 0){
	if($p1['line']=='1'){echo'<tr class="player-item-line team-0" style="background-color: #F5FFEF;" role="row">';}
	elseif($p1['line']=='2'){echo'<tr class="player-item-line team-0" style="background-color: #E2FFD2;" role="row">';}
	elseif($p1['line']=='3'){echo'<tr class="player-item-line team-0" style="background-color: #ccf3b5;" role="row">';}
	elseif($p1['line']=='4'){echo'<tr class="player-item-line team-0" style="background-color: #b0ea8f;" role="row">';}else{
	echo'<tr style="background:#FFF7E7;">';
	}
	}
/* 		else{
			if($p2['line']=='1'){echo'<tr class="player-item-line team-0" style="background-color: #434343;" role="row">';}
	elseif($p2['line']=='2'){echo'<tr class="player-item-line team-0" style="background-color: #363636;" role="row">';}
	elseif($p2['line']=='3'){echo'<tr class="player-item-line team-0" style="background-color: #262525;" role="row">';}
	elseif($p2['line']=='4'){echo'<tr class="player-item-line team-0" style="background-color: #1e1e1e;" role="row">';}else{
	echo'<tr style="background:#FFF7E7;">';
	}	
			
		} */
		

		
		
	echo '<td>'.$play1[0] . '</td><td style="text-align: left;"><a href="/player/' . $play1[1] . '" style="width: 100%; display: block;">';
	
	
	
	//echo'<img src="/images/forma/s_'.$kom1[forma].'.png" style="margin:-3px 0px;" alt=""/>';
$q = @mysql_query("select * from `r_player` where id='" . $play1[1] . "'  LIMIT 1;");
$arr = @mysql_fetch_array($q);

if ($arr['photo']) {
echo '<img src="/images/players/' . $arr['photo'] . '" width="25px" style="margin:-3px 0px;" alt=""/>';
} else if ($arr[line] == '1')
{
echo '<img src="/images/players/gk.png" width="25px" style="margin:-3px 0px;" alt=""/>';
}
else
{
echo '<img src="/images/players/cm.png" width="25px" style="margin:-3px 0px;" alt=""/>';
}


echo'	' . $play1[2] . '';
$g22 = mysql_query("select * from `r".$prefix."game` where id = '" . $id . "' LIMIT 1;");
$game22 = mysql_fetch_array($g22);
switch ($game22[chemp])
{
case "champ_retro":
if($arr[yc]){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$arr[yc].'</div>';
}
break;

case "unchamp":
if($arr[yc_unchamp]){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$arr[yc_unchamp].'</div>';
}
break;

case "liga_r":
if($arr[yc_liga_r]){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$arr[yc_liga_r].'</div>';
}
break;

case "le":
if($arr[yc_le]){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$arr[yc_le].'</div>';
}
break;

default:
break;
}

echo'</a></td><td style="font-weight: bold;"><th>' . $play1[3] . '</td></th>';


echo'</tr>';

	}
	
	}
	
 echo '</table>';


 /* 
 echo'<div   class="cardview x-block-center" style="max-width: 100%" bis_skin_checked="1">
		<div  class="x-row" bis_skin_checked="1">	
		<div class="x-col-1 x-vh-center x-font-250 x-color-white x-bg-green" bis_skin_checked="1">
		<i class="font-icon font-icon-settings"></i>		</div>	
		<div  class="x-col-2 x-p-3" bis_skin_checked="1">Схема: <b>' . $tactics1[0] . '</b><br/>';	
		switch ($tactics1[1])	{	
		case "0":	echo 'Пасы: <b>Смешанные</b><br/>';	break;	
		case "1":	echo 'Пасы: <b>Дальние</b><br/>';	break;	
		case "2":	echo 'Пасы: <b>Короткие</b><br/>';	break;	}	
		switch ($tactics1[2])	{	
		case "0":	echo 'Стратегия: <b>Нормальная</b><br/>';	break;	
		case "1":	echo 'Стратегия: <b>Дальние удары</b><br/>';	break;	
		case "2":	echo 'Стратегия: <b>Техничная игра</b><br/>';	break;	
		case "3":	echo 'Стратегия: <b>Игра в пас</b><br/>';	break;	}
		switch ($tactics1[4])	{	
		case "0":	echo 'Прессинг: <b>Нет</b><br/>';	break;	
		case "1":	echo 'Прессинг: <b>Да</b><br/>';	break;	}
		
		switch ($tactics1[3])	{	
		case "10":	echo 'Тактика: <b>Суперзащитная</b>';	break;	
		case "20":	echo 'Тактика: <b>Суперзащитная</b>';	break;	
		case "30":	echo 'Тактика: <b>30 Защитная</b>';	break;	
		case "40":	echo 'Тактика: <b>Защитная</b>';	break;	
		case "50":	echo 'Тактика: <b>Нормальная</b>';	break;	
		case "60":	echo 'Тактика: <b>Нормальная</b>';	break;	
		case "70":	echo 'Тактика: <b>Атакующая</b>';	break;	
		case "80":	echo 'Тактика: <b>Атакующая</b>';	break;	
		case "90":	echo 'Тактика: <b>Суператакующая</b>';	break;	
		case "100":	echo 'Тактика: <b>Суператакующая</b>';	break;	}		
		// echo'<br>Сила: <b>' . floor($tactics2[5]) . '</b>	';
echo'</div>';
echo'</div>';
echo'</div>';

 */
		

 

// echo'</div>';
	// echo'<aside >';
// echo'<div class="x-block-center x-my-3 t-wrapper" style="max-width: 480px;">';
echo'<table id="sidebar" class="t-table x-text-center" style="margin: 0 auto;"><tr class="whiteheader"><th colspan="3"><b>' . $kom2[name] . '</b><th>Опыт</th></th></tr>';
	



	$all2 = sizeof($players2);
	if($all2)
	{
	for($i=0; $i<($all2-1); $i++)
	{$play2 = explode("|",$players2[$i]);
		$reqs2 = mysql_query("SELECT * FROM `r_player` where `id`='".$play2[1]."'  ");
$p2 = mysql_fetch_array($reqs2);
	
	if($datauser[black] == 0){
	if($p2['line']=='1'){echo'<tr class="player-item-line team-0" style="background-color: #F5FFEF;" role="row">';}
	elseif($p2['line']=='2'){echo'<tr class="player-item-line team-0" style="background-color: #E2FFD2;" role="row">';}
	elseif($p2['line']=='3'){echo'<tr class="player-item-line team-0" style="background-color: #ccf3b5;" role="row">';}
	elseif($p2['line']=='4'){echo'<tr class="player-item-line team-0" style="background-color: #b0ea8f;" role="row">';}else{
	echo'<tr style="background:#FFF7E7;">';
	}
	}
/* 		else{
			if($p2['line']=='1'){echo'<tr class="player-item-line team-0" style="background-color: #434343;" role="row">';}
	elseif($p2['line']=='2'){echo'<tr class="player-item-line team-0" style="background-color: #363636;" role="row">';}
	elseif($p2['line']=='3'){echo'<tr class="player-item-line team-0" style="background-color: #262525;" role="row">';}
	elseif($p2['line']=='4'){echo'<tr class="player-item-line team-0" style="background-color: #1e1e1e;" role="row">';}else{
	echo'<tr style="background:#FFF7E7;">';
	}	
			
		} */
		

		
		
	echo '<td>'.$play2[0] . '</td><td style="text-align: left;"><a href="/player/' . $play2[1] . '" style="width: 100%; display: block;">';
	
	
	
	//echo'<img src="/images/forma/s_'.$kom2[forma].'.png" style="margin:-3px 0px;" alt=""/>';
$q = @mysql_query("select * from `r_player` where id='" . $play2[1] . "'  LIMIT 1;");
$arr = @mysql_fetch_array($q);

if ($arr['photo']) {
echo '<img src="/images/players/' . $arr['photo'] . '" width="25px" style="margin:-3px 0px;" alt=""/>';
} else if ($arr[line] == '1')
{
echo '<img src="/images/players/gk.png" width="25px" style="margin:-3px 0px;" alt=""/>';
}
else
{
echo '<img src="/images/players/cm.png" width="25px" style="margin:-3px 0px;" alt=""/>';
}


echo'	' . $play2[2] . '';
$g22 = mysql_query("select * from `r".$prefix."game` where id = '" . $id . "' LIMIT 1;");
$game22 = mysql_fetch_array($g22);
switch ($game22[chemp])
{
case "champ_retro":
if($arr[yc]){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$arr[yc].'</div>';
}
break;

case "unchamp":
if($arr[yc_unchamp]){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$arr[yc_unchamp].'</div>';
}
break;

case "liga_r":
if($arr[yc_liga_r]){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$arr[yc_liga_r].'</div>';
}
break;

case "le":
if($arr[yc_le]){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$arr[yc_le].'</div>';
}
break;

default:
break;
}

echo'</a></td><td style="font-weight: bold;"><th>' . $play2[3] . '</td></th></tr>';
	}
	
	}
echo '</table>';



/* 
		echo'<div class="cardview x-block-center" style="max-width: 480px" bis_skin_checked="1">
		<div class="x-row" bis_skin_checked="1">	
		<div class="x-col-1 x-vh-center x-font-250 x-color-white x-bg-green" bis_skin_checked="1">
		<i class="font-icon font-icon-settings"></i>		</div>	
		<div class="x-col-2 x-p-3" bis_skin_checked="1">Схема: <b>' . $tactics2[0] . '</b><br/>';	
		switch ($tactics2[1])	{	
		case "0":	echo 'Пасы: <b>Смешанные</b><br/>';	break;	
		case "1":	echo 'Пасы: <b>Дальние</b><br/>';	break;	
		case "2":	echo 'Пасы: <b>Короткие</b><br/>';	break;	}	
		switch ($tactics2[2])	{	
		case "0":	echo 'Стратегия: <b>Нормальная</b><br/>';	break;	
		case "1":	echo 'Стратегия: <b>Дальние удары</b><br/>';	break;	
		case "2":	echo 'Стратегия: <b>Техничная игра</b><br/>';	break;	
		case "3":	echo 'Стратегия: <b>Игра в пас</b><br/>';	break;	}
		switch ($tactics2[4])	{	
		case "0":	echo 'Прессинг: <b>Нет</b><br/>';	break;	
		case "1":	echo 'Прессинг: <b>Да</b><br/>';	break;	}
		
		switch ($tactics2[3])	{	
		case "10":	echo 'Тактика: <b>Суперзащитная</b>';	break;	
		case "20":	echo 'Тактика: <b>Суперзащитная</b>';	break;	
		case "30":	echo 'Тактика: <b>30 Защитная</b>';	break;	
		case "40":	echo 'Тактика: <b>Защитная</b>';	break;	
		case "50":	echo 'Тактика: <b>Нормальная</b>';	break;	
		case "60":	echo 'Тактика: <b>Нормальная</b>';	break;	
		case "70":	echo 'Тактика: <b>Атакующая</b>';	break;	
		case "80":	echo 'Тактика: <b>Атакующая</b>';	break;	
		case "90":	echo 'Тактика: <b>Суператакующая</b>';	break;	
		case "100":	echo 'Тактика: <b>Суператакующая</b>';	break;	}		
		// echo'<br>Сила: <b>' . floor($tactics2[5]) . '</b>	';
echo'</div>';
echo'</div>';
echo'</div>';
echo'</div>'; */
	// echo'</aside>';

	}




		
		
	
		///////////////////////////////////////	
		









echo'</div>';
  echo'</div>';
	
echo'
<div id="h2hdiv" class="content">
   ';

   echo'<div class="phdr">Последние игры: ' . $arr7[name] . '</div>';
   echo'
   <div class="c">
      <table id="example">
         <tbody>';
		 $q = mysql_query("SELECT * FROM `r_game` where `id_team1`='".$arr7[id]."' OR `id_team2`='".$arr7[id]."' order by time desc LIMIT 5;");
if($q <= 0){
	echo'<div class="content_empty" bis_skin_checked="1">                <img src="/images/no_report.png" alt="x" style="vertical-align:middle">                <p>Последних 15 матчей не было</p>        </div>';
}
else{


while ($res = mysql_fetch_array($q))
{
$k1 = @mysql_query("select * from `r_team` where id='" . $res[id_team1] . "' LIMIT 1;");
$kom1 = @mysql_fetch_array($k1);

$k2 = @mysql_query("select * from `r_team` where id='" . $res[id_team2] . "' LIMIT 1;");
$kom2 = @mysql_fetch_array($k2);






echo is_integer($i / 2) ? '<tr class="oddrows">' : '<tr class="evenrows">';
echo' 
               <td width="20%" align="center">';
			   if(date("d.m.y", $res['time']) == date("d.m.y", $realtime) ){									
echo'<span style="color:#A9A9A9;"><span class="today">Сегодня</span></span>';
								}
								else{
echo''.date("d.m.y", $res['time']).'';
								}
								echo'</td>
               <td>';
echo '<span class="flags c_'.$kom1[flag].'_14"  title="'.$kom1[flag].'"></span> <a href="/team/' . $res['id_team1'] . '">';


if ($res[rez1] > $res[rez2])
{
echo '<b>'.$kom1[name].'</b>';
}
else
{
echo ''.$kom1[name].'';
}


echo '</a>';
echo'			   - ';
echo '<span class="flags c_'.$kom2[flag].'_14"  title="'.$kom2[flag].'"></span> <a href="/team/' . $res['id_team2'] . '">';


if ($res[rez2] > $res[rez1])
{
echo '<b>'.$kom2[name].'</b>';
}
else
{
echo ''.$kom2[name].'';
}


echo '</a>';
echo'</td>
               <td width="15%">
                  <center>';
				  
if (!empty($res[rez1]) || !empty($res[rez2]) || $res[rez1] == '0' || $res[rez2] == '0')
{
echo '<a href="/report/' . $res['id'] . '"><font color="green"><b>'.$res[rez1].':'.$res[rez2].'</b></font></a>';
}
else
{
echo '<a href="/game/' . $res['id'] . '"><font color="green"><b>?:?</b></font></a>';
}
echo'</center>
               </td>
               <td align="center">';
			   if ($res[rez2] > $res[rez1])
{
               echo'   <div style="border-radius: 5px;min-width: 20px;min-height: 20px;width: 20px;height: 20px;background-color: #DD2729;text-align: center;"><span style="line-height: 22px;font-weight: bold;text-align: center; border-radius: 2px; color: white;">П</span></div>';
}
			   if ($res[rez1] > $res[rez2])
{
	echo'<div style="border-radius: 5px;min-width: 20px;min-height: 20px;width: 20px;height: 20px;background-color: #15BB16;text-align: center;"><span style="width: 20px;height: 20px;min-width: 20px;min-height: 20px;line-height: 22px;font-weight: bold;text-align: center; border-radius: 2px; color: white;">В</span></div>';
}
if ($res[rez1] == $res[rez2]){
	echo'<div style="border-radius: 5px;min-width: 20px;min-height: 20px;width: 20px;height: 20px;background-color: #F4A62E;text-align: center;"><span style="min-width: 20px;min-height: 20px;width: 20px;height: 20px;line-height: 22px;font-weight: bold;text-align: center; border-radius: 2px; color: white;">Н</span></div>';
}

			 echo'</td>
            </tr>
           
            ';



++$i;
}




}
 echo'          
         </tbody>
      </table>
   </div>
   ';
  
   echo'<div class="phdr">Последние игры: ' . $arr77[name] . '</div>';
   echo'
   <div class="c">
      <table id="example">
         <tbody>';
		 $q = mysql_query("SELECT * FROM `r_game` where `id_team1`='".$arr77[id]."' OR `id_team2`='".$arr77[id]."' order by time desc LIMIT 5;");
if($q <= 0){
	echo'<div class="content_empty" bis_skin_checked="1">                <img src="/images/no_report.png" alt="x" style="vertical-align:middle">                <p>Последних 15 матчей не было</p>        </div>';
}
else{


while ($res = mysql_fetch_array($q))
{
$k1 = @mysql_query("select * from `r_team` where id='" . $res[id_team1] . "' LIMIT 1;");
$kom1 = @mysql_fetch_array($k1);

$k2 = @mysql_query("select * from `r_team` where id='" . $res[id_team2] . "' LIMIT 1;");
$kom2 = @mysql_fetch_array($k2);







echo is_integer($i / 2) ? '<tr class="oddrows">' : '<tr class="evenrows">';

echo' 
               <td width="20%" align="center">';
			   if(date("d.m.y", $res['time']) == date("d.m.y", $realtime) ){									
echo'<span style="color:#A9A9A9;"><span class="today">Сегодня</span></span>';
								}
								else{
echo''.date("d.m.y", $res['time']).'';
								}
								echo'</td>
               <td>';
echo '<span class="flags c_'.$kom2[flag].'_14"  title="'.$kom2[flag].'"></span> <a href="/team/' . $res['id_team2'] . '">';


if ($res[rez2] > $res[rez1])
{
echo '<b>'.$kom2[name].'</b>';
}
else
{
echo ''.$kom2[name].'';
}


echo '</a>';
echo'			   - ';
echo '<span class="flags c_'.$kom1[flag].'_14"  title="'.$kom1[flag].'"></span> <a href="/team/' . $res['id_team1'] . '">';



if ($res[rez1] > $res[rez2])
{
echo '<b>'.$kom1[name].'</b>';
}
else
{
echo ''.$kom1[name].'';
}


echo '</a>';
echo'</td>
               <td width="15%">
                  <center>';
				  
if (!empty($res[rez1]) || !empty($res[rez2]) || $res[rez1] == '0' || $res[rez2] == '0')
{
echo '<a href="/report/' . $res['id'] . '"><font color="green"><b>'.$res[rez2].':'.$res[rez1].'</b></font></a>';
}
else
{
echo '<a href="/game/' . $res['id'] . '"><font color="green"><b>?:?</b></font></a>';
}
echo'</center>
               </td>
               <td align="center">';
			   if ($res[rez1] > $res[rez2])
{
               echo'   <div style="border-radius: 5px;min-width: 20px;min-height: 20px;width: 20px;height: 20px;background-color: #DD2729;text-align: center;"><span style="line-height: 22px;font-weight: bold;text-align: center; border-radius: 2px; color: white;">П</span></div>';
}
			   if ($res[rez2] > $res[rez1])
{
	echo'<div style="border-radius: 5px;min-width: 20px;min-height: 20px;width: 20px;height: 20px;background-color: #15BB16;text-align: center;"><span style="width: 20px;height: 20px;min-width: 20px;min-height: 20px;line-height: 22px;font-weight: bold;text-align: center; border-radius: 2px; color: white;">В</span></div>';
}
if ($res[rez1] == $res[rez2]){
	echo'<div style="border-radius: 5px;min-width: 20px;min-height: 20px;width: 20px;height: 20px;background-color: #F4A62E;text-align: center;"><span style="min-width: 20px;min-height: 20px;width: 20px;height: 20px;line-height: 22px;font-weight: bold;text-align: center; border-radius: 2px; color: white;">Н</span></div>';
}

			 echo'</td>
            </tr>
           
            ';



++$i;
}




}
 echo'          
         </tbody>
      </table>
   </div>
   ';
 
 
   echo'
   <div class="phdr">Очные встречи: '.$arr7[name].'-'.$arr77[name].'</div>';
   echo'
   <div class="c">
      <table id="example">
         <tbody>';
$qqo1 = mysql_query("SELECT * FROM `r_game` where (`id_team1`='".$arr7[id]."' and `id_team2`='".$arr77[id]."'and (`rez1`!='' or `rez2`!='') ) or (`id_team2`='".$arr7[id]."' and `id_team1`='".$arr77[id]."' and (`rez1`!='' or `rez2`!='') )  order by time desc LIMIT 5;");		 
$totalfsss = mysql_num_rows($qqo1);
if($totalfsss){  
while ($res = mysql_fetch_array($qqo1))
{      
$k1 = @mysql_query("select * from `r_team` where id='" . $res[id_team1] . "' LIMIT 1;");
$kom1 = @mysql_fetch_array($k1);

$k2 = @mysql_query("select * from `r_team` where id='" . $res[id_team2] . "' LIMIT 1;");
$kom2 = @mysql_fetch_array($k2);
echo is_integer($i / 2) ? '<tr class="oddrows">' : '<tr class="evenrows">';

		echo'   
               <td width="20%" align="center">';
			   if(date("d.m.y", $res['time']) == date("d.m.y", $realtime) ){									
echo'<span style="color:#A9A9A9;"><span class="today">Сегодня</span></span>';
								}
								else{
echo''.date("d.m.y", $res['time']).'';
								}
								echo'</td>
               <td><span class="flags c_'.$kom1[flag].'_14"  title="'.$kom1[flag].'"></span><a href="/team/' . $res['id_team1'] . '" >';

if ($res[rez1] > $res[rez2])
{
echo '<b> '.$kom1[name].'</b>';
}
else
{
echo ' '.$kom1[name].'';
}

echo'			   </a> - <span class="flags c_'.$kom2[flag].'_14"  title="'.$kom2[flag].'"></span><a href="/team/' . $res['id_team2'] . '" >';
if ($res[rez2] > $res[rez1])
{
echo '<b> '.$kom2[name].'</b>';
}
else
{
echo ' '.$kom2[name].'';
}
echo'</a></td>
               <td width="15%">
                  <center>';
				  if (!empty($res[rez1]) || !empty($res[rez2]) || $res[rez1] == '0' || $res[rez2] == '0')
{
echo '<a href="/report/' . $res['id'] . '"><font color="green"><b>'.$res[rez1].':'.$res[rez2].'</b></font></a>';
}
else
{
echo '<a href="/game/' . $res['id'] . '"><font color="green"><b>?:?</b></font></a>';
}
echo'</center>
               </td>
            </tr>';
}
}
else{
	echo'<div class="game-ui__history">
                        <div style="font-size:140%;">
                            История противостояния
                            <span class="green">' . $arr7['name'] . ' - ' . $arr77['name'] . '</span>
                        </div>
                        <div id="history-prematch" style="margin-bottom:20px;"><br>Данная статистика доступна для владельцев <a href="/vip.php?action=compare&amp;type=1"><img src="/images/ico/vip1.png" title="Улучшенный Премиум-аккаунт" style="width: 40px;border: none;vertical-align: middle;"></a></div>
                    </div>';	
}
       echo'  </tbody>
      </table>
   </div>
   ';
   echo'
</div>
';

  $bb = mysql_query("SELECT * FROM `news_2` WHERE `tid`='".$id."' ORDER BY `time` DESC ");
 if(mysql_num_rows($bb)>0){
 	echo '<div class="gmenu">';
echo 'Статистика игры:</div>';				
				$i="0";
                
                while ($bb1 = mysql_fetch_assoc($bb))
                {
                    echo ceil(ceil($i / 2) - ($i / 2)) == 0 ? '<div class="list1">' : '<div class="list2">';
                    					$menu = explode("|",$bb1['news']);
echo $div .'<img src="/imgages/txt/m_' . $menu[1] . '.gif" alt=""/>  ' . $menu[2] .'</div>';
echo'</div>';
                    ++$i;
                }}
 // 





require_once ("../incfiles/end.php");
?>
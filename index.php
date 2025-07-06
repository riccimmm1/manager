<?php
define('_IN_JOHNCMS', 1);
$headmod = 'game';
$textl = 'Игра '.$arr['kubok_nomi'].'';

require_once ("../incfiles/core.php");
require_once ("../incfiles/head.php");
require_once ("../game/func_game.php");
$prefix = !empty($_GET['union']) ? '_union_' : '_';
$issetun = !empty($_GET['union']) ? '&amp;union=isset' : '';
$dirs = !empty($_GET['union']) ? '/union/' : '/';

$g = mysql_query("select * from `r".$prefix."game` where id = '" . $id . "' LIMIT 1;");
$game = mysql_fetch_array($g);



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


<?




$pezda1 = $game['id_team1'];
$pezda2 = $game['id_team2'];
function complite_team1($id)
{
    global $game; // Используем глобальную переменную $game из контекста JohnCMS
    
    // Получаем данные команды
    $sql = mysql_query("SELECT * FROM `r_team` WHERE `id` = '".(int)$game['id_team1']."' LIMIT 1");
    if(mysql_num_rows($sql))
    {
        $team = mysql_fetch_assoc($sql);
        for($i = 1; $i <= 11; $i++)
        {
            // Если позиция в составе пустая
            if(empty($team['i'.$i]))
            {
                // Выбор игрока по позиции
                if($i == 1) {
                    $sql = "SELECT `id` FROM `r_player` WHERE `team` = '".(int)$game['id_team1']."' AND `line` = '1' AND `sostav` = '0' ORDER BY `rm` DESC LIMIT 1";
                } elseif($i >= 2 && $i <= 4) {
                    $sql = "SELECT `id` FROM `r_player` WHERE `team` = '".(int)$game['id_team1']."' AND `line` = '2' AND `sostav` = '0' ORDER BY `rm` DESC LIMIT 1";
                } elseif($i >= 5 && $i <= 9) {
                    $sql = "SELECT `id` FROM `r_player` WHERE `team` = '".(int)$game['id_team1']."' AND `line` = '3' AND `sostav` = '0' ORDER BY `rm` DESC LIMIT 1";
                } elseif($i >= 10) {
                    $sql = "SELECT `id` FROM `r_player` WHERE `team` = '".(int)$game['id_team1']."' AND `line` = '4' AND `sostav` = '0' ORDER BY `rm` DESC LIMIT 1";
                }

                $res = mysql_query($sql);
                
                // Если игроков по специализации нет - берем любого
                if(!mysql_num_rows($res)) {
                    $sql = "SELECT `id` FROM `r_player` WHERE `team` = '".(int)$game['id_team1']."' AND `sostav` = '0' ORDER BY `rm` DESC LIMIT 1";
                    $res = mysql_query($sql);
                }
                
                // Если нашли игрока
                if(mysql_num_rows($res)) {
                    $player = mysql_fetch_assoc($res);
                    $player_id = (int)$player['id'];
                    
                    // Обновляем позицию в команде
                    mysql_query("UPDATE `r_team` SET `i$i` = '$player_id' WHERE `id` = '".(int)$game['id_team1']."'");
                    
                    // Помечаем игрока как занятого
                    mysql_query("UPDATE `r_player` SET `sostav` = '1' WHERE `id` = '$player_id'");
                    
                    echo 'Автобалансировка составов (команда 1, позиция '.$i.')<br>';
                }
            }
        }
    }
}

function complite_team2($id)
{
    global $game;
    
    $sql = mysql_query("SELECT * FROM `r_team` WHERE `id` = '".(int)$game['id_team2']."' LIMIT 1");
    if(mysql_num_rows($sql))
    {
        $team = mysql_fetch_assoc($sql);
        for($i = 1; $i <= 11; $i++)
        {
            if(empty($team['i'.$i]))
            {
                if($i == 1) {
                    $sql = "SELECT `id` FROM `r_player` WHERE `team` = '".(int)$game['id_team2']."' AND `line` = '1' AND `sostav` = '0' ORDER BY `rm` DESC LIMIT 1";
                } elseif($i >= 2 && $i <= 4) {
                    $sql = "SELECT `id` FROM `r_player` WHERE `team` = '".(int)$game['id_team2']."' AND `line` = '2' AND `sostav` = '0' ORDER BY `rm` DESC LIMIT 1";
                } elseif($i >= 5 && $i <= 9) {
                    $sql = "SELECT `id` FROM `r_player` WHERE `team` = '".(int)$game['id_team2']."' AND `line` = '3' AND `sostav` = '0' ORDER BY `rm` DESC LIMIT 1";
                } elseif($i >= 10) {
                    $sql = "SELECT `id` FROM `r_player` WHERE `team` = '".(int)$game['id_team2']."' AND `line` = '4' AND `sostav` = '0' ORDER BY `rm` DESC LIMIT 1";
                }

                $res = mysql_query($sql);
                
                if(!mysql_num_rows($res)) {
                    $sql = "SELECT `id` FROM `r_player` WHERE `team` = '".(int)$game['id_team2']."' AND `sostav` = '0' ORDER BY `rm` DESC LIMIT 1";
                    $res = mysql_query($sql);
                }
                
                if(mysql_num_rows($res)) {
                    $player = mysql_fetch_assoc($res);
                    $player_id = (int)$player['id'];
                    
                    mysql_query("UPDATE `r_team` SET `i$i` = '$player_id' WHERE `id` = '".(int)$game['id_team2']."'");
                    mysql_query("UPDATE `r_player` SET `sostav` = '1' WHERE `id` = '$player_id'");
                    
                    echo 'Автобалансировка составов (команда 2, позиция '.$i.')<br>';
                }
            }
        }
    }
}


 

$sql = mysql_query("SELECT * FROM `r_team` WHERE `id`='".$game['id_team1']."' LIMIT 1");
	if(mysql_num_rows($sql))
	{
		$team = mysql_fetch_assoc($sql);
		for($i = 1; $i <= 11; $i++)
		{
			if(!$team['i'.$i])
			{
				if($i==1)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' and `line`='1' and `sostav`='0' order by `rm` desc limit 1");
				}
				elseif($i==2 || $i==3 || $i==4)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' and `line`='2'  and `sostav`='0' order by `rm` desc limit 1");				
				}
				elseif($i==5 || $i==6 || $i==7 || $i==8 || $i==9)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' and `line`='3'  and `sostav`='0' order by `rm` desc limit 1");				
				}
				elseif($i==10 || $i==11)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' and `line`='4' and `sostav`='0' order by `rm` desc limit 1");					
				}
				
				 if(!mysql_num_rows($sql))
				{
				$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' and `sostav`='0' and `line`!='1' order by `rm` limit 1");
						// $sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' and `sostav`='0' order by `rm` limit 1");
				} 
				
				$player = mysql_fetch_assoc($sql);
				
				mysql_query("UPDATE `r_team` SET `i".$i."`='".$player[id]."' WHERE `id`='".$game['id_team1']."' LIMIT 1");
				 mysql_query("UPDATE `r_team` SET `i$i`='' WHERE `id`!='$id' LIMIT 1");
				mysql_query("UPDATE `r_player` SET `sostav`='1' WHERE `id`='".$player[id]."' LIMIT 1");
				mysql_query("UPDATE `r_player` SET `sostav`='0' WHERE `id`!='$player[id]' LIMIT 1");
// echo'автобалансировка составов <br>';
			}
		}
	}
	
	$sql = mysql_query("SELECT * FROM `r_team` WHERE `id`='".$game['id_team2']."' LIMIT 1");
	if(mysql_num_rows($sql))
	{
		$team = mysql_fetch_assoc($sql);
		for($i = 1; $i <= 11; $i++)
		{
			if(!$team['i'.$i])
			{
				if($i==1)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' and `line`='1' and `sostav`='0' order by `rm` desc limit 1");
				}
				elseif($i==2 || $i==3 || $i==4)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' and `line`='2'  and `sostav`='0' order by `rm` desc limit 1");				
				}
				elseif($i==5 || $i==6 || $i==7 || $i==8 || $i==9)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' and `line`='3'  and `sostav`='0' order by `rm` desc limit 1");				
				}
				elseif($i==10 || $i==11)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' and `line`='4' and `sostav`='0' order by `rm` desc limit 1");					
				}
				
				 if(!mysql_num_rows($sql))
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' and `sostav`='0' and `line`!='1' order by `rm` limit 1");
					// $sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' and `sostav`='0' order by `rm` limit 1");
				} 
				
				$player = mysql_fetch_assoc($sql);
				
				mysql_query("UPDATE `r_team` SET `i".$i."`='".$player[id]."' WHERE `id`='".$game['id_team2']."' LIMIT 1");
				mysql_query("UPDATE `r_team` SET `i$i`='' WHERE `id`!='$id' LIMIT 1");
				mysql_query("UPDATE `r_player` SET `sostav`='1' WHERE `id`='".$player[id]."' LIMIT 1");
				mysql_query("UPDATE `r_player` SET `sostav`='0' WHERE `id`!='$player[id]' LIMIT 1");
// echo'автобалансировка составов <br>';
			}
		}
}



	
	 		$q1auy = @mysql_query("select * from `r_judge` WHERE `id`='".$game[judge]."'  LIMIT 1;");
$aayr = @mysql_fetch_array($q1auy);
			
			echo'
			<div class="game22 game-ui__referee">
                        <div>
                            <b><img src="/images/gen4/whistle.png" class="va" alt=""> Главный арбитр матча</b>
                        </div>
                        <div>
                                                     <a href="/judge/index.php?id='.$aayr[id].'"><span class="flags c_'.$aayr[flag].'_18" style="vertical-align: middle;" title="'.$aayr[flag].'"></span> '.$aayr[name].'              </a>          </div>
                    </div>';
			
			

	
if (!empty($game[rez1]) || !empty($game[rez2]) || $game[rez1] == '0' || $game[rez2] == '0')
{
header('location: /report'.$dirs.''.$id);
exit;
}

if (empty($game[id]) || empty($game[id_team1]) || empty($game[id_team2]))
{
						echo'	<div class="cardview-wrapper x-overlay" id="errorMsg">
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
// echo display_error('Игра отменена');
require_once ("../incfiles/end.php");
exit;
}


if ($act == "add")
{

if ($datauser[manager2] == $game[id_team1])
{
mysql_query("update `r".$prefix."game` set `go1`='1' where id='" . $id . "' LIMIT 1;");
}
elseif ($datauser[manager2] == $game[id_team2])
{
mysql_query("update `r".$prefix."game` set `go2`='1' where id='" . $id . "' LIMIT 1;");
}

header('location: /game'.$dirs.''.$id);
exit;
}

		

// ЕСЛИ НЕТ ПОДТВЕРЖДЕНИЯ
if ($game[go1] != 1 || $game[go2] != 1)
{


if ($game['time'] > $realtime)
{
$ostime = $game[time]-$realtime;
$q1 = @mysql_query("select * from `r_team` where id='" . $game[id_team1] . "' LIMIT 1;");
$count1 = mysql_num_rows($q1);
$arr1 = @mysql_fetch_array($q1);

$q2 = @mysql_query("select * from `r_team` where id='" . $game[id_team2] . "' LIMIT 1;");
$count2 = mysql_num_rows($q2);
$arr2 = @mysql_fetch_array($q2);

















$k1 = @mysql_query("select * from `r_team` where id='" . $game[id_team1] . "' LIMIT 1;");
$kom1 = @mysql_fetch_array($k1);

$k2 = @mysql_query("select * from `r_team` where id='" . $game[id_team2] . "' LIMIT 1;");
$kom2 = @mysql_fetch_array($k2);


/* 	/*автобалансировка составов
	
	
	complite_team1($kom1['id']);

	complite_team2($kom2['id']); */
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



switch ($game[kubok])
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
case "150":
$c_name = ''.$c_150.'';
break;
case "60":
$c_name = ''.$c_60.'';
break;
case "109":
$c_name = ''.$c_109.'';
break;
case "151":
$c_name = ''.$c_151.'';
break;
case "152":
$c_name = ''.$c_152.'';
break;
case "153":
$c_name = ''.$c_153.'';
break;
case "154":
$c_name = ''.$c_154.'';
break;
case "155":
$c_name = ''.$c_155.'';
break;
case "156":
$c_name = ''.$c_156.'';
break;
case "157":
$c_name = ''.$c_157.'';
break;
case "158":
$c_name = ''.$c_158.'';
break;
case "159":
$c_name = ''.$c_159.'';
break;
case "160":
$c_name = ''.$c_160.'';
break;
case "61":
$c_name = ''.$c_61.'';
break;
case "62":
$c_name = ''.$c_62.'';
break;
case "63":
$c_name = ''.$c_63.'';
break;
case "64":
$c_name = ''.$c_64.'';
break;
case "65":
$c_name = ''.$c_65.'';
break;
case "66":
$c_name = ''.$c_66.'';
break;
case "67":
$c_name = ''.$c_67.'';
break;
case "68":
$c_name = ''.$c_68.'';
break;
case "69":
$c_name = ''.$c_69.'';
break;
case "70":
$c_name = ''.$c_70.'';
break;
case "71":
$c_name = ''.$c_71.'';
break;
case "72":
$c_name = ''.$c_72.'';
break;
case "73":
$c_name = ''.$c_73.'';
break;
case "74":
$c_name = ''.$c_74.'';
break;
case "75":
$c_name = ''.$c_75.'';
break;
case "76":
$c_name = ''.$c_76.'';
break;
case "77":
$c_name = ''.$c_77.'';
break;
case "78":
$c_name = ''.$c_78.'';
break;
case "79":
$c_name = ''.$c_79.'';
break;
case "80":
$c_name = ''.$c_80.'';
break;
case "81":
$c_name = ''.$c_81.'';
break;
case "82":
$c_name = ''.$c_82.'';
break;
case "83":
$c_name = ''.$c_83.'';
break;
case "84":
$c_name = ''.$c_84.'';
break;
case "85":
$c_name = ''.$c_85.'';
break;
case "86":
$c_name = ''.$c_86.'';
break;
case "87":
$c_name = ''.$c_87.'';
break;
case "88":
$c_name = ''.$c_88.'';
break;
case "89":
$c_name = ''.$c_89.'';
break;
case "90":
$c_name = ''.$c_90.'';
break;
case "91":
$c_name = ''.$c_91.'';
break;
case "92":
$c_name = ''.$c_92.'';
break;
case "93":
$c_name = ''.$c_93.'';
break;
case "94":
$c_name = ''.$c_94.'';
break;
case "95":
$c_name = ''.$c_95.'';
break;
case "96":
$c_name = ''.$c_96.'';
break;
case "97":
$c_name = ''.$c_97.'';
break;
case "98":
$c_name = ''.$c_98.'';
break;
case "99":
$c_name = ''.$c_99.'';
break;
case "100":
$c_name = ''.$c_100.'';
break;
case "101":
$c_name = ''.$c_101.'';
break;
case "102":
$c_name = ''.$c_102.'';
break;
case "103":
$c_name = ''.$c_103.'';
break;
case "104":
$c_name = ''.$c_104.'';
break;
case "105":
$c_name = ''.$c_105.'';
break;
case "106":
$c_name = ''.$c_106.'';
break;
case "107":
$c_name = ''.$c_107.'';
break;
case "108":
$c_name = ''.$c_108.'';
break;
case "cup_netto":
										$c_name='Кубок Нетто';
										break;
										
										case "cup_charlton":
										$c_name='Кубок Чарльтона';
										break;
										case "cup_en":
										$c_name='Кубок Англии';
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



switch ($game[chemp])
{
	
	
case "cup_en":

echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=en">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;

case "cup_ru":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=ru">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;

case "cup_de":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=de">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;

case "cup_pt":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=pt">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;
case "cup_es":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=es">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;
case "cup_it":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=it">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;

case "cup_fr":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=fr">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;

case "cup_nl":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"><font color="white"><a href="/fedcup2/fed.php?act=nl">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;

	 case "cup_netto":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"  style="text-align:left"><font color="white"><a href="/fedcup/fed.php?act=netto">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';



break;

case "cup_charlton":

echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"  style="text-align:left"><font color="white"><a href="/fedcup/fed.php?act=charlton">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;

case "cup_muller":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"  style="text-align:left"><font color="white"><a href="/fedcup/fed.php?act=muller">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;

case "cup_puskas":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"  style="text-align:left"><font color="white"><a href="/fedcup/fed.php?act=puskas">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;

case "cup_fachetti":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"  style="text-align:left"><font color="white"><a href="/fedcup/fed.php?act=fachetti">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;

case "cup_kopa":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"  style="text-align:left"><font color="white"><a href="/fedcup/fed.php?act=kopa">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;

case "cup_distefano":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"  style="text-align:left"><font color="white"><a href="/fedcup/fed.php?act=distefano">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;
case "cup_garrinca":
echo'<link rel="stylesheet" href="/theme/cups/cup.css" type="text/css" />';
echo '<div class="phdr_cup"  style="text-align:left"><font color="white"><a href="/fedcup/fed.php?act=garrinca">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_' . $game['id_kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';


break;
			
case "maradona":
	echo'<link rel="stylesheet" href="/theme/cups/maradona.css" type="text/css" />';
echo '<div class="gmenu"><center><a href="/' . $game['id_kubok'] . '"><b></b></a></center> </div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="phdr_lk"  style="text-align:center">'.$game['kubok_nomi'].'</div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center"><img src="/images/cup/b_maradona.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;
case "unchamp":
echo'<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />';
echo '<div class="phdr_lk"  style="text-align:left"><font color="white"><a href="/' . $game['id_kubok'] . '">'.$game['kubok_nomi'].'</a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';

echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/union/logo/cup' . $game['id_kubok'] . '.jpg" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';

break;

case "champ":
echo '<div class="phdr">Чемпионат<b class="rlink">'.date("d.m.Y H:i", $game['time']).'</b></div>';
echo '<div class="gmenu"><center><a href="/champ00/index.php?act=' . $game['kubok'] . '"><b>'.$game['kubok_nomi'].'</b></a></center> </div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_00' . $game['kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';

break;

case "champ_retro":
echo '<div class="phdr"  style="text-align:left">Чемпионат<b class="rlink">'.date("d.m.Y H:i", $game['time']).'</b></div>';
	
echo '<div class="gmenu"><center><a href="/champ/index.php?act=' . $game['id_kubok'] . '"><b>'.$game['kubok_nomi'].'</b></a></center> </div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_00' . $game['kubok'] . '.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';

break;

case "cup":
	echo '<div class="phdr"  style="text-align:left"><a href="/cup/' . $game['id_kubok'] . '">'.$c_name.'</a><b class="rlink">'.date("d.m.Y H:i", $game['time']).'</b></div>';
// echo '<div class="gmenu"><center><a href="/cup/' . $game['id_kubok'] . '"><b>'.$c_name.'</b></a></center> </div>';

break;
case "brend":
	echo '<div class="phdr"  style="text-align:left"><a href="/brendcup/' . $game['id_kubok'] . '">'.$c_name.'</a><b class="rlink">'.date("d.m.Y H:i", $game['time']).'</b></div>';
// echo '<div class="gmenu"><center><a href="/cup/' . $game['id_kubok'] . '"><b>'.$c_name.'</b></a></center> </div>';

break;

case "liga_r":
	echo'<link rel="stylesheet" href="/theme/cups/lc.css" type="text/css" />';
echo '<div class="phdr_lc"  style="text-align:left"><font color="white"><a href="/' . $game['id_kubok'] . '"><b>'.$game['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';

echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;

case "liga_r2":
	echo'<link rel="stylesheet" href="/theme/cups/lc.css" type="text/css" />';
echo '<div class="phdr_lc"  style="text-align:left"><font color="white"><a href="/' . $game['id_kubok'] . '"><b>'.$game['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';

echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;

case "liberta":
	echo'<link rel="stylesheet" href="/theme/cups/liberta.css" type="text/css" />';
echo '<div class="phdr_le"><font color="white"><a href="/' . $arr['id_kubok'] . '/"><b>'.$arr['kubok_nomi'].'</b></a></font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $arr['time']).'</b></font></div>';

echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_liberta.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';
break;
case "le":
		echo'<link rel="stylesheet" href="/theme/cups/le.css" type="text/css" />';
echo '<div class="phdr_le"  style="text-align:left"><font color="white">'.$game['kubok_nomi'].'</font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '<div class="phdr_le"  style="text-align:left"><center>
<a href="/' . $game['id_kubok'] . '"><b>'.$game['kubok_nomi'].'</b></a></center> </div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_le.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';

	break;
	
case "super_cup":
		echo'<link rel="stylesheet" href="/theme/cups/super_cup.css" type="text/css" />';
echo '<div class="phdr_le"  style="text-align:left"><font color="white">'.$game['kubok_nomi'].'</font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '<div class="phdr_le"  style="text-align:left"><center>
<a href="/super_cup/"><b>'.$game['kubok_nomi'].'</b></a></center> </div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_super_cup.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';

	break;
	
case "super_cup2":
		echo'<link rel="stylesheet" href="/theme/cups/super_cup.css" type="text/css" />';
echo '<div class="phdr_le"  style="text-align:left"><font color="white">'.$game['kubok_nomi'].'</font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '<div class="phdr_le"  style="text-align:left"><center>
<a href="/super_cup2/"><b>'.$game['kubok_nomi'].'</b></a></center> </div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/cup/b_super_cup.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';

	break;
	
	
case "lk":
	echo'<link rel="stylesheet" href="/theme/cups/lk.css" type="text/css" />';
echo '<div class="phdr_lk"  style="text-align:left"><font color="white">'.$game['kubok_nomi'].'</font><b class="rlink"><font color="white">'.date("d.m.Y H:i", $game['time']).'</b></font></div>';
echo '<div class="phdr_lk"  style="text-align:left"><center>
<a href="/' . $game['id_kubok'] . '"><b>'.$game['kubok_nomi'].'</b></a></center> </div>';
echo'<div xmlns="http://www.w3.org/1999/xhtml" class="top" style="text-align: center">
<img src="/images/ico/cup_ico/lc2.png" height="64" alt="*"><br><div class="text_top"><b></b></div></div>';

break;

default:
	
		echo '<div class="phdr">Матч<b class="rlink">'.date("d.m.Y H:i", $game['time']).'</b></div>';
echo '<div class="gmenu"><center><a href="/cup/' . $game['id_kubok'] . '"><b>'.$c_name.'</b></a></center> </div>';

break;

}



echo'<br>';













echo '<table id="pallet"><tr>';
if (!empty($kom1[logo]))
{
echo '<td width="50%"><center><a href="/team/' . $kom1['id'] . '"><img src="/manager/logo/big' . $kom1[logo] . '" alt=""/> </a>';
}
else
{
echo '<td width="50%"><center><a href="/team/' . $kom1['id'] . '"><img src="/manager/logo/b_0.jpg" alt=""/></a> ';
}


echo "<div class=''>";


echo"<a href='/team/" . $kom1['id'] . "'><span class='flags c_" . $kom1['flag'] . "_14' style='vertical-align: middle;' title='" . $kom1['flag'] . "'></span> ".$kom1[name]."</a><br>";
 if($kom1[id_admin] > 0){
			//VIP-Статус

$us1 = mysql_query("SELECT * FROM `users` WHERE `id`=$kom1[id_admin] LIMIT 1;");
$uss1 = @mysql_fetch_array($us1);   
	  if ($uss1[vip] == 0) {
                 echo'<span style="opacity:0.4"><a href="/vip.php?action=compare&amp;type=0"><img src="/images/ico/vip0_m.png" title="Базовый аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss1[name] . '</span></a>';
	}
    elseif ($uss1[vip] == 1) {
                echo'<span style="opacity:0.4"><a href="/vip.php?action=compare&amp;type=1"><img src="/images/ico/vip1_m.png" title="Улучшенный Премиум-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss1[name] . '</span></a>';
		}
    elseif ($uss1[vip] == 2) {
	echo'<span style="opacity:0.4"><a href="/vip.php?action=compare&amp;type=2"><img src="/images/ico/vip2_m.png" title="Улучшенный VIP-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss1[name] . '</span></a>';
	}
    elseif ($uss1[vip] == 3) {
		 echo'<span style="opacity:0.4"><a href="/vip.php?action=compare&amp;type=3"><img src="/images/ico/vip3_m.png" title="Представительский Gold-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss1[name] . '</span></a>';
			}
			
 }


echo"</div>";

echo"</center></td>";


 
if (!empty($kom2[logo]))
{
echo ' <td><center><a href="/team/' . $kom2['id'] . '"><img src="/manager/logo/big' . $kom2[logo] . '" alt=""/></a> ';
}
else
{
echo '<td><center> <a href="/team/' . $kom2['id'] . '"><img src="/manager/logo/b_0.jpg" alt=""/></a>';
}
echo "<div class=''><a href='/team/" . $kom2['id'] . "'>".$kom2[name]." <span class='flags c_" . $kom2['flag'] . "_14' style='vertical-align: middle;' title='" . $kom2['flag'] . "'></span></a><br>";



 
echo"</div>";
 if($kom2[id_admin] > 0){
			//VIP-Статус
              
$us2 = mysql_query("SELECT * FROM `users` WHERE `id`=$kom2[id_admin] LIMIT 1;");
$uss2 = @mysql_fetch_array($us2);   
	  if ($uss2[vip] == 0) {
                 echo'<span style="opacity:0.4"><a href="/vip.php?action=compare&amp;type=0"><img src="/images/ico/vip0_m.png" title="Базовый аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss2[name] . '</span></a>';
	}
 else   if ($uss2[vip] == 1) {
                echo'<span style="opacity:0.4"><a href="/vip.php?action=compare&amp;type=1"><img src="/images/ico/vip1_m.png" title="Улучшенный Премиум-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss2[name] . '</span></a>';
		}
  else  if ($uss2[vip] == 2) {
	echo'<span style="opacity:0.4"><a href="/vip.php?action=compare&amp;type=2"><img src="/images/ico/vip2_m.png" title="Улучшенный VIP-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss2[name] . '</span></a>';
	}
  else  if ($uss2[vip] == 3) {
		 echo'<span style="opacity:0.4"><a href="/vip.php?action=compare&amp;type=3"><img src="/images/ico/vip3_m.png" title="Представительский Gold-аккаунт" style="width: 12px;border: none;vertical-align: middle;">' . $uss2[name] . '</span></a> ';
			}
			
 }
 echo"</center></td>";



 
echo '</tr></table>';

















echo'<div style="display: flex;
    text-align: center;
    width: 100%;
    justify-content: center;
    align-items: center;">
                    <div class="tab-p but head_button" type="button" id="addteam">Расстановка</div>
                    <div class="tab-p but head_button" type="button" id="sostav">Составы</div>
                     <div class="tab-p but head_button" type="button" id="h2h">H2H</div>
                     <div class="tab-p but head_button" type="button" id="bets">Ставки</div>
                     <div class="tab-p but head_button" type="button" id="information">Информация</div>
                  
                </div>';
              
echo'<div id="addteamdiv" class="content">';

echo'<div class="phdr" style="text-align:center">Стартовый состав</div>';




					
echo'<div id="pagewrap"><div style="display: flex; justify-content: space-around;">';

	//////////////////////////tactics1


	$kk = @mysql_query("select * from `r_team` where id='" . $arr1[id] . "' LIMIT 1;");
	$kom = @mysql_fetch_array($kk);
	$totalkom = mysql_num_rows($kk);
		$igrok1 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i1']."'");
$i1 = mysql_fetch_array($igrok1);	
	$igrok2 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i2']."'");
$i2 = mysql_fetch_array($igrok2);	
	$igrok3 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i3']."'");
$i3 = mysql_fetch_array($igrok3);	
	$igrok4 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i4']."'");
$i4 = mysql_fetch_array($igrok4);	
	$igrok5 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i5']."'");
$i5 = mysql_fetch_array($igrok5);	
	$igrok6 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i6']."'");
$i6 = mysql_fetch_array($igrok6);	
	$igrok7 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i7']."'");
$i7 = mysql_fetch_array($igrok7);	
	$igrok8 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i8']."'");
$i8 = mysql_fetch_array($igrok8);	
	$igrok9 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i9']."'");
$i9 = mysql_fetch_array($igrok9);	
	$igrok10 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i10']."'");
$i10 = mysql_fetch_array($igrok10);	
	$igrok11 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i11']."'");
$i11 = mysql_fetch_array($igrok11);	

echo'



    <table class="schema_table2" border="0">';

switch ($arr1[shema])
{
	case "4-3-3":
	echo'<tbody>
    
                
                            
    <tr style="height:35%"><td colspan="4">
    <table border="0" style="height:100%; width:100%">
    <tbody>
	<tr>
	<td style="padding-top:10%"><div class="team_name2"><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td>
	<td style="padding-top:10%"><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>';
	if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';}
	
	echo'</a></td>
	<td style="padding-top:10%"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td>
	</tr>
    </tbody></table>
    </td></tr>
    
    <tr style="height:30%"><td colspan="4" style="">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr>
	<td style="padding-top:0%"><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td>
	<td style="padding-top:0%"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; 
	if($i7['utime']){
		echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';
		}
		switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}
	if($i7['utime']){
		echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';
		}else{
			echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';
			}echo'</a></td>
	<td style="padding-top:0%"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>
    
    <tr style="height:20%">
	<td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td>
	<td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td>
	<td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td>
	<td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:15%">
	<td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></div></td></tr>

   ';
				
    
   echo' </tbody>';
		break;
												
													
case "3-4-3":
echo'<tbody>
    
    <tr style="height:35%"><td style="padding-top:0%"><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>
	'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'
	
	</a></td><td style="padding-top:0%"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:30%"><td style="padding-top:0%" rowspan="2"><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td style="padding-top:0%"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td><td style="padding-top:0%" rowspan="2"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:5%"><td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:15%"><td colspan="3"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;

case "2-5-3":
echo'<tbody>
    
    <tr style="height:30%"><td colspan="4">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr><td style="padding-top:10%"><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td><td style="padding-top:10%"><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td><td style="padding-top:10%"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>
    
    <tr style="height:40%"><td style="padding-top:0%" rowspan="2"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td><td style="padding-top:0%"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td><td style="padding-top:0%"><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td style="padding-top:0%" rowspan="2"><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:5%"><td colspan="2"><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td colspan="4" style="">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr><td style=""><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td style=""><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>

    <tr style="height:15%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;


case "5-3-2":
echo'<tbody>
    
    <tr style="height:40%"><td colspan="4" style="">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr><td style=""><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td><td style=""><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td></tr>
    <tr><td style=""><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td style=""><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>
    
    <tr style="height:15%"><td colspan="4"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>

    <tr style="height:15%"><td colspan="4"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;

case "4-4-2":
echo'<tbody>
    
    <tr style="height:40%"><td colspan="4" style="">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr><td><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td><td><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>
    
    <tr style="height:10%"><td rowspan="2"><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td colspan="2"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td><td rowspan="2"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:10%"><td colspan="2"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:30%"><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:10%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;

case "3-5-2":
echo'<tbody>
    
    <tr style="height:25%"><td colspan="4" style="">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr><td><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td><td><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>
    
    <tr><td rowspan="3" valign="bottom"><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td colspan="2"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td><td rowspan="3" valign="bottom"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    <tr><td colspan="2"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td></tr>
    <tr><td colspan="2"><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td colspan="4">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td><td style=""><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>
    
    <tr style="height:10%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;

case "5-4-1":
echo'<tbody>
    
    <tr style="height:40%"><td colspan="4"><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td></tr>
    
    <tr style="height:15%"><td rowspan="2"><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td colspan="2"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td><td rowspan="2"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:5%"><td colspan="2"><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:15%"><td colspan="4"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:10%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;

case "4-5-1":
echo'<tbody>
    
    <tr style="height:35%"><td colspan="4" style=""><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td></tr>
    
    <tr style="height:10%"><td colspan="4" style=""><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:10%"><td rowspan="2"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td><td colspan="2"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td><td rowspan="2"><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:10%"><td colspan="2"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:20%"><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;


case "6-3-1":

	echo'<tbody>
    <tr style="height:45%"><td colspan="4"><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td></tr>
    
    <tr style="height:25%"><td><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td><td colspan="2"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:10%"><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:10%"><td colspan="2"><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td colspan="2"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:10%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="//images/forma/'.$kom[forma].'.gif"alt="">'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    </tbody>';
	break;
	
	
	
	
	
	
}
   echo' </table>
  <br> ';

//////////////////////////tactics1

		


//////////////////////////tactics2




	$kk = @mysql_query("select * from `r_team` where id='" . $arr2[id] . "' LIMIT 1;");
	$kom = @mysql_fetch_array($kk);
	$totalkom = mysql_num_rows($kk);
		$igrok1 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i1']."'");
$i1 = mysql_fetch_array($igrok1);	
	$igrok2 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i2']."'");
$i2 = mysql_fetch_array($igrok2);	
	$igrok3 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i3']."'");
$i3 = mysql_fetch_array($igrok3);	
	$igrok4 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i4']."'");
$i4 = mysql_fetch_array($igrok4);	
	$igrok5 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i5']."'");
$i5 = mysql_fetch_array($igrok5);	
	$igrok6 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i6']."'");
$i6 = mysql_fetch_array($igrok6);	
	$igrok7 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i7']."'");
$i7 = mysql_fetch_array($igrok7);	
	$igrok8 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i8']."'");
$i8 = mysql_fetch_array($igrok8);	
	$igrok9 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i9']."'");
$i9 = mysql_fetch_array($igrok9);	
	$igrok10 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i10']."'");
$i10 = mysql_fetch_array($igrok10);	
	$igrok11 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$kom['i11']."'");
$i11 = mysql_fetch_array($igrok11);	



echo'
    <table class="schema_table2" border="0">';

switch ($arr1[shema])
{
	case "4-3-3":
	echo'<tbody>
    
    <tr style="height:35%"><td colspan="4">
    <table border="0" style="height:100%; width:100%">
    <tbody>
	<tr>
	<td style="padding-top:10%"><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td>
	<td style="padding-top:10%"><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>';
	if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';}
	
	echo'</a></td>
	<td style="padding-top:10%"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td>
	</tr>
    </tbody></table>
    </td></tr>
    
    <tr style="height:30%"><td colspan="4" style="">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr>
	<td style="padding-top:0%"><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td>
	<td style="padding-top:0%"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; 
	if($i7['utime']){
		echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';
		}
		switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}
	if($i7['utime']){
		echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';
		}else{
			echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';
			}echo'</a></td>
	<td style="padding-top:0%"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>
    
    <tr style="height:20%">
	<td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td>
	<td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td>
	<td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td>
	<td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:15%">
	<td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>

   ';
				
    
   echo' </tbody>';
		break;
												
													
case "3-4-3":
echo'<tbody>
    
    <tr style="height:35%"><td style="padding-top:0%"><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>
	'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'
	
	</a></td><td style="padding-top:0%"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:30%"><td style="padding-top:0%" rowspan="2"><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td style="padding-top:0%"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td><td style="padding-top:0%" rowspan="2"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:5%"><td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:15%"><td colspan="3"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;

case "2-5-3":
echo'<tbody>
    
    <tr style="height:30%"><td colspan="4">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr><td style="padding-top:10%"><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td><td style="padding-top:10%"><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td><td style="padding-top:10%"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>
    
    <tr style="height:40%"><td style="padding-top:0%" rowspan="2"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td><td style="padding-top:0%"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td><td style="padding-top:0%"><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td style="padding-top:0%" rowspan="2"><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:5%"><td colspan="2"><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td colspan="4" style="">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr><td style=""><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td style=""><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>

    <tr style="height:15%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;


case "5-3-2":
echo'<tbody>
    
    <tr style="height:40%"><td colspan="4" style="">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr><td style=""><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td><td style=""><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td></tr>
    <tr><td style=""><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td style=""><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>
    
    <tr style="height:15%"><td colspan="4"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>

    <tr style="height:15%"><td colspan="4"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;

case "4-4-2":
echo'<tbody>
    
    <tr style="height:40%"><td colspan="4" style="">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr><td><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td><td><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>
    
    <tr style="height:10%"><td rowspan="2"><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td colspan="2"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td><td rowspan="2"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:10%"><td colspan="2"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:30%"><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:10%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;

case "3-5-2":
echo'<tbody>
    
    <tr style="height:25%"><td colspan="4" style="">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr><td><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td><td><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>
    
    <tr><td rowspan="3" valign="bottom"><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td colspan="2"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td><td rowspan="3" valign="bottom"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    <tr><td colspan="2"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td></tr>
    <tr><td colspan="2"><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td colspan="4">
    <table border="0" style="height:100%; width:100%">
    <tbody><tr><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td><td style=""><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>
    </tbody></table>
    </td></tr>
    
    <tr style="height:10%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;

case "5-4-1":
echo'<tbody>
    
    <tr style="height:40%"><td colspan="4"><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td></tr>
    
    <tr style="height:15%"><td rowspan="2"><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td colspan="2"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td><td rowspan="2"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:5%"><td colspan="2"><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:15%"><td colspan="4"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:10%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;

case "4-5-1":
echo'<tbody>
    
    <tr style="height:35%"><td colspan="4" style=""><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td></tr>
    
    <tr style="height:10%"><td colspan="4" style=""><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:10%"><td rowspan="2"><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td><td colspan="2"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td><td rowspan="2"><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:10%"><td colspan="2"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:20%"><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:15%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="/images/forma/59.gif" alt=""><br>'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    
    </tbody>';
break;


case "6-3-1":

	echo'<tbody>
    <tr style="height:45%"><td colspan="4"><a href="/player/'.$i11['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i11['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i11[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i11[yc].'</div>';}break;case "unchamp":if($i11[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i11[yc_unchamp].'</div>';}break;case "liga_r":if($i11[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i11[yc_liga_r].'</div>';}break;case "le":if($i11[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i11[yc_le].'</div>';}break;}if($i11['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i11['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i11['name']).'</span>';} echo'</a></td></tr>
    
    <tr style="height:25%"><td><a href="/player/'.$i6['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i8['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i8[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i8[yc].'</div>';}break;case "unchamp":if($i8[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i8[yc_unchamp].'</div>';}break;case "liga_r":if($i8[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i8[yc_liga_r].'</div>';}break;case "le":if($i8[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i8[yc_le].'</div>';}break;}if($i8['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i8['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i8['name']).'</span>';}echo'</a></td><td colspan="2"><a href="/player/'.$i7['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Cm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i9['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i9[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i9[yc].'</div>';}break;case "unchamp":if($i9[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i9[yc_unchamp].'</div>';}break;case "liga_r":if($i9[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i9[yc_liga_r].'</div>';}break;case "le":if($i9[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i9[yc_le].'</div>';}break;}if($i9['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i9['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i9['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i8['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rm)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i10['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i10[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i10[yc].'</div>';}break;case "unchamp":if($i10[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i10[yc_unchamp].'</div>';}break;case "liga_r":if($i10[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i10[yc_liga_r].'</div>';}break;case "le":if($i10[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i10[yc_le].'</div>';}break;}if($i10['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i10['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i10['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:10%"><td><a href="/player/'.$i2['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i4['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i4[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i4[yc].'</div>';}break;case "unchamp":if($i4[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i4[yc_unchamp].'</div>';}break;case "liga_r":if($i4[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i4[yc_liga_r].'</div>';}break;case "le":if($i4[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i4[yc_le].'</div>';}break;}if($i4['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i4['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i4['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i3['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Ld)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i5['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i5[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i5[yc].'</div>';}break;case "unchamp":if($i5[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i5[yc_unchamp].'</div>';}break;case "liga_r":if($i5[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i5[yc_liga_r].'</div>';}break;case "le":if($i5[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i5[yc_le].'</div>';}break;}if($i5['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i5['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i5['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i5['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i6['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i6[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i6[yc].'</div>';}break;case "unchamp":if($i6[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i6[yc_unchamp].'</div>';}break;case "liga_r":if($i6[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i6[yc_liga_r].'</div>';}break;case "le":if($i6[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i6[yc_le].'</div>';}break;}if($i6['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i6['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i6['name']).'</span>';}echo'</a></td><td><a href="/player/'.$i4['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rd)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i7['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i7[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i7[yc].'</div>';}break;case "unchamp":if($i7[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i7[yc_unchamp].'</div>';}break;case "liga_r":if($i7[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i7[yc_liga_r].'</div>';}break;case "le":if($i7[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i7[yc_le].'</div>';}break;}if($i7['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i7['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i7['name']).'</span>';}echo'</a></td></tr>
    <tr style="height:10%"><td colspan="2"><a href="/player/'.$i9['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Lf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i2['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i2[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i2[yc].'</div>';}break;case "unchamp":if($i2[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i2[yc_unchamp].'</div>';}break;case "liga_r":if($i2[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i2[yc_liga_r].'</div>';}break;case "le":if($i2[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i2[yc_le].'</div>';}break;}if($i2['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i2['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i2['name']).'</span>';}echo'</a></td><td colspan="2"><a href="/player/'.$i10['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Rf)"><img src="/images/forma/'.$kom[forma].'.gif" alt=""><br>'; if($i3['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i3[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i3[yc].'</div>';}break;case "unchamp":if($i3[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i3[yc_unchamp].'</div>';}break;case "liga_r":if($i3[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i3[yc_liga_r].'</div>';}break;case "le":if($i3[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i3[yc_le].'</div>';}break;}if($i3['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i3['name']).'</span>';}else{echo'<div class="team_name2"><span class="schema_plname">'.full_name_to_short($i3['name']).'</span>';}echo'</a></td></tr>
    
    <tr style="height:10%"><td colspan="4"><a href="/player/'.$i1['id'].'" class="schema_plink" title="Нажмите, чтобы заменить игрока (Gk)"><img src="//images/forma/'.$kom[forma].'.gif"alt="">'; if($i1['utime']){echo'<img src="/images/gen4/yrc.png" class="hint" title="Пропускает следующий матч из-за перебора желтых карточек" alt="">';}switch ($game[chemp]){case "champ_retro":if($i1[yc]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$i1[yc].'</div>';}break;case "unchamp":if($i1[yc_unchamp]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек">'.$i1[yc_unchamp].'</div>';}break;case "liga_r":if($i1[yc_liga_r]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в КЕЧ">'.$i1[yc_liga_r].'</div>';}break;case "le":if($i1[yc_le]){echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$i1[yc_le].'</div>';}break;}if($i1['utime']){echo'<div class="team_name2"><span class="__fio2">'.full_name_to_short($i1['name']).'</span>';}else{echo'<span class="schema_plname">'.full_name_to_short($i1['name']).'</span>';}echo'</a></td></tr>
    </tbody>';
	break;
	
	
	
	
	
	
}
   echo' </table>
';

//////////////////////////tactics2

		echo'</div>';
echo"</div>";

echo'</div>';
echo'<div id="betsdiv" class="content">';
///////////////bets games



echo'<link rel="stylesheet" href="/game/bets.css" type="text/css" />   ';
	
	$g6 = @mysql_query("select * from `r_game` where `id_match` = '" . $id . "' LIMIT 1;");
$game6 = @mysql_fetch_array($g6);

if($user_id)
{
$ratRes = mysql_fetch_array(mysql_query("SELECT `money` FROM `r_team` WHERE `id` = $datauser[manager2];"));
  $rat = $ratRes[$ratField];
}

if(!$user_id)
  header("Location: #");

if(empty($_GET['id']) || !ctype_digit($_GET['id']))
  header("Location: #");
$id = $_GET['id'];

$game5 = mysql_fetch_array(mysql_query("SELECT * FROM `t_games` WHERE `champ`='".$game['chemp']."' and `id_match` = ".$game['id_match'].";"));
if(!$game5 ){

			
			echo''.display_error('На этот матч нет ставок').'';
	
  // header("Location: #");
}
else{
$ddd = $game['time'] -60;
 
if($ddd > $realtime)

{
		// echo'Ставки закроют '.vaqt_b($ddd).'';
	// echo''.vaqt_b($game['time']).'';
 $teams = explode('|', $game5['teams']); $teamsCount = sizeof($teams);
  $coefs = explode('|', $game5['coefs']);

  echo '<div class="phdr"  style="text-align:center">Сделать ставку</div>';
// echo '<div class="gmenu">На вашем счету: ' . $ratRes[money] . '</div>';


  
		
if($ratRes[money] >= 10)
  {
    if($_POST['submit'])
    {
		
		
		
		

		
		
        $winner = FALSE;
        if(!empty($_POST['winner']) && ctype_digit($_POST['winner']))
            $winner = $_POST['winner'];
        $mil = FALSE;
if(!empty($_POST['mil']) && ctype_digit($_POST['mil']) && $_POST['mil'] >= 10 && $_POST['mil'] <= $ratRes[money])
            $mil = $_POST['mil'];

        if($winner && $mil)
        {
            $query = mysql_query("INSERT INTO `t_mils` VALUES(0, '" . $id . "', '" . $user_id . "', '" . $mil . "', '" . $winner . "');");
            if($query)
            {
				if($winner == 1){
					$aaa=''.$teams[0].' <b>П1</b> ';
				}
				elseif($winner == 2){
				$aaa=''.$teams[1].' <b>П2</b> ';
				}
				else{
				$aaa=''.$teams[0].'-'.$teams[1].' <b>Ничья</b> ';
				}
				
mysql_query("UPDATE `r_team` SET `money` = (`money` - $mil) where `id` = $datauser[manager2];");
mysql_query("insert into `news` set
`time`='".$realtime."',
`money`='-".$mil."',
`text`='Ставка  ".$aaa."',
`team_id`='" . $kom[id] . "'
;");           

		   header("Location: #");
            }
            else
                echo '<div class="rmenu">Произошла ошибка. Приносим вам свои извинения.</div>';
        }
        else
            echo '<div class="rmenu">Вы заполнили не все поля либо заполнили их не верно</div>';
    }
    echo '<div class="menu">';
    echo '<form action="?id=' . $id . '" method="POST">';
    // echo 'На кого вы ставите:<br/>';
	

echo'
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
                     <div  class="cardview x-text-center" style="display: flex; flex: 1 1 0%; position: relative; height: 30px;"><label>
                        <div class="factor-td--3ZZULU cell-state-normal--iYJc0x">
                           <div class="t--4zyb4K text-state-normal--1L40o3"><input type="radio" name="winner" value="1"/>'.$teams[0].'</div>
                           <div class="v--1iHcVX value-state-normal--4JL4xN">' . $coefs[0] . '</div>
                        </div>
                     </div>
                  </div>
				   </label>
                  <div class="cell-wrap--LHnTwg">
                     <div style="display: flex; flex: 1 1 0%; position: relative; height: 30px;"><label>
                        <div class="factor-td--3ZZULU cell-state-normal--iYJc0x">
                           <div class="t--4zyb4K text-state-normal--1L40o3"><input type="radio" name="winner" value="' . ($teamsCount + 1) . '"/>Ничья</div>
                           <div class="v--1iHcVX value-state-normal--4JL4xN">' . $coefs[$teamsCount] . '</div>
                        </div>
                     </div>
                  </div>
				   </label>
                  <div class="cell-wrap--LHnTwg">
                     <div style="display: flex; flex: 1 1 0%; position: relative; height: 30px;"><label>
                        <div class="factor-td--3ZZULU cell-state-normal--iYJc0x">
                           <div class="t--4zyb4K text-state-normal--1L40o3"><input type="radio" name="winner" value="2"/>
						   '.$teams[1].'</div>
                           <div class="v--1iHcVX value-state-normal--4JL4xN">' . $coefs[1] . '</div>
                        </div>
                     </div>
                  </div>
               </div>
             </label>
                 
                  <div class="cell-wrap--LHnTwg">
                     <div style="display: flex; flex: 1 1 0%; position: relative; height: 30px;">

                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

';

    // for($i = 0; $i < $teamsCount; $i++)
    // {
	// echo '<label><input type="radio" name="winner" value="' . ($i + 1) . '"/>' . $teams[$i] . ' (' . $coefs[$i] . ')</label><br/>';}
    // echo '<label><input type="radio" name="winner" value="' . ($teamsCount + 1) . '"/>Ничья (' . $coefs[$teamsCount] . ')</label>';

echo '<br/><label>Ставка: <input type="text" name="mil" value="10" maxlength="' . strlen($ratRes[money]) . '"/>(10-' . $ratRes[money] . ')</label><br/>';
    echo '<input type="submit" name="submit" value="Поставить"/>';
    echo '</form>';
    echo '</div>';
  }
  else
    echo '<div class="rmenu">У вас не достаточно денег для ставки! Минимальная ставка: 10 <img src="/images/m_game3.gif" class="money"></div>';
}
else{
  echo '<div class="rmenu">Приём ставок окончен</div>';
}
     if(!$history)
        {
     	$g65 = @mysql_query("SELECT * FROM `t_mils` WHERE `user`='".$user_id."' and `refid` = " . $game['id'] . "");

       $allMils = mysql_num_rows(mysql_query("SELECT * FROM `t_mils` WHERE `user`='".$user_id."' and `refid` = " . $game['id'] . ";"));

echo'<center>';
echo '<div class="gmenu">Всего ставок: ' . $allMils . '</div>';

 while($gamerrr = mysql_fetch_array($g65))
  {
echo'
<tr class="coupon__table-row--6OoyA1">
   <td class="coupon__table-col--3p8NRM" colspan="2">
      <span class="coupon__sport-icon--4VDiOV _use_color_settings--SQwskl" style="background-image: url(&quot;//origin.bk6bba-resources.com/ContentCommon/Logotypes/SportKinds/new-design/white_new/1-football.svg&quot;);"></span>
      <span>'.$game[name_team1].' – '.$game[name_team2].'</span>
   </td>
   <td class="coupon__table-col--3p8NRM _type_stake--4UOCA4">
      <span><b>';
      if($gamerrr[winner] == 1)
      echo'П1';
      elseif($gamerrr[winner] == 2)
      echo'П2';
      else
      echo'Ничья';
      echo'</b></span>';
	  
	   echo' '.$gamerrr[mil].' <img src="/images/m_game3.gif" class="money">';
   echo'</td>
   <td class="coupon__table-col--3p8NRM coupon__table-status--77SBfz _type_factor-value--5U80LG _type_label--4DkPHs _status_lose--h4Xx4x" title="Пари не сыграло. (1:0)">
      <span class="coupon__table-stake--PyBpdc">';
  if($gamerrr[winner] == 1){
		$coefs[0];}
      elseif($gamerrr[winner] == 2){
      $coefs[1];
	  }
  else{
     $coefs[2];
}
	  
	  
	  echo'</span>';
	  
	     	echo'
   </td>
</tr>
';

// echo'
// <div class="coupon__info--630O8g">
   // <div class="coupon__info-head--6Vvxw5">
      // <div class="coupon__info-item-inner--yZFTIb">
         // <div class="coupon__info-item--7bN5Q9" title="Тип пари"><i class="coupon__icon--3foYqw _type_info--4M4Sar _icon_info-black--3zl3SC"></i><i class="coupon__info-text--1McvPX">Экспресс</i></div>
         // <div class="coupon__info-item--7bN5Q9" title="Общий коэффициент"><i class="icon--5s9fxK _icon_at-black--1tmMQN coupon__icon--3foYqw _type_info--4M4Sar"></i><i class="coupon__info-text--1McvPX">5.43</i></div>
      // </div>
      // <div class="coupon__info-item--7bN5Q9 _type_items--2iVOPQ _lose--7bkdvA _withoutWinAmount--erLpry"><i class="coupon__icon--3foYqw _type_info--4M4Sar _type_win--3qGDku _icon-coupon-lose--8t9QrS"></i><i class="coupon__info-text--1McvPX base-amount--2mYDrF"><span>'.$gamerrr[mil].'</span></i></div>
      // <div class="coupon__info-label--1NOYoS _style_loose--46YakN _style_colored--8dYqV3"><span>Проигрыш</span>
      // </div>
   // </div>
   // <div></div>
// </div>
// ';
	

	 
	 echo'<br>';

  }
  echo'</center>';
		}
		

	echo'<br>';
}







///////////////bets games
echo'</div>';
echo'<div id="h2hdiv" class="content">';
///////////////History games
require_once ("../game/history3.php");
///////////////History games
echo'</div>';
echo'<div id="informationdiv" class="content">';
///////////////information games



///////////////stadion
$std11 = mysql_query("SELECT * FROM `r_stadium` where `id`='".$game[id_stadium]."' ;");
            $std11 = mysql_fetch_array($std11);
			if($game[id_stadium]){

////////////////stdion
echo'<div class="game-ui__history">
                                                    <div style="float: left; margin-right: 40px;">';
														if($std11[std]){
															
                    echo'            <img src="/images/stadium/'.$game[id_stadium].'.jpg" style="width: 480px; height: 240px; border: 1px solid var(--primary-color-border); margin-top:7px;" alt="">';
														}     
else{
  echo' <img src="/images/stadium/stadium.jpg" style="width: 480px; height: 240px; border: 1px solid var(--primary-color-border); margin-top:7px;" alt="">';
							
}	

			echo'			   </div>
                                                <div style="font-size:140%;margin-top:20px;">Место проведения матча</div>
                        <div style="font-size:170%;">'.$std11[name].'</div>
                        <div style="font-size:160%;color:green;">'.$game[zritel].' зрителей</div>';
						if($game[chemp]='!frend'){
                      echo'  <div>город '.$std11[city].'</div>';
						}
                    echo'</div>';
////////////////stdion
	
			}
///////////////stadion
$j1 = @mysql_query("select * from `r_team` where id='" . $game[id_team1] . "' LIMIT 1;");
$jam1 = @mysql_fetch_array($j1);

$j2 = @mysql_query("select * from `r_team` where id='" . $game[id_team2] . "' LIMIT 1;");
$jam2 = @mysql_fetch_array($j2);

///////////////information games
echo'</div>';



echo'<div id="sostavdiv" class="content">';
echo '<div class="phdr orangebk"><center><b>Состав</b></center></div>';



echo'<table id="example" class="t-table">';
	
	
echo '<tr bgcolor="40B832" align="center" class="whiteheader" >';
echo '<td><b>'.$jam1[name].'</b></td><td><b>'.$jam2[name].'</b></td></tr>';

echo'<tr>';
$rq = mysql_query("SELECT * FROM `r_player` where `team`='".$jam1[id]."' and (`id`='".$jam1['i1']."' or `id`='".$jam1['i2']."' or `id`='".$jam1['i3']."' or `id`='".$jam1['i4']."' or `id`='".$jam1['i5']."' or `id`='".$jam1['i6']."' or `id`='".$jam1['i7']."' or `id`='".$jam1['i8']."' or `id`='".$jam1['i9']."' or `id`='".$jam1['i10']."' or `id`='".$jam1['i11']."')  and `sostav`!='4'  order by line asc, poz asc;");

echo '<td width="50%">';
$d = 1;
while ($parr1 = mysql_fetch_array($rq))
{
		if($datauser[black] == 0){
if($parr1[line] == 1){
echo '<div style="background-color:#fff7e7" class="gmenu2">';
}else if($parr1[line] == 2){
echo '<div style="background-color:#f7ffef" class="gmenu2">';
}else if($parr1[line] == 3){
echo '<div style="background-color:#e7f7ff" class="gmenu2">';
}else if($parr1[line] == 4){
echo '<div style="background-color:#ffefef" class="gmenu2">';
}
		}
		else{
	if($parr1[line] == 1){
echo '<div style="background-color:#434343" class="gmenu2">';
}else if($parr1[line] == 2){
echo '<div style="background-color:#363636" class="gmenu2">';
}else if($parr1[line] == 3){
echo '<div style="background-color:#262525" class="gmenu2">';
}else if($parr1[line] == 4){
echo '<div style="background-color:#1e1e1e" class="gmenu2">';
}		
			
		}
		
echo '<span class="flags c_'.$parr1[flag].'_18" style="vertical-align: middle;" title="'.$parr1[flag].'"></span> ';
echo '<b>'.$parr1[poz].'</b> ';
echo'<a href="/player/'.$parr1['id'].'">'.$parr1['name'].' ';
	
		 switch ($game[chemp])
{
case "champ_retro":
if($parr1['yc'] > 0){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$parr1[yc].'</div>';
}
break;
case "unchamp":
if($parr1['yc_unchamp'] > 0){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Союзном Чемпионате">'.$parr1[yc_unchamp].'</div>';
}
break;
case "liga_r":
if($parr1['yc_liga_r'] > 0){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Ретро Кубке Чемпионов">'.$parr1[yc_liga_r].'</div>';
}
break;
case "le":
if($parr1['yc_le'] > 0){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$parr1[yc_le].'</div>';
}
break;
if($parr1['rc'] > 0){
echo'<div class="player-cards-2" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$parr1[rc].'</div>';
}

	 }
echo'</a>';
echo '</div>';
++$d;
}
echo '</td>';

$rq2 = mysql_query("SELECT * FROM `r_player` where `team`='".$jam2[id]."' and (`id`='".$jam2['i1']."' or `id`='".$jam2['i2']."' or `id`='".$jam2['i3']."' or `id`='".$jam2['i4']."' or `id`='".$jam2['i5']."' or `id`='".$jam2['i6']."' or `id`='".$jam2['i7']."' or `id`='".$jam2['i8']."' or `id`='".$jam2['i9']."' or `id`='".$jam2['i10']."' or `id`='".$jam2['i11']."')  and `sostav`!='4' order by line asc, poz asc;");
echo '<td width="50%">';
$d = 1;
while ($parr2 = mysql_fetch_array($rq2))
{
		if($datauser[black] == 0){
if($parr2[line] == 1){
echo '<div style="background-color:#fff7e7" class="gmenu2">';
}else if($parr2[line] == 2){
echo '<div style="background-color:#f7ffef" class="gmenu2">';
}else if($parr2[line] == 3){
echo '<div style="background-color:#e7f7ff" class="gmenu2">';
}else if($parr2[line] == 4){
echo '<div style="background-color:#ffefef" class="gmenu2">';
}
}
		else{
	if($parr2[line] == 1){
echo '<div style="background-color:#434343" class="gmenu2">';
}else if($parr2[line] == 2){
echo '<div style="background-color:#363636" class="gmenu2">';
}else if($parr2[line] == 3){
echo '<div style="background-color:#262525" class="gmenu2">';
}else if($parr2[line] == 4){
echo '<div style="background-color:#1e1e1e" class="gmenu2">';
}		
			
		}
		echo '<span class="flags c_'.$parr2[flag].'_18" style="vertical-align: middle;" title="'.$parr2[flag].'"></span> ';
echo '<b>'.$parr2[poz].'</b> <a href="/player/'.$parr2['id'].'">'.$parr2['name'].' ';
		 switch ($game[chemp])
{
case "champ_retro":
if($parr2['yc'] > 0){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$parr2[yc].'</div>';
}
break;
case "unchamp":
if($parr2['yc_unchamp'] > 0){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Союзном Чемпионате">'.$parr2[yc_unchamp].'</div>';
}
break;
case "liga_r":
if($parr2['yc_liga_r'] > 0){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Ретро Кубке Чемпионов">'.$parr2[yc_liga_r].'</div>';
}
break;
case "le":
if($parr2['yc_le'] > 0){
echo'<div class="player-cards-1" title="Кол-во НЕ сгоревших желтых карточек в Кубке УЕФА">'.$parr2[yc_le].'</div>';
}
break;
if($parr2['rc'] > 0){
echo'<div class="player-cards-2" title="Кол-во НЕ сгоревших желтых карточек в Чемпионате">'.$parr2[rc].'</div>';
}

	 }
echo'</a>';
echo '</div>';
++$d;
}
echo '</td>';



echo '</tr></table>';
echo'</div>';









echo '<meta http-equiv="refresh" content="60;url=/game'.$dirs.''.$id.'"/>';

echo '<center><div class="info">Матч начнется через: '.date("i:s", $ostime).'</div></center>';
if ($datauser[manager2] == $game[id_team1] or $datauser[manager2] == $game[id_team2])
{
echo '<br/><center><form action="/team/sostav.php"><input type="submit" title="Нажмите для изменения состава" name="submit" value="Изменить состав"/></form>';
echo '<form action="/team/tactic.php"><input type="submit" title="Нажмите для изменения тактики" name="submit" value="Изменить тактику"/></form></center><br/>';
}











	////////////////////////Убираем игрока с дисквалификацией из состава/////////////////////
	 if ($game[chemp] == 'champ_retro'){
		 
$test1 = mysql_query("SELECT * FROM `r_player` where (`id`='".$jam1['i1']."' or `id`='".$jam1['i2']."' or `id`='".$jam1['i3']."' or `id`='".$jam1['i4']."' or `id`='".$jam1['i5']."' or `id`='".$jam1['i6']."' or `id`='".$jam1['i7']."' or `id`='".$jam1['i8']."' or `id`='".$jam1['i9']."' or `id`='".$jam1['i10']."' or `id`='".$jam1['i11']."') AND `team`='".$jam1['id']."' and `team`='".$jam1['id']."'  limit 11 ");
//$eee = mysql_fetch_array($test);


  
	   while ($pidr = mysql_fetch_array($test1))
{ if ($pidr[utime] > 0) {
	
                            mysql_query("update `r_player` set `sostav`='4' where `id`='" . $pidr['id'] . "';");
                            mysql_query("update `r_team` set `i1`='' where `i1`='" . $pidr['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $pidr['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $pidr['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $pidr['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $pidr['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $pidr['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $pidr['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $pidr['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $pidr['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $pidr['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $pidr['id'] . "';");
                  echo'<div class ="error">Мы убрали '.$pidr['name'].' из состава. У него дисквалификация</div>'; 
						}
   }
					
$test2 = mysql_query("SELECT * FROM `r_player` where (`id`='".$jam2['i1']."' or `id`='".$jam2['i2']."' or `id`='".$jam2['i3']."' or `id`='".$jam2['i4']."' or `id`='".$jam2['i5']."' or `id`='".$jam2['i6']."' or `id`='".$jam2['i7']."' or `id`='".$jam2['i8']."' or `id`='".$jam2['i9']."' or `id`='".$jam2['i10']."' or `id`='".$jam2['i11']."') AND `team`='".$jam2['id']."' LIMIT 11");
//$eee = mysql_fetch_array($test);


  
	   while ($pidr2 = mysql_fetch_array($test2))
{ if ($pidr2[utime] > 0) {
                            mysql_query("update `r_player` set `sostav`='4' where `id`='" . $pidr2['id'] . "';");
                            mysql_query("update `r_team` set `i1`='' where `i1`='" . $pidr2['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $pidr2['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $pidr2['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $pidr2['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $pidr2['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $pidr2['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $pidr2['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $pidr2['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $pidr2['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $pidr2['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $pidr2['id'] . "';");
                  echo'<div class ="error">Мы убрали '.$pidr2['name'].' из состава. У него дисквалификация</div>'; 
						}
   }
	 }
		////////////////////////Убираем игрока с дисквалификацией из состава/////////////////////

	


	
	//////////////////////autosostav

	

$result =  @mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$jam1['i1']."' or `id`='".$jam1['i2']."' or `id`='".$jam1['i3']."' or `id`='".$jam1['i4']."' or `id`='".$jam1['i5']."' or `id`='".$jam1['i6']."' or `id`='".$jam1['i7']."' or `id`='".$jam1['i8']."' or `id`='".$jam1['i9']."' or `id`='".$jam1['i10']."' or `id`='".$jam1['i11']."') AND `team`='".$jam1['id']."' and `sostav` !='4' ");
 // $result =  @mysql_query("SELECT * FROM `r_player` WHERE `sostav`='1' AND `team`='".$jam1['id']."' LIMIT 11");
 // $result = @mysql_query("SELECT * FROM `r_player` WHERE `sostav` ='0' AND `team`='".$jam1['id']."' and `sostav` !='4' LIMIT 11");
 $myrow = mysql_fetch_row($result); 


 if ($myrow < 11)
 {
echo $jam1['name'].' У вас меньше 11 игроков<br>';
 
	
$sql = mysql_query("SELECT * FROM `r_team` WHERE `id`='".$game['id_team1']."' LIMIT 1");
	if(mysql_num_rows($sql))
	{
		$team = mysql_fetch_assoc($sql);
		for($i = 1; $i <= 11; $i++)
		{
			if(!$team['i'.$i])
			{
				if($i==1)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' and `line`='1' and `sostav`='0' order by `rm` desc limit 1");
				}
				elseif($i==2 || $i==3 || $i==4)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' and `line`='2'  and `sostav`='0' order by `rm` desc limit 1");				
				}
				elseif($i==5 || $i==6 || $i==7 || $i==8 || $i==9)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' and `line`='3'  and `sostav`='0' order by `rm` desc limit 1");				
				}
				elseif($i==10 || $i==11)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' and `line`='4' and `sostav`='0' order by `rm` desc limit 1");					
				}
				
				 if(!mysql_num_rows($sql))
				{
				$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' and `sostav`='0' and `line`!='1' order by `rm` limit 1");
						// $sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team1']."' and `sostav`='0' order by `rm` limit 1");
				} 
				
				$player = mysql_fetch_assoc($sql);
				
				mysql_query("UPDATE `r_team` SET `i".$i."`='".$player[id]."' WHERE `id`='".$game['id_team1']."' LIMIT 1");
				 mysql_query("UPDATE `r_team` SET `i$i`='' WHERE `id`!='$id' LIMIT 1");
				mysql_query("UPDATE `r_player` SET `sostav`='1' WHERE `id`='".$player[id]."' LIMIT 1");
				mysql_query("UPDATE `r_player` SET `sostav`='0' WHERE `id`!='$player[id]' LIMIT 1");
// echo'автобалансировка составов <br>';
			}
		}
	}
	



 }
 
$result2 =  @mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$jam2['i1']."' or `id`='".$jam2['i2']."' or `id`='".$jam2['i3']."' or `id`='".$jam2['i4']."' or `id`='".$jam2['i5']."' or `id`='".$jam2['i6']."' or `id`='".$jam2['i7']."' or `id`='".$jam2['i8']."' or `id`='".$jam2['i9']."' or `id`='".$jam2['i10']."' or `id`='".$jam2['i11']."') AND `team`='".$jam2['id']."' and `sostav` !='4' ");
 // $result2 = @mysql_query("SELECT * FROM `r_player` WHERE `sostav` ='0' AND `team`='".$jam2['id']."' and `sostav` !='4' LIMIT 11");
 // $result2 =  @mysql_query("SELECT * FROM `r_player` WHERE `sostav`='1' AND `team`='".$jam2['id']."' LIMIT 11");
 $myrow2 = mysql_fetch_row($result2); 


 if ($myrow2 < 11)
 {
	echo $jam2['name'].' У вас меньше 11 игроков<br>';
 
	$sql = mysql_query("SELECT * FROM `r_team` WHERE `id`='".$game['id_team2']."' LIMIT 1");
	if(mysql_num_rows($sql))
	{
		$team = mysql_fetch_assoc($sql);
		for($i = 1; $i <= 11; $i++)
		{
			if(!$team['i'.$i])
			{
				if($i==1)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' and `line`='1' and `sostav`='0' order by `rm` desc limit 1");
				}
				elseif($i==2 || $i==3 || $i==4)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' and `line`='2'  and `sostav`='0' order by `rm` desc limit 1");				
				}
				elseif($i==5 || $i==6 || $i==7 || $i==8 || $i==9)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' and `line`='3'  and `sostav`='0' order by `rm` desc limit 1");				
				}
				elseif($i==10 || $i==11)
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' and `line`='4' and `sostav`='0' order by `rm` desc limit 1");					
				}
				
				 if(!mysql_num_rows($sql))
				{
					$sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' and `sostav`='0' and `line`!='1' order by `rm` limit 1");
					// $sql = mysql_query("SELECT `id` FROM `r_player` WHERE `team`='".$game['id_team2']."' and `sostav`='0' order by `rm` limit 1");
				} 
				
				$player = mysql_fetch_assoc($sql);
				
				mysql_query("UPDATE `r_team` SET `i".$i."`='".$player[id]."' WHERE `id`='".$game['id_team2']."' LIMIT 1");
				mysql_query("UPDATE `r_team` SET `i$i`='' WHERE `id`!='$id' LIMIT 1");
				mysql_query("UPDATE `r_player` SET `sostav`='1' WHERE `id`='".$player[id]."' LIMIT 1");
				mysql_query("UPDATE `r_player` SET `sostav`='0' WHERE `id`!='$player[id]' LIMIT 1");
// echo'автобалансировка составов <br>';
			}
		}
}


 }







		

++$kmess;
++$stgame;
++$i;
require_once ("../incfiles/end.php");
exit;
}



}


$q1 = @mysql_query("select * from `r_team` where id='" . $game[id_team1] . "' LIMIT 1;");
$count1 = mysql_num_rows($q1);
$arr1 = @mysql_fetch_array($q1);

$q2 = @mysql_query("select * from `r_team` where id='" . $game[id_team2] . "' LIMIT 1;");
$count2 = mysql_num_rows($q2);
$arr2 = @mysql_fetch_array($q2);

if($game['tactics1']=='' and $game['tactics2']=='')
{
    
    $text = $text.''.func_text(twist_one,1,01,$arr1['name']).'\r\n';
    $text = $text.''.func_text(twist_two,1,46,$arr1['name']).'\r\n';
    $text = $text.''.func_text(finish_one,1,45,$arr1['name']).'\r\n';
    $text = $text.''.func_text(finish_two,1,90,$arr1['name']).'\r\n';
// if ($rezult[0] == $rezult[1])
    // {
	// $text = $text.''.func_text(twist_three,91,91,$arr1['name']).'\r\n';
    // $text = $text.''.func_text(twist_four,91,106,$arr1['name']).'\r\n';
	// $text = $text.''.func_text(finish_three,91,105,$arr1['name']).'\r\n';
    // $text = $text.''.func_text(finish_four,91,120,$arr1['name']).'\r\n';
	// $text = $text.''.func_text(fiks_two,91,122,$arr1['name']).'\r\n';
	
	// }
    $text = $text.''.func_text(fiks,1,93,$arr1['name']).'\r\n';
	
    
///////////////////////////////////////////////////////////////////
//////////////////          РАСЧЕТ СЧЕТА          ////////////////////////
///////////////////////////////////////////////////////////////////
///антистратегия
$st1=0;
	$sp1=0;
	$sk1=0;
		if($arr1['strat']==0 AND $arr2['strat']==3){
			$st1=2;
		}elseif($arr1['strat']==1 AND $arr2['strat']==0){
			$st1=2;
		}elseif($arr1['strat']==2 AND $arr2['strat']==1){
			$st1=2;
		}elseif($arr1['strat']==3 AND $arr2['strat']==2){
			$st1=2;
		}elseif($arr2['strat']==0 AND $arr1['strat']==3){
			$st1=-2;
		}elseif($arr2['strat']==1 AND $arr1['strat']==0){
			$st1=-2;
		}elseif($arr2['strat']==2 AND $arr1['strat']==1){
			$st1=-2;
		}elseif($arr2['strat']==3 AND $arr1['strat']==2){
			$st1=-2;
		}
		
		if($arr1['pas']==0 AND $arr2['pas']==1){
			$sp1=2;
		}elseif($arr1['pas']==1 AND $arr2['pas']==2){
			$sp1=2;
		}elseif($arr1['pas']==2 AND $arr2['pas']==0){
			$sp1=2;
		}elseif($arr2['pas']==0 AND $arr1['pas']==1){
			$sp1=-2;
		}elseif($arr2['pas']==1 AND $arr1['pas']==2){
			$sp1=-2;
		}elseif($arr2['pas']==2 AND $arr1['pas']==0){
			$sp1=-2;
		}
		
		if($arr1['tactic']==100 AND $arr2['tactic']==10){
			$sk1=3;
		}elseif($arr1['tactic']==90 AND $arr2['tactic']==10){
			$sk1=1;
		}elseif($arr1['tactic']==100 AND $arr2['tactic']==20){
			$sk1=1;
		}elseif($arr1['tactic']==90 AND $arr2['tactic']==20){
			$sk1=3;
		}elseif($arr1['tactic']==80 AND $arr2['tactic']==60){
			$sk1=3;
		}elseif($arr1['tactic']==80 AND $arr2['tactic']==50){
			$sk1=1;
		}elseif($arr1['tactic']==70 AND $arr2['tactic']==60){
			$sk1=1;
		}elseif($arr1['tactic']==70 AND $arr2['tactic']==50){
			$sk1=3;
		}elseif($arr1['tactic']==60 AND $arr2['tactic']==40){
			$sk1=3;
		}elseif($arr1['tactic']==60 AND $arr2['tactic']==30){
			$sk1=1;
		}elseif($arr1['tactic']==50 AND $arr2['tactic']==40){
			$sk1=1;
		}elseif($arr1['tactic']==50 AND $arr2['tactic']==30){
			$sk1=3;
		}elseif($arr1['tactic']==40 AND $arr2['tactic']==90){
			$sk1=3;
		}elseif($arr1['tactic']==30 AND $arr2['tactic']==100){
			$sk1=3;
		}elseif($arr1['tactic']==40 AND $arr2['tactic']==100){
			$sk1=1;
		}elseif($arr1['tactic']==30 AND $arr2['tactic']==90){
			$sk1=1;
		}elseif($arr1['tactic']==20 AND $arr2['tactic']==70){
			$sk1=3;
		}elseif($arr1['tactic']==10 AND $arr2['tactic']==80){
			$sk1=3;
		}elseif($arr1['tactic']==20 AND $arr2['tactic']==80){
			$sk1=1;
		}elseif($arr1['tactic']==10 AND $arr2['tactic']==70){
			$sk1=1;
		}elseif($arr2['tactic']==100 AND $arr1['tactic']==10){
			$sk1=-3;
		}elseif($arr2['tactic']==90 AND $arr1['tactic']==10){
			$sk1=-1;
		}elseif($arr2['tactic']==100 AND $arr1['tactic']==20){
			$sk1=-1;
		}elseif($arr2['tactic']==90 AND $arr1['tactic']==20){
			$sk1=-3;
		}elseif($arr2['tactic']==80 AND $arr1['tactic']==60){
			$sk1=-3;
		}elseif($arr2['tactic']==80 AND $arr1['tactic']==50){
			$sk1=-5;
		}elseif($arr2['tactic']==70 AND $arr1['tactic']==60){
			$sk1=-1;
		}elseif($arr2['tactic']==70 AND $arr1['tactic']==50){
			$sk1=-3;
		}elseif($arr2['tactic']==60 AND $arr1['tactic']==40){
			$sk1=-3;
		}elseif($arr2['tactic']==60 AND $arr1['tactic']==30){
			$sk1=-1;
		}elseif($arr2['tactic']==50 AND $arr1['tactic']==40){
			$sk1=-1;
		}elseif($arr2['tactic']==50 AND $arr1['tactic']==30){
			$sk1=-3;
		}elseif($arr2['tactic']==40 AND $arr1['tactic']==90){
			$sk1=-3;
		}elseif($arr2['tactic']==30 AND $arr1['tactic']==100){
			$sk1=-3;
		}elseif($arr2['tactic']==40 AND $arr1['tactic']==100){
			$sk1=-1;
		}elseif($arr2['tactic']==30 AND $arr1['tactic']==90){
			$sk1=-1;
		}elseif($arr2['tactic']==20 AND $arr1['tactic']==70){
			$sk1=-3;
		}elseif($arr2['tactic']==10 AND $arr1['tactic']==80){
			$sk1=-3;
		}elseif($arr2['tactic']==20 AND $arr1['tactic']==80){
			$sk1=-1;
		}elseif($arr2['tactic']==10 AND $arr1['tactic']==70){
			$sk1=-1;
		}
		
		
		
//СИЛА 1 команда

		$igrok1 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr1['i1']."'");
$i1 = mysql_fetch_array($igrok1);	
	$igrok2 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr1['i2']."'");
$i2 = mysql_fetch_array($igrok2);	
	$igrok3 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr1['i3']."'");
$i3 = mysql_fetch_array($igrok3);	
	$igrok4 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr1['i4']."'");
$i4 = mysql_fetch_array($igrok4);	
	$igrok5 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr1['i5']."'");
$i5 = mysql_fetch_array($igrok5);	
	$igrok6 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr1['i6']."'");
$i6 = mysql_fetch_array($igrok6);	
	$igrok7 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr1['i7']."'");
$i7 = mysql_fetch_array($igrok7);	
	$igrok8 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr1['i8']."'");
$i8 = mysql_fetch_array($igrok8);	
	$igrok9 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr1['i9']."'");
$i9 = mysql_fetch_array($igrok9);	
	$igrok10 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr1['i10']."'");
$i10 = mysql_fetch_array($igrok10);	
	$igrok11 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr1['i11']."'");
$i11 = mysql_fetch_array($igrok11);	






///////Сила игроков 1
$s1=$i1['mas']*$i1['fiz']/100;
$s2=$i2['mas']*$i2['fiz']/100;
$s3=$i3['mas']*$i3['fiz']/100;
$s4=$i4['mas']*$i4['fiz']/100;
$s5=$i5['mas']*$i5['fiz']/100;
$s6=$i6['mas']*$i6['fiz']/100;
$s7=$i7['mas']*$i7['fiz']/100;
$s8=$i8['mas']*$i8['fiz']/100;
$s9=$i9['mas']*$i9['fiz']/100;
$s10=$i10['mas']*$i10['fiz']/100;
$s11=$i11['mas']*$i11['fiz']/100;

/////////////////ПОДКУПИТЬ СУДЬЮ
$s12=1000;
/////////////////ПОДКУПИТЬ СУДЬЮ

////оптимальность схемы 1

	$G=1;
			if($arr1['shema']== "4-3-3"){
				
				$D=4;
				$M=3;
				$F=3;
			}elseif($arr1['shema']== "3-4-3"){
				$D=3;
				$M=4;
				$F=3;
			}elseif($arr1['shema']== "2-5-3"){
				$D=2;
				$M=5;
				$F=3;
			}
			elseif($arr1['shema']== "5-3-2"){
				$D=5;
				$M=3;
				$F=2;
			}
			elseif($arr1['shema']== "4-4-2"){
				$D=4;
				$M=4;
				$F=2;
			}
			elseif($arr1['shema']== "3-5-2"){
				$D=3;
				$M=5;
				$F=2;
			}
			elseif($arr1['shema']== "6-3-1"){
				$D=6;
				$M=3;
				$F=1;
			}
			elseif($arr1['shema']== "5-4-1"){
				$D=5;
				$M=4;
				$F=1;
			}
			elseif($arr1['shema']== "4-5-1"){
				$D=4;
				$M=5;
				$F=1;
			}

$G1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='1' ");
$TG1 = mysql_num_rows($G1);
$D1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='2' ");
$TD1 = mysql_num_rows($D1);
$M1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='3' ");
$TM1 = mysql_num_rows($M1);
$F1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='4' ");
$TF1 = mysql_num_rows($F1);

		
		if($G==0 or $TG1==0){
			$pg=0;
		}
		elseif($G<=$TG1){
		$pg=$G/$TG1;
		}elseif($TG1<=$G){
			$pg=$TG1/$G;
		}
		
		
		if($D==0 or $TD1==0){
			$pd=0;
		}
		elseif($D<=$TD1){
		$pd=$D/$TD1;
		}elseif($TD1<=$D){
			$pd=$TD1/$D;
		}
		
		
	if($M==0 or $TM1==0){
			$pm=0;
		}
		elseif($M<=$TM1){
		$pm=$M/$TM1;
		}elseif($TM1<=$M){
			$pm=$TM1/$M;
		}
		
			if($F==0 or $TF1==0){
			$pf=0;
		}
		elseif($F<=$TF1){
		$pf=$F/$TF1;
		}elseif($TF1<=$F){
			$pf=$TF1/$F;
		}
		
		
		$p1=($pg+$pd+$pm+$pf)/4;
		$p1=1;		
		
		
		
///сила  команды 1
/////////////////ПОДКУПИТЬ СУДЬЮ
if($arr1['ref']){
	$sila1 = $s1+$s2+$s3+$s4+$s5+$s6+$s7+$s8+$s9+$s10+$s11+$s12;
}
/////////////////ПОДКУПИТЬ СУДЬЮ
else{
$sila1 = $s1+$s2+$s3+$s4+$s5+$s6+$s7+$s8+$s9+$s10+$s11;
}
$allsila1=$sila1*$p1;

////////////////////////////////////////////////////////////////////////////////////////
//////////////команда 2
	$aigrok1 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr2['i1']."'");
$a1 = mysql_fetch_array($aigrok1);	
	$aigrok2 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr2['i2']."'");
$a2 = mysql_fetch_array($aigrok2);	
	$aigrok3 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr2['i3']."'");
$a3 = mysql_fetch_array($aigrok3);	
	$aigrok4 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr2['i4']."'");
$a4 = mysql_fetch_array($aigrok4);	
	$aigrok5 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr2['i5']."'");
$a5 = mysql_fetch_array($aigrok5);	
	$aigrok6 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr2['i6']."'");
$a6 = mysql_fetch_array($aigrok6);	
	$aigrok7 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr2['i7']."'");
$a7 = mysql_fetch_array($aigrok7);	
	$aigrok8 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr2['i8']."'");
$a8 = mysql_fetch_array($aigrok8);	
	$aigrok9 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr2['i9']."'");
$a9 = mysql_fetch_array($aigrok9);	
	$aigrok10 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr2['i10']."'");
$a10 = mysql_fetch_array($aigrok10);	
	$aigrok11 = mysql_query("SELECT * FROM `r_player` WHERE `id`='".$arr2['i11']."'");
$a11 = mysql_fetch_array($aigrok11);	

///////Сила игроков
$as1=$a1['mas']*$a1['fiz']/100;
$as2=$a2['mas']*$a2['fiz']/100;
$as3=$a3['mas']*$a3['fiz']/100;
$as4=$a4['mas']*$a4['fiz']/100;
$as5=$a5['mas']*$a5['fiz']/100;
$as6=$a6['mas']*$a6['fiz']/100;
$as7=$a7['mas']*$a7['fiz']/100;
$as8=$a8['mas']*$a8['fiz']/100;
$as9=$a9['mas']*$a9['fiz']/100;
$as10=$a10['mas']*$a10['fiz']/100;
$as11=$a11['mas']*$a11['fiz']/100;

/////////////////ПОДКУПИТЬ СУДЬЮ
$as12=1000;
/////////////////ПОДКУПИТЬ СУДЬЮ


////оптимальность схемы

	$aG=1;
			if($arr2['shema']== "4-3-3"){
				
				$aD=4;
				$aM=3;
				$aF=3;
			}elseif($arr2['shema']== "3-4-3"){
				$aD=3;
				$aM=4;
				$aF=3;
			}elseif($arr2['shema']== "2-5-3"){
				$aD=2;
				$aM=5;
				$aF=3;
			}
			elseif($arr2['shema']== "5-3-2"){
				$aD=5;
				$aM=3;
				$aF=2;
			}
			elseif($arr2['shema']== "4-4-2"){
				$aD=4;
				$aM=4;
				$aF=2;
			}
			elseif($arr2['shema']== "3-5-2"){
				$aD=3;
				$aM=5;
				$aF=2;
			}
			elseif($arr2['shema']== "6-3-1"){
				$aD=6;
				$aM=3;
				$aF=1;
			}
			elseif($arr2['shema']== "5-4-1"){
				$aD=5;
				$aM=4;
				$aF=1;
			}
			elseif($arr2['shema']== "4-5-1"){
				$aD=4;
				$aM=5;
				$aF=1;
			}
$G2 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='1' ");
$TG2 = mysql_num_rows($G2);
$D2 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='2' ");
$TD2 = mysql_num_rows($D2);
$M2 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='3' ");
$TM2 = mysql_num_rows($M2);
$F2 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='4' ");
$TF2 = mysql_num_rows($F2);

		
		if($aG==0 or $TG2==0){
			$pg2=0;
		}
		elseif($aG<=$TG2){
		$pg2=$aG/$TG2;
		}elseif($TG2<=$aG){
			$pg2=$TG2/$aG;
		}
		
		
		if($aD==0 or $TD1==0){
			$pd2=0;
		}
		elseif($aD<=$TD2){
		$pd2=$aD/$TD2;
		}elseif($TD2<=$aD){
			$pd2=$TD2/$aD;
		}
		
		
	if($aM==0 or $TM2==0){
			$pm2=0;
		}
		elseif($aM<=$TM2){
		$pm2=$aM/$TM2;
		}elseif($TM2<=$aM){
			$pm2=$TM2/$aM;
		}
		
			if($aF==0 or $TF2==0){
			$pf2=0;
		}
		elseif($aF<=$TF2){
		$pf2=$aF/$TF2;
		}elseif($TF2<=$aF){
			$pf2=$TF2/$aF;
		}
		
		
		$p2=($pg2+$pd2+$pm2+$pf2)/4;
		$p2=1;
	///сила  команды 2
/////////////////ПОДКУПИТЬ СУДЬЮ
if($arr2['ref']){
$sila2 = $as1+$as2+$as3+$as4+$as5+$as6+$as7+$as8+$as9+$as10+$as11+$as12;
}
/////////////////ПОДКУПИТЬ СУДЬЮ
else{
$sila2 = $as1+$as2+$as3+$as4+$as5+$as6+$as7+$as8+$as9+$as10+$as11;
}
$allsila2=$sila2*$p2;
		
		
		$p=$allsila1/($allsila1+$allsila2)*100;	
		
		$p=$p+$st1+$sp1+$sk1;
		
		///////////////////////////////////////////////////////////////////
                //////////////////          РAСЧЕТ СЧЕТА          ////////////////////////
                ///////////////////////////////////////////////////////////////////

                


                if ($allsila1 > $allsila2) {
                    $razn1 = $allsila1 - $allsila2;

                    if ($razn1 > 850) {
                        $input = array("6:1", "7:2", "10:2", "9:1", "8:1");
                    } elseif ($razn1 > 550) {
                        $input = array("6:0", "7:1", "5:0", "5:1");
                    } elseif ($razn1 > 450) {
                        $input = array("4:0", "4:1", "3:0", "3:2", "3:1", "2:1", "0:0", "1:1");
                    } elseif ($razn1 > 300) {
                        $input = array("3:0", "3:0", "4:1", "3:1", "2:1", "0:0", "1:1");
                    } elseif ($razn1 > 200) {
                        $input = array("2:0", "2:0", "1:0", "3:1", "0:1", "1:2", "0:0", "1:1", "2:2");
                    } else {
                        $input = array("1:0", "0:0", "1:1", "2:2", "2:1", "3:2", "0:1", "1:2");
                    }


                } elseif ($allsila2 > $allsila1) {
                    $razn2 = $allsila2 - $allsila1;

                    if ($razn2 > 850) {
                        $input = array("1:6", "2:7", "2:10", "1:8", "1:9");
                    } elseif ($razn2 > 550) {
                        $input = array("0:6", "1:7", "0:5", "1:5");
                    } elseif ($razn2 > 450) {
                        $input = array("0:4", "1:4", "0:3", "2:3", "1:3", "1:2", "0:0", "1:1");
                    } elseif ($razn2 > 300) {
                        $input = array("0:3", "0:3", "1:4", "1:3", "1:2", "0:0", "1:1");
                    } elseif ($razn2 > 200) {
                        $input = array("0:2", "0:2", "0:1", "1:3", "1:0", "2:1", "2:1", "0:0", "1:1", "2:2");
                    } else {
                        $input = array("0:1", "0:0", "1:1", "1:2", "2:3", "1:0", "2:1");
                    }

                } else {
                        $input = array("0:1", "0:0", "1:1", "2:2", "1:2", "2:3", "1:0", "2:1");
                }



// $req = mysql_query("SELECT * FROM `r_player` where `team`='".$jam1[id]."' and `sostav` = '1';");
// $totalplayer = mysql_num_rows($req);

// if ($totalplayer < 7)


$pizda1 = $TG1 + $TD1 + $TM1 + $TF1;
$pizda2 = $TG2 + $TD2 + $TM2 + $TF2;

if($pizda2 < 7) {
	
	mysql_query("update `r".$prefix."game` set`teh_end` = '1' where id='" . $id . "' LIMIT 1;");
$input = array ("3:0");
}


if($pizda1 < 7) {

	mysql_query("update `r".$prefix."game` set`teh_end` = '1' where id='" . $id . "' LIMIT 1;");
$input = array ("0:3");}


///в тхт



    $minuta = rand(2,5);if ($minuta < 10){$minuta = '0'.$minuta;}
$rs=rand(0,100);
    if ($p>$rs)
    {
    $text = $text.''.func_text(play,1,$minuta,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta,0).'\r\n';
	}
	$minuta1 = rand(6,10);if ($minuta1 < 10){$minuta1 = '0'.$minuta1;}
$rs1=rand(0,100);
    if ($p>$rs1)
    {
    $text = $text.''.func_text(play,1,$minuta1,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta1,0).'\r\n';
	}
	//3
		$minuta2 = rand(11,15);
$rs2=rand(0,100);
    if ($p>$rs2)
    {
    $text = $text.''.func_text(play,1,$minuta2,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta2,0).'\r\n';
	}
	//4
	$minuta3 = rand(16,20);
$rs3=rand(0,100);
    if ($p>$rs3)
    {
    $text = $text.''.func_text(play,1,$minuta3,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta3,0).'\r\n';
	}
		//5
	$minuta4 = rand(21,25);
$rs4=rand(0,100);
    if ($p>$rs4)
    {
    $text = $text.''.func_text(play,1,$minuta4,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta4,0).'\r\n';
	}
			//6
	$minuta5 = rand(26,30);
$rs5=rand(0,100);
    if ($p>$rs5)
    {
    $text = $text.''.func_text(play,1,$minuta5,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta5,0).'\r\n';
	}
	//7
	$minuta6 = rand(31,35);
$rs6=rand(0,100);
    if ($p>$rs6)
    {
    $text = $text.''.func_text(play,1,$minuta6,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta6,0).'\r\n';
	}
	//8
	$minuta7 = rand(36,40);
$rs7=rand(0,100);
    if ($p>$rs7)
    {
    $text = $text.''.func_text(play,1,$minuta7,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta7,0).'\r\n';
	}
	//9
	$minuta8 = rand(41,43);
$rs8=rand(0,100);
    if ($p>$rs8)
    {
    $text = $text.''.func_text(play,1,$minuta8,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta8,0).'\r\n';
	}
		//10
	$minuta9 = rand(47,50);
$rs9=rand(0,100);
    if ($p>$rs9)
    {
    $text = $text.''.func_text(play,1,$minuta9,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta9,0).'\r\n';
	}
	//11
	$minuta10 = rand(51,55);
$rs10=rand(0,100);
    if ($p>$rs10)
    {
    $text = $text.''.func_text(play,1,$minuta10,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta10,0).'\r\n';
	}
	//12
	$minuta11 = rand(56,60);
$rs11=rand(0,100);
    if ($p>$rs11)
    {
    $text = $text.''.func_text(play,1,$minuta11,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta11,0).'\r\n';
	}
	//13
	$minuta12 = rand(61,65);
$rs12=rand(0,100);
    if ($p>$rs12)
    {
    $text = $text.''.func_text(play,1,$minuta12,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta12,0).'\r\n';
	}
	//14
	$minuta13 = rand(66,70);
$rs13=rand(0,100);
    if ($p>$rs13)
    {
    $text = $text.''.func_text(play,1,$minuta13,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta13,0).'\r\n';
	}
		//15
	$minuta14 = rand(71,75);
$rs14=rand(0,100);
    if ($p>$rs14)
    {
    $text = $text.''.func_text(play,1,$minuta14,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta14,0).'\r\n';
	}
		//16
	$minuta15 = rand(76,80);
$rs15=rand(0,100);
    if ($p>$rs15)
    {
    $text = $text.''.func_text(play,1,$minuta15,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta15,0).'\r\n';
	}
		//17
	$minuta16 = rand(81,85);
$rs16=rand(0,100);
    if ($p>$rs16)
    {
    $text = $text.''.func_text(play,1,$minuta16,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta16,0).'\r\n';
	}
	//18
	$minuta17 = rand(86,88);
$rs17=rand(0,100);
    if ($p>$rs17)
    {
    $text = $text.''.func_text(play,1,$minuta17,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta17,0).'\r\n';
	}
	//18
	$minuta18 = rand(89,91);
$rs18=rand(0,100);
    if ($p>$rs18)
    {
    $text = $text.''.func_text(play,1,$minuta18,0).'\r\n';
    }else{
    $text = $text.''.func_text(play,2,$minuta18,0).'\r\n';
	}
	
	
	
	// 19
	// $minuta19 = rand(92,95);
// $rs19=rand(0,122);
    // if ($p>$rs19)
    // {
    // $text = $text.''.func_text(play,1,$minuta19,0).'\r\n';
    // }else{
    // $text = $text.''.func_text(play,2,$minuta19,0).'\r\n';
	// }
	
	// 20
	// $minuta20 = rand(96,100);
// $rs20=rand(0,122);
    // if ($p>$rs20)
    // {
    // $text = $text.''.func_text(play,1,$minuta20,0).'\r\n';
    // }else{
    // $text = $text.''.func_text(play,2,$minuta20,0).'\r\n';
	// }
	
	// 18
	// $minuta21 = rand(101,104);
// $rs21=rand(0,122);
    // if ($p>$rs21)
    // {
    // $text = $text.''.func_text(play,1,$minuta21,0).'\r\n';
    // }else{
    // $text = $text.''.func_text(play,2,$minuta21,0).'\r\n';
	// }
	
	// 18
	// $minuta22 = rand(105,109);
// $rs22=rand(0,122);
    // if ($p>$rs22)
    // {
    // $text = $text.''.func_text(play,1,$minuta22,0).'\r\n';
    // }else{
    // $text = $text.''.func_text(play,2,$minuta22,0).'\r\n';
	// }
	// 18
	// $minuta23 = rand(110,114);
// $rs23=rand(0,122);
    // if ($p>$rs23)
    // {
    // $text = $text.''.func_text(play,1,$minuta23,0).'\r\n';
    // }else{
    // $text = $text.''.func_text(play,2,$minuta23,0).'\r\n';
	// }
	// 18
	// $minuta24 = rand(115,118);
// $rs24=rand(0,122);
    // if ($p>$rs24)
    // {
    // $text = $text.''.func_text(play,1,$minuta24,0).'\r\n';
    // }else{
    // $text = $text.''.func_text(play,2,$minuta24,0).'\r\n';
	// }
	// 18
	// $minuta25 = rand(119,121);
// $rs25=rand(0,122);
    // if ($p>$rs25)
    // {
    // $text = $text.''.func_text(play,1,$minuta25,0).'\r\n';
    // }else{
    // $text = $text.''.func_text(play,2,$minuta25,0).'\r\n';
	// }
	
	
    ///////////////////////////////////////////////////////////////////
    //////////////////          РЕЗУЛЬТАТ          ///////////////////////////
    ///////////////////////////////////////////////////////////////////

    $rand_keys = array_rand ($input);
    $rezult = explode(":",$input[$rand_keys]);






    ///////////////////////////////////////////////////////////////////
    //////////////////           ПЕНАЛЬТИ          //////////////////////////
    ///////////////////////////////////////////////////////////////////

    if ($rezult[0] == $rezult[1] and ($game[gr]='1/8' or $game[gr]='1/4' OR $game[gr]='1/2' or $game[gr]='1/1') )
    {
    $input = array ("5:3", "5:4", "4:2", "4:3", "3:2", "3:5", "4:5", "2:4", "3:4", "2:3");
    $rand_keys = array_rand ($input);

    $penult = explode(":",$input[$rand_keys]);

    $pen1 = $penult[0];
    $pen2 = $penult[1];
    }






    /////////////////////////////////////////////////////////////////
    //////////////////////         КТО ЗАБИЛ         //////////////////////
    /////////////////////////////////////////////////////////////////

	if($rezult[0] > 0) {







	if($rezult[0]>=5){
		
		
		/* 
 $rand = rand(1, 1);
                   
                    if ($rand == 1 && $arr1['id_admin'] != 0) {
                       
			$z11 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `role`='pen' LIMIT 1 ");
$zs11 = mysql_fetch_array($z11);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal1_pen|' . $zs11['id'] . '|' . $zs11['name'] . '\r\n';
	   	   $text = $text . func_text(goal1_pen, 1, $minuta, $zs11['name']) . '\r\n';

                         
                    }  */
					
					
		$rand1=mt_rand(1,100);
		if($rand1<6){
			$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='2' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}elseif($rand1<30 && $rand1>=6){
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='3' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='4' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);
$zn1 = mysql_num_rows($z1);

	    $minuta = mt_rand(10, 90);
		if($zn1>0){
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
			$t1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') ORDER BY RAND() ");
$zt1 = mysql_fetch_array($t1);
			$menus = $menus . $minuta . '|goal|' . $zt1['id'] . '|' . $zt1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zt1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zt1['goal']+1)." WHERE `id`='" . $zt1['id'] . "' LIMIT 1;");	
		
		}

		}		
	}
	if($rezult[0]>=4){
		
		
		/* 
 $rand = rand(1, 1);
                   
                    if ($rand == 1 && $arr1['id_admin'] != 0) {
                       
			$z11 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `role`='pen' LIMIT 1 ");
$zs11 = mysql_fetch_array($z11);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal1_pen|' . $zs11['id'] . '|' . $zs11['name'] . '\r\n';
	   	   $text = $text . func_text(goal1_pen, 1, $minuta, $zs11['name']) . '\r\n';

                       
                    }
					 */   
					
					
		$rand1=mt_rand(1,100);
		if($rand1<6){
			$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='2' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}elseif($rand1<30 && $rand1>=6){
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='3' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='4' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);
$zn1 = mysql_num_rows($z1);

	    $minuta = mt_rand(10, 90);
		if($zn1>0){
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
			$t1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') ORDER BY RAND() ");
$zt1 = mysql_fetch_array($t1);
			$menus = $menus . $minuta . '|goal|' . $zt1['id'] . '|' . $zt1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zt1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zt1['goal']+1)." WHERE `id`='" . $zt1['id'] . "' LIMIT 1;");	
		
		}		
	}
	}
	if($rezult[0]>=3){
		
	/* 	 
 $rand = rand(1, 1);
                   
                    if ($rand == 1 && $arr1['id_admin'] != 0) {
                       
			$z11 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `role`='pen' LIMIT 1 ");
$zs11 = mysql_fetch_array($z11);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal1_pen|' . $zs11['id'] . '|' . $zs11['name'] . '\r\n';
	   	   $text = $text . func_text(goal1_pen, 1, $minuta, $zs11['name']) . '\r\n';

                         
                    }*/  
					
					
					
		$rand1=mt_rand(1,100);
		if($rand1<6){
			$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='2' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}elseif($rand1<30 && $rand1>=6){
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='3' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='4' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);
$zn1 = mysql_num_rows($z1);

	    $minuta = mt_rand(10, 90);
		if($zn1>0){
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
			$t1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') ORDER BY RAND() ");
$zt1 = mysql_fetch_array($t1);
			$menus = $menus . $minuta . '|goal|' . $zt1['id'] . '|' . $zt1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zt1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zt1['goal']+1)." WHERE `id`='" . $zt1['id'] . "' LIMIT 1;");	
		
		}		
	}
	}
	if($rezult[0]>=2){
		
		/* 
 $rand = rand(1, 1);
                   
                    if ($rand == 1 && $arr1['id_admin'] != 0) {
                       
			$z11 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `role`='pen' LIMIT 1 ");
$zs11 = mysql_fetch_array($z11);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal1_pen|' . $zs11['id'] . '|' . $zs11['name'] . '\r\n';
	   	   $text = $text . func_text(goal1_pen, 1, $minuta, $zs11['name']) . '\r\n';

                         
                    } */
					 
					
		$rand1=mt_rand(1,100);
		if($rand1<6){
			$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='2' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}elseif($rand1<30 && $rand1>=6){
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='3' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='4' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);
$zn1 = mysql_num_rows($z1);

	    $minuta = mt_rand(10, 90);
		if($zn1>0){
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
			$t1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') ORDER BY RAND() ");
$zt1 = mysql_fetch_array($t1);
			$menus = $menus . $minuta . '|goal|' . $zt1['id'] . '|' . $zt1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zt1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zt1['goal']+1)." WHERE `id`='" . $zt1['id'] . "' LIMIT 1;");	
		
		}		
	}
	}
	if($rezult[0]>=1){
		
/* 		
 $rand = rand(1, 1);
                   
                    if ($rand == 1 && $arr1['id_admin'] != 0) {
                       
			$z11 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `role`='pen' LIMIT 1 ");
$zs11 = mysql_fetch_array($z11);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal1_pen|' . $zs11['id'] . '|' . $zs11['name'] . '\r\n';
	   	   $text = $text . func_text(goal1_pen, 1, $minuta, $zs11['name']) . '\r\n';

                         
                    } */
					 
					
		$rand1=mt_rand(1,100);
		if($rand1<6){
			$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='2' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}elseif($rand1<30 && $rand1>=6){
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='3' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') AND `line`='4' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);
$zn1 = mysql_num_rows($z1);

	    $minuta = mt_rand(10, 90);
		if($zn1>0){
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
			$t1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') ORDER BY RAND() ");
$zt1 = mysql_fetch_array($t1);
			$menus = $menus . $minuta . '|goal|' . $zt1['id'] . '|' . $zt1['name'] . '\r\n';
	     	   $text = $text . func_text(goal1, 1, $minuta, $zt1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zt1['goal']+1)." WHERE `id`='" . $zt1['id'] . "' LIMIT 1;");	
		
		}		
	}
		
	}
	}
	
	/////////кто забил 2 команда
	
	 	if($rezult[1] > 0) {

/*
$rand = rand(1, 1);          
                    if ($rand == 1 && $arr2['id_admin'] != 0) {
			$z22 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `role`='pen' LIMIT 1 ");
$zs22 = mysql_fetch_array($z22);
	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal2_pen|' . $zs22['id'] . '|' . $zs22['name'] . '\r\n';
	   	   $text = $text . func_text(goal2_pen, 2, $minuta, $zs22['name']) . '\r\n';
mysql_query("UPDATE `r_player` SET  `goal`=".($zs22['goal']+1)." WHERE `id`='" . $zs22['id'] . "' LIMIT 1;");	
		  } */
 

	if($rezult[1]>=5){
		
		
					
					
		$rand1=mt_rand(1,100);
		if($rand1<6){
			$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='2' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	   	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}elseif($rand1<30 && $rand1>=6){
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='3' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	   	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='4' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);
$zn1 = mysql_num_rows($z1);

	    $minuta = mt_rand(10, 90);
		if($zn1>0){
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}
		
		
		
		else{
$t1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."')  ORDER BY RAND() ");
$zt1 = mysql_fetch_array($t1);
			$menus = $menus . $minuta . '|goal|' . $zt1['id'] . '|' . $zt1['name'] . '\r\n';
	     	   $text = $text . func_text(goal2, 2, $minuta, $zt1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zt1['goal']+1)." WHERE `id`='" . $zt1['id'] . "' LIMIT 1;");	
		
		}		
	}
	}
	if($rezult[1]>=4){
		
		
					
					
		$rand1=mt_rand(1,100);
		if($rand1<6){
			$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='2' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	   	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}elseif($rand1<30 && $rand1>=6){
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='3' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	   	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='4' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);
$zn1 = mysql_num_rows($z1);

	    $minuta = mt_rand(10, 90);
		if($zn1>0){
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$t1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."')  ORDER BY RAND() ");
$zt1 = mysql_fetch_array($t1);
			$menus = $menus . $minuta . '|goal|' . $zt1['id'] . '|' . $zt1['name'] . '\r\n';
	     	   $text = $text . func_text(goal2, 2, $minuta, $zt1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zt1['goal']+1)." WHERE `id`='" . $zt1['id'] . "' LIMIT 1;");	
		
		}
	}
	}
	if($rezult[1]>=3){
		
		
					
					
		$rand1=mt_rand(1,100);
		if($rand1<6){
			$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='2' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	   	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}elseif($rand1<30 && $rand1>=6){
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='3' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	   	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='4' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);
$zn1 = mysql_num_rows($z1);

	    $minuta = mt_rand(10, 90);
		if($zn1>0){
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$t1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."')  ORDER BY RAND() ");
$zt1 = mysql_fetch_array($t1);
			$menus = $menus . $minuta . '|goal|' . $zt1['id'] . '|' . $zt1['name'] . '\r\n';
	     	   $text = $text . func_text(goal2, 2, $minuta, $zt1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zt1['goal']+1)." WHERE `id`='" . $zt1['id'] . "' LIMIT 1;");	
		
		}		
	}
	}
	if($rezult[1]>=2){
		
		
					
					
		$rand1=mt_rand(1,100);
		if($rand1<6){
			$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='2' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	   	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}elseif($rand1<30 && $rand1>=6){
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='3' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	   	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='4' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);
$zn1 = mysql_num_rows($z1);

	    $minuta = mt_rand(10, 90);
		if($zn1>0){
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$t1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."')  ORDER BY RAND() ");
$zt1 = mysql_fetch_array($t1);
			$menus = $menus . $minuta . '|goal|' . $zt1['id'] . '|' . $zt1['name'] . '\r\n';
	     	   $text = $text . func_text(goal2, 2, $minuta, $zt1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zt1['goal']+1)." WHERE `id`='" . $zt1['id'] . "' LIMIT 1;");	
		
		}
	}
	}
	if($rezult[1]>=1){
		
		 
					
		$rand1=mt_rand(1,100);
		if($rand1<6){
			$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='2' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	   	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}elseif($rand1<30 && $rand1>=6){
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='3' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);

	    $minuta = mt_rand(10, 90);
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	   	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$z1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') AND `line`='4' ORDER BY RAND() ");
$zs1 = mysql_fetch_array($z1);
$zn1 = mysql_num_rows($z1);

	    $minuta = mt_rand(10, 90);
		if($zn1>0){
	   $menus = $menus . $minuta . '|goal|' . $zs1['id'] . '|' . $zs1['name'] . '\r\n';
	     	   $text = $text . func_text(goal2, 2, $minuta, $zs1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zs1['goal']+1)." WHERE `id`='" . $zs1['id'] . "' LIMIT 1;");	
		}else{
$t1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."')  ORDER BY RAND() ");
$zt1 = mysql_fetch_array($t1);
			$menus = $menus . $minuta . '|goal|' . $zt1['id'] . '|' . $zt1['name'] . '\r\n';
	     	   $text = $text . func_text(goal2, 2, $minuta, $zt1['name']) . '\r\n';
	   mysql_query("UPDATE `r_player` SET  `goal`=".($zt1['goal']+1)." WHERE `id`='" . $zt1['id'] . "' LIMIT 1;");	
		
		}	
	}
		
	}
		}
	


 

 

    /////////////////////////////////////////////////////////////////
                //////////////////////         КТО ЗАБИЛ         //////////////////////
                /////////////////////////////////////////////////////////////////









$g = @mysql_query("select * from `r".$prefix."game` where id = '" . $id . "' LIMIT 1;");
$game = @mysql_fetch_array($g);


if ($game[chemp] == 'frend'){
///////////////////////////////////



    ///////////////////////////////////////
    ///////       Состав первой         /////////
    ///////////////////////////////////////

			$kk1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') order by line asc LIMIT 11");



    while ($kom1 = mysql_fetch_array($kk1))
    {
    // Травмы 1
    if (($allsila1-700) > $allsila2)
    {
    $input1 = array ("1", "1", "1", "15"); // наши числа
    $rand_keys1 = array_rand ($input1);
    $koffiza1 = $input1[$rand_keys1];
	if($rand_keys1==15){
		   	  $text = $text . func_text(crest, 1, $minuta, $kom1['name']) . '\r\n';
	}
    }
    else
    {
    $koffiza1 = 1;
    }
    $allfiza1 = rand(7,18);
    $fiza1 = $kom1[fiz] - ($allfiza1*$koffiza1);
    $rmmas1 = round($kom1[mas]/100*$fiza1);

$op1 = $kom1[tal];


    $oputplay1 = $kom1[oput] + $op1;
    $gameplay1 = $kom1[game]+1;


    if ($rezult[0] > $rezult[1])
    {
    $mor1 = $kom1[mor] + 3;
    }
    elseif ($rezult[0] < $rezult[1])
    {
    $mor1 = $kom1[mor] - 3;
    }
    else
    {
    $mor1 = $kom1[mor];
    }

    $imgfiza1 = '';
    if ($fiza1 < 0)
    {
    $imgfiza1 = ' <img src="/images/trav.gif" alt=""/>';
    $mor1 = $kom1[mor]-15;
    }




                  
    $players1 = $players1.''.$kom1['poz'].'|'.$kom1[id].'|'.$kom1['name'].' '.$imgfiza1.'|+'.$op1.'\r\n';

mysql_query("update `r_player` set `fiz`='" . $fiza1 . "', `mor`='" . $mor1 . "',  `game`='" . $gameplay1 . "', `oput`='" . $oputplay1 . "' where id='" . $kom1[id] . "' LIMIT 1;");
    }




///////////////////////////////////

}else {
    ///////////////////////////////////////
    ///////       Состав первой         /////////
    ///////////////////////////////////////

			$kk1 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr1['i1']."' or `id`='".$arr1['i2']."' or `id`='".$arr1['i3']."' or `id`='".$arr1['i4']."' or `id`='".$arr1['i5']."' or `id`='".$arr1['i6']."' or `id`='".$arr1['i7']."' or `id`='".$arr1['i8']."' or `id`='".$arr1['i9']."' or `id`='".$arr1['i10']."' or `id`='".$arr1['i11']."') order by line asc LIMIT 11");

   

    while ($kom1 = mysql_fetch_array($kk1))
    {
		
		
		
		
		
		
		
		
    // Травмы 1
    if (($allsila1-700) > $allsila2)
    {
    $input1 = array ("1", "1", "1", "15"); // наши числа
    $rand_keys1 = array_rand ($input1);
    $koffiza1 = $input1[$rand_keys1];
	if($rand_keys1==15){
		   	  $text = $text . func_text(crest, 1, $minuta, $kom1['name']) . '\r\n';
	}
    }
    else
    {
    $koffiza1 = 1;
    }
    $allfiza1 = rand(7,18);
    $fiza1 = $kom1[fiz] - ($allfiza1*$koffiza1);
    $rmmas1 = round($kom1[mas]/100*$fiza1);
switch ($arr1[trener])
{
case "0":$koftrener1 = 1;break;
case "1":$koftrener1 = 2;break;
case "2":$koftrener1 = 3;break;
case "3":$koftrener1 = 4;break;
case "4":$koftrener1 = 5;break;
}
switch ($arr1[vrat])
{
case "0":$kofvrat1 = 1;break;
case "1":$kofvrat1 = 2;break;
case "2":$kofvrat1 = 3;break;
case "3":$kofvrat1 = 4;break;
case "4":$kofvrat1 = 5;break;
}

    if ($kom1[line] != '1')
    {
    $op1 = round($kom1[tal]*$koftrener1);
    }elseif ($kom1[line] == '1')
    {
    $op1 = round($kom1[tal]*$kofvrat1);
    }
else{
$op1 = $kom1[tal];
}
    

    $oputplay1 = $kom1[oput] + $op1;
    $gameplay1 = $kom1[game]+1;


    if ($rezult[0] > $rezult[1])
    {
    $mor1 = $kom1[mor] + 3;
    }
    elseif ($rezult[0] < $rezult[1])
    {
    $mor1 = $kom1[mor] - 3;
    }
    else
    {
    $mor1 = $kom1[mor];
    }

    $imgfiza1 = '';
    if ($fiza1 < 0)
    {
    $imgfiza1 = ' <img src="/images/trav.gif" alt=""/>';
    $mor1 = $kom1[mor]-15;
    }
  $players1 = $players1.''.$kom1['poz'].'|'.$kom1[id].'|'.$kom1['name'].' '.$imgfiza1.'|+'.$op1.'\r\n';

mysql_query("update `r_player` set `fiz`='" . $fiza1 . "', `mor`='" . $mor1 . "',  `game`='" . $gameplay1 . "', `oput`='" . $oputplay1 . "' where id='" . $kom1[id] . "' LIMIT 1;");
  	    


 if ($game[chemp] != 'frend'){
//////////////////////YELLOW CARD//////////////////////
  $ssdaada = $kom1[yc]-3;
    if ($kom1['yc'] >= 3) {
							
                         
                            mysql_query("update `r_player` set `sostav`='4', `utime`='3',`yc`='".$ssdaada."' where id='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i1`='' where `i1`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $kom1['id'] . "';");
							 }
							 if ($kom1['rc'] == 1) {
							
                         
                            mysql_query("update `r_player` set `sostav`='4', `utime`='2',`rc`='0' where id='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i1`='' where `i1`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $kom1['id'] . "';");
							 }



                     

					  
						
/* $pizda1 = $kom1['utime'] - 1;
$pizda2 = $kom1['btime'] - 1;
 if ($kom1['utime'] > 0) {
mysql_query("update `r_player` set  `utime`='" . $pizda1 . "', `btime`='" . $pizda2 . "' where id='" . $kom1['id'] . "';");
 } */
  if ($game[chemp] == 'champ_retro'){
	   if ($kom1['tactic'] <= 40){
		   $rand = rand(1, 25); 
	   }
	   else{
 $rand = rand(1, 100);
	   }
                   
                    // if ($rand == 25 && $arr1['id_admin'] != 0) {
                    if ($rand == 25 ) {

							$minuta = mt_rand(10, 90);
								$menus1 = $menus1.''.$minuta.'|yellow|'.$kom1[id].'|'.$kom1['name'].'\r\n';


                        $text = $text . func_text(yellow, 1, $minuta, $kom1['name']) . '\r\n';  
                        $kom1xyi1 = $kom1['yc'] + 1;
                          
                    
                            mysql_query("update `r_player` set  `yc`='" . $kom1xyi1 . "' where id='" . $kom1['id'] . "';");
                	$q1auy = @mysql_query("select * from `r_judge` WHERE `id`='".$game[judge]."'  LIMIT 1;");
$aayr = @mysql_fetch_array($q1auy);
$gggs = $aayr[yc]+1;
mysql_query("update `r_judge` set `yc`='".$gggs."' where id='" . $game[judge] . "' LIMIT 1;");

                    }
					}
					else  if ($game[chemp] == 'unchamp'){
					 $ssdaada = $kom1[yc_unchamp]-3;
    if ($kom1['yc_unchamp'] >= 3) {
							
                         
                            mysql_query("update `r_player` set `sostav`='4', `utime`='3',`yc_unchamp`='".$ssdaada."' where id='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i1`='' where `i1`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $kom1['id'] . "';");
							 }
							 
							    if ($kom1['tactic'] <= 40){
		   $rand = rand(1, 25); 
	   }
	   else{
 $rand = rand(1, 100);
	   }
	   
					 // $rand = rand(1, 100);
                   
                    // if ($rand == 25 && $arr1['id_admin'] != 0) {
                    if ($rand == 25 ) {

							$minuta = mt_rand(10, 90);
								$menus1 = $menus1.''.$minuta.'|yellow|'.$kom1[id].'|'.$kom1['name'].'\r\n';


                        $text = $text . func_text(yellow, 1, $minuta, $kom1['name']) . '\r\n';  
                        $kom1xyi1 = $kom1['yc_uchamp'] + 1;
                          
                    
                            mysql_query("update `r_player` set  `yc_uchamp`='" . $kom1xyi1 . "' where id='" . $kom1['id'] . "';");
                	$q1auy = @mysql_query("select * from `r_judge` WHERE `id`='".$game[judge]."'  LIMIT 1;");
$aayr = @mysql_fetch_array($q1auy);
$gggs = $aayr[yc]+1;
mysql_query("update `r_judge` set `yc`='".$gggs."' where id='" . $game[judge] . "' LIMIT 1;");
					}
					}
					else  if ($game[chemp] == 'liga_r'){
					 $ssdaada = $kom1[yc_liga_r]-3;
    if ($kom1['yc_liga_r'] >= 3) {
							
                         
                            mysql_query("update `r_player` set `sostav`='4', `utime`='3',`yc_liga_r`='".$ssdaada."' where id='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i1`='' where `i1`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $kom1['id'] . "';");
							 }
							    if ($kom1['tactic'] <= 40){
		   $rand = rand(1, 25); 
	   }
	   else{
 $rand = rand(1, 100);
	   }
					 // $rand = rand(1, 100);
                   
                    // if ($rand == 25 && $arr1['id_admin'] != 0) {
                    if ($rand == 25 ) {

							$minuta = mt_rand(10, 90);
								$menus1 = $menus1.''.$minuta.'|yellow|'.$kom1[id].'|'.$kom1['name'].'\r\n';


                        $text = $text . func_text(yellow, 1, $minuta, $kom1['name']) . '\r\n';  
                        $kom1xyi1 = $kom1['yc_liga_r'] + 1;
                          
                    
                            mysql_query("update `r_player` set  `yc_liga_r`='" . $kom1xyi1 . "' where id='" . $kom1['id'] . "';");
                	$q1auy = @mysql_query("select * from `r_judge` WHERE `id`='".$game[judge]."'  LIMIT 1;");
$aayr = @mysql_fetch_array($q1auy);
$gggs = $aayr[yc]+1;
mysql_query("update `r_judge` set `yc`='".$gggs."' where id='" . $game[judge] . "' LIMIT 1;");
					}
					}else  if ($game[chemp] == 'le'){
					 $ssdaada = $kom1[yc_le]-3;
    if ($kom1['yc_le'] >= 3) {
							
                         
                            mysql_query("update `r_player` set `sostav`='4', `utime`='3',`yc_le`='".$ssdaada."' where id='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i1`='' where `i1`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $kom1['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $kom1['id'] . "';");
							 }
					 // $rand = rand(1, 100);
                      if ($kom1['tactic'] <= 40){
		   $rand = rand(1, 25); 
	   }
	   else{
 $rand = rand(1, 100);
	   }
                    // if ($rand == 25 && $arr1['id_admin'] != 0) {
                    if ($rand == 25 ) {

							$minuta = mt_rand(10, 90);
								$menus1 = $menus1.''.$minuta.'|yellow|'.$kom1[id].'|'.$kom1['name'].'\r\n';


                        $text = $text . func_text(yellow, 1, $minuta, $kom1['name']) . '\r\n';  
                        $kom1xyi1 = $kom1['yc_le'] + 1;
                          
                    
                            mysql_query("update `r_player` set  `yc_le`='" . $kom1xyi1 . "' where id='" . $kom1['id'] . "';");
                	$q1auy = @mysql_query("select * from `r_judge` WHERE `id`='".$game[judge]."'  LIMIT 1;");
$aayr = @mysql_fetch_array($q1auy);
$gggs = $aayr[yc]+1;
mysql_query("update `r_judge` set `yc`='".$gggs."' where id='" . $game[judge] . "' LIMIT 1;");
					}
					}
					  // if ($rand == 75 && $krr1['id_admin'] != 0) {
					  if ($rand == 75 ) {
                    $imgfiza1 = ' <img src="imgages/trav.gif" alt=""/>';
                    $news = 'Игрок ' . $kom1['name'] . ' из команды ' . $game['name_team1'] .
                        ' очень сильно травмировался и будет находиться на лечении 2 следующих игры.';
                    mysql_query("update `r_player` set `sostav`='3', `btime`='" . ($realtime +
                        174000) . "' where id='" . $kom1['id'] . "';");
                   
                }
				
					

 
					//////////////////////YELLOW CARD//////////////////////
					
					
					
					
					
					
					
					
			
 }
    	         
    }

}





if ($game[chemp] == 'frend'){

/////////////////////////////////


    ///////////////////////////////////////
    ///////       Отнимаем физу  Добавляем опыт	/////////
    ///////////////////////////////////////

  
			$kk2 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') order by line asc LIMIT 11");

    while ($kom2 = mysql_fetch_array($kk2))
    {
    // Травмы 2
    if (($allsila2-700) > $allsila1)
    {
    $input2 = array ("1", "1", "1", "15"); // наши числа
    $rand_keys2 = array_rand ($input2);
    $koffiza2 = $input2[$rand_keys2];
		if($rand_keys1==15){
	 $text = $text . func_text(crest, 2, $minuta, $kom2['name']) . '\r\n';
		}
    }
    else
    {
    $koffiza2 = 1;
    }

    $allfiza2 = rand(7,18);
    $fiza2 = $kom2[fiz] - ($allfiza2*$koffiza2);
    $rmmas2 = round($kom2[mas]/100*$fiza2);

    $op2 = $kom2[tal];

    $oputplay2 = $kom2[oput] + $op2;
    $gameplay2 = $kom2[game]+1;


    if ($rezult[1] > $rezult[0])
    {
    $mor2 = $kom2[mor] + 3;
    }
    elseif ($rezult[1] < $rezult[0])
    {
    $mor2 = $kom2[mor] - 3;
    }
    else
    {
    $mor2 = $kom2[mor];
    }


    $imgfiza2 = '';
    if ($fiza2 < 0)
    {
    $imgfiza2 = ' <img src="/images/trav.gif" alt=""/>';
    $mor2 = $kom2[mor]-15;
    }


    $players2 = $players2.''.$kom2['poz'].'|'.$kom2[id].'|'.$kom2['name'].' '.$imgfiza2.'|+'.$op2.'\r\n';


mysql_query("update `r_player` set `fiz`='" . $fiza2 . "', `mor`='" . $mor2 . "',  `game`='" . $gameplay2 . "', `oput`='" . $oputplay2 . "' where id='" . $kom2[id] . "' LIMIT 1;");
    }



////////////////////////////////
}else {
    ///////////////////////////////////////
    ///////       Отнимаем физу  Добавляем опыт	/////////
    ///////////////////////////////////////

  
			$kk2 = mysql_query("SELECT * FROM `r_player` WHERE (`id`='".$arr2['i1']."' or `id`='".$arr2['i2']."' or `id`='".$arr2['i3']."' or `id`='".$arr2['i4']."' or `id`='".$arr2['i5']."' or `id`='".$arr2['i6']."' or `id`='".$arr2['i7']."' or `id`='".$arr2['i8']."' or `id`='".$arr2['i9']."' or `id`='".$arr2['i10']."' or `id`='".$arr2['i11']."') order by line asc LIMIT 11");

    while ($kom2 = mysql_fetch_array($kk2))
    {
    // Травмы 2
    if (($allsila2-700) > $allsila1)
    {
    $input2 = array ("1", "1", "1", "15"); // наши числа
    $rand_keys2 = array_rand ($input2);
    $koffiza2 = $input2[$rand_keys2];
		if($rand_keys1==15){
	 $text = $text . func_text(crest, 2, $minuta, $kom2['name']) . '\r\n';
		}
    }
    else
    {
    $koffiza2 = 1;
    }

    $allfiza2 = rand(7,18);
    $fiza2 = $kom2[fiz] - ($allfiza2*$koffiza2);
    $rmmas2 = round($kom2[mas]/100*$fiza2);

switch ($arr2[trener])
{
case "0":$koftrener2 = 1;break;
case "1":$koftrener2 = 2;break;
case "2":$koftrener2 = 3;break;
case "3":$koftrener2 = 4;break;
case "4":$koftrener2 = 5;break;
}
switch ($arr2[vrat])
{
case "0":$kofvrat2 = 1;break;
case "1":$kofvrat2 = 2;break;
case "2":$kofvrat2 = 3;break;
case "3":$kofvrat2 = 4;break;
case "4":$kofvrat2 = 5;break;
}

    if ($kom2[line] != '1')
    {
    $op2 = round($kom2[tal]*$koftrener2);
    }elseif ($kom2[line] == '1')
    {
    $op2 = round($kom2[tal]*$kofvrat2);
    }
else{
$op2 = $kom2[tal];
}
    

    $oputplay2 = $kom2[oput] + $op2;
    $gameplay2 = $kom2[game]+1;


    if ($rezult[1] > $rezult[0])
    {
    $mor2 = $kom2[mor] + 3;
    }
    elseif ($rezult[1] < $rezult[0])
    {
    $mor2 = $kom2[mor] - 3;
    }
    else
    {
    $mor2 = $kom2[mor];
    }


    $imgfiza2 = '';
    if ($fiza2 < 0)
    {
    $imgfiza2 = ' <img src="/images/trav.gif" alt=""/>';
    $mor2 = $kom2[mor]-15;
    }
	    $players2 = $players2.''.$kom2['poz'].'|'.$kom2[id].'|'.$kom2['name'].' '.$imgfiza2.'|+'.$op2.'\r\n';


mysql_query("update `r_player` set `fiz`='" . $fiza2 . "', `mor`='" . $mor2 . "',  `game`='" . $gameplay2 . "', `oput`='" . $oputplay2 . "' where id='" . $kom2[id] . "' LIMIT 1;");
  
	
	 if ($game[chemp] != 'frend'){
						//////////////////////YELLOW CARD//////////////////////
   $ssdaada = $kom2[yc]-3;
     if ($kom2['yc'] >= 3) {
					    mysql_query("update `r_player` set `sostav`='4', `utime`='3',`yc`='".$ssdaada."' where id='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i1`='' where `i1`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $kom2['id'] . "';");
                  
						}

     if ($kom2['rc'] == 1) {
					    // mysql_query("update `r_player` set `sostav`='4', `utime`='2',`rc`='0' where id='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i1`='' where `i1`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $kom2['id'] . "';");
                  
						}
						
						
						  if ($game[chemp] == 'champ_retro'){
							  
 // $rand = rand(1, 100);
                      if ($kom2['tactic'] <= 40){
		   $rand = rand(1, 25); 
	   }
	   else{
 $rand = rand(1, 100);
	   }
                   // if ($rand == 25 && $arr2['id_admin'] != 0) {
                   if ($rand == 25 ) {
                      
							$minuta = mt_rand(10, 90);
							$menus1 = $menus1.''.$minuta.'|yellow|'.$kom2[id].'|'.$kom2['name'].'\r\n';

						 $text = $text . func_text(yellow, 2, $minuta, $kom2['name']) . '\r\n';
                       $kom2xyi2 = $kom2['yc'] + 1;
                    
                            mysql_query("update `r_player` set  `yc`='" . $kom2xyi2 . "' where id='" . $kom2['id'] . "';");
                        $q1auy = @mysql_query("select * from `r_judge` WHERE `id`='".$game[judge]."'  LIMIT 1;");
$aayr = @mysql_fetch_array($q1auy);
$gggs = $aayr[yc]+1;
mysql_query("update `r_judge` set `yc`='".$gggs."' where id='" . $game[judge] . "' LIMIT 1;");
 }
				   }
				   elseif ($game[chemp] == 'unchamp'){
					      $ssdaada = $kom2['yc_unchamp']-3;
     if ($kom2['yc_unchamp'] >= 3) {
					    mysql_query("update `r_player` set `sostav`='4', `utime`='3',`yc_unchamp`='".$ssdaada."' where id='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i1`='' where `i1`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $kom2['id'] . "';");
                  
						}
					    // $rand = rand(1, 100);
                           if ($kom2['tactic'] <= 40){
		   $rand = rand(1, 25); 
	   }
	   else{
 $rand = rand(1, 100);
	   }
                   // if ($rand == 25 && $arr2['id_admin'] != 0) {
                   if ($rand == 25 ) {
                      
							$minuta = mt_rand(10, 90);
							$menus1 = $menus1.''.$minuta.'|yellow|'.$kom2[id].'|'.$kom2['name'].'\r\n';

						 $text = $text . func_text(yellow, 2, $minuta, $kom2['name']) . '\r\n';
                       $kom2xyi2 = $kom2['yc_uchamp'] + 1;
                    
                            mysql_query("update `r_player` set  `yc_uchamp`='" . $kom2xyi2 . "' where id='" . $kom2['id'] . "';");
                        $q1auy = @mysql_query("select * from `r_judge` WHERE `id`='".$game[judge]."'  LIMIT 1;");
$aayr = @mysql_fetch_array($q1auy);
$gggs = $aayr[yc]+1;
mysql_query("update `r_judge` set `yc`='".$gggs."' where id='" . $game[judge] . "' LIMIT 1;");
 }
					   
					   
				   } elseif ($game[chemp] == 'liga_r'){
					   				      $ssdaada = $kom2['yc_liga_r']-3;
     if ($kom2['yc_liga_r'] >= 3) {
					    mysql_query("update `r_player` set `sostav`='4', `utime`='3',`yc_liga_r`='".$ssdaada."' where id='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i1`='' where `i1`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $kom2['id'] . "';");
                  
						}
					    // $rand = rand(1, 100);
                           if ($kom2['tactic'] <= 40){
		   $rand = rand(1, 25); 
	   }
	   else{
 $rand = rand(1, 100);
	   }
                   // if ($rand == 25 && $arr2['id_admin'] != 0) {
                   if ($rand == 25 ) {
                      
							$minuta = mt_rand(10, 90);
							$menus1 = $menus1.''.$minuta.'|yellow|'.$kom2[id].'|'.$kom2['name'].'\r\n';

						 $text = $text . func_text(yellow, 2, $minuta, $kom2['name']) . '\r\n';
                       $kom2xyi2 = $kom2['yc_liga_r'] + 1;
                    
                            mysql_query("update `r_player` set  `yc_liga_r`='" . $kom2xyi2 . "' where id='" . $kom2['id'] . "';");
                        $q1auy = @mysql_query("select * from `r_judge` WHERE `id`='".$game[judge]."'  LIMIT 1;");
$aayr = @mysql_fetch_array($q1auy);
$gggs = $aayr[yc]+1;
mysql_query("update `r_judge` set `yc`='".$gggs."' where id='" . $game[judge] . "' LIMIT 1;");
 }
					   
					   
				    } elseif ($game[chemp] == 'le'){
					   					   				      $ssdaada = $kom2['yc_le']-3;
     if ($kom2['yc_le'] >= 3) {
					    mysql_query("update `r_player` set `sostav`='4', `utime`='3',`yc_le`='".$ssdaada."' where id='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i1`='' where `i1`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i2`='' where `i2`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i3`='' where `i3`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i4`='' where `i4`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i5`='' where `i5`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i6`='' where `i6`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i7`='' where `i7`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i8`='' where `i8`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i9`='' where `i9`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i10`='' where `i10`='" . $kom2['id'] . "';");
                            mysql_query("update `r_team` set `i11`='' where `i11`='" . $kom2['id'] . "';");
                  
						}
					    // $rand = rand(1, 100);
                           if ($kom2['tactic'] <= 40){
		   $rand = rand(1, 25); 
	   }
	   else{
 $rand = rand(1, 100);
	   }
                   // if ($rand == 25 && $arr2['id_admin'] != 0) {
                   if ($rand == 25 ) {
                      
							$minuta = mt_rand(10, 90);
							$menus1 = $menus1.''.$minuta.'|yellow|'.$kom2[id].'|'.$kom2['name'].'\r\n';

						 $text = $text . func_text(yellow, 2, $minuta, $kom2['name']) . '\r\n';
                       $kom2xyi2 = $kom2['yc_le'] + 1;
                    
                            mysql_query("update `r_player` set  `yc_le`='" . $kom2xyi2 . "' where id='" . $kom2['id'] . "';");
                        $q1auy = @mysql_query("select * from `r_judge` WHERE `id`='".$game[judge]."'  LIMIT 1;");
$aayr = @mysql_fetch_array($q1auy);
$gggs = $aayr[yc]+1;
mysql_query("update `r_judge` set `yc`='".$gggs."' where id='" . $game[judge] . "' LIMIT 1;");
 }
					   
					   
				   }
  // if ($rand == 75 && $krr1['id_admin'] != 0) {
 		  if ($rand == 75 ) {
                    $imgfiza1 = ' <img src="imgages/trav.gif" alt=""/>';
                    $news = 'Игрок ' . $kom2['name'] . ' из команды ' . $game['name_team2'] .
                        ' очень сильно травмировался и будет находиться на лечении 2 следующих игры.';
                    mysql_query("update `r_player` set `sostav`='3', `btime`='" . ($realtime +
                        174000) . "' where id='" . $kom2['id'] . "';");
                   
                }
 
 
 
                    
					
 											//////////////////////YELLOW CARD//////////////////////
	 }
           
  }

}

 if ($game[chemp] != 'frend'){
//////////////////Убираем 1 матч дисквалификации//////////////////////////
$kk11 = mysql_query("SELECT * FROM `r_player` WHERE `team`='".$arr1['id']."' ");

    while ($kom11 = mysql_fetch_array($kk11))
    {
$pizda1 = $kom11['utime'] - 1;
$pizda_unchamp = $kom11['utime_unchamp'] - 1;
$pizda_liga_r = $kom11['utime_liga_r'] - 1;
$pizda_le = $kom11['utime_le'] - 1;
$pizda11 = $kom11['btime'] - 1;
 if ($kom11['utime'] > 0) {
mysql_query("update `r_player` set  `utime`='" . $pizda1 . "' where id='" . $kom11['id'] . "';");
 }
  elseif ($kom11['utime_unchamp'] > 0) {
mysql_query("update `r_player` set  `utime_unchamp`='" . $pizda_unchamp . "' where id='" . $kom11['id'] . "';");
 }  elseif ($kom11['utime_liga_r'] > 0) {
mysql_query("update `r_player` set  `utime_liga_r`='" . $pizda_liga_r . "' where id='" . $kom11['id'] . "';");
 }elseif ($kom11['utime_le'] > 0) {
mysql_query("update `r_player` set  `utime_le`='" . $pizda_le . "' where id='" . $kom11['id'] . "';");
 }
  // if ($kom11['btime'] > 0) {
// mysql_query("update `r_player` set  `btime`='" . $pizda11 . "' where id='" . $kom11['id'] . "';");
 // }
	}
	
$kk22 = mysql_query("SELECT * FROM `r_player` WHERE `team`='".$arr2['id']."' ");

    while ($kom22 = mysql_fetch_array($kk22))
    {
$pizda2 = $kom22['utime'] - 1;
$pizda2_unchamp = $kom22['utime_unchamp'] - 1;
$pizda2_liga_r = $kom22['utime_liga_r'] - 1;
$pizda2_le = $kom22['utime_le'] - 1;
$pizda22 = $kom22['btime'] - 1;
 if ($kom22['utime'] > 0) {
mysql_query("update `r_player` set  `utime`='" . $pizda2 . "' where id='" . $kom22['id'] . "';");
 } 
  elseif ($kom22['utime_unchamp'] > 0) {
mysql_query("update `r_player` set  `utime_unchamp`='" . $pizda2_unchamp . "' where id='" . $kom22['id'] . "';");
 }  elseif ($kom22['utime_liga_r'] > 0) {
mysql_query("update `r_player` set  `utime_liga_r`='" . $pizda2_liga_r . "' where id='" . $kom22['id'] . "';");
 }elseif ($kom22['utime_le'] > 0) {
mysql_query("update `r_player` set  `utime_le`='" . $pizda2_le . "' where id='" . $kom22['id'] . "';");
 }
 // if ($kom22['btime'] > 0) {
// mysql_query("update `r_player` set  `btime`='" . $pizda22 . "' where id='" . $kom22['id'] . "';");
 // }
	}
//////////////////Убираем 1 матч дисквалификации//////////////////////////
 }








    ////////////////////////////         ПРОВЕРКА 2           //////////////////////////////////
    $g = @mysql_query("select * from `r".$prefix."game` where id = '" . $id . "' LIMIT 1;");
    $game = @mysql_fetch_array($g);

    if (!empty($game[rez1]) || !empty($game[rez2]) || $game[rez1] == '0' || $game[rez2] == '0')
    {
		$mt=($realtime-$game['time'])*18;
$mt=floor($mt/60);
		if($mt>93){
    header('location: /report'.$dirs.''.$id);
    exit;
    }else{
		  header('location: /txt'.$dirs.''.$id);
	}
	

    if (empty($game[id]) || empty($game[id_team1]) || empty($game[id_team2]))
    {
					echo'	<div class="cardview-wrapper x-overlay" id="errorMsg">
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
    // echo display_error('Игра отменена');
    // require_once ("../incfiles/end.php");
    // exit;
    }

}






//////////////////////////////////////////////////////////////
//////////////         ПИШЕМ В R_GAME            //////////////////
//////////////////////////////////////////////////////////////


mysql_query("update `r".$prefix."game` set

`players1`='".$players1."',
`players2`='".$players2."',

`tactics1`='".$arr1['shema']."|".$arr1['pas']."|".$arr1['strat']."|".$arr1['tactic']."|".$arr1['pres']."|".$sila1."',
`tactics2`='".$arr2['shema']."|".$arr2['pas']."|".$arr2['strat']."|".$arr2['tactic']."|".$arr2['pres']."|".$sila2."',

`menus`='".$menus."',
`menus1`='".$menus1."',
`text`='".$text."',

`rez1`='',
`rez2`='',

`pen1`='',
`pen2`=''

where id='" . $id . "' LIMIT 1;");


}











header('location: /txt'.$dirs.''.$id);
require_once ("../incfiles/end.php");
?>
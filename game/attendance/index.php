<?php
include_once("./_common.php");

$g5['title'] = "출석확인 게임";

include_once(G5_PATH.'/_head.php');

$game_point = 10; // 게임포인트 설정
$game_besu1 = 10; // 퍼펙트 게임포인트 배수 설정
$game_besu2 = 3; // ±1 게임포인트 배수 설정
$game_besu3 = 1; // ±3 게임포인트 배수 설정
$game_range1 = 1; // ±1 게임 점수 설정
$game_range2 = 3; // ±3 게임 점수 설정
$game_minus = 30; // ±30 게임 감점점수 설정

// 게임 평균
function mb_average($mb_id,$agelevel)
{
	global $g5;

	$sql = "select count(po_id) as cnt from `{$g5['point_table']}` where mb_id='$mb_id' and po_rel_table = '@attendance' and SUBSTRING(po_rel_action,1,1) = '$agelevel' ";
	$row = sql_fetch($sql);
	$game_average = $row['cnt'];

	return $game_average;
}
//로그인 테이블기준 로그인 여부 체크
function mb_loginchk($mb_id)
{
	global $g5;

	if ($mb_id) {
		$sql = "select mb_id from `{$g5['login_table']}` where mb_id='$mb_id' ";
		$mb = sql_fetch($sql);

		if($mb['mb_id'])
			$message = "접속중";
		else
			$message = "";
		return $message;
	}
}

// 게임점수
function mb_gamechk($mb_id)
{
	global $g5;
	$tmp = array();
	if ($mb_id) {
		$sql = "select po_point, SUBSTRING(po_rel_action,1,1) as agelevel from `{$g5['point_table']}` where mb_id='$mb_id' and SUBSTRING(po_datetime,1,10) = '".G5_TIME_YMD."' and po_rel_table = '@attendance' ";
		$po = sql_fetch($sql);

		if($po['po_point'])
			$tmp['point'] = $po['po_point'];
		else
			$tmp['point'] = "";

		if($po['agelevel'])
			$tmp['agelevel'] = $po['agelevel'];
		else
			$tmp['agelevel'] = "";

		return $tmp;
	}
}

// 총 인원 수
$sql = " select count(*) as cnt from '{$g5['member_table']}' where mb_today_login like '".G5_TIME_YMD."%' and mb_id != '{$config['cf_admin']}' order by mb_today_login";
$total = sql_fetch($sql);

?>
<script src="<?php echo G5_JS_URL;?>/sideview.js"></script>
<script LANGUAGE="JavaScript">
function Title() {document.title="STOP ON 1o0 By  Nolan Gendron"; window.setTimeout("Title1();",1);}
function Title1() {document.title="STOP ON 10o By Nolan Gendron"; window.setTimeout("Title();",1);}
    counter=0;
function Counter1() {
    window.status="Counter: " + counter;
    document.game.number.value=counter;
    counter++;
    Time=window.setTimeout("Counter1();",1);
    if (counter==201) {
        counter=0;
    }
}

function Results() {
    window.clearTimeout(Time);
	document.game.number.value=counter;
	document.game.submit();
}
</script>

<div class="alert alert-info">
  Start 클릭하고 숫자가 100이 되는 순간 Stop 클릭!
</div>
<div class="alert alert-info">
  <img src="./img/gold.svg" style="width:16px"> 100점에 정확히 맞힐 경우 : <?php echo number_format($game_point * $game_besu1);?>포인트
</div>
<div class="alert alert-info">
  <img src="./img/silver.svg" style="width:16px"> ±1점에 맞힐 경우 : <?php echo number_format($game_point * $game_besu2);?>포인트
</div>
<div class="alert alert-info">
  <img src="./img/bronze.svg" style="width:16px"> ±3점에 맞힐 경우 : <?php echo number_format($game_point * $game_besu3);?>포인트
</div>
<div class="alert alert-warning">
  <img src="./img/ico_medal_e.png" style="width:16px"> ±30점 초과해 맞힐 경우 : -<?php echo number_format($game_point * $game_besu3);?>포인트
</div>

<form name="game" action="attendance_update.php" method="post">
          <input type="hidden" name="point" VALUE="<?php echo $game_point;?>">
          <input type="hidden" name="besu1" VALUE="<?php echo $game_besu1;?>">
          <input type="hidden" name="besu2" VALUE="<?php echo $game_besu2;?>">
          <input type="hidden" name="besu3" VALUE="<?php echo $game_besu3;?>">
          <input type="hidden" name="range1" VALUE="<?php echo $game_range1;?>">
          <input type="hidden" name="range2" VALUE="<?php echo $game_range2;?>">
          <input type="hidden" name="minus" VALUE="<?php echo $game_minus;?>">

<div class="mx-auto m-5" style="width:200px">
						<div><input class="test11" type="text" border=0 name="number" VALUE="0" onFocus="this.blur();" style="border: 0;font-size: 5rem;width:130px;text-align:center"></div>

						<img id="Start" src="./img/_start.png" onClick="Counter1(); counter=0;"  class="btn btn-primary">
						<input type=image id="Stop" src="./img/_stop.png" onClick="Results(); counter=0;" class="btn btn-primary">
</div>

</form>

<ul class="list-group list-group-horizontal">
  <li class="list-group-item border-0"><img src="./img/gold.svg" style="width:16px"> <?php echo mb_average($member['mb_id'],"a");?></li>
  <li class="list-group-item border-0"><img src="./img/silver.svg" style="width:16px"> <?php echo mb_average($member['mb_id'],"b");?></li>
  <li class="list-group-item border-0"><img src="./img/bronze.svg" style="width:16px"> <?php echo mb_average($member['mb_id'],"c");?></li>
  <li class="list-group-item border-0"><img src="./img/ico_medal_e.png" style="width:16px"> <?php echo mb_average($member['mb_id'],"e");?></li>
</ul>
<br />

<!-- 목록 나오는 부분{ -->
<table class="table">
<tr>
	<th>순서</th>
	<th>로그인시간</th>
	<th>레벨</th>
	<th>닉네임</th>
	<th>접속여부</th>
	<th>오늘</th>
    <th>겜포인트</th>
    <th>금</th>
    <th>은</th>
    <th>동</th>
    <th>-</th>
	<th>총포인트</th>
</tr>
<?php
$sql = " select mb_id, mb_name, mb_nick, mb_level, mb_email, mb_homepage, mb_today_login, mb_point
				from `{$g5['member_table']}`
					where SUBSTRING(mb_today_login,1,10) = '".G5_TIME_YMD."'
					and mb_level < '10'
				order by mb_today_login ";
$result = sql_query($sql);

for ($i=1; $row=sql_fetch_array($result); $i++)
{
	// 자신이라면 체크
	if ($row['mb_id'] == $member['mb_id'])
		$bgcolor = "#FFFFFF";

	$mb_nick = get_sideview($row['mb_id'], $row['mb_nick'], $row['mb_email'], $row['mb_homepage']);
// 본명사용시 사용 $mb_name = get_sideview($row[mb_id], $row[mb_name], $row[mb_email], $row[mb_homepage]);
	$mb_loginchk = mb_loginchk($row['mb_id']);
	$mb_gamechk = mb_gamechk($row['mb_id']);
?>

  <tr>
        <td><?php echo $i?></td>
        <td><?php echo substr($row['mb_today_login'],11,8)?></td>
	    <td><?php echo $row['mb_level'];?></td>
		<td><?php echo $mb_nick ?></td>
		<td><?php echo $mb_loginchk?></td>
		<td><img src="./img/ico_medal_<?php echo $mb_gamechk['agelevel'];?>.png"></td>
        <td><?php echo $mb_gamechk[point]?>P</td>
        <td><?php echo mb_average($row['mb_id'],"a");?></td>
        <td><?php echo mb_average($row['mb_id'],"b");?></td>
        <td><?php echo mb_average($row['mb_id'],"c");?></td>
        <td><?php echo mb_average($row['mb_id'],"e");?></td>
        <td><?php echo number_format($row['mb_point'])?>P</td>
    </tr>
<? } ?>
</table>
<!-- 목록 나오는 부분{ -->
<?php
include_once(G5_PATH.'/_tail.php');

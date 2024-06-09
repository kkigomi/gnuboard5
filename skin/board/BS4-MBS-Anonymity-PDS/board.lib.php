<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

function na_anonymity() {
	global $bo_table, $member, $boset;

	$day = $boset['nday'];
	$day = ($day > 0) ? $day : 0;

	$name = get_cookie($bo_table.'_anonymity_'.$member['mb_id']);
	if(!$name || !$day) {
		$type = $boset['ntype'];
		$head = $boset['nhead'];
		$num = $boset['ntail'];
		if(!$num) $num = 4;

		$tail = '';
		if($num > 0) {
			if($type == "1") { // 한글만
				$str = "가,나,다,라,마,바,사,아,자,차,카,타,파,하,야,거,너,더,러,머,버,서,어,저,처,커,터,퍼,허,겨,녀,려,며,벼,셔,여,져,쳐,켜,텨,펴,혀,고,노,도,로,모,보,소,오,조,초,코,토,포,호,교,뇨,료,묘,쇼,요,죠,쵸,쿄,툐,표,효,구,누,두,루,무,부,수,우,주,추,쿠,투,푸,후,규,뉴,류,뮤,뷰,슈,유,쥬,츄,큐,튜,퓨,휴,그,느,드,르,므,브,스,으,즈,츠,크,트,프,흐,기,니,디,리,미,비,시,이,지,치,키,티,피,히,개,내,대,래,매,배,새,애,재,채,캐,태,패,해,게,네,데,레,메,베,세,에,제,체,케,테,페,헤,예,폐,혜";
			} else if($type == "2") { //영어만
				$str = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z";
			} else if($type == "3") { //숫자만
				$str = "0,1,2,3,4,5,6,7,8,9";
			} else { //영어 + 숫자조합
				$str = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9";
			}

			$temp = explode(",", $str);
			$cnt = count($temp);
			$j = $cnt;
			for($i=0; $i < $cnt; $i++) {
				$temp[$j] = $temp[$i];
				$j++;
			}
			shuffle($temp);
			$tail .= implode('',array_slice($temp,0,$num));
		} 

		$name = $head.$tail;
		set_cookie($bo_table.'_anonymity_'.$member['mb_id'], $name, 3600 * $day); // 24시간
	}

	return $name;
} 

?>
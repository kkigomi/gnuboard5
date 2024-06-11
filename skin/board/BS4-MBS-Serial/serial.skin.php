<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// Serial Board Paging
function na_serial_paging($write_pages, $cur_page, $total_page, $url, $add='') {

	$first = '<i class="fa fa-angle-double-left"></i>';
	$prev = '<i class="fa fa-angle-left"></i>';
	$next = '<i class="fa fa-angle-right"></i>';
	$last = '<i class="fa fa-angle-double-right"></i>';

    //$url = preg_replace('#(&amp;)?page=[0-9]*#', '', $url);
	//$url .= substr($url, -1) === '?' ? 'page=' : '&amp;page=';

	if(!$cur_page) $cur_page = 1;
	if(!$total_page) $total_page = 1;

	$str = '';
	if($first) {
		if ($cur_page < 2) {
			$str .= '<li class="page-first page-item disabled"><a class="page-link">'.$first.'</a></li>';
		} else {
			$str .= '<li class="page-first page-item"><a class="page-link" href="'.$url.'1'.$add.'">'.$first.'<span class="sr-only">(first)</span></a></li>';
		}
	}

	$start_page = (((int)(($cur_page - 1 ) / $write_pages)) * $write_pages) + 1;
	$end_page = $start_page + $write_pages - 1;

	if ($end_page >= $total_page) { 
		$end_page = $total_page;
	}

	if ($start_page > 1) { 
		$str .= '<li class="page-prev page-item"><a class="page-link" href="'.$url.($start_page-1).$add.'">'.$prev.'<span class="sr-only">(previous)</span></a></li>';
	} else {
		$str .= '<li class="page-prev page-item disabled"><a class="page-link">'.$prev.'</a></li>'; 
	}

	if ($total_page > 0){
		for ($k=$start_page;$k<=$end_page;$k++){
			if ($cur_page != $k) {
				$str .= '<li class="page-item"><a class="page-link" href="'.$url.$k.$add.'">'.$k.'</a></li>';
			} else {
				$str .= '<li class="page-item active" aria-current="page"><a class="page-link">'.$k.'<span class="sr-only">(current)</span>
</a></li>';
			}
		}
	}

	if ($total_page > $end_page) {
		$str .= '<li class="page-next page-item"><a class="page-link" href="'.$url.($end_page+1).$add.'">'.$next.'<span class="sr-only">(next)</span></a></li>';
	} else {
		$str .= '<li class="page-next page-item disabled"><a class="page-link">'.$next.'</a></li>';
	}

	if($last) {
		if ($cur_page < $total_page) {
			$str .= '<li class="page-last page-item"><a class="page-link" href="'.$url.($total_page).$add.'">'.$last.'<span class="sr-only">(last)</span></a></li>';
		} else {
			$str .= '<li class="page-last page-item disabled"><a class="page-link">'.$last.'</a></li>';
		}
	}

	return $str;
}

//목록수
$boset['spage_rows'] = (isset($boset['spage_rows']) && (int)$boset['spage_rows']) ? $boset['spage_rows'] : 10;
$boset['smpage_rows'] = (isset($boset['smpage_rows']) && (int)$boset['smpage_rows']) ? $boset['smpage_rows'] : 10;
$spage_rows = (G5_IS_MOBILE) ? $boset['smpage_rows'] : $boset['spage_rows'];

if(!$spage_rows || $spage_rows < 1)
	$spage_rows = 10;

// 페이지가 없으면 첫 페이지 (1 페이지)
if (!isset($spage) || (int)$spage < 1) { 
	$spage = 1; 
} 

// 공통 쿼리
$sql_serial = "from $write_table where wr_is_comment = '0' and wr_1 <> '' and wr_1 = '{$sid}'";

// 목록 정리
$stotal = sql_fetch("select count(*) as cnt $sql_serial ", false);
$stotal_count = $stotal['cnt'];
$stotal_page  = ceil($stotal_count / $spage_rows);  // 전체 페이지 계산
$spage_start = ($spage - 1) * $spage_rows; // 시작 열을 구함

$list = array();
$result = sql_query(" select * $sql_serial order by wr_num, wr_reply limit $spage_start, $spage_rows ", false);
for ($i=0; $row=sql_fetch_array($result); $i++) { 
	$list[$i] = get_list($row, $board, $board_skin_url, G5_IS_MOBILE ? $board['bo_mobile_subject_len'] : $board['bo_subject_len']);
	$list_num = $stotal_count - ($spage - 1) * $spage_rows;
	$list[$i]['href'] = get_pretty_url($board['bo_table'], $list[$i]['wr_id'], $qstr.'&amp;spage='.$spage);
	$list[$i]['num'] = $list_num - $i;
}

$list_cnt = count($list);

if(!$list_cnt)
	return;

// 목록 헤드
$head_color = (isset($boset['head_color']) && $boset['head_color']) ? $boset['head_color'] : 'primary';
if(isset($boset['head_skin']) && $boset['head_skin']) {
	add_stylesheet('<link rel="stylesheet" href="'.NA_URL.'/skin/head/'.$boset['head_skin'].'.css">', 0);
	$head_class = 'list-head';
} else {
	$head_class = 'na-table-head border-'.$head_color;
}

// 글 이동
$is_list_link = false;
$boset['target'] = isset($boset['target']) ? $boset['target'] : '';
switch($boset['target']) {
	case '1' : $target = ' target="_blank"'; break;
	case '2' : $is_list_link = true; break;
	case '3' : $target = ' target="_blank"'; $is_list_link = true; break;
	default	 : $target = ''; break; 
}

?>

<section id="serial_list" class="my-4">

	<!-- 목록 헤드 -->
	<div class="d-block d-md-none w-100 mb-0 bg-<?php echo $head_color ?>" style="height:4px;"></div>

	<div class="na-table d-none d-md-table w-100 mb-0">
		<div class="<?php echo $head_class ?> d-md-table-row">
			<div class="d-md-table-cell nw-5 px-md-1">번호</div>
			<div class="d-md-table-cell pr-md-1">
				연재 목록
			</div>
			<div class="d-md-table-cell nw-6 pr-md-1">날짜</div>
			<div class="d-md-table-cell nw-4 pr-md-1">조회</div>
			<?php if($is_good) { ?>
				<div class="d-md-table-cell nw-3 pr-md-1">추천</div>
			<?php } ?>
			<?php if($is_nogood) { ?>
				<div class="d-md-table-cell nw-3 pr-md-1">비추</div>
			<?php } ?>
		</div>
	</div>

	<ul id="list-body" class="na-table d-md-table w-100">
	<?php
	for ($i=0; $i < $list_cnt; $i++) { 

		// 글 아이콘
		$wr_icon = '';
		$is_lock = false;
		if ($list[$i]['icon_secret']) {
			$wr_icon = '<span class="na-icon na-secret"></span>';
			$is_lock = true;
		} else if ($list[$i]['icon_hot']) {
			$wr_icon = '<span class="na-icon na-hot"></span>';
		} else if ($list[$i]['icon_new']) {
			$wr_icon = '<span class="na-icon na-new"></span>';
		}

		// 링크 이동
		if($is_list_link && $list[$i]['wr_link1']) {
			$list[$i]['href'] = $list[$i]['link_href'][1];
		}

		// 전체 보기에서 분류 출력하기
		if(!$sca && $is_category && $list[$i]['ca_name']) {
			$list[$i]['subject'] = $list[$i]['ca_name'].' <span class="na-bar"></span> '.$list[$i]['subject'];
		}

		// 공지, 현재글 스타일 체크
		$li_css = '';
		if ($wr_id == $list[$i]['wr_id']) {
			$li_css = ' bg-light';
			$list[$i]['num'] = '<span class="na-text text-primary">열람</span>';
			$list[$i]['subject'] = '<b class="text-primary">'.$list[$i]['subject'].'</b>';
		} else {
			$list[$i]['num'] = '<span class="sr-only">번호</span>'.$list[$i]['num'];
		}

		// 파일 아이콘
		$icon_file = '';
		if(isset($list[$i]['as_thumb']) && $list[$i]['as_thumb']) {
			$icon_file = '<span class="na-ticon na-image"></span>';
		} else if(isset($list[$i]['icon_file'])) {
			$icon_file = '<span class="na-ticon na-file"></span>';
		}

	?>
		<li class="list-item d-md-table-row px-3 py-2 p-md-0 text-md-center text-muted border-bottom<?php echo $li_css;?>">
			<div class="d-none d-md-table-cell nw-5 f-sm font-weight-normal py-md-2 px-md-1">
				<?php echo $list[$i]['num'] ?>
			</div>
			<div class="d-md-table-cell text-left py-md-2 pr-md-1">
				<div class="na-title float-md-left">
					<div class="na-item">
						<a href="<?php echo $list[$i]['href'] ?>" class="na-subject"<?php echo $target ?>>
							<?php
								if($list[$i]['icon_reply'])
									echo '<span class="na-hicon na-reply"></span>'.PHP_EOL;

								echo $wr_icon;
							?>
							<?php echo $list[$i]['subject'] ?>
						</a>
						<?php echo $icon_file ?>
						<?php
							//if(isset($list[$i]['icon_link']) && $list[$i]['icon_link'])
								//echo '<span class="na-ticon na-link"></span>'.PHP_EOL;
						?>
						<?php if($list[$i]['wr_comment']) { ?>
							<div class="na-info">
								<span class="sr-only">댓글</span>
								<span class="count-plus orangered">
									<?php echo $list[$i]['wr_comment'] ?>
								</span>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="float-left float-md-none d-md-table-cell nw-6 nw-md-auto f-sm font-weight-normal py-md-2 pr-md-1">
				<span class="sr-only">등록일</span>
				<?php echo na_date($list[$i]['wr_datetime'], 'orangered', 'H:i', 'm.d', 'Y.m.d') ?>
			</div>
			<div class="float-left float-md-none d-md-table-cell nw-4 nw-md-auto f-sm font-weight-normal py-md-2 pr-md-1">
				<i class="fa fa-eye d-md-none" aria-hidden="true"></i>
				<span class="sr-only">조회</span>
				<?php echo $list[$i]['wr_hit'] ?>
			</div>
			<?php if($is_good) { ?>
				<div class="float-left float-md-none d-md-table-cell nw-3 nw-md-auto f-sm font-weight-normal py-md-2 pr-md-1">
					<i class="fa fa-thumbs-o-up d-md-none" aria-hidden="true"></i>
					<span class="sr-only">추천</span>
					<?php echo $list[$i]['wr_good'] ?>
				</div>
			<?php } ?>
			<?php if($is_nogood) { ?>
				<div class="float-left float-md-none d-md-table-cell nw-3 nw-md-auto f-sm font-weight-normal py-md-2 pr-md-1">
					<i class="fa fa-thumbs-o-down d-md-none" aria-hidden="true"></i>
					<span class="sr-only">비추천</span>
					<?php echo $list[$i]['wr_nogood'] ?>
				</div>
			<?php } ?>
			<div class="clearfix d-block d-md-none"></div>
		</li>
	<?php } ?>
	</ul>
	<?php if (!$list_cnt) { ?>
		<div class="f-de font-weight-normal px-3 py-5 text-muted text-center border-bottom">연재글이 없습니다.</div>
	<?php } ?>
</section>

<!-- 연재글 페이징 시작 { -->
<div class="font-weight-normal px-3 px-sm-0">
	<ul class="pagination justify-content-center en mb-0">
		<?php echo na_serial_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $spage, $stotal_page, get_pretty_url($bo_table, $sid, $qstr.'&amp;spage='));?>
	</ul>
</div>
<!-- } 연재글 페이징 끝 -->
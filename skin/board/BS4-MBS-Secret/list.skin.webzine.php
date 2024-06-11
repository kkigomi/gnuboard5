<?php
if (!defined('_GNUBOARD_')) {
	include_once('./_common.php');
	include_once(NA_PATH.'/bbs/list.rows.php');
	$is_ajax = true;
}

// 이미지 영역 및 썸네일 크기 설정
$boset['wthumb_w'] = ($boset['wthumb_w'] == "") ? 400 : (int)$boset['wthumb_w'];
$boset['wthumb_h'] = ($boset['wthumb_h'] == "") ? 225 : (int)$boset['wthumb_h'];

if($boset['wthumb_w'] && $boset['wthumb_h']) {
	$img_height = ($boset['wthumb_h'] / $boset['wthumb_w']) * 100;
} else {
	$img_height = ($boset['wthumb_d']) ? $boset['wthumb_d'] : '56.25';
}

$cap_new = (isset($boset['new']) && $boset['new']) ? $boset['new'] : 'primary';
$cut_txt = (isset($boset['wcut']) && (int)$boset['wcut']) ? $boset['wcut'] : 80;
$img_loc = (isset($boset['wimg']) && $boset['wimg']) ? ' order-sm-3' : '';

// 이미지 미리보기
$is_popover = (!G5_IS_MOBILE && isset($boset['popover'])) ? $boset['popover'] : '';

// No 이미지
$no_img = isset($boset['wno_img']) ? na_url($boset['wno_img']) : '';

// 이미지 크기
$boset['wxl'] = isset($boset['wxl']) ? (int)$boset['wxl'] : 4;
$boset['wlg'] = isset($boset['wlg']) ? (int)$boset['wlg'] : 0;
$boset['wmd'] = isset($boset['wmd']) ? (int)$boset['wmd'] : 0;
$boset['wsm'] = isset($boset['wsm']) ? (int)$boset['wsm'] : 5;

$img_cols = '';
if($boset['wsm'])
	$img_cols .= ' col-sm-'.$boset['wsm'];
if($boset['wmd'])
	$img_cols .= ' col-md-'.$boset['wmd'];
if($boset['wlg'])
	$img_cols .= ' col-lg-'.$boset['wlg'];
if($boset['wxl'])
	$img_cols .= ' col-xl-'.$boset['wxl'];

// 글 이동
$is_list_link = false;
$boset['target'] = isset($boset['target']) ? $boset['target'] : '';
switch($boset['target']) {
	case '1' : $target = ' target="_blank"'; break;
	case '2' : $is_list_link = true; break;
	case '3' : $target = ' target="_blank"'; $is_list_link = true; break;
	default	 : $target = ''; break; 
}

// 글 수
$list_cnt = count($list);
?>

<?php if(!$is_ajax) { //더보기 오픈 ?>

<section id="bo_list" class="mb-4">

	<!-- 목록 헤드 -->
	<div class="w-100 mb-0 bg-<?php echo $head_color ?>" style="height:4px;"></div>

	<ul class="na-table d-md-table w-100 mb-3">
	<?php
	// 공지
	if($board['bo_notice']) {
		for ($i=0; $i < $list_cnt; $i++) { 

			if(!$list[$i]['is_notice'])
				continue;

			$wr_icon = '';
			$is_lock = false;
			if ($list[$i]['icon_secret']) {
				$wr_icon = '<span class="na-icon na-secret"></span>';
				$is_lock = true;
			} else if ($list[$i]['icon_new']) {
				$wr_icon = '<span class="na-icon na-new"></span>';
			}

			// 현재 글
			$li_css = ($wr_id == $list[$i]['wr_id']) ? ' bg-light' : '';

			// 현재 글
			if($wr_id == $list[$i]['wr_id']) {
				$li_css = ' bg-light';
				$list[$i]['num'] = '<span class="na-text text-primary">열람</span>';
				$list[$i]['subject'] = '<b class="text-primary">'.$list[$i]['subject'].'</b>';
			} else {
				$li_css = '';
				$list[$i]['num'] = '<span class="na-notice bg-'.$head_color.'"></span><span class="sr-only">공지사항</span>';
				$list[$i]['subject'] = '<b>'.$list[$i]['subject'].'</b>';
			}

			// 이미지 미리보기
			$wr_popover = $thumb = '';
			if($is_popover) {
				$img = na_wr_img($bo_table, $list[$i]);
				$thumb = ($boset['wthumb_w']) ? na_thumb($img, $boset['wthumb_w'], $boset['wthumb_h']) : $img;
				if($thumb) {
					$wr_popover = ' data-toggle="popover-img" data-img="'.$thumb.'"';
				}
			}

			// 파일 아이콘
			$icon_file = '';
			if($thumb || (isset($list[$i]['as_thumb']) && $list[$i]['as_thumb'])) {
				$icon_file = '<span class="na-ticon na-image"></span>';
			} else if(isset($list[$i]['icon_file'])) {
				$icon_file = '<span class="na-ticon na-file"></span>';
			}

	?>
		<li class="d-md-table-row px-3 py-2 p-md-0 text-md-center text-muted border-bottom<?php echo $li_css;?>">
			<div class="d-none d-md-table-cell nw-5 f-sm font-weight-normal py-md-2 px-md-1">
				<?php echo $list[$i]['num'] ?>
			</div>
			<div class="d-md-table-cell text-left py-md-2 pr-md-1">
				<div class="na-title float-md-left">
					<div class="na-item">
						<?php if ($is_checkbox) { ?>
							<input type="checkbox" class="mb-0 mr-2" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
						<?php } ?>
						<a href="<?php echo $list[$i]['href'] ?>" class="na-subject"<?php echo $wr_popover ?>>
							<?php echo $wr_icon; ?>
							<?php echo $list[$i]['subject'] ?>
						</a>
						<?php echo $icon_file ?>
						<?php if($list[$i]['wr_comment']) { ?>
							<div class="na-info mr-3">
								<span class="sr-only">댓글</span>
								<span class="count-plus orangered">
									<?php echo $list[$i]['wr_comment'] ?>
								</span>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="float-right float-md-none d-md-table-cell nw-10 nw-md-auto text-left f-sm font-weight-normal pl-2 py-md-2 pr-md-1">
				<span class="sr-only">등록자</span>
				<?php echo na_name_photo($list[$i]['mb_id'], $list[$i]['name']) ?>
			</div>
			<div class="float-left float-md-none d-md-table-cell nw-6 nw-md-auto f-sm font-weight-normal py-md-2 pr-md-1">
				<i class="fa fa-clock-o d-md-none" aria-hidden="true"></i>
				<span class="sr-only">등록일</span>
				<?php echo na_date($list[$i]['wr_datetime'], 'orangered', 'H:i', 'm.d', 'Y.m.d') ?>
			</div>
			<div class="float-left float-md-none d-md-table-cell nw-4 nw-md-auto f-sm font-weight-normal py-md-2 pr-md-1">
				<i class="fa fa-eye d-md-none" aria-hidden="true"></i>
				<span class="sr-only">조회</span>
				<?php echo $list[$i]['wr_hit'] ?>
			</div>
			<div class="clearfix d-block d-md-none"></div>
		</li>
	<?php 
		}
	} // 공지 ?>
	</ul>

	<div id="bo_webzine">
		<ul id="list-body">
<?php } // 더보기 닫기 ?>

		<?php
		// 리스트
		$n = 0;
		for ($i=0; $i < $list_cnt; $i++) { 

			// 공지는 제외	
			if($list[$i]['is_notice'])
				continue;

			// 글수 체크
			$n++;

			// 이미지용
			$wr_alt = get_text(str_replace('"', '', $list[$i]['wr_subject']));

			// 아이콘 체크
			$wr_icon = $wr_tack = $wr_cap = '';
			if ($list[$i]['icon_secret']) {
				$is_lock = true;
				$wr_icon = '<span class="na-icon na-secret"></span>';
			}

			// 링크 이동
			if($is_list_link && $list[$i]['wr_link1']) {
				$list[$i]['href'] = $list[$i]['link_href'][1];
			}

			// 새 글, 현재 글 스타일
			$wr_now = '';
			if ($wr_id == $list[$i]['wr_id']) {
				$list[$i]['subject'] = '<b class="text-primary">'.$list[$i]['subject'].'</b>';
				$wr_now = '<div class="wr-now"></div>';
				$wr_cap = '<span class="label-cap en bg-orangered">Now</span>';
			} else if($list[$i]['icon_new']) {
				$wr_cap = '<span class="label-cap en bg-'.$cap_new.'">New</span>';
			}

			// 이미지 추출
			$img = na_wr_img($bo_table, $list[$i]);

			// 썸네일 생성
			$thumb = ($boset['wthumb_w']) ? na_thumb($img, $boset['wthumb_w'], $boset['wthumb_h']) : $img;
			if(!$thumb && $no_img) {
				$thumb = $no_img;
			}
		?>
			<li class="list-item border-bottom pb-3 mb-3">
				<div class="card border-0">
					<div class="row no-gutters">
						<?php if($thumb) { ?>
							<div class="order-1<?php echo $img_loc ?> col-12<?php echo $img_cols ?>">
								<div class="mx-3 mx-sm-0 mb-3 mb-sm-0">
									<div class="img-wrap bg-light na-round" style="padding-bottom:<?php echo $img_height ?>%;">
										<div class="img-item">
											<?php if ($is_checkbox) { ?>
												<span class="chk-box">
													<input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
												</span>
											<?php } ?>
											<a href="<?php echo $list[$i]['href'] ?>"<?php echo $target ?>>
												<?php echo $wr_now ?>
												<?php echo $wr_tack ?>
												<?php echo $wr_cap ?>
												<img src="<?php echo $thumb ?>" alt="<?php echo $wr_alt ?>" class="img-render na-round">
											</a>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
						<div class="col order-2">
							<div class="card-body pt-2 pb-0">
								<h5 class="card-title font-weight-bold">
									<a href="<?php echo $list[$i]['href'] ?>"<?php echo $target ?>>
										<?php echo $wr_icon ?>
										<?php echo $list[$i]['subject'] ?>
									</a>
									<?php if($list[$i]['wr_comment']) { ?>
										<span class="sr-only">댓글</span>
										<span class="count-plus orangered">
											<?php echo $list[$i]['wr_comment'] ?>
										</span>
									<?php } ?>								
								</h5>

								<div class="clearfix font-weight-normal f-sm mb-2">
									<div class="float-right ml-2">
										<span class="sr-only">등록자</span>
										<?php echo na_name_photo($list[$i]['mb_id'], $list[$i]['name']) ?>
									</div>
									<div class="float-left text-muted">
										<i class="fa fa-clock-o" aria-hidden="true"></i>
										<span class="sr-only">등록일</span>
										<?php echo na_date($list[$i]['wr_datetime'], 'orangered', 'H:i', 'm.d', 'm.d') ?>

										<i class="fa fa-eye ml-3 ml-sm-4" aria-hidden="true"></i>
										<span class="sr-only">조회</span>
										<?php echo $list[$i]['wr_hit'] ?>

										<?php if($is_good) { ?>
											<i class="fa fa-thumbs-o-up ml-3 ml-sm-4" aria-hidden="true"></i>
											<span class="sr-only">추천</span>
											<?php echo $list[$i]['wr_good'] ?>
										<?php } ?>

										<?php if($is_nogood) { ?>
											<i class="fa fa-thumbs-o-down ml-3 ml-sm-4" aria-hidden="true"></i>
											<span class="sr-only">비추천</span>
											<?php echo $list[$i]['wr_nogood'] ?>
										<?php } ?>
									</div>
								</div>

								<p class="card-text">
									<?php if($is_category && $list[$i]['ca_name']) { ?>
										<?php echo $list[$i]['ca_name'] ?>
										<span class="na-bar"></span>
									<?php } ?>
									<?php echo na_cut_text($list[$i]['wr_content'], $cut_txt) ?>
								</p>
							</div>
						</div>
					</div>
				</div>
			</li>
		<?php } ?>

<?php if(!$is_ajax) { //더보기 오픈 ?>
		</ul>

		<?php if(!$n) { ?>
			<div class="f-de px-3 py-5 text-muted text-center">
				게시물이 없습니다.
			</div>
		<?php } ?>
	</div>
</section>
<?php } //더보기 닫기 ?>
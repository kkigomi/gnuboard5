<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 목록 타입
$list_type = isset($boset['list_type']) ? $boset['list_type'] : '';

// 더보기 & 무한스크롤
if($list_type) {
	na_script('imagesloaded');
	na_script('infinite');
}

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

// 목록 상단 컨텐츠
if(isset($boset['contt']) && $boset['contt']) {
	@include($board_skin_path.'/content/'.$boset['contt'].'.php');
}

// 목록 스타일
$list_default = isset($boset['list_style']) ? $boset['list_style'] : '';
$list_style = get_session($bo_table.'_list');
$list_style = ($list_style) ? $list_style : $list_default;
$list_style = (in_array($list_style, array('list', 'gallery', 'webzine'))) ? $list_style : 'list';

// 스킨설정
$is_skin_setup = (($is_admin == 'super' || IS_DEMO) && is_file($board_skin_path.'/setup.skin.php')) ? true : false;

// 목록 헤드
$head_color = (isset($boset['head_color']) && $boset['head_color']) ? $boset['head_color'] : 'primary';
if(isset($boset['head_skin']) && $boset['head_skin']) {
	add_stylesheet('<link rel="stylesheet" href="'.NA_URL.'/skin/head/'.$boset['head_skin'].'.css">', 0);
	$head_class = 'list-head';
} else {
	$head_class = 'na-table-head border-'.$head_color;
}

?>

<!-- 게시판 목록 시작 { -->
<div id="bo_list_wrap" class="mb-4">

	<?php @include_once($board_skin_path.'/tags.skin.php') ?>

	<!-- 검색창 시작 { -->
	<div id="bo_search" class="collapse<?php echo ((isset($boset['search_open']) && $boset['search_open']) || $stx) ? ' show' : ''; ?>">
		<div class="alert bg-light border p-2 p-sm-3 mb-3 mx-3 mx-sm-0">
			<form id="fsearch" name="fsearch" method="get" class="m-auto" style="max-width:600px;">
				<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
				<input type="hidden" name="sca" value="<?php echo $sca ?>">
				<div class="form-row mx-n1">
					<div class="col-6 col-sm-3 px-1">
						<label for="sfl" class="sr-only">검색대상</label>
						<select name="sfl" class="custom-select">
							<?php echo get_board_sfl_select_options($sfl); ?>
						</select>
					</div>
					<div class="col-6 col-sm-3 px-1">
						<select name="sop" class="custom-select">
							<option value="and"<?php echo get_selected($sop, "and") ?>>그리고</option>
							<option value="or"<?php echo get_selected($sop, "or") ?>>또는</option>
						</select>	
					</div>
					<div class="col-12 col-sm-6 pt-2 pt-sm-0 px-1">
						<label for="stx" class="sr-only">검색어</label>
						<div class="input-group">
							<input type="text" id="bo_stx" name="stx" value="<?php echo stripslashes($stx) ?>" required class="form-control" placeholder="검색어를 입력해 주세요.">
							<div class="input-group-append">
								<button type="submit" class="btn btn-primary" title="검색하기">
									<i class="fa fa-search" aria-hidden="true"></i>
									<span class="sr-only">검색하기</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<!-- } 검색창 끝 -->

    <?php 
	// 게시판 카테고리
	if ($is_category) 
		include_once($board_skin_path.'/category.skin.php'); 
	?>

	<form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
		<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
		<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
		<input type="hidden" name="stx" value="<?php echo $stx ?>">
		<input type="hidden" name="spt" value="<?php echo $spt ?>">
		<input type="hidden" name="sca" value="<?php echo $sca ?>">
		<input type="hidden" name="sst" value="<?php echo $sst ?>">
		<input type="hidden" name="sod" value="<?php echo $sod ?>">
		<input type="hidden" name="page" value="<?php echo $page ?>">
		<input type="hidden" name="sw" value="">

		<!-- 게시판 페이지 정보 및 버튼 시작 { -->
		<div id="bo_btn_top" class="clearfix f-de font-weight-normal mb-2">
			<div class="d-sm-flex align-items-center">
				<div id="bo_list_total" class="flex-sm-grow-1">
					<div class="px-3 px-sm-0">
						<?php echo (isset($sca) && $sca) ? $sca : '전체'; ?>
						<b><?php echo number_format((int)$total_count) ?></b> / <?php echo $page ?> 페이지
					</div>
					<div class="d-block d-sm-none border-top my-2"></div>
				</div>
				<div class="px-3 px-sm-0 text-right">
					<?php if ($is_admin == 'super' || $admin_href || $is_auth || IS_DEMO) {  ?>
						<div class="btn-group" role="group">
							<button type="button" class="btn btn_admin nofocus dropdown-toggle dropdown-toggle-empty dropdown-toggle-split p-1" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false" title="게시판 관리 옵션">
								<i class="fa fa-cog fa-spin fa-fw fa-md" aria-hidden="true"></i>
								<span class="sr-only">게시판 관리 옵션</span>
							</button>
							<div class="dropdown-menu dropdown-menu-right p-0 border-0 bg-transparent text-right">
								<div class="btn-group-vertical">
									<?php if ($admin_href) { ?>
										<a href="<?php echo $admin_href ?>" class="btn btn-primary py-2" role="button">
											<i class="fa fa-cog fa-fw" aria-hidden="true"></i> 보드설정
										</a>
									<?php } ?>
									<?php if($is_skin_setup) { ?>
										<a href="<?php echo na_setup_href('board', $bo_table) ?>" class="btn btn-primary btn-setup py-2" role="button">
											<i class="fa fa-cogs fa-fw" aria-hidden="true"></i> 스킨설정
										</a>
									<?php } ?>
									<?php if ($is_checkbox) { ?>
										<a href="javascript:;" class="btn btn-primary py-2" role="button">
											<label class="p-0 m-0" for="allCheck">
												<i class="fa fa-check-square-o fa-fw" aria-hidden="true"></i> 
												전체선택						
											</label>
											<div class="sr-only">
												<input type="checkbox" id="allCheck" onclick="if (this.checked) all_checked(true); else all_checked(false);">
											</div>
										</a>
										<button type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value" class="btn btn-primary py-2">
											<i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> 
											선택삭제
										</button>
										<button type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value" class="btn btn-primary py-2">
											<i class="fa fa-files-o fa-fw" aria-hidden="true"></i> 
											선택복사
										</button>
										<button type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value" class="btn btn-primary py-2">
											<i class="fa fa-arrows fa-fw" aria-hidden="true"></i>
											선택이동
										</button>
									<?php } ?>
								</div>
							</div>
						</div>
					<?php }  ?>
					<?php if ($rss_href) { ?>
						<a href="<?php echo $rss_href ?>" class="btn btn_b01 nofocus p-1" title="RSS">
							<i class="fa fa-rss fa-fw fa-md" aria-hidden="true"></i>
							<span class="sr-only">RSS</span>
						</a>
					<?php } ?>
					<div class="btn-group" role="group">
						<button type="button" class="btn btn_b01 nofocus dropdown-toggle dropdown-toggle-empty dropdown-toggle-split p-1" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false" title="게시물 정렬">
							<?php
								switch($sst) {
									case 'wr_datetime'	:	$sst_icon = 'history'; 
															$sst_txt = '날짜순 정렬'; 
															break;
									case 'wr_hit'		:	$sst_icon = 'eye'; 
															$sst_txt = '조회순 정렬'; 
															break;
									case 'wr_good'		:	$sst_icon = 'thumbs-o-up'; 
															$sst_txt = '추천순 정렬'; 
															break;
									case 'wr_nogood'	:	$sst_icon = 'thumbs-o-down'; 
															$sst_txt = '비추천순 정렬'; 
															break;
									default				:	$sst_icon = 'sort-numeric-desc'; 
															$sst_txt = '게시물 정렬'; 
															break;
								}
							?>
							<i class="fa fa-<?php echo $sst_icon ?> fa-fw fa-md" aria-hidden="true"></i>
							<span class="sr-only"><?php echo $sst_txt ?></span>
						</button>
						<div class="dropdown-menu dropdown-menu-right p-0 border-0 bg-transparent text-right">
							<div class="btn-group-vertical bg-white border rounded py-1">
								<?php echo str_replace('>', ' class="btn px-3 py-1 text-left" role="button">', subject_sort_link('wr_datetime', $qstr2, 1)) ?>
									날짜순
								</a>
								<?php echo str_replace('>', ' class="btn px-3 py-1 text-left" role="button">', subject_sort_link('wr_hit', $qstr2, 1)) ?>
									조회순
								</a>
								<?php if($is_good) { ?>
									<?php echo str_replace('>', ' class="btn px-3 py-1 text-left" role="button">', subject_sort_link('wr_good', $qstr2, 1)) ?>
										추천순
									</a>
								<?php } ?>
								<?php if($is_nogood) { ?>
									<?php echo str_replace('>', ' class="btn px-3 py-1 text-left" role="button">', subject_sort_link('wr_nogood', $qstr2, 1)) ?>
										비추천순
									</a>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php if($list_style != 'list') { ?>
						<button type="button" class="btn btn_b01 nofocus p-1" title="리스트 스타일" onclick="list_style('list');">
							<i class="fa fa-bars fa-fw fa-md" aria-hidden="true"></i>
							<span class="sr-only">리스트 스타일</span>
						</button>
					<?php } ?>
					<?php if($list_style != 'webzine') { ?>
						<button type="button" class="btn btn_b01 nofocus p-1" title="웹진 스타일" onclick="list_style('webzine');">
							<i class="fa fa-list-ul fa-fw fa-md" aria-hidden="true"></i>
							<span class="sr-only">웹진 스타일</span>
						</button>
					<?php } ?>
					<?php if($list_style != 'gallery') { ?>
						<button type="button" class="btn btn_b01 nofocus p-1" title="갤러리 스타일" onclick="list_style('gallery');">
							<i class="fa fa-th fa-fw fa-md" aria-hidden="true"></i>
							<span class="sr-only">갤러리 스타일</span>
						</button>
					<?php } ?>
					<button type="button" class="btn btn_b01 nofocus p-1" title="게시판 검색" data-toggle="collapse" data-target="#bo_search" aria-expanded="false" aria-controls="bo_search">
						<i class="fa fa-search fa-fw fa-md" aria-hidden="true"></i>
						<span class="sr-only">게시판 검색</span>
					</button>
					<?php if ($write_href && !$wr_id) { ?>
						<a href="<?php echo $write_href ?>" class="btn btn-primary nofocus py-1 ml-2" role="button">
							<i class="fa fa-pencil" aria-hidden="true"></i>
							쓰기
						</a>
					<?php } ?>
				</div>
			</div>
		</div>
		<!-- } 게시판 페이지 정보 및 버튼 끝 -->

		<!-- 게시물 목록 시작 { -->
		<?php
			$is_ajax = false;
			@include_once($board_skin_path.'/list.skin.'.$list_style.'.php'); 
		?>
		<!-- } 게시물 목록 끝 -->
		<?php if($list_type) { ?>
			<div id="list-nav">
				<a href="<?php echo $board_skin_url ?>/list.skin.<?php echo $list_style ?>.php?bo_table=<?php echo $bo_table ?><?php echo preg_replace("/&amp;page\=([0-9]+)/", "", $qstr) ?>&amp;npg=<?php echo ($page > 1) ? ($page - 1) : 0; ?>&amp;page=2"></a>
			</div>

			<?php if($list_type == "1") { ?>
				<div class="row mt-3 mb-4">
					<div class="col-6 offset-3 col-lg-4 offset-lg-4">
						<button id="list-more" class="btn btn-primary btn-block py-0" title="더보기">
							<i class="fa fa-angle-down fa-3x" aria-hidden="true"></i>
							<span class="sr-only">더보기</span>
						</button>					
					</div>
				</div>
			<?php } ?>

			<script>
			$(function(){
				var $container = $('#list-body');
				$container.infinitescroll({
					navSelector  : '#list-nav',    // selector for the paged navigation
					nextSelector : '#list-nav a',  // selector for the NEXT link (to page 2)
					itemSelector : '.list-item',     // selector for all items you'll retrieve
					loading: {
						msgText: '로딩 중...',
						finishedMsg: '더이상 게시물이 없습니다.',
						img: '<?php echo NA_URL ?>/img/loader.gif',
					}
				}, function(newElements) { // trigger Masonry as a callback
					var $newElems = $(newElements).css({ opacity: 1 });
					$newElems.imagesLoaded(function(){
						$container.append($newElems);
					}).animate({ opacity: 1 });
					// 이름 레이어 숨기기 : IE 때문에...ㅠㅠ
					$('#list-body .sv_wrap .sv').hide();
				});
				<?php if($list_type == "1") { ?>
				$(window).unbind('#list-body .infscr');
				$('#list-more').click(function(){
				   $container.infinitescroll('retrieve');
				   $('#list-nav').show();
					return false;
				});
				$(document).ajaxError(function(e,xhr,opt){
					if(xhr.status==404) $('#list-nav').remove();
				});
				<?php } ?>
			});
			</script>
		<?php } ?>

		<!-- 페이징 시작 { -->
		<div class="font-weight-normal px-3 px-sm-0">
			<ul class="pagination justify-content-center en mb-0">
				<?php if($prev_part_href) { ?>
					<li class="page-item"><a class="page-link" href="<?php echo $prev_part_href;?>">Prev</a></li>
				<?php } ?>
				<?php echo na_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, get_pretty_url($bo_table, '', $qstr.'&amp;page='));?>
				<?php if($next_part_href) { ?>
					<li class="page-item"><a  class="page-link" href="<?php echo $next_part_href;?>">Next</a></li>
				<?php } ?>
			</ul>
		</div>
		<!-- } 페이징 끝 -->
	</form>

</div>

<?php if ($is_checkbox) { ?>
<noscript>
<p align="center">자바스크립트를 사용하지 않는 경우 별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>

<script>
function all_checked(sw) {
	var f = document.fboardlist;

	for (var i=0; i<f.length; i++) {
		if (f.elements[i].name == "chk_wr_id[]")
			f.elements[i].checked = sw;
	}
}
function fboardlist_submit(f) {
	var chk_count = 0;

	for (var i=0; i<f.length; i++) {
		if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
			chk_count++;
	}

	if (!chk_count) {
		alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
		return false;
	}

	if(document.pressed == "선택복사") {
		select_copy("copy");
		return;
	}

	if(document.pressed == "선택이동") {
		select_copy("move");
		return;
	}

	if(document.pressed == "선택삭제") {
		if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다.\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
			return false;

		f.removeAttribute("target");
        f.action = g5_bbs_url+"/board_list_update.php";
	}

	return true;
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
	var f = document.fboardlist;

	if (sw == "copy")
		str = "복사";
	else
		str = "이동";

	var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

	f.sw.value = sw;
	f.target = "move";
    f.action = g5_bbs_url+"/move.php";
	f.submit();
}
</script>
<?php } ?>
<script>
function list_style(type){ 
	$.ajax({
		type: "POST", 
		url: "<?php echo $board_skin_url ?>/ajax.list.php",
		data: {bo_table : "<?php echo $bo_table ?>", list_style : type },
		success: function(data){
			window.location.reload();
		},
		error: function(e){
			console.log("ERROR : ", e); // 전송 후 에러 발생 시 실행 코드
		}
	});
}
</script>
<!-- } 게시판 목록 끝 -->

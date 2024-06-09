<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!isset($boset['na_tag']) || !$boset['na_tag'])
	return;

// 데모용
if(IS_DEMO) {
	include_once($board_skin_path.'/demo.php');
}

// 태그 정리
$tlist = array();
$stags = array();
$n = 0;
$z = 0;
if(isset($boset['d']['tab']) && is_array($boset['d']['tab']) && isset($boset['d']['tag']) && is_array($boset['d']['tag'])) {
	$data_cnt = count($boset['d']['tab']);
	for($i=0; $i < $data_cnt; $i++) {
		if($boset['d']['tab'][$i] && $boset['d']['tag'][$i]) {
			$tlist[$n]['tab'] = $boset['d']['tab'][$i];
			$tlist[$n]['tag'] = na_explode(",", $boset['d']['tag'][$i]);
			if($sql_stag && !$z) {
				$tlist_cnt = count($tlist[$n]['tag']);
				for($k=0; $k < $tlist_cnt;$k++) {
					if(in_array($tlist[$n]['tag'][$k], $stag)) {
						$tlist[$n]['on'] = true;
						$z = 1;	
					}
				}
			} else {
				$tlist[$n]['on'] = false;
			}
			$n++;
		}
	}

	if($n && !$z) {
		$tlist[0]['on'] = true;
	}
}

$tlist_cnt = $n;

if(!$tlist_cnt)
	return;

// 탭
na_script('sly');

// 태그 가로수
$boset['txl'] = isset($boset['txl']) ? (int)$boset['txl'] : 5;
$boset['tlg'] = isset($boset['tlg']) ? (int)$boset['tlg'] : 4;
$boset['tmd'] = isset($boset['tmd']) ? (int)$boset['tmd'] : 3;
$boset['tsm'] = isset($boset['tsm']) ? (int)$boset['tsm'] : 3;
$boset['txs'] = isset($boset['txs']) ? (int)$boset['txs'] : 2;
$tags_row_cols = na_row_cols($boset['txs'], $boset['tsm'], $boset['tmd'], $boset['tlg'], $boset['txl']);

?>
<div class="list-tags">
	<form name="staglist" id="staglist" method="post" onsubmit="return staglist_submit(this);" role="form" class="form">
	<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
	<input type="hidden" name="sca" value="<?php echo $sca ?>">
	<input type="hidden" name="sst" value="<?php echo $sst ?>">
	<input type="hidden" name="sod" value="<?php echo $sod ?>">
	<input type="hidden" name="tag" value="" id="stag_val">

	<nav id="bo_tag" class="sly-tab font-weight-normal">
		<h3 class="sr-only"><?php echo $board['bo_subject'] ?> 태그분류 목록</h3>
		<div class="px-3 px-sm-0">
			<div class="d-flex">
				<div id="bo_tag_list" class="sly-wrap flex-grow-1">
					<ul id="bo_tag_ul" class="sly-list nav d-flex border-left-0 text-nowrap" role="tablist">
						<li class="nav-item">
							<a class="nav-link py-2 px-3" href="<?php echo get_pretty_url($bo_table) ?>"><b>전체</b></a>
						</li>
					<?php for($i=0; $i < $tlist_cnt; $i++) { ?>
						<li class="nav-item">
							<a class="nav-link py-2 px-3<?php echo (isset($tlist[$i]['on']) && $tlist[$i]['on']) ? ' active' : '';?>" id="tag_tab_<?php echo $i ?>" href="#tags_<?php echo $i ?>" aria-controls="tags_<?php echo $i ?>" role="tab" data-toggle="tab" aria-selected="true"><b><?php echo $tlist[$i]['tab'] ?></b></a>
						</li>
					<?php } ?>
					</ul>
				</div>
				<div>
					<a href="javascript:;" class="sly-btn sly-prev tag-prev py-2 px-3">
						<i class="fa fa-angle-left" aria-hidden="true"></i>
						<span class="sr-only">이전 태그</span>
					</a>
				</div>
				<div>
					<a href="javascript:;" class="sly-btn sly-next tag-next py-2 px-3">
						<i class="fa fa-angle-right" aria-hidden="true"></i>
						<span class="sr-only">다음 태그</span>
					</a>				
				</div>
			</div>
		</div>
		<hr/>
	</nav>
	<div class="tab-content p-3 border border-top-0 mt-0 mb-3 f-de">
		<?php for($i=0; $i < $tlist_cnt; $i++) { ?>
			<div class="tab-pane<?php echo (isset($tlist[$i]['on']) && $tlist[$i]['on']) ? ' show active' : '';?>" id="tags_<?php echo $i ?>" role="tabpanel" aria-labelledby="tag_tab_<?php echo $i ?>">
				<div class="row<?php echo $tags_row_cols ?> mx-n2">
				<?php
				$stags = $tlist[$i]['tag'];
				$stags_cnt = count($stags);
				for($k=0; $k < $stags_cnt; $k++) {
					$stags_checked = ($sql_stag && in_array($stags[$k], $stag)) ? ' checked' : '';
				?>
					<div class="col px-1">
						<div class="custom-control custom-switch custom-control-inline">
						  <input type="checkbox" class="custom-control-input tag-direct" name="stag[]" value="<?php echo $stags[$k] ?>" id="tag-item-<?php echo $i.$k ?>"<?php echo $stags_checked ?>>
						  <label class="custom-control-label" for="tag-item-<?php echo $i.$k ?>"><?php echo $stags[$k] ?></label>
						</div>
					</div>
				<?php } ?>
				</div>
			</div>
		<?php } ?>

		<div class="row justify-content-center mt-3">
			<div class="col-sm-6 col-lg-4">
				<div class="input-group">
					<select name="sto" id="sto" class="custom-select">
						<option value="">또는</option>
						<option value="1"<?php echo get_selected($sto, "1") ?>>그리고</option>
					</select>	
					<div class="input-group-append">
						<button type="submit" class="btn btn-primary" type="button">
							<i class="fa fa-tags" aria-hidden="true"></i>
							목록 보기
						</button>
						<a class="btn btn-primary active" role="button" href="<?php echo get_pretty_url($bo_table) ?>" title="초기화">
							<i class="fa fa-refresh" aria-hidden="true"></i>
							<span class="sr-only">초기화</span>
						</a>
					</div>
				</div>
			</div>
		</div>

	</div>

	</form>

	<script>
		function staglist_submit(f) {
			var chk_cnt = 0;
			var stag = '';
			for (var i=0; i<f.length; i++) {
				if (f.elements[i].name == "stag[]" && f.elements[i].checked) {
					if(chk_cnt > 0) {
						stag = stag + ',' + f.elements[i].value;
					} else {
						stag = f.elements[i].value;
					}
					chk_cnt++;
				}
			}
			$("#stag_val").val(stag);
			return true;
		}

		$(document).ready(function() {
			$('#bo_tag .sly-wrap').sly({
				horizontal: 1,
				itemNav: 'basic',
				smart: 1,
				mouseDragging: 1,
				touchDragging: 1,
				releaseSwing: 1,
				speed: 300,
				prevPage: '#bo_tag .tag-prev',
				nextPage: '#bo_tag .tag-next'
			});

			// Sly Tab
			var tag_id = 'bo_tag';
			var tag_size = na_sly_size(tag_id);

			na_sly(tag_id, tag_size);

			$(window).resize(function(e) {
				na_sly(tag_id, tag_size);
			});
		});
	</script>
</div>
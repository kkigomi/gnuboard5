<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<!-- Player Modal -->
<div class="modal fade" id="playModal" tabindex="-1" role="dialog" aria-labelledby="playModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-body">
				<div id="youtubeContent">
					<div id="youtubeLoading"><i class="fa fa-spinner fa-spin fa-4x text-primary"></i></div>
					<div class="youtube-player">
						<iframe id="youtubePlayer" src="" width="720" height="405" frameborder="0" ref="" title="" allowfullscreen></iframe>
					</div>	
				</div>
				<div class="clearfix mt-3">
					<button type="button" class="close float-left" data-dismiss="modal" aria-hidden="true">×</button>
					<button type="button" class="btn btn-primary float-right" id="write_video" onclick="write_video();" style="display:none;"><i class="fa fa-upload"></i> 등록하기</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo $board_skin_url ?>/youtube.js"></script>
<script>
	var youtubeUrl = "<?php echo $board_skin_url ?>";
	var maxResults = 16;
</script>

<div class="alert bg-light border p-2 p-sm-3 mb-3 mx-3 mx-sm-0">
	<form id="youtubeForm" class="form" role="form" onsubmit="na_youtube(); show_list('search', 'post'); return false;">
		<input type="hidden" id="searchnext" name="searchnext" value="">
		<input type="hidden" id="searchprev" name="searchprev" value="">

		<div class="form-row mx-n1">
			<div class="col-5 col-md-2 col-lg-2 px-1">
				<label for="searchorder" class="sr-only">검색조건</label>
				<select id="searchorder" name="searchorder" class="custom-select">
					<option value="date">최신</option>
					<option value="rating">추천</option>
					<option value="viewCount">조회</option>
					<option value="relevance">관련</option>
					<option value="title">제목</option>
				</select>
			</div>
			<div class="col-7 col-md-5 col-lg-6 px-1">
				<label for="stx" class="sr-only">검색어</label>
				<div class="input-group">
					<input type="text" id="searchquery" name="searchquery" value="<?php echo isset($q) ? $q : '';?>" class="form-control" placeholder="Search Youtube...">
					<div class="input-group-append">
						<button type="submit" class="btn btn-primary" title="검색하기">
							<i class="fa fa-search" aria-hidden="true"></i>
							<span class="sr-only">검색하기</span>
						</button>
					</div>
				</div>
			</div>
			<div class="col-12 col-md-5 col-lg-4 pt-2 pt-md-0 px-1">
				<div class="btn-group w-100 en" role="group">
					<a role="button" class="btn btn-basic active w-25" id="post_btn" onclick="show_list('post', 'search');" title="Post">
						<i class="fa fa-pencil"></i>
					</a>
					<a role="button" class="btn btn-basic w-25" id="search_btn" onclick="show_list('search', 'post');" title="Youtube">
						<i class="fa fa-youtube-play"></i>
					</a>
					<a role="button" class="btn btn-basic w-25" id="openModal" title="Play">
						<i class="fa fa-play"></i>
					</a>
					<a role="button" class="btn btn-basic w-25" id="stopPlayer" title="Stop">
						<i class="fa fa-pause"></i>
					</a>
				</div>
			</div>
		</div>
	</form>
</div>

<div id="search_list" class="px-3 px-sm-0" style="display:none;">

	<div class="clearfix">
		<div class="float-left">
			<h4 class="mb-0">
				<i class="fa fa-video-camera"></i> <span id="searchtotal">0</span>
			</h4>
		</div>

		<div class="float-right">
			<span class="cursor" onclick="na_youtube('prev');">
				<i class="fa fa-chevron-circle-left fa-2x text-muted"></i>
			</span>
			<span class="cursor" onclick="na_youtube('next');">
				<i class="fa fa-chevron-circle-right fa-2x text-muted"></i>
			</span>
		</div>
	</div>
	<div class="youtube-list">
		<div id="videoList">
			<div class="video-wrap">
				<div id="videoMsg" class="youtube-none text-center bg-light">
					검색어를 입력해 주세요.
				</div>
				<div id="videoLoading">
					<i class="fa fa-spinner fa-spin fa-4x text-primary"></i>
				</div>
			</div>
		</div>
	</div>

	<div id="searchbtn" class="mt-2 text-center" style="display:none;">
		<span class="cursor" onclick="na_youtube('prev1');">
			<i class="fa fa-chevron-circle-left fa-4x text-muted"></i>
		</span>
		&nbsp;
		<span class="cursor" onclick="na_youtube('next1');">
			<i class="fa fa-chevron-circle-right fa-4x text-muted"></i>
		</span>
	</div>

	<p class="text-muted text-center mt-4 en">
		AMINA YouTube Video Search &copy; 
		<a href="http://amina.co.kr" target="_blank">AMINA</a>
	</p>

</div>
<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 이동주소 체크
$burl =  get_pretty_url($bo_table);
$furl = G5_BBS_URL.'/link.php?bo_table='.$bo_table.'&wr_id='.$wr_id.'&no=1';
?>
<style> 
	#feed_mask { position:fixed; z-index:9000; background:rgba(0,0,0,0.95); display:none; left:0; top:0; width:100%; height:100%; } 
	.feed-window { position:absolute; width:400px; height:200px; left:50%; margin-left:-200px; top:50%; margin-top:-100px; z-index:10000; text-align:center; color:#fff; line-height:26px; }
	.feed-window a { color:#fff !important; }
</style> 
<div id="feed_mask">
	<div class="feed-window">
		<h3 class="mb-4">클릭을 하셔야 원글 보기가 가능합니다.</h3>
		
		<a href="<?php echo $furl ?>" onclick="window.open(this.href); feedBack(); return false;">
			<i class="fa fa-external-link-square fa-4x"></i>

			<h4 class="mt-3">Click!</h4>
		</a>
	</div>
</div> 
<script>
function feedBack() { 
	<?php if($_SERVER['HTTP_REFERER']) { //이전 페이지 주소가 있으면 ?>
		history.back();
	<?php } else { ?>
		document.location.href = "<?php echo $burl ?>";
	<?php } ?>
} 

function feedPopup(url) { 
	var pop = window.open(url);
	if (pop == null) { 
		$('#feed_mask').fadeIn();
	} else {
		feedBack();
	}
}

$(document).ready(function(){
	feedPopup("<?php echo $furl ?>");
});
</script>

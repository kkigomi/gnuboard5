/*!
 * AMINA YouTube Video Search
 * http://amina.co.kr/
 * Copyright 2013-2015, AMINA
 * Released under the APMS Licenses
 *
 * Date: May 27 2015
 */

function number_format(numstr) {
	var numstr = String(numstr);
	var re0 = /(\d+)(\d{3})($|\..*)/;
	if (re0.test(numstr)) {
		return numstr.replace(re0, function(str,p1,p2,p3) { return number_format(p1) + "," + p2 + p3; });
	} else {
		return numstr;
	}
}

function na_youtube(page, q) {
	var url;
	var items;
	var order = $('#searchorder option:selected').val();
	var next = $('#searchnext').val().trim();
	var prev = $('#searchprev').val().trim();
	var results = $('#videoList');
	var count = 0;
	var html = '';
	var total = '';

	if(q) {
		$('#searchquery').val(q);
		show_list('search', 'post'); //Change Layer
	} else {
		q = $('#searchquery').val().trim();
	}

	url = youtubeUrl + '/youtube.php?bo_table=' + g5_bo_table + '&order=' + order + '&max=' + maxResults + '&q=' + encodeURI(q);

	// Page
	if(page == 'next' || page == 'next1') {
		if(next == '') {
			alert('마지막입니다.');
			return false;
		}
		url += '&pg=' + next;
	} else if(page == 'prev' || page == 'prev1') {
		if(prev == '') {
			alert('처음입니다.');
			return false;
		}
		url += '&pg=' + prev;
	}

	$('#videoMsg').hide();
	$('#videoLoading').show();

	$.get(url, function(data) {
		if (data.items) {
			items = data.items;
			html += '<div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 mx-n2 mx-sm-n3">';
			items.forEach(function (item) {
				//item.snippet.thumbnails.default.url or item.snippet.thumbnails.medium.url;
				ytitle = encodeURIComponent(item.snippet.title);
				html += '<div class="col px-2 px-sm-3"><div class="item-list">';
				html += '<div class="imgframe"><div class="img-wrap"><div class="img-item">';
				html += '<a href="javascript:playVideo(\'' + item.id.videoId + '\',\'' + ytitle + '\')"><img src="' + item.snippet.thumbnails.high.url + '" class="youtube-thumb"></a>';
				html += '</div></div></div>';
				html += '<p class="pt-2 pb-4"><a href="javascript:playVideo(\'' + item.id.videoId + '\',\'' + ytitle + '\');" class="na-clamp-2">' + item.snippet.title + '</a></p>';
				html += '</div></div>';
				count++;
			});
			html += '</div>';
		}

		if(data.pageInfo.totalResults >= 0) {
			total = number_format(data.pageInfo.totalResults);
		} else {
			total = count;
		}
		$('#searchtotal').text(total);
		$('#searchnext').val(data.nextPageToken);
		$('#searchprev').val(data.prevPageToken);

		if (count === 0) {
			results.html('<div class="item-list youtube-none text-center bg-light">검색된 동영상이 없습니다.</div>');
			$('#searchbtn').hide();
		} else {
			results.html(html);
			$('#searchbtn').show();
		}
	}, "json");

	if(page == 'prev1' || page == 'next1') {
		$('html, body').animate({
			scrollTop: $("#search_list").offset().top - 100
		}, 500);
	}
}

var $youtubePlayer = $('#youtubePlayer');

function loadVideo(videoID,title) {

	if(videoID == '') return;

	$('#write_video').show();
	$('#youtubeLoading').show();
	$youtubePlayer.attr('src', 'https://www.youtube.com/embed/' + videoID + '?autohide=1&autoplay=1&vq=hd720&loop=1');
	$youtubePlayer.attr('ref', 'https://youtu.be/' + videoID);
	$youtubePlayer.attr('title', title);
	$('#youtubeLoading').hide();
}

function playVideo(videoID,title){
	$('#playModal').modal('show').on('shown.bs.modal', function (e) {
		loadVideo(videoID,title);
	});
}

function show_list(sid, hid) {
	$('#'+hid+'_btn').removeClass('active');
	$('#'+hid+'_list').hide();
	$('#'+sid+'_btn').addClass('active');
	$('#'+sid+'_list').show();
}

function write_video() {
	document.location.href = g5_bbs_url + '/write.php?bo_table=' + g5_bo_table + '&vurl=' + encodeURIComponent($youtubePlayer.attr('ref')) + '&vtitle=' + encodeURIComponent($youtubePlayer.attr('title'));
}

$(function(){
	$('#openModal').click(function () {
		$('#playModal').modal('toggle');
	});

	$('#stopPlayer').click(function () {
		$youtubePlayer.attr('src', '');
	});
});
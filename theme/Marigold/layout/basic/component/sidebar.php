<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<div class="px-3 mb-4">
	<?php echo na_widget('outlogin'); // 외부로그인 ?>
</div>

<div class="na-menu">
	<div class="nav nav-pills nav-vertical">

		<?php if (IS_SHOP) { // 쇼핑몰에서 ?>

			<div id="sidebar-shop-basic" class="mb-3">
				<div class="nav-item">
					<a class="nav-link" href="#todayviewOffcanvas" data-bs-toggle="offcanvas" data-bs-target="#todayviewOffcanvas" aria-controls="todayviewOffcanvas" data-placement="left">
						<i class="bi-bag-check nav-icon"></i>
						<span class="nav-link-title">
							오늘 본 상품
							<?php if($member['todayview_cnt']) { ?>
								<span class="small">
									<b class="badge bg-primary rounded-pill fw-normal"><?php echo $member['todayview_cnt'] ?></b>
								</span>
							<?php } ?>
						</span>
					</a>
				</div>
				<div class="nav-item">
					<a class="nav-link<?php echo ($page_id === G5_SHOP_DIR.'-page-cart') ? ' active' : ''; ?> " href="#cartOffcanvas" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" aria-controls="cartOffcanvas" data-placement="left">
						<i class="bi-basket nav-icon"></i>
						<span class="nav-link-title">
							장바구니
							<?php if($member['cart_cnt']) { ?>
								<span class="small">
									<b class="badge bg-primary rounded-pill fw-normal"><?php echo $member['cart_cnt'] ?></b>
								</span>
							<?php } ?>
						</span>
					</a>
				</div>
			</div>

			<div class="dropdown-header">
				Event
			</div>
			<div id="sidebar-shop-event" class="mb-3">
				<?php
				// 이벤트
				$evrow = array();
				$evids = array();
				$evresult = sql_query(" select ev_id, ev_subject from {$g5['g5_shop_event_table']} where ev_use = '1' order by ev_id desc ");
				if(sql_num_rows($evresult)) {
					for ($i=0; $row=sql_fetch_array($evresult); $i++) {
						$evrow[] = $row;
						$evids[] = $row['ev_id'];
					}
				}

				if(count($evids)) {

					$expanded = 'false';
					$show = '';
					if(isset($ev_id) && $ev_id && in_array($ev_id, $evids)) {
						$expanded = 'true';
						$show = ' show';
					}
				?>
					<div class="nav-item">
						<a class="nav-link dropdown-toggle collapsed" href="#shot-item-event" role="button" data-bs-toggle="collapse" data-bs-target="#shot-item-event" aria-expanded="<?php echo $expanded ?>" aria-controls="shot-item-type">
							<i class="bi-gift nav-icon"></i>
							<span class="nav-link-title">이벤트</span>
						</a>
						<div id="shot-item-event" class="nav-collapse collapse<?php echo $show ?>" data-bs-parent="#sidebar-shop-event">
							<?php for($i=0; $i < count($evids); $i++) { ?>
								<a class="nav-link<?php echo (isset($ev_id) && $ev_id == $evrow[$i]['ev_id']) ? ' active' : ''; ?>" href="<?php echo G5_SHOP_URL ?>/event.php?ev_id=<?php echo urlencode($evrow[$i]['ev_id']) ?>">
									<?php echo get_text($evrow[$i]['ev_subject']) ?>
								</a>
							<?php } ?>
						</div>
					</div>
				<?php } ?>

				<div class="nav-item">
					<?php
					// 상품 유형별
					$type_arr = array();
					for($i=1; $i < 6; $i++) {
						$type_arr[] = G5_SHOP_DIR.'-shop-type-'.$i;
					}

					$expanded = 'false';
					$show = '';
					if(in_array($page_id, $type_arr)) {
						$expanded = 'true';
						$show = ' show';
					}
					?>
					<a class="nav-link dropdown-toggle collapsed" href="#sidebar-shop-item-type" role="button" data-bs-toggle="collapse" data-bs-target="#sidebar-shop-item-type" aria-expanded="<?php echo $expanded ?>" aria-controls="shot-item-type">
						<i class="bi-grid nav-icon"></i>
						<span class="nav-link-title">유형별</span>
					</a>
					<div id="sidebar-shop-item-type" class="nav-collapse collapse<?php echo $show ?>" data-bs-parent="#sidebar-shop-event">
						<?php
							$itypes = array('', '히트', '추천', '최신', '인기', '할인');
							for ($i=1; $i < count($itypes); $i++) { 
						?>
							<a class="nav-link<?php echo ($page_id == G5_SHOP_DIR.'-shop-type-'.$i) ? ' active' : ''; ?>" href="<?php echo shop_type_url($i) ?>">
								<?php echo $itypes[$i] ?>상품
							</a>
						<?php } ?>
					</div>
				</div>
			</div>

			<?php
			// 쇼핑몰 카테고리
			$shop_category = na_shop_category();
			if(count($shop_category) > 0) {
			?>
				<div class="dropdown-header">
					Category
				</div>

				<div id="sidebar-shop-category" class="mb-3">
				<?php 
				for ($i=0; $i < count($shop_category); $i++) {
					// 주메뉴
					$me = $shop_category[$i];

					$collapsed = ' collapsed';
					$expanded = 'false';
					$active = $show = '';
					if($me['on']) {
						if($me['eq']) {
							$active = $collapsed = ' active';
						}
						$expanded = 'true';
						$show = ' show';
					}

					// 1차서브
					if($me['is_sub']) {  
						$id_s1 = 'sidebar-sub-c'.$i;
					?>
					<div class="nav-item">
						<a class="nav-link dropdown-toggle<?php echo $collapsed ?>" href="<?php echo $me['me_link'] ?>" role="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $id_s1 ?>" aria-expanded="<?php echo $expanded ?>" aria-controls="<?php echo $id_s1 ?>" target="<?php echo $me['me_target'] ?>">
							<i class="<?php echo ($me['icon']) ? $me['icon'] : 'bi-box'; ?> nav-icon"></i>
							<span class="nav-link-title" onclick="na_href('<?php echo $me['me_link'] ?>','<?php echo $me['me_target'] ?>');"><?php echo $me['me_name'] ?></span>
						</a>
						<div id="<?php echo $id_s1 ?>" class="nav-collapse collapse<?php echo $show ?>" data-bs-parent="#sidebar-shop-category">
							<?php
							// 1차 서브
							for ($j=0; $j < count($me['s']); $j++) { 
								$me1 = $me['s'][$j];

								$collapsed1 = ' collapsed';
								$expanded1 = 'false';
								$active1 = $show1 = '';
								if($me1['on']) {
									if($me1['eq']) {
										$collapsed1 = $active1 = ' active';
									}
									$expanded1 = 'true';
									$show1 = ' show';
								}

								// 2차 서브							
								if($me1['is_sub']) {
									$id_s2 = $id_s1.$j;
							?>
									<div id="<?php echo $id_s2.'-head' ?>">
										<div class="nav-item">
											<a class="nav-link dropdown-toggle<?php echo $collapsed1 ?>" href="<?php echo $me1['me_link'] ?>" role="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $id_s2 ?>" aria-expanded="<?php echo $expanded1 ?>" aria-controls="<?php echo $id_s2 ?>" target="<?php echo $me1['me_target'] ?>">
												<span onclick="na_href('<?php echo $me1['me_link'] ?>','<?php echo $me1['me_target'] ?>');">
													<?php echo $me1['me_name'] ?>
												</span>
											</a>
											<div id="<?php echo $id_s2 ?>" class="nav-collapse collapse<?php echo $show1 ?>" data-bs-parent="#<?php echo $id_s2.'-head' ?>">
												<?php 
												for ($k=0; $k < count($me1['s']); $k++) { 
													$me2 = $me1['s'][$k];
													$active2 = ($me2['on']) ? ' active' : '';
												?>
													<a class="nav-link<?php echo $active2 ?>" href="<?php echo $me2['me_link'] ?>" target="<?php echo $me2['me_target'] ?>">
														<?php echo $me2['me_name'] ?>
													</a>
												<?php } ?>
											</div>
										</div>
									</div>
								<?php } else { ?>
									<a class="nav-link<?php echo $active1 ?>" href="<?php echo $me1['me_link'] ?>" target="<?php echo $me1['me_target'] ?>">
										<?php echo $me1['me_name'] ?>
									</a>
								<?php } ?>
							<?php } ?>
						</div>
					</div>
					<?php } else { ?>
					<div class="nav-item">
						<a class="nav-link<?php echo $active ?>" href="<?php echo $me['me_link'] ?>" data-placement="left" target="<?php echo $me['me_target'] ?>">
							<i class="<?php echo ($me['icon']) ? $me['icon'] : 'bi-box'; ?> nav-icon"></i>
							<span class="nav-link-title"><?php echo $me['me_name'] ?></span>
						</a>
					</div>
					<?php } // end is_sub ?>
				<?php } // end for 쇼핑몰 카테고리  ?>
				</div>
			<?php } // end is_category ?>
		<?php } // end IS_SHOP ?>

		<?php 
		// 사이트 메뉴
		$menu_cnt = count($menu);
		if($menu_cnt) {
		?>
			<div class="dropdown-header">
				Site Menu
			</div>

			<div id="sidebar-site-menu" class="mb-3">
			<?php
			for ($i=0; $i < $menu_cnt; $i++) { 
				// 주메뉴
				$me = $menu[$i];

				$collapsed = ' collapsed';
				$expanded = 'false';
				$active = $show = '';
				if($me['on']) {
					if($me['eq']) {
						$active = $collapsed = ' active';
					}
					$expanded = 'true';
					$show = ' show';
				}

				// 1차서브
				if($me['is_sub']) {  
					$id_s1 = 'sidebar-sub-s'.$i;
				?>
				<div class="nav-item">
					<a class="nav-link dropdown-toggle collapsed<?php echo $collapsed ?>" href="#<?php echo $id_s1 ?>" role="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $id_s1 ?>" aria-expanded="<?php echo $expanded ?>" aria-controls="<?php echo $id_s1 ?>">
						<i class="<?php echo ($me['icon']) ? $me['icon'] : 'bi-clipboard'; ?> nav-icon"></i>
						<?php if($me['me_link'] === '#') { ?>
							<span class="nav-link-title">
						<?php } else { ?>
							<span class="nav-link-title" onclick="na_href('<?php echo $me['me_link'] ?>','<?php echo $me['me_target'] ?>');">
						<?php } ?>
							<?php echo $me['me_name'] ?>
							<?php if($me['new']) { ?>
								<span class="small">
									<b class="badge bg-primary rounded-pill fw-normal"><?php echo $me['new'] ?></b>
								</span>
							<?php } ?>
						</span>
					</a>
					<div id="<?php echo $id_s1 ?>" class="nav-collapse collapse<?php echo $show ?>" data-bs-parent="#sidebar-site-menu">
						<?php
						// 1차 서브
						for ($j=0; $j < count($me['s']); $j++) { 
							$me1 = $me['s'][$j];

							$collapsed1 = ' collapsed';
							$expanded1 = 'false';
							$active1 = $show1 = '';
							if($me1['on']) {
								if($me1['eq']) {
									$collapsed1 = $active1 = ' active';
								}
								$expanded1 = 'true';
								$show1 = ' show';
							}

							// 2차 서브							
							if($me1['is_sub']) {
								$id_s2 = $id_s1.$j;
						?>
								<div id="<?php echo $id_s2.'-head' ?>">
									<div class="nav-item">
										<a class="nav-link dropdown-toggle<?php echo $collapsed1 ?>" href="#<?php echo $id_s2 ?>" role="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $id_s2 ?>" aria-expanded="<?php echo $expanded1 ?>" aria-controls="<?php echo $id_s2 ?>">
											<?php if($me1['me_link'] === '#') { ?>
												<span>
											<?php } else { ?>
												<span onclick="na_href('<?php echo $me1['me_link'] ?>','<?php echo $me1['me_target'] ?>');">
											<?php } ?>
												<?php echo $me1['me_name'] ?>
												<?php if($me1['new']) { ?>
													<span class="small">
														<b class="badge bg-primary rounded-pill fw-normal"><?php echo $me1['new'] ?></b>
													</span>
												<?php } ?>
											</span>
										</a>
										<div id="<?php echo $id_s2 ?>" class="nav-collapse collapse<?php echo $show1 ?>" data-bs-parent="#<?php echo $id_s2.'-head' ?>">
											<?php 
											for ($k=0; $k < count($me1['s']); $k++) { 
												$me2 = $me1['s'][$k];
												$active2 = ($me2['on']) ? ' active' : '';
											?>
												<a class="nav-link<?php echo $active2 ?>" href="<?php echo $me2['me_link'] ?>" target="<?php echo $me2['me_target'] ?>">
													<?php echo $me2['me_name'] ?>
													<?php if($me2['new']) { ?>
														<span class="small">
															<b class="badge bg-primary rounded-pill fw-normal"><?php echo $me2['new'] ?></b>
														</span>
													<?php } ?>
												</a>
											<?php } ?>
										</div>
									</div>
								</div>
							<?php } else { ?>
								<a class="nav-link<?php echo $active1 ?>" href="<?php echo $me1['me_link'] ?>" target="<?php echo $me1['me_target'] ?>">
									<?php echo $me1['me_name'] ?>
									<?php if($me1['new']) { ?>
										<span class="small">
											<b class="badge bg-primary rounded-pill fw-normal"><?php echo $me1['new'] ?></b>
										</span>
									<?php } ?>
								</a>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
				<?php } else { ?>
				<div class="nav-item">
					<a class="nav-link<?php echo $active ?>" href="<?php echo $me['me_link'] ?>" data-placement="left" target="<?php echo $me['me_target'] ?>">
						<i class="<?php echo ($me['icon']) ? $me['icon'] : 'bi-clipboard'; ?> nav-icon"></i>
						<span class="nav-link-title">
							<?php echo $me['me_name'] ?>
							<?php if($me['new']) { ?>
								<span class="small">
									<b class="badge bg-primary rounded-pill fw-normal"><?php echo $me['new'] ?></b>
								</span>
							<?php } ?>
						</span>
					</a>
				</div>
				<?php } // end is_sub 1차서브 ?>
			<?php } // end for 주메뉴 ?>
			</div>
		<?php } // end is_menu ?>

		<?php if(IS_SHOP) { ?>
			<div class="dropdown-header">
				Shopping
			</div>

			<div id="sidebar-misc-menu" class="mb-3">
			<?php
				$iRow = array();
				$iRow[] = array(G5_SHOP_DIR.'-page-orderinquiry', 'bi-bag-check', '주문내역', G5_SHOP_URL.'/orderinquiry.php');
				$iRow[] = array(G5_SHOP_DIR.'-page-personalpay', 'bi-credit-card', '개인결제', G5_SHOP_URL.'/personalpay.php');
				$iRow[] = array(G5_SHOP_DIR.'-page-itemuselist', 'bi-pencil-square', '사용후기', G5_SHOP_URL.'/itemuselist.php');
				$iRow[] = array(G5_SHOP_DIR.'-page-itemqalist', 'bi-chat-dots', '상품문의', G5_SHOP_URL.'/itemqalist.php');
				$iRow[] = array(G5_SHOP_DIR.'-page-search', 'bi-search-heart', '상품검색', G5_SHOP_URL.'/search.php');
				$iRow[] = array(G5_SHOP_DIR.'-page-couponzone', 'bi-ticket', '쿠폰존', G5_SHOP_URL.'/couponzone.php');

				for ($i=0; $i < count($iRow); $i++) { 
			?>
				<div class="nav-item">
					<a class="nav-link<?php echo ($page_id == $iRow[$i][0]) ? ' active' : ''; ?>" href="<?php echo $iRow[$i][3] ?>" data-placement="left">
						<i class="<?php echo $iRow[$i][1] ?> nav-icon"></i>
						<span class="nav-link-title"><?php echo $iRow[$i][2] ?></span>
					</a>
				</div>
			<?php } ?>
			</div>
		<?php } ?>
		
		<div class="dropdown-header">
			Miscellaneous
		</div>

		<div id="sidebar-misc-menu" class="mb-3">
		<?php
			// 현재접속자
			$login_connect = ($member['login_cnt']) ? ' <span class="small"><b class="badge bg-primary rounded-pill fw-normal">'.$member['login_cnt'].'</b></span>' : '';

			$iRow = array();
			$iRow[] = array(G5_BBS_DIR.'-page-faq', 'bi-question-circle', 'FAQ', G5_BBS_URL.'/faq.php');
			$iRow[] = array(G5_BBS_DIR.'-qa', 'bi-chat-heart', '1:1문의', G5_BBS_URL.'/qalist.php');
			$iRow[] = array(G5_BBS_DIR.'-page-new', 'bi-pencil', '새글모음', G5_BBS_URL.'/new.php');
			$iRow[] = array(G5_BBS_DIR.'-page-tag', 'bi-tags', '태그모음', G5_BBS_URL.'/tag.php');
			$iRow[] = array(G5_BBS_DIR.'-page-search', 'bi-search', '게시물검색', G5_BBS_URL.'/search.php');
			$iRow[] = array(G5_BBS_DIR.'-page-current_connect', 'bi-people', '현재접속자'.$login_connect, G5_BBS_URL.'/current_connect.php');

			for ($i=0; $i < count($iRow); $i++) { 
		?>
			<div class="nav-item">
				<a class="nav-link<?php echo ($page_id == $iRow[$i][0]) ? ' active' : ''; ?>" href="<?php echo $iRow[$i][3] ?>" data-placement="left">
					<i class="<?php echo $iRow[$i][1] ?> nav-icon"></i>
					<span class="nav-link-title"><?php echo $iRow[$i][2] ?></span>
				</a>
			</div>
		<?php } ?>
		</div>

		<div class="dropdown-header">
			About us
		</div>

		<div id="sidebar-misc-menu" class="mb-3">
			<?php
				$iRow = array();
				$iRow[] = array(G5_BBS_DIR.'-content-company', 'bi-balloon-heart', '사이트 소개', get_pretty_url('content', 'company'));
				$iRow[] = array(G5_BBS_DIR.'-content-provision', 'bi-check2-square', '서비스 이용약관', get_pretty_url('content', 'provision'));
				$iRow[] = array(G5_BBS_DIR.'-content-privacy', 'bi-person-lock', '개인정보 처리방침', get_pretty_url('content', 'privacy'));

				for ($i=0; $i < count($iRow); $i++) { 
			?>
				<div class="nav-item">
					<a class="nav-link<?php echo ($page_id == $iRow[$i][0]) ? ' active' : ''; ?>" href="<?php echo $iRow[$i][3] ?>" data-placement="left">
						<i class="<?php echo $iRow[$i][1] ?> nav-icon"></i>
						<span class="nav-link-title"><?php echo $iRow[$i][2] ?></span>
					</a>
				</div>
			<?php } ?>

			<?php if (IS_YC) { ?>
				<div class="nav-item">
					<a class="nav-link" href="<?php echo (IS_SHOP) ? G5_URL : G5_SHOP_URL; ?>" data-placement="left">
						<i class="bi-door-open nav-icon"></i>
						<span class="nav-link-title">
							<?php if(IS_SHOP) { ?>
								<?php echo $config['cf_title'] ?>
							<?php } else { ?>
								<?php echo (isset($nariya['seo_shop_title']) && $nariya['seo_shop_title']) ? $nariya['seo_shop_title'] : '쇼핑몰'; ?>
							<?php } ?>
						</span>
					</a>
				</div>
			<?php } ?>

			<div class="nav-item">
				<a class="nav-link" href="<?php echo get_device_change_url() ?>" data-placement="left">
					<i class="<?php echo (G5_IS_MOBILE) ? 'bi-pc-display' : 'bi-tablet'; ?> nav-icon"></i>
					<span class="nav-link-title"><?php echo (G5_IS_MOBILE) ? 'PC' : '모바일'; ?> 버전</span>
				</a>
			</div>

		</div>
	</div>
</div><!-- end .na-menu -->


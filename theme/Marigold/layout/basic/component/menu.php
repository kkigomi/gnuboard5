<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<nav class="site-nav d-none d-lg-block" role="navigation">
	<ul class="list-unstyled p-0 m-0">
	<?php if (IS_SHOP) { ?>
		<li>
			<a href="#menuOffcanvas" data-bs-toggle="offcanvas" data-bs-target="#menuOffcanvas" aria-controls="menuOffcanvas">
				<i class="bi bi-bag-check"></i>
				카테고리
			</a>		
		</li>
	<?php } // End IS_SHOP ?>
	<?php for ($i=0; $i < count($menu); $i++) {// 주메뉴 
		$me = $menu[$i];
	?>
	<li<?php echo ($me['is_sub']) ? ' class="has-sub"' : ''; ?>>
		<a href="<?php echo $me['me_link'] ?>" target="<?php echo $me['me_target'] ?>" class="nav-link<?php echo ($me['on']) ? ' active' : ''; ?>">
			<?php echo $me['me_name'] ?>
			<?php if($me['new']) { ?>
				<span class="small">
					<b class="badge bg-primary rounded-pill fw-normal"><?php echo $me['new'] ?></b>
				</span>
			<?php } ?>
		</a>
		<?php if($me['is_sub']) { //1차 서브 ?>
		<ul class="dropdown">
		<?php for ($j=0; $j < count($me['s']); $j++) { 
			$me1 = $me['s'][$j];
		?>
		<li<?php echo ($me1['is_sub']) ? ' class="has-sub"' : ''; ?>>
			<a href="<?php echo $me1['me_link'] ?>" target="<?php echo $me1['me_target'] ?>" <?php echo ($me1['on']) ? ' class="active"' : ''; ?>>
				<?php echo $me1['me_name'] ?>
				<?php if($me1['new']) { ?>
					<span class="small">
						<b class="badge bg-primary rounded-pill fw-normal"><?php echo $me1['new'] ?></b>
					</span>
				<?php } ?>
			</a>
			<?php if($me1['is_sub']) { //2차 서브 ?>
			<ul class="dropdown">
				<?php for ($k=0; $k < count($me1['s']); $k++) { 
					$me2 = $me1['s'][$k];
				?>
				<li>
					<a href="<?php echo $me2['me_link'] ?>" target="<?php echo $me2['me_target'] ?>" <?php echo ($me2['on']) ? ' class="active"' : ''; ?>>
						<?php echo $me2['me_name'] ?>
						<?php if($me2['new']) { ?>
							<span class="small">
								<b class="badge bg-primary rounded-pill fw-normal"><?php echo $me2['new'] ?></b>
							</span>
						<?php } ?>
					</a>
				</li>
				<?php } ?>
			</ul>	
			<?php } ?>
		</li>
		<?php } ?>
		</ul>
		<?php } ?>
	</li>
	<?php } ?>
	</ul>
</nav>
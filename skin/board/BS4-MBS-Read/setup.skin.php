<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// input의 name을 boset[배열키] 형태로 등록
?>
<ul class="list-group">
	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">열람 설정</label>
			<div class="col-sm-10">
				<div class="table-responsive">
					<table class="table table-bordered mb-0">
					<tbody>
					<tr class="bg-light">
					<th class="text-center nw-c1">구분</th>
					<th class="text-center nw-c2">설정</th>
					<th class="text-center">비고</th>
					</tr>
					<tr>
					<td class="text-center">등록자 적립</td>
					<td>
						<div class="input-group">
							<?php $boset['rrp'] = (isset($boset['rrp']) && (int)$boset['rrp']) ? $boset['rrp'] : ''; ?>
							<input type="text" name="boset[rrp]" value="<?php echo $boset['rrp'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">%</span>
							</div>
						</div>
					</td>
					<td class="text-muted">&nbsp;</td>
					</tr>
					<tr>
					<td class="text-center">최소 포인트</td>
					<td class="text-center">
						<div class="input-group">
							<?php $boset['nrp'] = (isset($boset['nrp']) && (int)$boset['nrp']) ? $boset['nrp'] : ''; ?>
							<input type="text" name="boset[nrp]" value="<?php echo $boset['nrp'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">점</span>
							</div>
						</div>
					</td>
					<td class="text-muted">&nbsp;</td>
					</tr>
					<tr>
					<td class="text-center">최대 포인트</td>
					<td class="text-center">
						<div class="input-group">
							<?php $boset['xrp'] = (isset($boset['xrp']) && (int)$boset['xrp']) ? $boset['xrp'] : ''; ?>
							<input type="text" name="boset[xrp]" value="<?php echo $boset['xrp'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">점</span>
							</div>
						</div>
					</td>
					<td class="text-muted">&nbsp;</td>
					</tr>
					<tr>
					<td class="text-center">열람 이력 유지</td>
					<td class="text-center">
						<div class="input-group">
							<?php $boset['rterm'] = (isset($boset['rterm']) && (int)$boset['rterm']) ? $boset['rterm'] : ''; ?>
							<input type="text" name="boset[rterm]" value="<?php echo $boset['rterm'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">일</span>
							</div>
						</div>
					</td>
					<td class="text-muted">
						미설정시 열람 포인트 1번만 차감
					</td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</li>

	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">목록 출력</label>
			<div class="col-sm-10">
				<div class="table-responsive">
					<table class="table table-bordered mb-0">
					<tbody>
					<tr class="bg-light">
						<th class="text-center nw-c1">구분</th>
						<th class="text-center nw-c2">설정</th>
						<th class="text-center">비고</th>
					</tr>
					<tr>
					<td class="text-center">검색창 보이기</td>
					<td class="text-center">
						<div class="custom-control custom-checkbox">
							<?php $boset['search_open'] = isset($boset['search_open']) ? $boset['search_open'] : ''; ?>
							<input type="checkbox" name="boset[search_open]" value="1"<?php echo get_checked('1', $boset['search_open'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					<td class="text-muted">
						글 목록 상단에 검색창이 보이도록 출력함
					</td>
					</tr>
					<tr>
					<td class="text-center">목록 글 이동</td>
					<td class="text-center">
						<select name="boset[target]" class="custom-select">
							<?php 
								$boset['target'] = isset($boset['target']) ? $boset['target'] : '';
								echo na_target_options($boset['target']);
							?>
						</select>
					</td>
					<td class="text-muted">
						글 내용 또는 관련 링크1 페이지로 이동
					</td>
					</tr>
					<tr>
					<td class="text-center">목록 헤드 스킨</td>
					<td class="text-center">
						<select name="boset[head_skin]" class="custom-select">
							<option value="">기본 헤드</option>
							<?php
								$wset['head_skin'] = isset($wset['head_skin']) ? $wset['head_skin'] : '';
								$skinlist = na_file_list(NA_PATH.'/skin/head', 'css');
								for ($k=0; $k<count($skinlist); $k++) {
									echo "<option value=\"".$skinlist[$k]."\"".get_selected($boset['head_skin'], $skinlist[$k]).">".$skinlist[$k]."</option>\n";
								} 
							?>
						</select>
					</td>
					<td class="text-muted">
						&nbsp;
					</td>
					</tr>
					<tr>
					<td class="text-center">헤드 라인 컬러</td>
					<td class="text-center">
						<select name="boset[head_color]" class="custom-select">
						<option value="">자동 컬러</option>
						<?php 
							$wset['head_color'] = isset($wset['head_color']) ? $wset['head_color'] : '';
							echo na_color_options($boset['head_color']);
						?>
						</select>
					</td>
					<td class="text-muted">
						기본 헤드 및 상단 라인 컬러
					</td>
					</tr>
					<tr>
					<td class="text-center">목록 스타일</td>
					<td class="text-center">
						<?php $wset['list_style'] = isset($wset['list_style']) ? $wset['list_style'] : 'list';	?>
						<select name="boset[list_style]" class="custom-select">
							<option value="list"<?php echo get_selected($boset['list_style'], 'list')?>>리스트 스타일</option>
							<option value="gallery"<?php echo get_selected($boset['list_style'], 'gallery')?>>갤러리 스타일</option>
							<option value="webzine"<?php echo get_selected($boset['list_style'], 'webzine')?>>웹진 스타일</option>
						</select>
					</td>
					<td class="text-muted">
						글 목록 기본 스타일
					</td>
					</tr>
					<tr>
					<td class="text-center">목록 타입</td>
					<td class="text-center">
						<?php $wset['list_type'] = isset($wset['list_type']) ? $wset['list_type'] : '';	?>
						<select name="boset[list_type]" class="custom-select">
							<option value=""<?php echo get_selected($boset['list_type'], '')?>>일반 목록</option>
							<option value="1"<?php echo get_selected($boset['list_type'], '1')?>>더보기 목록</option>
							<option value="2"<?php echo get_selected($boset['list_type'], '2')?>>무한스크롤 목록</option>
						</select>
					</td>
					<td class="text-muted">
						글 목록 기본 타입
					</td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</li>

	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">컨텐츠 출력</label>
			<div class="col-sm-10">
				<div class="table-responsive">
					<table class="table table-bordered mb-0">
					<tbody>
					<tr class="bg-light">
						<th class="text-center nw-c1">구분</th>
						<th class="text-center nw-c2">설정</th>
						<th class="text-center">비고</th>
					</tr>
					<tr>
					<td class="text-center">글 목록 상단</td>
					<td class="text-center">
						<select name="boset[contt]" class="custom-select">
							<option value="">출력안함</option>
							<?php
								$ctlist = na_file_list($board_skin_path.'/content', 'php');
								$ctlist_cnt = count($ctlist);
								$boset['contt'] = isset($boset['contt']) ? $boset['contt'] : '';
								for ($k=0; $k < $ctlist_cnt; $k++) {
									echo '<option value="'.$ctlist[$k].'"'.get_selected($ctlist[$k], $boset['contt']).'>'.$ctlist[$k].'</option>'.PHP_EOL;
								} 
							?>
						</select>
					</td>
					<td class="text-muted">
						보드스킨 내 /content 폴더에 있는 php 파일
					</td>
					</tr>
					<tr>
					<td class="text-center">글 내용 상단</td>
					<td class="text-center">
						<select name="boset[contm]" class="custom-select">
							<option value="">출력안함</option>
							<?php
								$boset['contm'] = isset($boset['contm']) ? $boset['contm'] : '';
								for ($k=0; $k < $ctlist_cnt; $k++) {
									echo '<option value="'.$ctlist[$k].'"'.get_selected($ctlist[$k], $boset['contm']).'>'.$ctlist[$k].'</option>'.PHP_EOL;
								} 
							?>
						</select>
					</td>
					<td class="text-muted">
						보드스킨 내 /content 폴더에 있는 php 파일
					</td>
					</tr>
					<tr>
					<td class="text-center">글 내용 하단</td>
					<td class="text-center">
						<select name="boset[contb]" class="custom-select">
							<option value="">출력안함</option>
							<?php
								$boset['contb'] = isset($boset['contb']) ? $boset['contb'] : '';
								for ($k=0; $k < $ctlist_cnt; $k++) {
									echo '<option value="'.$ctlist[$k].'"'.get_selected($ctlist[$k], $boset['contb']).'>'.$ctlist[$k].'</option>'.PHP_EOL;
								} 
							?>
						</select>
					</td>
					<td class="text-muted">
						보드스킨 내 /content 폴더에 있는 php 파일
					</td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</li>

	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">리스트 스타일</label>
			<div class="col-sm-10">
				<div class="table-responsive">
					<table class="table table-bordered mb-0">
					<tbody>
					<tr class="bg-light">
						<th class="text-center nw-c1">구분</th>
						<th class="text-center nw-c2">설정</th>
						<th class="text-center">비고</th>
					</tr>
					<tr>
					<td class="text-center">이미지 미리보기</td>
					<td class="text-center">
						<div class="custom-control custom-checkbox">
							<?php $boset['popover'] = isset($boset['popover']) ? $boset['popover'] : ''; ?>
							<input type="checkbox" name="boset[popover]" value="1"<?php echo get_checked('1', $boset['popover'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					<td class="text-muted">
						미리보기 이미지 크기는 갤러리 스타일의 썸네일 크기, 모바일 작동안함
					</td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</li>

	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">갤러리 스타일</label>
			<div class="col-sm-10">
				<div class="table-responsive">
					<table class="table table-bordered mb-0">
					<tbody>
					<tr class="bg-light">
					<th class="text-center nw-c1">구분</th>
					<th class="text-center nw-c2">설정</th>
					<th class="text-center">비고</th>
					</tr>
					<tr>
					<td class="text-center">썸네일 너비</td>
					<td>
						<div class="input-group">
							<?php $boset['thumb_w'] = (!isset($boset['thumb_w']) || $boset['thumb_w'] == "") ? 400 : (int)$boset['thumb_w']; ?>
							<input type="text" name="boset[thumb_w]" value="<?php echo $boset['thumb_w'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">px</span>
							</div>
						</div>
					</td>
					<td class="text-muted">기본값 : 400 - 0 설정시 썸네일 생성 안 함</td>
					</tr>
					<tr>
					<td class="text-center">썸네일 높이</td>
					<td class="text-center">
						<div class="input-group">
							<?php $boset['thumb_h'] = (!isset($boset['thumb_h']) || $boset['thumb_h'] == "") ? 225 : (int)$boset['thumb_h']; ?>
							<input type="text" name="boset[thumb_h]" value="<?php echo $boset['thumb_h'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">px</span>
							</div>
						</div>
					</td>
					<td class="text-muted">기본값 : 225 - 0 설정시 이미지 비율대로 생성</td>
					</tr>
					<tr>
					<td class="text-center">썸네일 기본 높이</td>
					<td>
						<div class="input-group">
							<?php $boset['thumb_d'] = (!isset($boset['thumb_d']) || $boset['thumb_d'] == "") ? '56.25%' : $boset['thumb_d']; ?>
							<input type="text" name="boset[thumb_d]" value="<?php echo $boset['thumb_d'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">%</span>
							</div>
						</div>
					</td>
					<td class="text-muted">기본값 : 56.25 - 썸네일 높이를 0으로 설정시 적용</td>
					</tr>
					<tr>
					<tr>
					<td class="text-center">XL 가로수</td>
					<td>
						<div class="input-group">
							<input name="boset[xl]" value="<?php echo isset($boset['xl']) ? $boset['xl'] : ''; ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">개</span>
							</div>
						</div>
					</td>
					<td class="text-muted">1200px 이상 : Extra large screen / wide desktop</td>
					</tr>
					<tr>
					<td class="text-center">LG 가로수</td>
					<td>
						<div class="input-group">
							<input name="boset[lg]" value="<?php echo isset($boset['lg']) ? $boset['lg'] : ''; ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">개</span>
							</div>
						</div>
					</td>
					<td class="text-muted">992px 이상 : Large screen / desktop</td>
					</tr>
					<tr>
					<td class="text-center">MD 가로수</td>
					<td>
						<div class="input-group">
							<input name="boset[md]" value="<?php echo isset($boset['md']) ? $boset['md'] : '3'; ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">개</span>
							</div>
						</div>
					</td>
					<td class="text-muted">768px 이상 : Medium screen / tablet</td>
					</tr>
					<tr>
					<td class="text-center">SM 가로수</td>
					<td>
						<div class="input-group">
							<input name="boset[sm]" value="<?php echo isset($boset['sm']) ? $boset['sm'] : ''; ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">개</span>
							</div>
						</div>
					</td>
					<td class="text-muted">576px 이상 : Small screen / phone</td>
					</tr>
					<tr>
					<td class="text-center">XS 가로수</td>
					<td>
						<div class="input-group">
							<input name="boset[xs]" value="<?php echo isset($boset['xs']) ? $boset['xs'] : '2'; ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">개</span>
							</div>
						</div>
					</td>
					<td class="text-muted">0px 이상 : Extra small screen / phone</td>
					</tr>
					<tr>
					<td class="text-center">No 이미지</td>
					<td colspan="2">
						<div class="input-group">
							<div class="input-group-prepend">
								<a href="<?php echo na_theme_href('image', 'no').'&amp;fid=no_img'; ?>" class="btn btn-primary btn-setup">
									<i class="fa fa-search"></i>
								</a>
							</div>
							<?php $boset['no_img'] = isset($boset['no_img']) ? $boset['no_img'] : ''; ?>
							<input type="text" id="no_img" name="boset[no_img]" value="<?php echo $boset['no_img'] ?>" class="form-control" placeholder="http://...">
						</div>
					</td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</li>
	<li class="list-group-item">
		<div class="form-group row mb-0">
			<label class="col-sm-2 col-form-label">웹진 스타일</label>
			<div class="col-sm-10">
				<div class="table-responsive">
					<table class="table table-bordered mb-0">
					<tbody>
					<tr class="bg-light">
					<th class="text-center nw-c1">구분</th>
					<th class="text-center nw-c2">설정</th>
					<th class="text-center">비고</th>
					</tr>
					<tr>
					<td class="text-center">글내용 길이</td>
					<td class="text-center">
						<div class="input-group">
							<?php $boset['wcut'] = (!isset($boset['wcut']) || $boset['wcut'] == "") ? 80 : (int)$boset['wcut']; ?>
							<input type="text" name="boset[wcut]" value="<?php echo $boset['wcut'] ?>" class="form-control" placeholder="80">
							<div class="input-group-append">
								<span class="input-group-text">자</span>
							</div>
						</div>
					</td>
					<td class="text-muted">기본값 : 80자</td>
					</tr>
					<tr>
					<td class="text-center">썸네일 너비</td>
					<td>
						<div class="input-group">
							<?php $boset['wthumb_w'] = (!isset($boset['wthumb_w']) || $boset['wthumb_w'] == "") ? 400 : (int)$boset['wthumb_w']; ?>
							<input type="text" name="boset[wthumb_w]" value="<?php echo $boset['wthumb_w'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">px</span>
							</div>
						</div>
					</td>
					<td class="text-muted">기본값 : 400 - 0 설정시 썸네일 생성 안 함</td>
					</tr>
					<tr>
					<td class="text-center">썸네일 높이</td>
					<td class="text-center">
						<div class="input-group">
							<?php $boset['wthumb_h'] = (!isset($boset['wthumb_h']) || $boset['wthumb_h'] == "") ? 225 : (int)$boset['wthumb_h']; ?>
							<input type="text" name="boset[wthumb_h]" value="<?php echo $boset['wthumb_h'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">px</span>
							</div>
						</div>
					</td>
					<td class="text-muted">기본값 : 225 - 0 설정시 이미지 비율대로 생성</td>
					</tr>
					<tr>
					<td class="text-center">썸네일 기본 높이</td>
					<td>
						<div class="input-group">
							<?php $boset['wthumb_d'] = (!isset($boset['wthumb_d']) || $boset['wthumb_d'] == "") ? '56.25%' : $boset['wthumb_d']; ?>
							<input type="text" name="boset[wthumb_d]" value="<?php echo $boset['wthumb_d'] ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">%</span>
							</div>
						</div>
					</td>
					<td class="text-muted">기본값 : 56.25 - 썸네일 높이를 0으로 설정시 적용</td>
					</tr>
					<tr>
					<tr>
					<td class="text-center">XL 이미지 크기</td>
					<td>
						<div class="input-group">
							<input name="boset[wxl]" value="<?php echo isset($boset['wxl']) ? $boset['wxl'] : '4'; ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">칼럼</span>
							</div>
						</div>
					</td>
					<td class="text-muted">1200px 이상 : Extra large screen / wide desktop</td>
					</tr>
					<tr>
					<td class="text-center">LG 이미지 크기</td>
					<td>
						<div class="input-group">
							<input name="boset[wlg]" value="<?php echo isset($boset['wlg']) ? $boset['wlg'] : ''; ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">칼럼</span>
							</div>
						</div>
					</td>
					<td class="text-muted">992px 이상 : Large screen / desktop</td>
					</tr>
					<tr>
					<td class="text-center">MD 이미지 크기</td>
					<td>
						<div class="input-group">
							<input name="boset[wmd]" value="<?php echo isset($boset['wmd']) ? $boset['wmd'] : ''; ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">칼럼</span>
							</div>
						</div>
					</td>
					<td class="text-muted">768px 이상 : Medium screen / tablet</td>
					</tr>
					<tr>
					<td class="text-center">SM 이미지 크기</td>
					<td>
						<div class="input-group">
							<input name="boset[wsm]" value="<?php echo isset($boset['wsm']) ? $boset['wsm'] : '5'; ?>" class="form-control">
							<div class="input-group-append">
								<span class="input-group-text">칼럼</span>
							</div>
						</div>
					</td>
					<td class="text-muted">576px 이상 : Small screen / phone</td>
					</tr>
					<tr>
					<td class="text-center">XS 이미지 크기</td>
					<td class="text-center">
						1단 고정
					</td>
					<td class="text-muted">0px 이상 : Extra small screen / phone</td>
					</tr>
					<tr>
					<td class="text-center">우측 이미지</td>
					<td class="text-center">
						<div class="custom-control custom-checkbox">
							<?php $boset['wimg'] = isset($boset['wimg']) ? $boset['wimg'] : ''; ?>
							<input type="checkbox" name="boset[wimg]" value="1"<?php echo get_checked('1', $boset['wimg'])?> class="custom-control-input" id="idCheck<?php echo $idn ?>">
							<label class="custom-control-label" for="idCheck<?php echo $idn; $idn++; ?>"></label>
						</div>
					</td>
					<td class="text-muted">
						이미지를 글 오른쪽에 출력함
					</td>
					</tr>
					<tr>
					<td class="text-center">No 이미지</td>
					<td colspan="2">
						<div class="input-group">
							<div class="input-group-prepend">
								<a href="<?php echo na_theme_href('image', 'no').'&amp;fid=wno_img'; ?>" class="btn btn-primary btn-setup">
									<i class="fa fa-search"></i>
								</a>
							</div>
							<?php $boset['wno_img'] = isset($boset['wno_img']) ? $boset['wno_img'] : ''; ?>
							<input type="text" id="wno_img" name="boset[wno_img]" value="<?php echo $boset['wno_img'] ?>" class="form-control" placeholder="http://...">
						</div>
					</td>
					</tr>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</li>
</ul>

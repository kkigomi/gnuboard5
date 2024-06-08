<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<style>
	.ul { list-style:disc; padding-left:15px; }
</style>
<div class="table-responsive">
	<table class="table table-bordered">
		<tbody>
		<tr class="bg-light">
			<th colspan="2" scope="col" class="text-center">구분</th>
			<th scope="col" class="text-center">변수</th>
			<th scope="col" class="text-center">설정값</th>
			<th scope="col" class="text-center">설정방법</th>
		</tr>
		<tr>
			<td rowspan="3" align="center">공통사항</td>
			<td align="center">선택</td>
			<td align="center"><b>name</b></td>
			<td align="center">-</td>
			<td>
				<ul class="ul">
				<li>미설정시 최고관리자 정보 자동등록</li>
				<li>회원은 "회원아이디,1" 형태 등록 ex) name="admin,1"</li>
				<li>비회원은 "이름" 형태 등록 ex) name="유튜브"</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td align="center">선택</td>
			<td align="center"><b>ca_name</b></td>
			<td align="center">분류명</td>
			<td>
				<ul class="ul">
				<li>미설정시 등록안함</li>
				<li>ex) ca_name="뮤직"</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td align="center">선택</td>
			<td align="center"><b>filter</b></td>
			<td align="center">단어,단어</td>
			<td>
				<ul class="ul">
				<li>지정 단어가 포함된 피드만 등록</li>
				<li>복수등록시 콤마(,)로 단어구분</li>
				<li>ex) filter="아미나,나리야"</li>
				</ul>
			</td>
		</tr>
		<tr class="bg-light">
			<td rowspan="3" align="center">RSS/ATOM</td>
			<td align="center">선택</td>
			<td align="center"><b>feed</b></td>
			<td align="center">rss</td>
			<td>
				<ul class="ul">
				<li>RSS/ATOM 피드수집 모드</li>
				<li>ex) feed="rss"</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td align="center"><b class="red">필수</b></td>
			<td align="center"><b>url</b></td>
			<td align="center">http://...</td>
			<td>
				<ul class="ul">
				<li>RSS/ATOM 피드 주소</li>
				<li>ex) url="http://..../rss"</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td align="center">예시</td>
			<td colspan="3">
				<ul class="ul">
				<li>feed="rss" url="http://amina.co.kr/rss/rss.php" name="나리야"</li>
				<li>url="http://amina.co.kr/rss" name="admin,1" ca_name="아미나" filter="테마,스킨,위젯"</li>
				</ul>
			</td>
		</tr>
		<tr class="bg-light">
			<td rowspan="7" align="center">유튜브</td>
			<td align="center"><b class="red">필수</b></td>
			<td align="center"><b>feed</b></td>
			<td align="center"><b>youtube</b></td>
			<td>
				<ul class="ul">
				<li>유튜브 검색결과 수집</li>
				<li>ex) feed="youtube"</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td align="center">선택</td>
			<td align="center"><b>rows</b></td>
			<td align="center">수집갯수</td>
			<td>
				<ul class="ul">
				<li>수집해올 갯수, 기본값 10, 최대값 50</li>
				<li>ex) rows="20"</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td align="center">선택</td>
			<td align="center"><b>channel</b></td>
			<td align="center">채널아이디</td>
			<td>
				<ul class="ul">
				<li>유튜브 채널피드 수집</li>
				<li>채널아이디는 1개만 등록가능</li>
				<li>ex) channel="채널아이디"</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td align="center">선택</td>
			<td align="center"><b>q</b></td>
			<td align="center">검색어</td>
			<td>
				<ul class="ul">
				<li>검색어 복수등록은 콤마(,)로 구분</li>
				<li>ex) q="테마,스킨,위젯"</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td align="center">선택</td>
			<td align="center"><b>order</b></td>
			<td align="center">정렬방법</td>
			<td>
				<ul class="ul">
				<li>1개만 지정가능함</li>
				<li>relevance – 검색 쿼리에 대한 관련성을 기준으로 리소스를 정렬(기본값)</li>
				<li>date – 리소스를 만든 날짜를 기준으로 최근 항목부터 시간 순서대로 리소스를 정렬.</li>
				<li>rating – 높은 평가부터 낮은 평가순으로 리소스를 정렬</li>
				<li>title – 제목에 따라 문자순으로 리소스를 정렬</li>
				<li>videoCount – 업로드한 동영상 수에 따라 채널을 내림차순으로 정렬</li>
				<li>viewCount – 리소스를 조회수가 높은 항목부터 정렬</li>
				<li>ex) order="viewCount"</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td align="center">선택</td>
			<td align="center"><b>region</b></td>
			<td align="center">지역코드</td>
			<td>
				<ul class="ul">
				<li>ISO 3166-1 alpha-2 국가 코드</li>
				<li>ex) region="kr"</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td align="center">예시</td>
			<td colspan="3">
				<ul class="ul">
				<li>feed="youtube" rows="20" q="테마,스킨,위젯" name="나리야" filter="나리야,아미나" region="kr"</li>
				<li>feed="youtube" channel="채널아이디" filter="유머,웃음" order="date"</li>
				</ul>
			</td>
		</tr>

		<tr class="bg-light">
			<td rowspan="3" align="center">비메오</td>
			<td align="center"><b class="red">필수</b></td>
			<td align="center"><b>feed</b></td>
			<td align="center"><b>vimeo</b></td>
			<td>
				<ul class="ul">
				<li>비메오 유저피드 수집</li>
				<li>ex) feed="vimeo"</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td align="center"><b class="red">필수</b></td>
			<td align="center"><b>user</b></td>
			<td align="center">유저네임</td>
			<td>
				<ul class="ul">
				<li>비메오 UserName 피드수집</li>
				<li>ex) user="유저네임"</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td align="center">예시</td>
			<td colspan="3">
				<ul class="ul">
				<li>feed="vimeo" user="유저네임" name="나리야" filter="나리야,아미나"</li>
				<li>feed="vimeo" user="유저네임" name="admin,1" ca_name="뮤직"</li>
				</ul>
			</td>
		</tr>

	</tbody>
	</table>
</div>

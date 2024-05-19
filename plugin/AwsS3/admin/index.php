<?php

use Gnuboard\Plugin\AwsS3\S3Admin;
use Gnuboard\Plugin\AwsS3\S3Service;

$sub_menu = '100920'; //메뉴는 admin.head 이전에 선언
include_once(dirname(__FILE__) . '/_common.php');
include_once(G5_ADMIN_PATH . '/admin.lib.php');

include_once(G5_PLUGIN_PATH . '/AwsS3/S3Service.php');
include_once(G5_PLUGIN_PATH . '/AwsS3/S3Admin.php');

global $auth;
auth_check_menu($auth, $sub_menu, 'r');

$admin_aws_config = [
    'access_key' => $_ENV['s3_access_key'] ?? '',
    'bucket_name' => $_ENV['s3_bucket_name'] ?? '',
    'bucket_region' => $_ENV['s3_bucket_region'] ?? '',
    'is_use_s3' => $_ENV['s3_is_use'] ?? '',
    'is_use_acl' => $_ENV['s3_is_use_acl'] ?? '',
];

$all_region = get_aws_regions();
$status = S3Service::getInstance()->get_connect_status();

/**
 * 기존 DB 커넥션을 받아서 (mysqli_stmt_bind_param 함수받아서) prepared 쿼리문 실행
 *
 * @param $sql
 * @param array $args 배열의 첫번째는 mysqli_stmt_bind_param()의 타입, 나머지는 쿼리에 바인딩될 변수들
 * @return mysqli_stmt
 */
function sql_bind_query($sql, $args)
{
    global $g5;
    $link = $g5['connect_db'];
    $length = count($args);
    for ($i = 0; $i < $length; $i++) {
        /* with call_user_func_array, array params must be passed by reference */
        $params[] = &$args[$i];
    }
    /**
     * @var mysqli_stmt
     */
    $stmt = $link->prepare(trim($sql));
    if ($stmt->error_list) {
        error_log($stmt->error);
    }

    /* use call_user_func_array, as $stmt->bind_param('s', $param); does not accept params array */
    call_user_func_array(array($stmt, 'bind_param'), $params);

    $stmt->execute();
    return $stmt;
}

function get_aws_regions()
{
    // https://docs.aws.amazon.com/ko_kr/general/latest/gr/rande.html
    return array(
        'ap-northeast-2' => '아시아 태평양(서울) - ap_northeast-2',
        'us-east-1' => '미국 동부(버지니아 북부) - us-east-1',
        'us-east-2' => '미국 동부(오하이오) - us-east-2',
        'us-west-1' => '미국 서부(캘리포니아 북부) - us-west-1',
        'us-west-2' => '미국 서부(오레곤) - us-west-2',
        'ap-east-1' => '아시아 태평양(홍콩) - ap-east-1',
        'ap-south-1' => '아시아 태평양(뭄바이) - ap-south-1',
        'ap-southeast-1' => '아시아 태평양(싱가포르) - ap-southeast-1',
        'ap-southeast-2' => '아시아 태평양(시드니) - ap-southeast-2',
        'ap-northeast-1' => '아시아 태평양(도쿄) - ap-northeast-1',
        'ap-northeast-3' => '아시아 태평양(오사카) - ap-northeast-3',
        'ca-central-1' => '캐나다(중부) - ca-central-1',
        'cn-north-1' => '중국(베이징) - cn-north-1',
        'cn-northwest-1' => '중국(닝샤) - cn-northwest-1',
        'eu-central-1' => 'EU(프랑크푸르트) - eu-central-1',
        'eu-west-1' => 'EU(아일랜드) - eu-west-1',
        'eu-west-2' => 'EU(런던) - eu-west-2',
        'eu-west-3' => 'EU(파리) - eu-west-3',
        'eu-north-1' => 'EU(스톡홀름) - eu-north-1',
        'me-south-1' => '중동(바레인) - me-south-1',
        'sa-east-1' => '남아메리카(상파울루) - sa-east-1',
        'us-gov-east-1' => 'AWS GovCloud (미국 동부) - us-gov-east-1',
        'us-gov-west-1' => 'AWS GovCloud (US) - us-gov-west-1',
    );
}

include_once(G5_ADMIN_PATH . '/admin.head.php');
add_stylesheet('<link rel="stylesheet" href="' . G5_PLUGIN_URL . '/AwsS3/admin/adm.style.css">', 0);

// 관리자 페이지 aws s3 설정
?>
    <div class="aws-s3-area">
        <form name="f_s3_key" id="f_s3_key" method="post" action="#"
              autocomplete="off" role="presentation">
            <input type="hidden" name="save_key" value="save">
            <input type="hidden" name="token" value="" id="token">
            <p id="anc_cf_basic" class="tab_tit close">AWS S3 연결 상태:
                <?php
                echo $status['message'];
                ?>
            </p>
            <section class="tab_con">
                <h2 class="h2_frm">AWS S3 설정</h2>
                <ul class="frm_ul">
                    <li>
					<span class="lb_block"><label for="bucket_name">버킷이름</label>
					<?php
                    echo help('aws s3에서 버킷을 생성 후 버킷이름'); ?>
					</span>
                        <input type="text" name="bucket_name" autocomplete="new-password" value="<?php
                        echo $admin_aws_config['bucket_name']; ?>" id="bucket_name" class="frm_input" size="60">
                    </li>
                    <li>
					<span class="lb_block"><label for="bucket_region">리전</label>
					<?php
                    echo help('aws s3버킷의 지역을 표시합니다.'); ?>
					</span>
                        <select name="bucket_region" id="bucket_region">
                            <?php
                            foreach ($all_region as $k => $v) {
                                $selected = ($admin_aws_config['bucket_region'] === $k) ? 'selected="selected"' : '';
                                echo '<option value="' . $k . '" ' . $selected . ' >' . $v . '</option>';
                            }
                            ?>
                        </select>
                    </li>
                </ul>
                <ul class="frm_ul">
                    <li>
					<span class="lb_block"><label for="access_key">엑세스 키</label>
					</span>
                        <input type="text" autocomplete="new-password" name="access_key" value="<?php
                        echo $admin_aws_config['access_key']; ?>" id="access_key" class="frm_input" size="60">
                    </li>
                    <li>
					<span class="lb_block"><label for="secret_key">비밀키</label>
					</span>
                        <input type="password" autocomplete="new-password" name="secret_key" value="" id="secret_key"
                            placeholder="비밀입니다 env 를 확인하세요."   
                            class="frm_input"
                            size="60">
                    </li>
                    <li>
					<span class="lb_block"><label for="is_use_acl">ACL 사용여부</label>
					<?php
                    echo help('ACL 은 처음에 S3로 설정할 때 정하셔야 합니다. ACL 설정된 상태에서 해제할경우 s3 콘솔에서 모든 파일의 ACL을 해제한 뒤 선택하셔야됩니다.'); ?>
					</span>

                        <label>
                            <select name="is_use_acl">
                                <option value="true" <?php
                                if ($admin_aws_config['is_use_acl'] === true) {
                                    echo 'selected';
                                } ?>>ACL 사용
                                </option>
                                <option value="false" <?php
                                if ($admin_aws_config['is_use_acl'] === false) {
                                    echo 'selected';
                                } ?>>ACL 사용안함
                                </option>
                            </select>
                        </label>
                    </li>
                </ul>

            </section>
        </form>

        <form name="f_s3_state" id="f_s3_state" method="post" action="#"
              autocomplete="off" role="presentation">
            <section class="tab_con">
                <ul class="frm_ul">
                    <input type="hidden" name="save" value="status">
                    <input type="hidden" name="token" value="">
                    <li>
					<span class="lb_block"><label for="access_control_list">파일 권한 ACL</label>
					<?php
                    echo help(
                        '개별 파일의 권한을 설정합니다. 이미지,비디오 확장자파일( jpg, jpeg, png, gif, webp, bmp, mp4, webm ) 은 항상 공개이며 (public-read)
                        private 이면 그 밖에 다른 확장자파일은 private 권한이 부여됩니다. <br>public-read 이면 업로드 되는 모든 파일은 public-read 권한이 부여됩니다.'
                    ); ?>
					</span>
                        <?php
                        $aws_s3_acl = array('private', 'public-read'); ?>
                        <select name="access_control_list" id="access_control_list">
                            <?php
                            foreach ($aws_s3_acl as $v) {
                                $txt = ($v === 'private') ? 'private (이미지제외)' : $v;
                                $selected = ($admin_aws_config['access_control_list'] === $v) ? 'selected="selected"' : '';
                                echo '<option value="' . $v . '" ' . $selected . ' >' . $txt . '</option>';
                            }
                            ?>
                        </select>
                    </li>
                </ul>
                <ul class="frm_ul">
                    <li>
                        <span class="lb_block"><label for="is_use_s3">S3 사용하기</label>
                        <?php
                        echo help(
                            'data 폴더 안에 item, editor, file 폴더를 S3에 업로드 한 뒤, 데이터를 S3에만 저장합니다.'
                        ); ?>
                        </span>
                        <input type="checkbox" name="is_use_s3" value="1"
                               id="is_use_s3"
                            <?php
                            $is_use = $admin_aws_config['is_use_s3'];
                            echo $is_use == 'true' ? 'checked="true"' : '' ?>
                        >
                        <label for="is_use_s3">
                            현재 s3 사용여부
                        </label>
                    </li>
                </ul>
            </section>
        </form>

    </div>

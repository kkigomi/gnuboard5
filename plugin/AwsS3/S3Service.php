<?php
/*
 * S3Service 그누보드용 AWS S3 플러그인
 * @version 1.0.2 2022.07.28
 * @version 1.0.3 2023.04.28
 * url: sir 플러그인 게시판
 */

namespace Gnuboard\Plugin\AwsS3;

if (!defined('_GNUBOARD_')) {
    exit;
} // 개별 페이지 접근 불가
const S3CONFIG_FILE = 's3config.php';
if (file_exists(G5_DATA_PATH . '/' . S3CONFIG_FILE)) {
    include_once(G5_DATA_PATH . '/' . S3CONFIG_FILE);
}


use Aws\CommandPool;
use Aws\Credentials\Credentials;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

class S3Service
{
    private $access_key = '';
    private $secret_key = '';
    private $region = '';
    private $bucket_name = '';
    /**
     * @var string 외부 저장소 주소
     */
    private $endpoint = '';

    /**
     * @var string
     * S3 ACL 사용시 개별 파일의 기본 ACL(access control list) 상태값
     * set_file_acl() 에서 설정.
     */
    private $acl_value = 'private';

    /**
     * @var bool
     */
    private $is_use_s3 = false;
    private $is_use_acl = false;
    /**
     * @var S3Client
     */
    private $s3_client;

//    private $extra_item_field = 'aws_images';
    private $storage_prefix = 'aws_s3';
//    private $shop_folder = 'item';
    private $table_name = 's3_config';

    // Hook 포함 클래스 작성 요령
    // https://github.com/Josantonius/PHP-Hook/blob/master/tests/Example.php
    // https://sir.kr/manual/g5/288

    /**
     * Class instance.
     * 싱글톤
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new self();
        }

        return $instance;
    }

    public function __clone()
    {
        // 객체 클론 방지
    }

    private function __construct()
    {
        $this->get_config();

        //s3 사용시에만 훅스 등록
//        var_dump($this->is_use_s3);
//        var_dump($this->bucket_name);
//        var_dump($this->access_key);
//        var_dump($this->secret_key);

        if ($this->is_use_s3 && $this->region && $this->bucket_name && $this->access_key && $this->secret_key) {

            $this->add_hooks();
            $this->s3_client()->registerStreamWrapperV2();
        }
    }

    /**
     * 객체 생성시 설정값 불러오기
     * @return void
     */
    private function get_config()
    {
        if (file_exists(G5_DATA_PATH . '/' . S3CONFIG_FILE)) {
            $this->bucket_name = G5_S3_BUCKET_NAME;
            $this->region = G5_S3_REGION;
            $this->access_key = G5_S3_ACCESS_KEY;
            $this->secret_key = G5_S3_SECRET_KEY;
            $this->is_use_acl = G5_S3_IS_USE_ACL;
        }

        $this->endpoint = "https://{$this->bucket_name}.s3.amazonaws.com";
        // 클라우드 프론트 사용시 endpoint 변경 'https://사용자.cloudfront.net';

        $table_name = G5_TABLE_PREFIX . $this->table_name;
        $sql = "SHOW TABLES LIKE '{$table_name}'";
        $is_install = sql_fetch($sql, false);
        if (!$is_install) {
            $this->db_set_up($table_name);
        } else {
            $sql = "select * from $table_name";
            $result = sql_fetch($sql, false);
            $this->acl_value = $result['acl_value'];
            $this->is_use_s3 = $result['is_use_s3'] == '1';
        }
    }

    private function db_set_up($table_name)
    {
        $sql = get_db_create_replace(
            "CREATE TABLE IF NOT EXISTS `$table_name` (
				  `acl_value` varchar(50) NOT NULL DEFAULT 'private',
				  `is_use_s3` tinyint(4) NOT NULL DEFAULT '1'
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );
        sql_query($sql, false);
        $sql = "INSERT INTO $table_name (`acl_value`, `is_use_s3`) VALUES ('private' ,0)";
        sql_query($sql);
    }

    /**
     * 테이블이름 추가
     * @return string
     */
    public function get_table_name()
    {
        return $this->table_name;
    }

    public function mime_content_type($filename)
    {
        $mime_types = [
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'webp' => 'image/webp',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            '7z' => 'application/x-7z-compressed',
            'gz' => 'application/gzip',
            'jar' => 'application/java-archive',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mpeg' => 'video/mpeg',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // MS Office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',

            // font
            'ttf' => 'application/x-font-ttf',
            'woff' => 'application/x-font-woff'
        ];

        $filenames = explode('.', $filename);
        $ext = strtolower(array_pop($filenames));
        if (!empty($ext)) {
            return $mime_types[$ext];
        }

        return 'application/octet-stream';
    }

    /**
     * S3 등 외부저장소에 업로드할때 필요한 에디터 경로 설정
     * 데이터폴더이름 + 에디터 + 날짜 + /
     * @return string
     */
    private function get_editor_path()
    {
        $ym = date('ym', G5_SERVER_TIME);
        return G5_DATA_DIR . '/' . G5_EDITOR_DIR . '/' . $ym . '/';
    }

    public function s3_client()
    {
        if ($this->s3_client === null) {
            //Create a S3Client
            $this->s3_client = new S3Client([
                'region' => $this->region,
                'version' => 'latest',
                'credentials' => [
                    'key' => $this->access_key,
                    'secret' => $this->secret_key
                ]
            ]);
        }

        return $this->s3_client;
    }

    /**
     * 연결 상태 확인
     * @return array
     */
    public function get_connect_status()
    {
        $response = [];

        $access_key = $this->access_key;
        $secret_key = $this->secret_key;
        $region = $this->region;
        $bucket_name = $this->bucket_name;

        if (empty($access_key) || empty($secret_key) || empty($region) || empty($bucket_name)) {
            $response['message'] = '설치 되어있지 않습니다.';
            return $response;
        }

        try {
            $credentials = new Credentials($access_key, $secret_key);
            $options = [
                'region' => $region,
                'version' => 'latest',
                'credentials' => $credentials
            ];
            $s3_client = new S3Client($options);
            //$bucket_region = $s3_client->getBucketLocation(['Bucket' => $bucket_name]);
            //$current_region = $bucket_region['LocationConstraint'];

           // if ('' !== $region) {
                //$response['message'] = "버킷 지역을 확인 후 다시 입력해주세요.";
                //$response['error'] = true;
                //return $response;
            //} else {
                $response['error'] = false;
                $response['message'] = "연결되어 있습니다.";
                return $response;
            //}
        } catch (S3Exception $s3Exception) {
            $response['error'] = true;
            $status_code = $s3Exception->getStatusCode();
            $error_message = $s3Exception->getAwsErrorMessage();
            $message = "HTTP 상태코드: {$status_code}\nAWS 메시지: {$error_message}\n연결에 실패했습니다. 버킷 이름과 지역, key 값을 확인해주세요.\n이름과 키 값이 올바른 경우 AWS 권한을 확인해주세요.";
            $response['message'] = $message;
        }

        return $response;
    }

    private function add_hooks()
    {

        // bbs,qa download.php 등에서 사용
        add_event('download_file_header', [$this, 'download_file_header'], 1, 2);

        // 에디터에서 파일삭제시
        add_event('delete_editor_file', [$this, 'delete_editor_file'], 1, 2);

        // 파일삭제시 썸네일 삭제
        add_event('delete_editor_thumbnail_after', [$this, 'delete_editor_thumbnail'], 2, 2);

        // bbs/write_update.php 등에서 쓰일수가 있음
        add_replace('write_update_upload_array', [$this, 'upload_file'], 1, 5);

        // bbs/write_update.php 훅스
        add_event('write_update_after', [$this, 'upload_gallery_thumbnail'], 2, 5);

        add_replace('download_file_exist_check', [$this, 'file_exist_check'], 1, 2);

        // bbs/view_image.php 파일에서 쓰임
        add_replace('get_editor_content_url', [$this, 'replace_url'], 1, 1);
        add_replace('get_file_board_url', [$this, 'replace_url'], 1, 1);

        // 썸네일 생성시 파일 체크함수
        add_replace('get_file_thumbnail_tags', [$this, 'get_thumbnail_tags'], 1, 2);

        // 에디터 파일 url이 aws s3 url 이 맞는지 체크
        add_replace('get_editor_filename', [$this, 'get_filename_url'], 1, 2);

        // 게시판 리스트에서 썸네일 출력
        add_replace('get_list_thumbnail_info', [$this, 'get_list_thumbnail_info'], 1, 2);

        // 파일 삭제시 체크 bbs/delete.php, bbs/delete_all.php 에서 사용됨
        add_replace('delete_file_path', [$this, 'delete_file'], 1, 2);

        // 에디터 url
        add_replace('get_editor_upload_url', [$this, 'editor_upload_url'], 1, 3);

        // wr_content 등 내용에서 내 도메인 이미지 url 을 aws s3 https 로 변환
        add_replace('get_view_thumbnail', [$this, 'get_view_thumbnail'], 1, 1);

        // bbs/view_image.php 사용됨
        add_replace('exists_view_image', [$this, 'exists_view_image'], 1, 3);
        add_replace('get_view_imagesize', [$this, 'set_external_storage_imagesize'], 2, 3);

        // 게시물 복사 또는 옮기기 bbs/move_update.php 에서 사용됨
        add_replace('bbs_move_update_file', [$this, 'bbs_move_update_file'], 1, 5);

        // 화면 랜더링 마지막에 실행
        add_event('tail_sub', [$this, 'add_onerror'], 1, 1);
    }

    public function bucket_exists($bucket)
    {
        return $this->s3_client()->doesBucketExist($bucket);
    }

    /**
     * S3 저장소에 파일 존재여부
     * @param string $key 파일이름
     * @param array $options
     * @return bool
     */
    public function object_exists($key, $options = array())
    {
        return $this->s3_client()->doesObjectExist($this->bucket_name, $key, $options);
    }

    /**
     * S3 저장소에서 파일 가져오기
     * @param $file_key
     * @return \Aws\Result
     */
    public function get_object($file_key)
    {
        $data = [
            'Bucket' => $this->bucket_name,
            'Key' => $file_key
        ];
        return $this->s3_client()->getObject($data);
    }

    /**
     * S3 에서 파일의 url 가져오기
     * @param $file_key
     * @return string
     */
    public function get_object_url($file_key)
    {
        return $this->s3_client()->getObjectUrl($this->bucket_name, $file_key);
    }

    /**
     * s3로 업로드
     * @param array $args AWS SDK의 put_object에 ACL 여부만 추가 ACL사용은 관리자에서 설정
     * @return \Aws\Result|string
     */

    public function put_object($args)
    {
        if ($this->is_use_acl == true) {
            $args['ACL'] = $this->set_file_acl($args['Key']);
        }
        return $this->s3_client()->putObject($args);
    }
    //    public function put_object($args)
//    {
//        if ($this->is_use_acl) {
//            $args['ACL'] = $this->set_file_acl($args['Key']);
//        }
//        $command = $this->s3_client()->getCommand('PutObject', $args);
//
//        $request =  $this->s3_client()->createPresignedRequest($command, '+10 minutes');
//        $presignedUrl = (string)$request->getUri();
//        var_dump($presignedUrl);
//        exit;
//
//        return $presignedUrl;
//        //return $this->s3_client()->putObject($args);
//    }

    /**
     * S3 에서 파일 삭제
     * @param $file_key
     * @return \Aws\Result
     */
    public function delete_object($file_key)
    {
        $data = [
            'Bucket' => $this->bucket_name,
            'Key' => $file_key
        ];
        return $this->s3_client()->deleteObject($data);
    }

    /**
     * S3 파일 복사
     * @param array $args AWS SDK의 copy_obejct에  ACL 여부만 추가 ACL사용은 관리자에서 설정
     * @return \Aws\Result
     */
    public function copy_object($args)
    {
        if ($this->is_use_acl === true) {
            $args['ACL'] = $this->set_file_acl($args['Key']);
        }
        return $this->s3_client()->copyObject($args);
    }

    /**
     * php sdk v3 getPaginator 와 동일 주로 개체(파일) 리스트의 페이지화
     * @url https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_paginators.html
     * @param string $action
     * @param array $args
     * @return \Aws\ResultPaginator
     */
    public function get_paginator($action, $args)
    {
        return $this->s3_client()->getPaginator($action, $args);
    }

    /**
     * 개체(파일)의 ACL 권한 부여
     * @param string $file_key
     * @return string 권한명
     */
    public function set_file_acl($file_key)
    {
        // https://docs.aws.amazon.com/ko_kr/AmazonS3/latest/dev/acl-overview.html

        if ($this->acl_value === 'public-read') {
            return 'public-read';
        }

        // 확장자가 이미지, 비디오인 경우 퍼블릭 권한을 부여
        if (preg_match('/(\.jpg|\.jpeg|\.gif|\.png|\.webp|\.bmp|\.mp4|\.webm)$/i', $file_key)) {
            return 'public-read';
        }

        return 'private';
    }

    public function storage()
    {
        return $this->storage_prefix . '_' . $this->bucket_name;
    }

    /**
     * S3 등 외부저장소가 아닌 웹 서버 내부 파일 삭제
     * @param string $filepath 파일이름포함 전체경로
     * @return bool
     */
    private function file_delete($filepath)
    {
        $replace_path = realpath($this->normalize_path($filepath));

        if (preg_match('/' . preg_quote(G5_DATA_PATH, '/') . '/i', $replace_path) !== false) {
            return unlink($replace_path); //@todo @ 삭제영향 확인
        }
        
        return false;
    }

    /**
     * 웹서버의 파일 가져올때 파일경로 정리
     * @param string $path
     * @return string
     */
    public function normalize_path($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('|(?<=.)/+|', '/', $path);
        if ($path[1] === ':') {
            $path = ucfirst($path);
        }
        return $path;
    }

    /**
     * S3 에서 폴더삭제
     * @param string $dirname
     * @return false|void
     */
    public function delete_folder($dirname)
    {
        if (!$this->s3_client()) {
            return false;
        }

        $prefix = G5_DATA_DIR . '/file/' . $dirname . '/';

        $results = $this->get_paginator('ListObjects', [
            'Bucket' => $this->bucket_name,
            'Prefix' => $prefix
        ]);

        foreach ($results as $result) {
            if (!isset($result['Contents']) || !$result['Contents']) {
                continue;
            }

            foreach ($result['Contents'] as $object) {
                $this->delete_object($object['Key']);
            }
        }
    }

    /**
     * S3 저장소 내부에서 파일 이동
     * @param string $oldfile 이동할 파일의 이름포함 전체경로
     * @param string $newfile 새로운 파일이름
     * @return false|void
     */
    public function move_file($oldfile, $newfile)
    {
        if ($oldfile === $newfile || !$this->s3_client()) {
            return false;
        }

        if ($this->object_exists($newfile)) {
            return false;
        }

        $this->copy_object([
            'Bucket' => $this->bucket_name,
            'Key' => $newfile,
            'CopySource' => $this->bucket_name . '/' . $oldfile
        ]);
    }


    /**
     * 게시물 복사 또는 옮기기 bbs/move_update.php 에서 사용됨
     * @param $files
     * @param $file_name
     * @param $bo_table
     * @param $move_bo_table
     * @param $insert_id
     * @return mixed
     */
    public function bbs_move_update_file($files, $file_name, $bo_table, $move_bo_table, $insert_id = 0)
    {
        if ($files['bf_fileurl'] && $files['bf_storage'] === $this->storage()) {
            $ori_filename = $this->get_filename_url('', parse_url($files['bf_fileurl']));
            if ($ori_filename) {
                $ori_key = G5_DATA_DIR . '/file/' . $bo_table . '/' . $ori_filename;
                $copy_key = G5_DATA_DIR . '/file/' . $move_bo_table . '/' . $file_name;

                $result = $this->copy_object([
                    'Bucket' => $this->bucket_name,
                    'Key' => $copy_key,
                    'CopySource' => $this->bucket_name . '/' . $ori_key
                ]);

                if (isset($result['ObjectURL']) && $result['ObjectURL']) {
                    $files['bf_fileurl'] = $result['ObjectURL'];

                    if ($files['bf_thumburl'] && $thumbname = $this->get_filename_url(
                            '',
                            parse_url($files['bf_thumburl'])
                        )) {
                        $ori_thumb_key = G5_DATA_DIR . '/file/' . $bo_table . '/' . $thumbname;
                        $copy_thumb_key = G5_DATA_DIR . '/file/' . $move_bo_table . '/' . str_replace(
                                'thumb-',
                                'thumb-copy-' . $insert_id . '-',
                                $thumbname
                            );

                        $result2 = $this->copy_object([
                            'Bucket' => $this->bucket_name,
                            'Key' => $copy_thumb_key,
                            'CopySource' => $this->bucket_name . '/' . $ori_thumb_key
                        ]);

                        if (isset($result2['ObjectURL']) && $result2['ObjectURL']) {
                            $files['bf_thumburl'] = $result['ObjectURL'];
                        }
                    }
                }
            }
        }

        return $files;
    }

    public function file_exist_check($bool, $fileinfo)
    {
        if ($bool === false && $fileinfo['bf_fileurl']) {
            $file_key = G5_DATA_DIR . '/file/' . $fileinfo['bo_table'] . '/' . basename($fileinfo['bf_fileurl']);
            if ($this->object_exists($file_key)) {
                return true;
            }
        }

        return $bool;
    }

    /**
     * wr_content 등 '에디터' 내용에서 내 도메인 이미지 url 을 aws s3 https 로 변환
     * @param $contents
     * @return array|string|string[]|null
     */
    public function get_view_thumbnail($contents)
    {
        $contents = preg_replace_callback(
            "/(<img[^>]*src *= *[\"']?)([^\"']*)/i",
            [$this, 'replace_url'],
            $contents
        );

        $contents = preg_replace_callback(
            "/(<video[^>]*poster *= *[\"']?)([^\"']*)/i",
            [$this, 'replace_url'],
            $contents
        );
        return $contents;
    }

    /**
     * AWS 및 CDN 으로 URL 변경
     * @param $matches
     * @return array|string|string[]
     */
    public function replace_url($matches)
    {
        $replace_url = $this->endpoint . '/' . G5_DATA_DIR;
        $storage_url = "https://{$this->bucket_name}.s3.amazonaws.com" . '/' . G5_DATA_DIR;

        if (is_array($matches)) {  //에디터 등
            if (strpos($matches[0], $storage_url) !== false) { //cdn 으로 주소가 변경되었을 경우 기존 s3 등 저장소 주소를 변경.
                return str_replace($storage_url, $replace_url, $matches[0]);
            }

            return str_replace(G5_DATA_URL, $replace_url, $matches[0]); //cdn 안쓸때.
        }

        if (strpos($matches, $storage_url) !== false) { //cdn 으로 주소가 변경되었을 경우 기존 s3 등 저장소 주소를 변경.
            return str_replace($storage_url, $replace_url, $matches);
        }
        return str_replace(G5_DATA_URL, $replace_url, $matches);
    }

    /**
     * 썸네일 생성시 파일 체크함수
     * @param $thumb_tag
     * @param $file_array
     * @return mixed|string
     */
    public function get_thumbnail_tags($thumb_tag = '', $file_array)
    {
        global $board, $g5;

        if ($file_array['path'] && $file_array['file'] && !($file_array['bf_fileurl'] || $file_array['bf_thumburl'])) {
            $filepath = str_replace(G5_URL, '', $file_array['path'] . '/' . $file_array['file']);

            // 내 서버에 해당 파일이 있으면 리턴
            if (file_exists($filepath)) {
                return $file_array;
            }

            $file_key = preg_replace('/^\/(\/)?/', '', $filepath);
            $is_check = !($file_array['bf_storage'] === 'no');

            $queryString = parse_url(htmlspecialchars_decode($file_array['href']));
            $queryString = $queryString['query'];
            $args = array();
            parse_str($queryString, $args);

            if ($is_check && $this->s3_client()) {
                if ($url = $this->get_object_url($file_key)) {
                    $file_array['bf_fileurl'] = $url;

                    $extension = strtolower(pathinfo($file_array['file'], PATHINFO_EXTENSION));
                    $thumb_width = isset($board['bo_image_width']) ? (int)$board['bo_image_width'] : 0;
                    $data_path = str_replace(G5_URL, '', $file_array['path']);

                    // 이미지가 jpg, png 이면 썸네일을 체크
                    if (in_array($extension, ['jpg', 'jpeg', 'gif', 'png'])
                        && $thumb_width && (int)$file_array['image_width'] > $thumb_width) {
                        // 썸네일 높이
                        $thumb_height = round(
                            ($thumb_width * $file_array['image_height']) / $file_array['image_width']
                        );

                        $arguments = [
                            'bo_table' => $args['bo_table'],
                            'wr_id' => $args['wr_id'],
                            'data_path' => $data_path,
                            'edt' => false,
                            'filename' => $file_array['file'],
                            'filepath' => str_replace(G5_DATA_URL, G5_DATA_PATH, $file_array['path']),
                            'thumb_width' => $thumb_width,
                            'thumb_height' => $thumb_height,
                            'is_create' => false,
                            'is_crop' => true,
                            'crop_mode' => 'center',
                            'is_sharpen' => false,
                            'um_value' => '',
                        ];

                        if ($thumb_info = $this->get_list_thumbnail_info(array(), $arguments)) {
                            $thumb_path_file = str_replace(G5_DATA_URL, G5_DATA_PATH, $thumb_info['src']);
                            $upload_mime = $this->mime_content_type($thumb_path_file);

                            $thumb_key = G5_DATA_DIR . str_replace(G5_DATA_URL, '', $thumb_info['src']);

                            // Upload thumbnail data.
                            $thumb_result = $this->put_object([
                                'Bucket' => $this->bucket_name,
                                'Key' => $thumb_key,
                                'Body' => fopen($thumb_path_file, 'rb'),
                                'ContentType' => $upload_mime
                            ]);

                            // 썸네일 파일을 aws s3에 성공적으로 업로드 했다면, 호스팅 공간에서 삭제합니다.
                            if (isset($thumb_result['ObjectURL']) && $thumb_result['ObjectURL']) {
                                $file_array['bf_thumburl'] = $thumb_result['ObjectURL'];

                                $this->file_delete($thumb_path_file);

                                $sql = " update {$g5['board_file_table']}
                                            set bf_fileurl = '" . $file_array['bf_fileurl'] . "',
                                                 bf_thumburl = '" . $file_array['bf_thumburl'] . "',
                                                 bf_storage = '" . $this->storage() . "'
                                          where bo_table = '{$args['bo_table']}'
                                                    and wr_id = '{$args['wr_id']}'
                                                    and bf_no = '{$args['no']}' ";

                                sql_query($sql);
                            }
                        }
                    }
                }
            }

            if (!$file_array['bf_fileurl']) {
                // 내서버 또는 aws S3 저장소에 파일이 없다면, 파일 테이블의 bf_storage 필드에 no를 기록하여 S3 저장소를 다시 체크하지 않게 합니다.

                $sql = " update {$g5['board_file_table']}
                            set bf_storage = 'no'
                          where bo_table = '{$args['bo_table']}'
                                    and wr_id = '{$args['wr_id']}'
                                    and bf_no = '{$args['no']}' ";
                sql_query($sql);
            }
        }

        if ((isset($file_array['bf_fileurl']) && $file_array['bf_fileurl']) || (isset($file_array['bf_thumburl']) && $file_array['bf_thumburl'])) {

            $thumburl = (isset($file_array['bf_thumburl']) && $file_array['bf_thumburl']) ? $file_array['bf_thumburl'] : $file_array['bf_fileurl'];
            $thumb_tag = '<a href="' . G5_BBS_URL . '/view_image.php?bo_table=' . $board['bo_table'] . '&amp;fn=' . urlencode(
                    $file_array['file']
                ) . '" target="_blank" class="view_image"><img src="' . $thumburl . '" alt="' . get_text(
                    $file_array['content']
                ) . '"/></a>';
        }

        return $thumb_tag;
    }

    /**
     *
     * @param string $download_path
     * @param $file_key
     * @return array|false
     */
    private function get_curl_image($download_path, $file_key)
    {
        // https://docs.aws.amazon.com/ko_kr/AmazonS3/latest/API/RESTBucketGET.html
        $image_url = $this->endpoint . '/' . $file_key;

        if (stripos($image_url, "https") === false) {
            $image_url = '';
        }

        if (empty($image_url)) {
            return array();
        }

        $curlUserAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:113.0) Gecko/20100101 Firefox/113.0";
        $curl = curl_init();
        $err_status = '';

        $fp = fopen($download_path, 'wb');
        curl_setopt($curl, CURLOPT_URL, $image_url);
        curl_setopt($curl, CURLOPT_FILE, $fp);
        curl_setopt($curl, CURLOPT_USERAGENT, $curlUserAgent);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 2);
        curl_setopt(
            $curl,
            CURLOPT_FOLLOWLOCATION,
            true
        ); // Follow redirects, the number of which is defined in CURLOPT_MAXREDIRS
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
        curl_exec($curl);
        $err_status = curl_error($curl);
        curl_close($curl);
        fclose($fp);

        if($err_status){
            error_log($err_status);
        }

        $image_info = array();

        if (strlen($err_status) == 0) {
            $image_info = @getimagesize($download_path);

            if ($image_info === null) {
                $image_info = array();
            }
        }
        return $image_info;
    }

    /**
     * 그누보드 썸네일
     * 네트워크로 인함 속도 문제로 인해 외부저장소에 업로드 안함.
     * @param $thumbnail_info
     * @param $arguments
     * @return array|mixed|string[]
     */
    public function get_list_thumbnail_info($thumbnail_info, $arguments)
    {
        $bo_table = $arguments['bo_table'] ?? '';
        $wr_id = $arguments['wr_id'] ?? '';
        $data_path = $arguments['data_path'] ?? '';
        $edt = $arguments['edt'] ?? '';
        $filename = $arguments['filename'] ?? '';
        $source_path = $target_path = $arguments['filepath'] ?? '';
        $thumb_width = $arguments['thumb_width'] ?? '';
        $thumb_height = $arguments['thumb_height'] ?? '';
        $is_create = $arguments['is_create'] ?? '';
        $is_crop = $arguments['is_crop'] ?? '';
        $crop_mode = $arguments['crop_mode'] ?? '';
        $is_sharpen = $arguments['is_sharpen'] ?? '';
        $um_value = $arguments['um_value'] ?? '';

        $tname = '';

        $thumb = array('src' => '', 'ori' => '', 'alt' => '');
        if (!$source_path && stripos($data_path, '/' . G5_EDITOR_DIR . '/') !== false) {
            $edt = true;
            $source_path = $target_path = G5_PATH . preg_replace(
                    '/^\/.*\/' . G5_DATA_DIR . '/',
                    '/' . G5_DATA_DIR,
                    dirname($data_path)
                );
        }

//        // 원본 파일이 내 호스팅에 있다면 리턴
//        if (file_exists($source_path . '/' . $filename)) {
//            return $thumbnail_info;
//        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // 이미지가 아니면 리턴
        if (!in_array($extension, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
            return $thumbnail_info;
        }

        $ori_filename = preg_replace("/\.[^\.]+$/i", "", $filename); // 확장자제거
        $theme_config = $GLOBALS['theme_config'] ?? null;
        if (!empty($bo_table) && $theme_config !== null) {
            $bo_gallery_width = $theme_config['bo_gallery_width'];
            $bo_gallery_height = $theme_config['bo_gallery_height'];
            $bo_mobile_gallery_width = $theme_config['bo_mobile_gallery_width'];
            $bo_mobile_gallery_height = $theme_config['bo_mobile_gallery_height'];

            if (!G5_IS_MOBILE) {
                if ($thumb_width == $bo_gallery_width && $thumb_height == $bo_gallery_height) {
                    $thumb['src'] = "thumb-{$ori_filename}_{$thumb_width}x{$thumb_height}." . $extension;
                    return $thumb;
                }
            } else { //PC
                if ($thumb_width == $bo_mobile_gallery_width && $thumb_height == $bo_mobile_gallery_height) {
                    $thumb['src'] = "thumb-{$ori_filename}_{$thumb_width}x{$thumb_height}." . $extension;
                    return $thumb;
                }
            }
        }

        $thumb_file_name = "thumb-{$ori_filename}_{$thumb_width}x{$thumb_height}." . $extension;
        $thumb_file = "$target_path/$thumb_file_name";

        $download_path = $source_path . '/' . $filename;
        $file_key = G5_DATA_DIR . str_replace(G5_DATA_PATH, '', $download_path);
        $image_info = $this->get_curl_image($download_path, $file_key);

        if (!$image_info) {
            $no_image_path = G5_PATH . '/img/no_img.png';
            if (file_exists($no_image_path)) {
                // 노 이미지로 썸네일 파일을 만들어 두번 다시 s3.amazonaws.com 에서 파일을 찾지 않도록 합니다.
                copy($no_image_path, $thumb_file);
                chmod($thumb_file, G5_FILE_PERMISSION);
            }

            // 생성한 파일 삭제
            unlink($download_path);
            return array();
        }

        if (file_exists($download_path)) {
            $tname = thumbnail(
                $filename,
                $source_path,
                $target_path,
                $thumb_width,
                $thumb_height,
                $is_create,
                $is_crop,
                $crop_mode,
                $is_sharpen,
                $um_value
            );

            // 다운받은 원본파일은 삭제
            unlink($download_path);
        }

        if ($tname) {
            if ($edt) {
                // 오리지날 이미지
                $ori = G5_URL . $data_path;
                // 썸네일 이미지
                $src = G5_URL . str_replace($filename, $tname, $data_path);
            } else {
                $ori = G5_DATA_URL . '/file/' . $bo_table . '/' . $filename;
                $src = G5_DATA_URL . '/file/' . $bo_table . '/' . $tname;
            }

            $thumbnail_info = [
                "src" => $src,
                "ori" => $ori,
                "alt" => ''
            ];
        }

        return $thumbnail_info;
    }

    /**
     * 파일 주소를 바꿔주는 유틸 함수.
     * @param string $url
     * @return array|string|string[]|null
     */
    public function fileurl_replace_key($url)
    {
        $parse_url_data = parse_url($url);
        $url_path = $parse_url_data['path'] ?? '';
        $path = preg_replace('/^\/.*\/' . G5_DATA_DIR . '/', G5_DATA_DIR, $url_path);
        return preg_replace('/^\/(\/)?/', '', $path);
    }

    /**
     * aws s3 에서 파일삭제
     * @param string $filepath
     * @param array $args
     * @return string
     */
    public function delete_file(string $filepath, $args = array())
    {
        if ($args['bf_fileurl'] && (stripos($args['bf_storage'], 'aws_s3') !== false) && $this->s3_client()) {
            $keyname = G5_DATA_DIR . "/file/{$args['bo_table']}/{$args['bf_file']}";
            $this->delete_object($keyname);

            if ($args['bf_thumburl']) {
                $this->delete_object($this->fileurl_replace_key($args['bf_thumburl']));
            }
        }

        return $filepath;
    }

    /**
     * url이 AWS 주소또는 host 로 설정한 주소인지 유효성검사
     * @param string $url
     * @return bool
     */
    public function url_validate($url)
    {
        $storage_url = "https://{$this->bucket_name}.s3.amazonaws.com";
        return (stripos($url, $this->endpoint) !== false) || stripos($url, $storage_url) !== false;
    }

    /**
     * file url 이 aws s3 url 이 맞는지 확인후 파일 이름 리턴
     * @param string $file_name
     * @param array $url_parse
     * @return string
     */
    public function get_filename_url($file_name, $url_parse)
    {
        $url = "{$url_parse['scheme']}://{$url_parse['host']}{$url_parse['path']}";
        if ($this->url_validate($url)) {
            $file_name = basename($url_parse['path']);
        }
        return $file_name;
    }

    /**
     * 에디터로 업로드한 url 얻기
     * @param string $fileurl 썸네일 함수 처리후 리턴값
     * @param string $ori_file_path 원본의 파일이름포함 경로
     * @param array $args ['file_name']
     * @return string url
     */
    public function editor_upload_url($fileurl, $ori_file_path, $args = array())
    {
        $editor_dir = G5_DATA_DIR . '/' . G5_EDITOR_DIR;

        $file_path = G5_DATA_PATH . '/' . G5_EDITOR_DIR . explode($editor_dir, $fileurl)[1];
        $file_key = $editor_dir . explode($editor_dir, $file_path)[1];
        $upload_mime = $this->mime_content_type($file_path);
        //원본
        $result = $this->put_object([
            'Bucket' => $this->bucket_name,
            'Key' => $file_key,
            'Body' => fopen($file_path, 'rb'),
            'ContentType' => $upload_mime
        ]);

        if (strpos($fileurl, 'thumb-') === false) {
            //썸네일 업로드
            $ori_file_path = G5_DATA_PATH . '/' . G5_EDITOR_DIR . explode($editor_dir, $ori_file_path)[1];
            //썸네일
            $thumb_file_name = $this->create_thumbnail($file_path);
            $thumb_file_path = dirname($ori_file_path) . '/' . $thumb_file_name;
            $thumb_file_key = $this->get_editor_path() . $thumb_file_name;

            $result = $this->put_object([
                'Bucket' => $this->bucket_name,
                'Key' => $thumb_file_key,
                'Body' => fopen($thumb_file_path, 'rb'),
                'ContentType' => $upload_mime
            ]);

            //글 업로드시 write_update에서 원본과 썸네일을 삭제합니다.
            if (isset($result['ObjectURL'])) {
                return $result['ObjectURL'];
            }
        }

        if (isset($result['ObjectURL'])) {
            return $result['ObjectURL'];
        }

        return $fileurl;
    }

    /**
     * 썸네일 생성
     * @param string $filepath 웹서버에 올라간 파일이름 포함 전체 경로
     * @return string $thumb_filepath
     */
    private function create_thumbnail($filepath, $thumb_width = 800, $thumb_height = null)
    {
        $file_name = basename($filepath);
        $thumb_path = dirname($filepath);
        return thumbnail($file_name, $thumb_path, $thumb_path, $thumb_width, $thumb_height, false);
    }

    /**
     * 훅스용 이미지가 있는지 확인
     * bbs/view_image.php 에 사용됨
     * @param $files
     * @param $filepath
     * @param $editor_file
     * @return bool
     */
    public function exists_view_image($files, $filepath, $editor_file)
    {
        $file_key = G5_DATA_DIR . str_replace(G5_DATA_PATH, '', $filepath);
        return $this->object_exists($file_key);
    }

    /**
     * 보여줄 이미지 크기 리턴
     * @param $files
     * @param string $server_filepath 웹서버의 파일 경로
     * @param $editor_file
     * @return array
     */
    public function set_external_storage_imagesize($files, $server_filepath, $editor_file)
    {
        //서버 랜더링이라 네트워크 과정에서 느려져서 임의로 크기설정
        return array(1 => '800', 2 => '900');
    }

    /**
     * 에디터에서 올린 파일 삭제시
     * @param string $file_path
     * @param bool $is_success
     * @return void
     */
    public function delete_editor_file($file_path, $is_success = false)
    {
        if (!$is_success && stripos($file_path, G5_DATA_PATH) === 0) {
            $keyname = $this->fileurl_replace_key($file_path);

            $this->delete_object($keyname);
        }
    }

    /**
     * 에디터 썸네일 삭제
     * @param $contents
     * @param $matchs
     * @return void
     */
    public function delete_editor_thumbnail($contents, $matchs)
    {
        $editor_path = G5_DATA_DIR . '/' . G5_EDITOR_DIR;
        $length = count($matchs[1]);
        for ($i = 0; $i < $length; $i++) {
            // 이미지 path 구함
            $img_url = explode($editor_path, $matchs[1][$i]);
            if (!isset($img_url[1])) {
                return;
            }
            $imgname = $img_url[1];
            //@todo thumb 로 대체.
            $filename = $editor_path . $imgname;

            $this->delete_object($filename);
        }
    }

    /**
     * (에디터만 해당) 이미지 없을때 대체이미지 onerror 등록
     * @return void
     */
    public function add_onerror()
    {
        echo <<<'EOD'
        <script>
        
        function addOnError(){
            const imgs = document.getElementsByTagName('img');
            let emptyImage = g5_url + '/img/no_img.png';

            //ie 11 지원
            for (let i = 0; i < imgs.length; i++) {
                if(imgs[i].getAttribute('src').includes('/data/editor/')) {
                    //- onerror 이벤트 chrome fix
                    let tempSrc = imgs[i].getAttribute('src');
                    imgs[i].setAttribute('src', null);
                    imgs[i].setAttribute('src', tempSrc);
                    //-
                    imgs[i].dataset['fallback'] = 0;
                    imgs[i].onerror = function() {  
                        let fallbackIndex = this.dataset['fallback'];
                        let hostImage = g5_url + '/data/editor/'+ this.getAttribute('src').split('/data/editor/')[1];
                        let fallbacks = [hostImage, emptyImage, '']
                        this.src = fallbacks[fallbackIndex];
                        if(fallbackIndex < fallbacks.length ){
                            this.dataset['fallback']++;
                        }
                    }
                }
            }
        }
        
        addOnError();
        </script>        
EOD;
        // nowdoc 문법에서 끝 태그를 들여쓰기 하면 안됩니다
        //그것은 PHP7.3부터 지원됩니다.

    }

    /**
     * bbs/download.php 등에서 쓰입니다.
     * @param array $fileinfo 파일정보 배열 ['bo_table'] => '' , ['bf_fileurl'] => ''. ['bf_source'] => ''
     * @param $file_exist_check
     * @return void
     */
    public function download_file_header($fileinfo, $file_exist_check)
    {
        if (!$file_exist_check) {
            $file_key = G5_DATA_DIR . '/file/' . $fileinfo['bo_table'] . '/' . basename($fileinfo['bf_fileurl']);
            $result = $this->get_object($file_key);

            $original_name = urlencode($fileinfo['bf_source']);

            header('Content-Description: File Transfer');
            //this assumes content type is set when uploading the file.
            header('Content-Type: ' . $result['ContentType']);
            header('Content-Disposition: attachment; filename=' . $original_name);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            //send file to browser for download.
            echo $result['Body'];

            exit;
        }
    }

    /**
     * bbs/write_update.php 등에서 쓰입니다
     * @param array $upload_info 업로드된 파일의 정보
     * @param string $filepath 파일이름 포함 전체 경로
     * @param array $board 그누보드 게시판변수
     */
    public function upload_file($upload_info, $filepath, $board, $wr_id, $w = '')
    {
        global $board;

        $file_key = G5_DATA_DIR . str_replace(G5_DATA_PATH, '', $filepath);
        $upload_mime = $this->mime_content_type($filepath);
        $thumb_result = [];

        // Upload data.
        $result = $this->put_object([
            'Bucket' => $this->bucket_name,
            'Key' => $file_key,
            'Body' => fopen($filepath, 'rb'),
            'ContentType' => $upload_mime
        ]);

        // 이미지 파일일 때
        if ($result['ObjectURL'] && $upload_info && ($upload_mime === 'image/png' || $upload_mime === 'image/jpeg')) {
            $size = $upload_info['image'];

            if ($size && isset($board['bo_image_width']) && $size[0] > $board['bo_image_width']) {
                $thumb_width = $board['bo_image_width'];

                // jpg 이면 exif 체크
                if ($size[2] == 2 && function_exists('exif_read_data')) {
                    $degree = 0;
                    $exif = @exif_read_data($filepath);
                    if (!empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 8:
                                $degree = 90;
                                break;
                            case 3:
                                $degree = 180;
                                break;
                            case 6:
                                $degree = -90;
                                break;
                        }

                        // 세로사진의 경우 가로, 세로 값 바꿈
                        if ($degree == 90 || $degree == -90) {
                            $tmp = $size;
                            $size[0] = $tmp[1];
                            $size[1] = $tmp[0];
                        }
                    }
                }

                // 썸네일 높이
                $thumb_height = round(($thumb_width * $size[1]) / $size[0]);
                $thumb_name = basename($filepath);
                $thumb_path = dirname($filepath);

                if ($thumb_file = thumbnail(
                    $thumb_name,
                    $thumb_path,
                    $thumb_path,
                    $thumb_width,
                    $thumb_height,
                    false
                )) {
                    $thumb_key = G5_DATA_DIR . str_replace(G5_DATA_PATH, '', $thumb_path . '/' . $thumb_file);

                    // Upload thumbnail data.
                    $thumb_result = $this->put_object([
                        'Bucket' => $this->bucket_name,
                        'Key' => $thumb_key,
                        'Body' => fopen($thumb_path . '/' . $thumb_file, 'rb'),
                        'ContentType' => $upload_mime
                    ]);

                    //썸네일 파일을 aws s3에 성공적으로 업로드 했다면, 호스팅 공간에서 삭제합니다.
                    if (isset($thumb_result['ObjectURL']) && $thumb_result['ObjectURL']) {
                        $this->file_delete($thumb_path . '/' . $thumb_file);
                    }
                }
            }
        }

        // 파일을 aws s3에 성공적으로 업로드 했다면, 호스팅 공간에서 삭제합니다.
        $this->file_delete($filepath);

        $return_value = [
            'fileurl' => $result['ObjectURL'],
            'thumburl' => $thumb_result['ObjectURL'] ?? '',
            'storage' => $this->storage(),
        ];

        return array_merge($upload_info, $return_value);
    }

    /**
     * 업로드 마지막에 훅스를 통해 갤러리용 썸네일을 만들어 올립니다.
     * @param $bo_table
     * @param $wr_id
     * @param $upload_file
     * @param $w
     * @return void
     */
    public function upload_gallery_thumbnail($board, $wr_id, $upload_file, $w, $redirect_url)
    {
        $wr_content = $GLOBALS['wr_content'];
        if (!empty(trim($wr_content))) { //훅스가 실행중인 wirte_update.php 파일의 변수
            $content = $wr_content;
        } else {
            $bo_table = $board['bo_table'];
            $table_name = G5_TABLE_PREFIX . "write_{$bo_table}";
            $get_content_query = "select wr_num, wr_content from {$table_name} where wr_id = ?";
            /**
             * @var \mysqli_stmt $stmt
             */
            $stmt = $GLOBALS['g5']['connect_db']->prepare($get_content_query);
            $stmt->bind_param('i', $wr_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $content = $row['wr_content'];
        }

        $matches = '';

        $content = str_replace("\\", '', $content);
        preg_match('/src="(.*?)"/i', $content, $matches);
        if (isset($matches[1]) && $matches[1]) {
            $parse_result = parse_url($matches[1]);
            $file_name = $parse_result['path'];

            $ori_filename = basename($file_name);
            $ori_file_path = G5_PATH . $parse_result['path'];
            $pc_thumb_width = $board['bo_gallery_width'];
            $pc_thumb_height = $board['bo_gallery_height'];
            $mobile_thumb_width = $board['bo_mobile_gallery_width'];
            $mobile_thumb_height = $board['bo_mobile_gallery_height'];

            if (file_exists($ori_file_path)) {
                $pc_thumb_file = $this->create_thumbnail($ori_file_path, $pc_thumb_width, $pc_thumb_height);
                $mobile_thumb_file = $this->create_thumbnail($ori_file_path, $mobile_thumb_width, $mobile_thumb_height);

                $pc_thumb_file_path = dirname($ori_file_path) . '/' . $pc_thumb_file;
                $pc_thumb_file_key = $this->get_editor_path() . $pc_thumb_file;

                $mobile_thumb_file_path = dirname($ori_file_path) . '/' . $mobile_thumb_file;
                $mobile_thumb_file_key = $this->get_editor_path() . $mobile_thumb_file;

                $upload_mime = $this->mime_content_type($ori_filename);
                $pc_upload_object = [
                    'Bucket' => $this->bucket_name,
                    'Key' => $pc_thumb_file_key,
                    'Body' => fopen($pc_thumb_file_path, 'rb'),
                    'ContentType' => $upload_mime
                ];
                if ($this->is_use_acl) {
                    $pc_upload_object['ACL'] = $this->set_file_acl($pc_upload_object['Key']);
                }

                $mobile_upload_object = [
                    'Bucket' => $this->bucket_name,
                    'Key' => $mobile_thumb_file_key,
                    'Body' => fopen($mobile_thumb_file_path, 'rb'),
                    'ContentType' => $upload_mime
                ];
                if ($this->is_use_acl) {
                    $mobile_upload_object['ACL'] = $this->set_file_acl($mobile_upload_object['Key']);
                }

                $commands = [
                    $this->s3_client->getCommand('PutObject', $pc_upload_object),
                    $this->s3_client->getCommand('PutObject', $mobile_upload_object)
                ];
                $pool = new CommandPool($this->s3_client, $commands);
                $promise = $pool->promise();
                // Force the pool to complete synchronously
                // 프로미스가 실행완료시까지 대기
                $promise->wait();

                @unlink($ori_file_path);
            } else {
                $no_image_path = G5_PATH . '/img/no_img.png';
                if (file_exists($no_image_path)) {
                    // 노 이미지로 썸네일 파일을 만들어 두번 다시 s3.amazonaws.com 에서 파일을 찾지 않도록 합니다.
                    @copy($no_image_path, $ori_file_path);
                    @chmod($ori_file_path, G5_FILE_PERMISSION);
                }
                @unlink($ori_file_path);
            }
        }
    }

    /**
     * AWS S3 에서 특정폴더 이하 썸네일 파일을 삭제합니다.
     * @param string $filePrefix 파일경로(s3객체 키)
     * @return void
     */
    public function delete_thumbnamil_by_prefix($filePrefix)
    {
        $files = $this->s3_client()->listObjects([
            'Bucket' => $this->bucket_name,
            'Prefix' => $filePrefix
        ]);

        if (!isset($files['Contents'])) {
            return;
        }
        $files = $files['Contents'];
        foreach ($files as $file) {
            if (strpos($file['Key'], 'thumb-') !== false) {
                $this->delete_object($file['Key']);
            }
        }
    }

}


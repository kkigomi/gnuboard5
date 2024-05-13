<?php

declare(strict_types=1);

class DamoangEditorImages
{
    /**
     * @return false|array{
     *      'hash': string,
     *      'size': int,
     *      'path': string,
     *      'url': string,
     * }
     */
    public static function existsImage(string $filepath, int $filesize = null)
    {
        $filehash = sha1_file($filepath);
        $filesize = filesize($filepath);

        $tableName = self::tableName();
        $result = sql_query("SELECT * FROM `{$tableName}` WHERE `filehash` = '{$filehash}'");

        while ($row = $result->fetch_assoc()) {
            if (intval($row['filesize']) === $filesize) {
                $datetime = \G5_TIME_YMDHIS;
                sql_query("UPDATE `{$tableName}` SET `uploaded_count` = `uploaded_count` + 1, `updated_at` = '{$datetime}'");
                return [
                    'path' => \G5_PATH . '/' . $row['filepath'],
                    'url' => \G5_URL . '/' . $row['filepath'],
                    'size' => $filesize,
                ];
            }
        }

        return false;
    }

    public static function logImage(string $filepath, string $filehash, int $filesize)
    {
        // 기존에 업로드된 파일이 없으면 DB에 기록
        $filepath = str_replace(G5_PATH, '', $filepath);
        $createdAt = \G5_TIME_YMDHIS;

        $tableName = self::tableName();
        $sql = "INSERT
            INTO `{$tableName}`
            SET `filepath` = '{$filepath}',
                `filesize` = {$filesize},
                `filehash` = '{$filehash}',
                `created_at` = '{$createdAt}'
        ";
        sql_query($sql, true);
    }

    /**
     * `prefix`를 포함한 테이블 이름을 반환
     */
    public static function tableName(): string
    {
        return \G5_TABLE_PREFIX . 'da_editor_images';
    }

    /**
     * 그누보드의 mysql connection
     *
     * @return \mysqli
     */
    public static function db()
    {
        return $GLOBALS['g5']['connect_db'];
    }
}

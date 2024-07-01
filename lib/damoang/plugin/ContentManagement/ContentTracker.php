<?php

declare(strict_types=1);

namespace Damoang\Plugin\ContentManagement;

class ContentTracker
{
    public const OPERATION_DELETE = '삭제';
    public const OPERATION_MODIFY = '수정';

    public static function tableName(): string
    {
        return \G5_TABLE_PREFIX . 'da_content_history';
    }

    public static function db()
    {
        return $GLOBALS['g5']['connect_db'];
    }

    public static function installed(): bool
    {
        $cacheKey = 'da-installed-content-history';
        return g5_get_cache($cacheKey) >= \DA_PLUGIN_CONTENTMANAGEMENT_VERSION;
    }

    public static function backupContent(string $bo_table, int $wr_id, array $write, string $operation): bool
    {
        $sql = self::getBackupInsertSQL($bo_table, $wr_id, $write, $operation);
        return self::executeQuery($sql);
    }

    public static function softDelete(string $bo_table, int $wr_id, array $write): bool
    {
        // 커스텀 예외와 적절한 처리 필요
        if (!self::backupContent($bo_table, $wr_id, $write, self::OPERATION_DELETE)) {
            throw new \Exception('Content backup failed');
        }
        $updateSql = self::getSoftDeleteUpdateSQL($bo_table, $wr_id);
        if (!self::executeQuery($updateSql)) {
            throw new \Exception('Content update failed');
        }
        return true;
    }

    public static function getContentHistory(string $bo_table, int $wr_id): array
    {
        $sql = self::getContentHistorySQL($bo_table, $wr_id);
        return self::executeAndMapQuery($sql);
    }

    public static function getLatestContentHistory(string $bo_table, int $wr_id): ?array
    {
        $sql = self::getLatestContentHistorySQL($bo_table, $wr_id);
        $result = self::executeAndMapQuery($sql);
        return !empty($result) ? $result[0] : null;
    }

    public static function getUserRecentContentHistory(string $mb_id, int $limit = 10): array
    {
        $sql = self::getUserRecentContentHistorySQL($mb_id, $limit);
        return self::executeAndMapQuery($sql);
    }

    private static function executeInTransaction(callable $operation): bool
    {
        $db = self::db();
        try {
            self::executeQuery("START TRANSACTION");
            $result = $operation();
            self::executeQuery("COMMIT");
            return $result;
        } catch (\Exception $e) {
            self::executeQuery("ROLLBACK");
            self::log($e->getMessage());
            return false;
        }
    }

    private static function executeQuery(string $sql): bool
    {
        return sql_query($sql, false, self::db());
    }

    private static function executeAndMapQuery(string $sql): array
    {
        $result = sql_query($sql, false, self::db());
        return self::mapQueryResultToArray($result);
    }

    private static function mapQueryResultToArray($result): array
    {
        $mappedResult = [];
        while ($row = sql_fetch_array($result)) {
            $mappedResult[] = [
                'id' => (int)$row['id'],
                'bo_table' => $row['bo_table'],
                'wr_id' => (int)$row['wr_id'],
                'wr_is_comment' => (int)$row['wr_is_comment'],
                'mb_id' => $row['mb_id'],
                'wr_name' => $row['wr_name'],
                'operation' => $row['operation'],
                'operated_by' => $row['operated_by'],
                'operated_at' => $row['operated_at'],
                'previous_data' => json_decode($row['previous_data'], true)
            ];
        }
        return $mappedResult;
    }

    private static function log(string $message, string $level = 'error'): void
    {
        error_log("[ContentTracker][$level] $message");
    }

    private static function getBackupInsertSQL(string $bo_table, int $wr_id, array $backupData, string $operation): string
    {
        $wr_is_comment = $backupData['wr_is_comment'];
        $mb_id = $backupData['mb_id'];
        $wr_name = $backupData['wr_name'];
        $backupData = json_encode($backupData, JSON_UNESCAPED_UNICODE);

        return " INSERT INTO " . self::tableName() . "
                SET bo_table = '$bo_table',
                    wr_id = $wr_id,
                    wr_is_comment = '$wr_is_comment',
                    mb_id = '$mb_id',
                    wr_name = '$wr_name',
                    operation = '$operation',
                    operated_by = '{$GLOBALS['member']['mb_id']}',
                    previous_data = '$backupData' ";
    }

    private static function getSoftDeleteUpdateSQL(string $bo_table, int $wr_id): string
    {
        return " UPDATE {$GLOBALS['g5']['write_prefix']}$bo_table
                 SET wr_subject = '',
                     wr_content = ''
                 WHERE wr_id = $wr_id ";
    }

    private static function getContentHistorySQL(string $bo_table, int $wr_id): string
    {
        return " SELECT *
                 FROM " . self::tableName() . "
                 WHERE bo_table = '$bo_table'
                   AND wr_id = $wr_id
                 ORDER BY operated_at DESC ";
    }

    private static function getLatestContentHistorySQL(string $bo_table, int $wr_id): string
    {
        return " SELECT *
                 FROM " . self::tableName() . "
                 WHERE bo_table = '$bo_table'
                   AND wr_id = $wr_id
                 ORDER BY operated_at DESC
                 LIMIT 1 ";
    }

    private static function getUserRecentContentHistorySQL(string $mb_id, int $limit): string
    {
        return " SELECT *
                 FROM " . self::tableName() . "
                 WHERE mb_id = '$mb_id'
                 ORDER BY operated_at DESC
                 LIMIT $limit ";
    }
}

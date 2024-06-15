<?php

namespace Damoang\Plugin\Dajoongi;

class Dajoongi
{
    const SCHEDULER_DATA_PATH = \G5_PATH . '/data/scheduler';
    const SCHEDULER_DATA_JSON_PATH = self::SCHEDULER_DATA_PATH . "/json";
    const SCHEDULER_DATA_LOG_PATH = self::SCHEDULER_DATA_PATH . "/log";
    const JSON_FILE_PATH = self::SCHEDULER_DATA_JSON_PATH . "/dajoongi.json";

    /** @var string */
    public $environment = 'development';
    /** @var array */
    private $oldList = [];
    /** @var array */
    private $newList = [];
    /** @var array */
    private $foundNewDajooni = [];

    public function __construct(string $environment = 'development')
    {
        $this->environment = $environment;
    }

    public function run(): void
    {
        try {
            $this->getOldJsonFile();
            $this->requestDajooniApi();
            $this->sendDiscordWebhook();
        } catch (\Exception $e) {
            file_put_contents(self::SCHEDULER_DATA_LOG_PATH . "/dajoongi.log", $e->getMessage() . "\n", FILE_APPEND);
        }
    }

    private function getOldJsonFile(): void
    {
        @mkdir(self::SCHEDULER_DATA_PATH);
        @mkdir(self::SCHEDULER_DATA_JSON_PATH);
        @mkdir(self::SCHEDULER_DATA_LOG_PATH);
        if (file_exists(self::JSON_FILE_PATH)) {
            $oldFile = fopen(self::JSON_FILE_PATH, "r");
            if (filesize(self::JSON_FILE_PATH) > 0) {
                $this->oldList = json_decode(@fread($oldFile, filesize(self::JSON_FILE_PATH)) ?? [], true);
            } else {
                $this->oldList = [];
            }
        } else {
            $this->oldList = [];
        }
    }

    private function requestDajooniApi(): void
    {
        global $g5;

        $this->foundNewDajooni = [];
        $this->newList = [
            'date' => date('Y-m-d H:i:s'),
            'data' => []
        ];

        $response = self::getList();

        if (isset($response)) {
            foreach ($response as $item) {
                if (isset($this->oldList) && isset($this->oldList['data'][$item['wr_ip']])) {
                    if (md5($item['dup_mb_ids']) != $this->oldList['data'][$item['wr_ip']]['hash']) {
                        $this->foundNewDajooni[] = $item;
                    }
                } else {
                    $this->foundNewDajooni[] = [
                        'title' => $item['wr_ip'],
                        'fields' => [
                            [
                                'name' => '중복 아이디',
                                'value' => $item['dup_mb_ids'],
                            ],
                        ],
                        'description' => number_format($item['cnt']) . "번 중복이 발견됨",
                        'color' => 15548997
                    ];
                }
                $this->newList['data'][$item['wr_ip']] = [
                    'hash' => md5($item['dup_mb_ids'] ?? ''),
                    'wr_ip' => $item['wr_ip'],
                    'dup_mb_ids' => $item['dup_mb_ids'],
                    'cnt' => $item['cnt'],
                    'dup_bd_nm' => $item['dup_bd_nm']
                ];
            }
        }
        $this->saveNewJsonFile();
    }

    private function saveNewJsonFile(): void
    {
        file_put_contents(self::JSON_FILE_PATH, json_encode($this->newList, JSON_PRETTY_PRINT));
    }

    private function sendDiscordWebhook(): void
    {
        $embeds = array_merge(
            [
                [
                    'title' => '다모앙 다중이 이벤트',
                    'description' => "다모앙 다중이 " . date('Y년 m월 d일') . " 목록 입니다. :grinning:",
                    "color" => 1127128
                ]
            ],
            count($this->foundNewDajooni) > 0 ? $this->foundNewDajooni : [
                [
                    'title' => '오늘 새로운 다중이는 없습니다',
                    'description' => "어제와 비교해서 새로운 다중이의 출현이 없는 깨끗한 날입니다. :sunglasses:",
                ]
            ]
        );

        if ($_ENV['DAJOONGI_WEBHOOK_URL'] ?? null) {
            Http::post(
                $_ENV['DAJOONGI_WEBHOOK_URL'],
                [
                    'embeds' => $embeds
                ],
                [
                    'Accept: application/json'
                ],
                true
            );
        }
    }

    public static function getList(): array
    {
        global $g5;

        // 목록 가져오기
        $result = sql_query("SELECT
                wr_ip,
                GROUP_CONCAT(DISTINCT mb_id) AS dup_mb_ids,
                GROUP_CONCAT(DISTINCT bo_table) AS dup_bd_nm,
                COUNT(1) AS cnt
            FROM
                {$g5['board_new_table']}
            WHERE
                wr_ip <> ''
                AND mb_id NOT IN ('', 'admin')
                AND bn_datetime >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)
            GROUP BY
                wr_ip
            HAVING
                COUNT(DISTINCT mb_id) > 1
        ");

        $list = $result->fetch_all(\MYSQLI_ASSOC);

        $list = array_map(function ($item) {
            $item['wr_ip'] = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", G5_IP_DISPLAY, $item['wr_ip']);
            return $item;
        }, $list);

        return $list;
    }
}

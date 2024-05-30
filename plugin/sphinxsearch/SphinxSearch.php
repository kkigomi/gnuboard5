<?php

class SphinxSearch {
    protected $result_ids;
    protected $result_count;

    protected $search_str; //검색어

    protected $sql_where_add = []; //정렬순서나 기타 조건에 따른 검색조건

    protected $write_table = "";

    protected $search_fields = array();
    protected $search_values = array();

    protected $search_operator = array(); //검색어가 여러개인경우 검색조건(& and | or);

    protected $sph_connect = NULL;
    protected $result = NULL;
    protected $sphinx_hostname = NULL;
    protected $sphinx_port = 9306;
    protected $sphinx_dbname = NULL;
    protected $charset = "utf-8";

    protected $debug_info = "";
    protected $sql = "";

    protected $indexed_tables = array();

    public function __construct($sphinx_hostname = "127.0.0.1", $sphinx_port = 9306) {

        if(!$sphinx_hostname) {
            $sphinx_hostname = "127.0.0.1";
        }

        $this->sphinx_port = $sphinx_port;

        $this->sphinx_hostname = $sphinx_hostname;

        $this->write_table = $write_table;

        $this->sphinx_connect();
    }

    public function sphinx_connect() {
        if(!$this->sph_connect) {
            $this->sph_connect = @mysqli_connect($this->sphinx_hostname . ":".$this->sphinx_port, "", "");
            //mysql_select_db("board", $this->sph_connect) or mysql_error();
            if ($this->sph_connect) {
                $rs = mysqli_query($this->sph_connect, "show tables ");
                while (($row = mysqli_fetch_array($rs)) != null) {
                    $this->indexed_tables[] = $row['Index'];
                }
            }
        }
    }

    public function setTable($write_table) {
        $this->write_table = $write_table;
    }

    /**
     * 색인이 되어 있는 테이블인지 확인한다. 색인된 테이블에서만 검색이 수행되도록 한다.
     * @param $bo_table 게시판명
     * @return bool
     */
    public function is_indexed_table($write_table) {
        if(in_array($write_table, $this->indexed_tables) > 0) {
            return true;
        }
        return false;
    }

    public function sphinx_close() {
        if($this->sph_connect) {
            @mysqli_close($this->sph_connect);
            $this->sph_connect = null;
        }
    }

    public function __destruct() {
        $this->sphinx_close();
    }

    public function setBoard($bo_table) {
        $this->bo_table = $bo_table;
    }

    public function addSearchField($field) {
        $this->search_fields[] = $field;
    }

    public function getDebugInfo() {
        return $this->debug_info;
    }

    public function add_where_condition($where_condition) {
        $this->sql_where_add[] = $where_condition;
    }
    public function set_sql_search($search_ca_name, $search_field, $search_text, $search_operator='and') {

        $this->search_operator = ($search_operator == "and") ? "&" : "|";

        $str = "";
        if ($search_ca_name) {
            $this->add_where_condition(" ca_name='{$search_ca_name}' ");
            if(!trim($search_text)) {
                $this->add_where_condition(" wr_is_comment=0 ");
            }
        }

        $search_text = strip_tags(($search_text));
        $search_text = trim(stripslashes($search_text));

        $this->search_str = $search_text;


        // 검색어를 구분자로 나눈다. 여기서는 공백
        $s = array();
        $s = explode(" ", $search_text);

        // 검색필드를 구분자로 나눈다. 여기서는 +
        $tmp = explode(",", trim($search_field));
        $field = explode("||", $tmp[0]);

        $this->search_fields = $field;

        $this->search_values = array();
        for ($i=0; $i<count($s); $i++) {
            // 검색어
            $search_str = trim($s[$i]);
            if ($search_str == "") continue;

            $this->search_values[] = $search_str;
        }

    }


    /**
     * match 문구를 조회한다..
     * @return string
     */
    public function getSearchQuery() {

        $match_part1 = implode(",", $this->search_fields);
        $match_part2 = "";
        for($i=0; $i<count($this->search_values); $i++) {
            $match_part2 .= "\"".$this->search_values[$i]."\" ".$this->search_operator;
        }
        $match_part2 = trim($match_part2, $this->search_operator);

        if(!$match_part1 || !$match_part2) {
            return "";
        }
        $sphinx_search_str = "MATCH('@($match_part1) ($match_part2) ') ";

        return $sphinx_search_str;
    }

    public function search($write_table, $orderby, $from_record, $rows, $max_matches=10000) {

        $this->result_items = array();

        $sphinx_search_str = $this->getSearchQuery();
        if($sphinx_search_str) {
            //array_unshift($this->sql_where_add, $sphinx_search_str);
            $sql_where_condition = implode(" AND ", array_merge([$sphinx_search_str], $this->sql_where_add));
        } else {
            $sql_where_condition = implode(" AND ", $this->sql_where_add);
        }

        if(!$max_matches) $max_matches = 10000;

        $sql = "SELECT wr_id, wr_parent, bo_table, wr_is_comment FROM {$write_table}
                                WHERE
                                {$sql_where_condition}
                                {$orderby}
                                LIMIT $from_record, $rows
                                OPTION max_matches={$max_matches}  
                     ";

        echo "sphinxsearch query = ".$sql."<br/>";

        //쿼리 시간 측정
        $start_time = microtime(true);
        $result = mysqli_query($this->sph_connect, $sql);
        for ($i=0; $row=mysqli_fetch_array($result); $i++) {
            $this->result_items[] = $row;
        }
        $end_time = microtime(true);
        $this->debug_info = "Query Time : " . ($end_time - $start_time) . " sec";

        echo $this->debug_info;

        @mysqli_free_result($result);
        $rs =  mysqli_query($this->sph_connect, "show meta like 'total' ");
        if(($row=mysqli_fetch_array($rs)) != null){
            $this->match_rows = $row[1];
        }
    }

    function query($sql) {
        if($this->sph_connect) {
            $result = @mysqli_query($this->sph_connect, $sql) or die("<p>$sql<p>" . mysqli_errno() . " : " .  mysqli_error() . "<p>error file : {$_SERVER['SCRIPT_NAME']}");
            return $result;
        }
    }

    public function get_total_count($write_table) {

        $sphinx_search_str = $this->getSearchQuery();
        if($sphinx_search_str) {
            //array_unshift($this->sql_where_add, $sphinx_search_str);
            $sql_where_condition = implode(" AND ", array_merge([$sphinx_search_str], $this->sql_where_add));
        } else {
            $sql_where_condition = implode(" AND ", $this->sql_where_add);
        }

        $count_sql = "select count(*) cnt from {$write_table} where {$sql_where_condition} ";

        $result = $this->query($count_sql);

        $row = mysqli_fetch_assoc($result);
        $total_count = $row['cnt'];

        return $total_count;
    }

    public function get_items() {
        return $this->result_items;
    }


    public function get_match_rows() {
        return $this->match_rows;
    }


    public function getQuery() {
        return $this->query;
    }

}

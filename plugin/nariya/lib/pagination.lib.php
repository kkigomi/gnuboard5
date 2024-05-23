<?php

declare(strict_types=1);

class CommonPagination
{
    public $list_count = null;
    public $page = null;
    public $count = null;
    public $one_section = 10;

    public function getPagination()
    {
        $list_count = $this->list_count;
        $page = $this->page;
        $count = $this->count;

        $total_page = ($count) ? ceil($count / $list_count) : 1;

        if ($page < 1 || ($total_page && $page > $total_page)) {
            return array();
        }

        $one_section = $this->one_section;
        $current_section = ceil($page / $one_section);
        $all_section = ceil($total_page / $one_section);

        $first_page = ($current_section * $one_section) - ($one_section - 1);

        $last_page = $current_section * $one_section;

        if ($current_section == $all_section)
            $last_page = $total_page;

        $prev_page = (($current_section - 1) * $one_section);
        $next_page = (($current_section + 1) * $one_section) - ($one_section - 1);

        $output = array();
        if ($page != 1) {
            $pagination = new stdClass();
            $pagination->type = 'onePage';
            $pagination->page = 1;

            array_push($output, $pagination);
        }

        if ($current_section != 1) {
            $pagination = new stdClass();
            $pagination->type = 'prevPage';
            $pagination->page = $prev_page;

            array_push($output, $pagination);
        }

        for ($i = $first_page; $i <= $last_page; $i++) {
            if ($i == $page) {
                $pagination = new stdClass();
                $pagination->type = 'currentPage';
                $pagination->page = $i;

                array_push($output, $pagination);
            } else {
                $pagination = new stdClass();
                $pagination->type = 'page';
                $pagination->page = $i;

                array_push($output, $pagination);
            }
        }

        if ($current_section != $all_section) {
            $pagination = new stdClass();
            $pagination->type = 'nextPage';
            $pagination->page = $next_page;

            array_push($output, $pagination);
        }

        if ($page != $total_page) {
            $pagination = new stdClass();
            $pagination->type = 'endPage';
            $pagination->page = $total_page;

            array_push($output, $pagination);
        }

        return $output;
    }
}

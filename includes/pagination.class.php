<?php
class pagination
{
    var $items_per_page;
    var $items_total;
    var $current_page;
    var $total_pages;
    var $max_pages;
    var $query_string;
    function __construct()
    {
        $this->current_page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        $this->query_string = $_SERVER['QUERY_STRING'];
        $this->query_string = str_replace('&page=' . $this->current_page, '', $this->query_string);
        $this->query_string = str_replace('page=' . $this->current_page . '&', '', $this->query_string);
        $this->query_string = str_replace('page=' . $this->current_page, '', $this->query_string);
    }
    function displayPages($addonQuery = null)
    {
        if (!empty($this->query_string))
            $this->query_string = '&' . $this->query_string;
        if (isset($addonQuery) && !strpos($this->query_string, $addonQuery)) {
            $this->query_string .= '&' . $addonQuery;
        }
        $this->total_pages = ceil($this->items_total / $this->items_per_page);
        if ($this->total_pages <= 1)
            return;
        $rtn = 'Pages:';
        if ($this->total_pages <= $this->max_pages) {
            for ($i = 1; $i <= $this->total_pages; $i++) {
                $dis = ($i == $this->current_page) ? " disabled" : "";
                $rtn .= ' <a class="button blue' . $dis . '" style="width:20px !important;" href="?page=' . $i . $this->query_string . '">' . $i . '</a>';
            }
            return $rtn;
        } else {
            $max = floor($this->max_pages / 3);
            $midrange = ceil($this->max_pages / 3);
            $midrange = range(floor($this->total_pages / 2) - floor($midrange / 2), floor($this->total_pages / 2) + ceil($midrange / 2) - 1);
            $lowrange = range(1, $max);
            $highrange = range($this->total_pages - $max + 1, $this->total_pages);
            if (in_array($this->current_page, $lowrange)) {
                $min = max($this->current_page - floor($max / 2), 1);
                $lowrange = range($min, $min + ceil($max) - 1);
                return $this->bypass($lowrange, $midrange, $highrange);
            }
            if (in_array($this->current_page, $midrange)) {
                $midrange = range($this->current_page - floor($max / 2), $this->current_page + ceil($max / 2) - 1);
                return $this->bypass($lowrange, $midrange, $highrange);
            }
            if (in_array($this->current_page, $highrange)) {
                $highrange = range($this->current_page - floor($max / 2), min($this->current_page + ceil($max / 2) - 1, $this->total_pages));
                return $this->bypass($lowrange, $midrange, $highrange);
            } else {
                $midrange = ceil($this->max_pages / 3);
                $midstart = $this->current_page - floor($midrange / 2);
                $midrange = range($midstart + 1, $midstart + $midrange);
                $medmax = max($midrange);
                $medmin = min($midrange);
                if (in_array($medmin, $lowrange)) {
                    $lowrange = array_unique(array_merge($lowrange, $midrange));
                    $midrange = array();
                } elseif (in_array($medmax, $highrange)) {
                    $highrange = array_unique(array_merge($midrange, $highrange));
                    $midrange = array();
                }
                foreach ($lowrange as $page) {
                    $dis = ($page == $this->current_page) ? " disabled" : "";
                    $rtn .= ' <a class="button blue' . $dis . '" style="width:20px !important;" href="?page=' . $page . $this->query_string . '">' . $page . '</a>';
                }
                $rtn .= ' . . . ';
                if (!empty($midrange)) {
                    foreach ($midrange as $page) {
                        $dis = ($page == $this->current_page) ? " disabled" : "";
                        $rtn .= ' <a class="button blue' . $dis . '" style="width:20px !important;" href="?page=' . $page . $this->query_string . '">' . $page . '</a>';
                    }
                    $rtn .= ' . . . ';
                }
                foreach ($highrange as $page) {
                    $dis = ($page == $this->current_page) ? " disabled" : "";
                    $rtn .= ' <a class="button blue' . $dis . '" style="width:20px !important;" href="?page=' . $page . $this->query_string . '">' . $page . '</a>';
                }
                return $rtn;
            }
        }
    }
    function limit()
    {
        $start = $this->current_page * $this->items_per_page - $this->items_per_page;
        return ' LIMIT ' . $start . ', ' . $this->items_per_page;
    }
    function bypass($lo, $mi, $hi)
    {
        $rtn = '';

        foreach ($lo as $page) {
            $dis = ($page == $this->current_page) ? " disabled" : "";
            $rtn .= ' <a class="button blue' . $dis . '" style="width:20px !important;" href="?page=' . $page . $this->query_string . '">' . $page . '</a>';
        }
        $rtn .= ' . . . ';
        foreach ($mi as $page) {
            $dis = ($page == $this->current_page) ? " disabled" : "";
            $rtn .= ' <a class="button blue' . $dis . '" style="width:20px !important;" href="?page=' . $page . $this->query_string . '">' . $page . '</a>';
        }
        $rtn .= ' . . . ';
        foreach ($hi as $page) {
            $dis = ($page == $this->current_page) ? " disabled" : "";
            $rtn .= ' <a class="button blue' . $dis . '" style="width:20px !important;" href="?page=' . $page . $this->query_string . '">' . $page . '</a>';
        }

        return $rtn;
    }
}

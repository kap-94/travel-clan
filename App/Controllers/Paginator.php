<?php

namespace App\Controllers;

/**
 * Paginator controller
 * 
 * PHP @version 7.4
 */
class Paginator
{
    /**
     * Number of records to return
     * @var int
     */
    public int $limit;

    /**
     * Number of records to skip before the page
     * @var int
     */
    public int $offset;

    /**
     * Previous page number
     * @var int
     */
    public int $previous;

    /**
     * Next page number
     * @var int
     */
    public int $next;

    /**
     * Class constructor
     * 
     * @param int $page Page number
     * @param int $records_per_page Number of records per page
     * @param int $total_records Total number of records
     * 
     * @return void
     */
    public function __construct(int $page, int $records_per_page, int $total_records)
    {
        $this->limit = $records_per_page;

        $page = filter_var($page, FILTER_VALIDATE_INT, [
            'options' => [
                'default' => 1,
                'min_range' => 1
            ]
        ]);

        if ($page > 1) {
            $this->previous = $page - 1;
        }

        $total_pages = ceil($total_records / $records_per_page);

        if ($page < $total_pages) {
            $this->next = $page + 1;
        }

        $this->offset = $records_per_page * ($page - 1);
    }
}

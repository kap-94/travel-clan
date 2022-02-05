<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Article;

/**
 * Admin controller
 * 
 * PHP @version 7.4
 */
class Admin extends Authenticated
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction(): void
    {
        $paginator = new Paginator($_GET['page'] ?? 1, 10, Article::getTotal());
        $base = strtok($_SERVER["REQUEST_URI"], '?');

        View::renderTemplate('Admin/articles.html', [
            'articles' => Article::getPage($paginator->limit, $paginator->offset),
            'paginator' => $paginator,
            'base' => $base
        ]);
    }
}

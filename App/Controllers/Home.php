<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Article;
use \App\Models\Category;

/**
 * Home controller
 *
 * PHP @version 7.4
 */
class Home extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction(): void
    {
        $paginator = new Paginator($_GET['page'] ?? 1, 9, Article::getTotal());
        $base = strtok($_SERVER["REQUEST_URI"], '?');

        $categories = Category::getAll();


        View::renderTemplate('Home/index.html', [
            'articles' => Article::getPage($paginator->limit, $paginator->offset),
            'paginator' => $paginator,
            'base' => $base,
            'categories' => $categories
        ]);
    }
}

<?php


namespace App\Controllers;

use \Core\View;
use \App\Flash;
use \App\Models\Article;
use \App\Models\Category;

/**
 * Articles controller
 * 
 * PHP @version 7.4
 */
class Articles extends \Core\Controller
{
    /**
     * Before filter - called before each action method
     */
    protected function before(): void
    {
        // To eliminate overriting
        parent::before();

        if (isset($this->route_params['id'])) {
            $this->articleWithCategories = Article::getWithCategories($this->route_params['id']);
            $this->article = Article::getById($this->route_params['id']);

            if (!$this->article) throw new \Exception("Article not found");
        }
    }

    /**
     * Show all articles
     * 
     * @return void
     */
    public function showAllAction(): void
    {
        $paginator = new Paginator($_GET['page'] ?? 1, 10, Article::getTotal());
        $base = strtok($_SERVER["REQUEST_URI"], '?');

        View::renderTemplate('Articles/articles.html', [
            'articles' => Article::getPage($paginator->limit, $paginator->offset),
            'paginator' => $paginator,
            'base' => $base
        ]);
    }

    /**
     * Show all by category
     * 
     * @return void
     */
    public function showByCategoryAction(): void
    {
        $category = $this->route_params['category'];
        $paginator = new Paginator($_GET['page'] ?? 1, 10, Article::getTotal());
        $base = strtok($_SERVER["REQUEST_URI"], '?');

        View::renderTemplate('Articles/articles.html', [
            'articles' => Article::getPageByCategory($paginator->limit, $paginator->offset, $category),
            'paginator' => $paginator,
            'base' => $base
        ]);
    }

    /**
     * Show article
     * 
     * @return void
     */
    public function showAction(): void
    {
        $base = $_SERVER['REQUEST_URI'];

        View::renderTemplate('Articles/article.html', [
            'article' => $this->articleWithCategories,
            'base' => $base,
        ]);
    }

    /**
     * Show delete article
     * 
     * @return void
     */
    public function showDeleteAction(): void
    {
        $this->requireLogin();

        View::renderTemplate('Admin/delete-article.html', [
            'article' => $this->article,
        ]);
    }

    /**
     * Show delete article image
     * 
     * @return void
     */
    public function showDeleteImageAction(): void
    {
        $this->requireLogin();

        View::renderTemplate('Admin/delete-image.html', [
            'article' => $this->article,
        ]);
    }

    /**
     * Show delete article
     * 
     * @return void
     */
    public function deleteAction(): void
    {
        $this->requireLogin();

        if ($this->article->delete()) {

            Flash::addMessage('The article has been deleted successfully');

            $this->redirect("/admin");
        }
    }


    /**
     * Show edit article
     * 
     * @return void
     */
    public function editAction(): void
    {
        $this->requireLogin();

        $category_ids = array_column($this->article->getCategories(), 'id');
        $categories = Category::getAll();

        View::renderTemplate('Admin/edit-article.html', [
            'article' => $this->article,
            'categories' => $categories,
            'category_ids' => $category_ids
        ]);
    }


    /**
     * Update the article's data
     * 
     * @return void
     */
    public function updateAction(): void
    {
        $this->requireLogin();

        $category_ids = $_POST['category'] ?? [];
        $categories = Category::getAll();

        if ($this->article->update($_POST)) {

            $this->article->setCategories($category_ids);

            Flash::addMessage('Changes saved');

            $this->redirect("/article/{$this->article->id}");
        } else {
            View::renderTemplate('Admin/edit-article.html', [
                'article' => $this->article,
                'categories' => $categories,
                'category_ids' => $category_ids
            ]);
        }
    }


    /**
     * Upload an article's image
     * 
     * @return void
     */
    public function uploadImageAction(): void
    {
        $this->requireLogin();

        if ($this->article->uploadImageFile()) {

            Flash::addMessage('Changes saved');

            $this->redirect("/admin");
        } else {
            View::renderTemplate('Admin/edit-article.html', [
                'article' => $this->article
            ]);
        }
    }


    /**
     * Upload an article's image
     * 
     * @return void
     */
    public function deleteImageAction(): void
    {
        $this->requireLogin();

        if ($this->article->deleteImageFile()) {

            Flash::addMessage('Article image deleated');

            $this->redirect("/admin");
        } else {
            View::renderTemplate('Admin/edit-article.html', [
                'article' => $this->article
            ]);
        }
    }

    /**
     * Create a new article
     * 
     * @return void
     */
    public function createAction(): void
    {
        $this->requireLogin();

        $article = new Article($_POST);

        $categories = Category::getAll();
        $category_ids = $_POST['category'] ?? [];

        if ($article->create()) {

            $article->setCategories($category_ids);

            Flash::addMessage('Article created successfully', FLASH::SUCCESS);

            $this->redirect("/article/$article->id");
        } else {
            View::renderTemplate('Admin/new-article.html', [
                'article' => $article,
                'categories' => $categories,
                'category_ids' => $category_ids
            ]);
        }
    }

    /**
     * Show the new article page
     * 
     * @return void
     */
    public function newAction(): void
    {
        $this->requireLogin();

        $category_ids = [];
        $categories = Category::getAll();

        View::renderTemplate(
            'Admin/new-article.html',
            [
                'categories' => $categories,
                'category_ids' => $category_ids
            ]
        );
    }
}

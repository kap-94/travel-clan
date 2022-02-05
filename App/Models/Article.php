<?php

namespace App\Models;

use App\File;
use PDO;

/**
 * Article
 *
 * A piece of wirting for publication
 */
class Article extends \Core\Model
{
    /**
     * Validation errors
     * @var array
     */
    public array $errors = [];

    /**
     * Class constructor
     *
     * @param array $data Initial property values
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }


    /**
     * Validate the properties, putting any validation error messages in the $erros property
     *
     * @return bool True if the current properties are valid, false otherwise
     */
    protected function validate()
    {
        // Title
        if ($this->title == '') {
            $this->errors[] = 'Title is required';
        }

        // Author
        if ($this->author == '') {
            $this->errors[] = 'Author is required';
        }

        // Location
        if ($this->location == '') {
            $this->errors[] = 'Location is required';
        }

        // Intro
        if ($this->intro == '') {
            $this->errors[] = 'Intro is required';
        }

        // Content
        if ($this->content == '') {
            $this->errors[] = 'Content is required';
        }

        // Date
        if ($this->published_at != '') {
            $date_time = date_create_from_format('Y-m-d H:i:s', $this->published_at);

            if ($date_time === false) {
                $this->errors[] = 'Invalid date and time';
            } else {
                $date_errors = date_get_last_errors();

                if ($date_errors['warning_count'] > 0) {
                    $this->errors[] = 'Invalid date and time';
                }
            }
        }

        return empty($this->errors);
    }


    /**
     * Validate the properties of the image file, putting any validation error messages in the $erros property
     *
     * @return bool True if the current properties are valid, false otherwise
     */
    protected function validateImageFile()
    {
        // Image uploading
        if (empty($_FILES)) {
            $this->errors[] = 'Invalid upload';
        }

        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $this->errors[] = 'No file uploaded';
                break;
            case UPLOAD_ERR_INI_SIZE:
                $this->errors[] = 'File is too large (from the server setttings)';
                break;
            default:
                $this->errors[] = 'An error ocurred';
        }

        // Restrict the file size
        if ($_FILES['file']['size'] > 1000000) {
            $this->errors[] = 'File is too large';
        }

        // Restrict the file type
        $mime_types = ['image/gif', 'image/png', 'image/jpeg'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES['file']['tmp_name']);

        if (!in_array($mime_type, $mime_types)) {
            $this->errors[] = 'Invalid file type';
        }

        return empty($this->errors);
    }

    /**
     * Move the uploaded file in the corespondant directory
     * 
     * @return bool True if the file was successfully uploaded, false otherwise
     */
    public function uploadImageFile()
    {
        $file = new File($_FILES);
        $file->renamingDuplicates();
        $destination = $file->getDestination();

        if (move_uploaded_file($_FILES['file']['tmp_name'], $destination)) {

            $previous_image = $this->image_file;

            if ($this->setImageFile($file->getFileName())) {

                if ($previous_image) {
                    unlink("../public/uploads/$previous_image");
                }
                return true;
            }
        } else {
            return false;
        }
    }


    /**
     * Move the uploaded file in the corespondant directory
     * 
     * @return bool True if the file was successfully uploaded, false otherwise
     */
    public function deleteImageFile(): bool
    {
        $previous_image = $this->image_file;

        if ($this->setImageFile(null)) {

            if ($previous_image) {
                unlink("../public/uploads/$previous_image");
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get all the articles
     *
     * @return array An associative array of all the article records
     */
    public static function getAll(): array
    {
        $sql = 'SELECT *
                FROM articles
                ORDER BY published_at';

        $db = static::getDB();

        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;
    }

    /**
     * Get a page of the articles
     *
     * @param int $limit Number of records to return
     * @param int $offset Number of records to skip
     *
     * @return array An associative array of the page of article records
     */
    public static function getPage(int $limit, int $offset): array
    {
        $sql = 'SELECT a.*, categories.name AS category_name
                FROM (
                    SELECT * 
                    FROM articles
                    ORDER BY published_at
                    LIMIT :limit
                    OFFSET :offset
                ) AS a  
                LEFT JOIN article_category
                ON a.id = article_category.article_id
                LEFT JOIN categories
                ON article_category.category_id = categories.id';

        $db = static::getDB();

        $stmt = $db->prepare($sql);

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $articles = [];

        $previous_id = null;

        foreach ($results as $row) {

            $article_id = $row['id'];

            if ($article_id != $previous_id) {
                $row['category_names'] = [];

                $articles[$article_id] = $row;
            }

            $articles[$article_id]['category_names'][] = $row['category_name'];

            $previous_id = $article_id;
        }

        return $articles;

        $stmt = null;
    }

    /**
     * Get a page of the articles by category
     *
     * @param int $limit Number of records to return
     * @param int $offset Number of records to skip
     * @param string $category The category to search for
     *
     * @return array An associative array of the page of article records
     */
    public static function getPageByCategory(int $limit, int $offset, string $category): array
    {
        $sql = 'SELECT a.*, categories.name AS category_name
                FROM (
                    SELECT * 
                    FROM articles
                    ORDER BY published_at
                    LIMIT :limit
                    OFFSET :offset
                ) AS a  
                LEFT JOIN article_category
                ON a.id = article_category.article_id
                LEFT JOIN categories
                ON article_category.category_id = categories.id
                WHERE categories.name = :category';


        $db = static::getDB();

        $stmt = $db->prepare($sql);

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $articles = [];

        $previous_id = null;

        foreach ($results as $row) {

            $article_id = $row['id'];

            if ($article_id != $previous_id) {
                $row['category_names'] = [];

                $articles[$article_id] = $row;
            }

            $articles[$article_id]['category_names'][] = $row['category_name'];

            $previous_id = $article_id;
        }

        return $articles;

        $stmt = null;
    }

    /**
     * Get the article record based on the ID
     *
     * @param int $id The article id
     * @param string $columns Optional list of columns for the select, default to *
     *
     * @return mixed An article object, or null if not found
     */
    public static function getById(int $id, string $columns = '*')
    {
        $sql = "SELECT $columns
                FROM articles
                WHERE id = :id";

        $db = static::getDB();

        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // CHECK ERROR: SQLSTATE[HY000]: General error: could not call class constructor
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        if ($stmt->execute()) {
            return $stmt->fetch();
        }
    }

    /**
     * Get the article record based on the ID along with associated categories, if any
     * 
     * @param int $id the article ID
     * 
     * @return array The article data with categories
     */
    public static function getWithCategories(int $id): array
    {
        $sql = 'SELECT articles.*, categories.name AS category_name
                FROM articles
                LEFT JOIN article_category
                ON articles.id = article_category.article_id
                LEFT JOIN categories
                ON article_category.category_id = categories.id
                WHERE articles.id = :id';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;
    }


    /**
     * Get the article's categories
     * 
     * @return array An associative array with the article's categories
     */
    public function getCategories(): array
    {
        $sql = 'SELECT categories.*
                FROM categories
                JOIN article_category
                ON categories.id = article_category.category_id
                WHERE article_id = :id ';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;
    }

    /**
     * Update the article with its current property values
     *
     * @return bool True if the update was successful, false otherwise
     */
    public function update(array $data = []): bool
    {
        $this->title = $data['title'];
        $this->author = $data['author'];
        $this->location = $data['location'];
        $this->description = $data['description'];
        $this->intro = $data['intro'];
        $this->content = $data['content'];

        if ($data['published_at'] != '') {
            $this->published_at = $data['published_at'];
        }

        $this->validate();

        if (!empty($this->errors)) return false;

        $sql = 'UPDATE articles
                    SET title = :title,
                        author = :author,
                        location = :location,
                        description = :description,
                        intro = :intro,
                        content = :content,
                        published_at = :published_at
                    WHERE id = :id';

        $db = static::getDB();

        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':author', $this->author, PDO::PARAM_STR);
        $stmt->bindValue(':location', $this->location, PDO::PARAM_STR);
        $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
        $stmt->bindValue(':intro', $this->intro, PDO::PARAM_STR);
        $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);

        if ($this->published_at == '') {
            $stmt->bindValue(':published_at', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':published_at', $this->published_at, PDO::PARAM_STR);
        }

        return $stmt->execute();
    }

    /**
     * Update the selected categories
     * 
     * @param array $ids Categoryd IDs
     * 
     * @return void
     */
    public function setCategories(array $ids): void
    {
        if ($ids) {

            // $sql = "INSERT IGNORE INTO article_category (article_id, category_id)
            //         VALUES ";

            $sql = "INSERT INTO article_category (article_id, category_id)
                    VALUES ";

            $values = [];

            foreach ($ids as $id) {
                $values[] = "({$this->id}, ?)";
            }

            $sql .= implode(", ", $values);

            $sql .= " ON CONFLICT (article_id, category_id) DO NOTHING";

            $db = static::getDB();

            $stmt = $db->prepare($sql);

            foreach ($ids as $i => $id) {
                $stmt->bindValue($i + 1, $id, PDO::PARAM_INT);
            }

            $stmt->execute();
        }

        $sql = "DELETE FROM article_category
                    WHERE article_id = {$this->id}";

        if ($ids) {
            $placeholders = array_fill(0, count($ids), '?');

            $sql .= " AND category_id NOT IN (" . implode(", ", $placeholders) . ")";
        }

        $db = static::getDB();

        $stmt = $db->prepare($sql);

        foreach ($ids as $i => $id) {
            $stmt->bindValue($i + 1, $id, PDO::PARAM_INT);
        }

        $stmt->execute();
    }

    /**
     * Delete the current article
     *
     * @return bool True if the delete was successful, false otherwise
     */
    public function delete(): bool
    {
        $sql = 'DELETE FROM articles
                WHERE id = :id';

        $db = static::getDB();

        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Insert a new article with its current property values
     *
     * @return bool True if the insert was successful, false otherwise
     */
    public function create(): bool
    {
        if ($this->validate()) {

            $sql = 'INSERT INTO articles (title, author, location, description, intro, content, published_at)
                    VALUES (:title, :author, :location, :description, :intro, :content, :published_at)';

            $db = static::getDB();

            $stmt = $db->prepare($sql);

            $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':author', $this->author, PDO::PARAM_STR);
            $stmt->bindValue(':location', $this->location, PDO::PARAM_STR);
            $stmt->bindValue(':description', $this->description, PDO::PARAM_STR);
            $stmt->bindValue(':intro', $this->intro, PDO::PARAM_STR);
            $stmt->bindValue(':content', $this->content, PDO::PARAM_STR);

            if ($this->published_at == '') {
                $stmt->bindValue(':published_at', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':published_at', $this->published_at, PDO::PARAM_STR);
            }

            if ($stmt->execute()) {
                $this->id = $db->lastInsertId();
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Get a count of the total number of records
     *
     * @return int The total number of records
     */
    public static function getTotal(): int
    {

        $db = static::getDB();

        return $db->query('SELECT COUNT(*) FROM articles')->fetchColumn();
    }

    /**
     * Update the image file property
     * 
     * @param string $filename The filename of the image file
     * 
     * @return bool True if was successful, false otherwise
     */
    public function setImageFile($filename)
    {
        $sql = 'UPDATE articles 
                SET image_file = :image_file
                WHERE id = :id';

        $db = static::getDB();

        $stmt = $db->prepare($sql);

        $stmt->bindValue(':image_file', $filename, $filename == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}

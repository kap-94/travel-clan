<?php

namespace Core;

use PDO;
use App\Config;

/**
 * Base model
 *
 * PHP version 7.4
 */
abstract class Model
{

    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        static $db = null;

        if ($db === null) {
            // $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
            // $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

            $heroku_db = parse_url(getenv("DATABASE_URL"));

            $db = new PDO("pgsql:" . sprintf(
                "host=%s;port=%s;user=%s;password=%s;dbname=%s",
                $heroku_db["host"],
                $heroku_db["port"],
                $heroku_db["user"],
                $heroku_db["pass"],
                ltrim($heroku_db["path"], "/")
            ));

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }
}

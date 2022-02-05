<?php

namespace App\Models;

use PDO;
use \App\Token;

/**
 * Remembered login model
 * 
 * PHP version 7.4
 */
class RememberedLogins  extends \Core\Model
{
    /**
     * Find the remembered login model by Token
     * 
     * @param string $token The remembered login token
     * 
     * @return mixed Remembered login object if found, false otherwise 
     */
    public static function findByToken(string $token)
    {
        $token = new Token($token);
        $token_hash = $token->getHash();

        $sql = 'SELECT * FROM remembered_logins 
                WHERE token_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Get the user model associated with this remembered login
     * 
     * @return User The user model;
     */
    public function getUser()
    {
        return User::findById($this->user_id);
    }

    /**
     * See if the remember token has expired or no, based on the current system time
     * 
     * @return bool True if the token has expired, false otherwise 
     */
    public function hasExpired(): bool
    {
        return strtotime($this->expires_at) < time();
    }

    /**
     * Delete this model
     * 
     * @return void
     */
    public function delete(): void
    {
        $sql = 'DELETE FROM remembered_logins
                WHERE token_hash = :token_hash';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);

        $stmt->execute();
    }
}

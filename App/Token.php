<?php

namespace App;

/**
 * Token class
 * 
 * PHP version 7.4
 */
class Token
{
    /**
     * The token value
     * @var string $token
     */
    protected string $token;

    /**
     * Class constructor. Create a new random token
     * 
     * @param string $token_value (optional) A token value 
     * 
     * @return string A 32-character token
     */
    public function __construct(string $token_value = null)
    {
        if ($token_value) {
            $this->token = $token_value;
        } else {
            $this->token = bin2hex(random_bytes(16));
        }
    }

    /**
     * Get the token value
     * 
     * @param string The value
     */
    public function getValue(): string
    {
        return $this->token;
    }

    /**
     * Get the hashed token value
     * 
     * @return string The hashed value
     */
    public function getHash(): string
    {
        return hash_hmac('sha256', $this->token, Config::SECRET_KEY); // sha256 = 64 chars
    }
}

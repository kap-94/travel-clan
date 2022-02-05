<?php

namespace App\Controllers;

use \App\Models\User;

/**
 * Class Account
 * 
 * PHP @version 7.4
 */
class Account extends \Core\Controller
{
    /**
     * Validate if the email is available (AJAX) for a new form
     * 
     * @return void
     */
    public function validateEmailAction(): void
    {
        $is_valid = !User::emailExists($_GET['email'], $_GET['ignore_id'] ?? null);

        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }
}

<?php

namespace App\Controllers;

use \App\Models\User;
use \Core\View;

/**
 * Password controller
 * 
 * PHP @version 7.4
 */
class Password extends \Core\Controller
{
    /**
     * Show the forgotten password page
     * 
     * @return void
     */
    public function forgot(): void
    {
        View::renderTemplate('Password/forgot.html');
    }

    /**
     * Send the password reset link to the supplied email
     * 
     * @return void
     */
    public function requestResetAction(): void
    {
        User::sendPasswordReset($_POST['email']);

        View::renderTemplate('Password/reset_requested.html');
    }

    /**
     * Show the reset password form
     * 
     * @return void
     */
    public function resetAction(): void
    {

        $token = $this->route_params['token'];

        $user = $this->getUserOrExit($token);

        View::renderTemplate('Password/reset.html', [
            'token' => $token,
        ]);
    }

    /**
     * Reset the user's password
     * 
     * @return void
     */
    public function resetPasswordAction(): void
    {
        $token = $_POST['token'];

        $user = $this->getUserOrExit($token);

        if ($user->resetPassword($_POST['password'])) {
            View::renderTemplate('Password/reset_success.html');
        } else {
            View::renderTemplate('Password/reset.html', [
                'token' => $token,
                'user' => $user
            ]);
        }
    }

    /**
     * Find the user model associated with the password reset token, or end the request with a message
     * 
     * @param string $token Password reset token sent to user
     * 
     * @return mixed User object if found and the hoken hasn't expired, null otherwise
     */
    protected function getUserOrExit(string $token)
    {
        $user = User::findByPasswordReset($token);

        if ($user) return $user;

        View::renderTemplate('Password/token_expired.html');
        exit;
    }
}

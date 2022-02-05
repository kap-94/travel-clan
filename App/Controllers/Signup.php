<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Signup controller
 * 
 * PHP @version 7.4
 */
class Signup extends \Core\Controller
{
    /**
     * Show the signup page
     * 
     * @return void
     */
    public function newAction(): void
    {
        View::renderTemplate('Signup/new.html');
    }

    /**
     * Signup a new user
     * 
     * @return void
     */
    public function createAction(): void
    {
        $user = new User($_POST);

        if ($user->save()) {

            $user->sendActivationEmail();

            $this->redirect('/signup/success');
        } else {
            View::renderTemplate('Signup/new.html', [
                'user' => $user,
            ]);
        }
    }

    /**
     * Show the signup success page
     * 
     * @return void
     */
    public function successAction(): void
    {
        View::renderTemplate('Signup/success.html');
    }

    /**
     * Activate a new account
     * 
     * @return void
     */
    public function activateAction(): void
    {
        User::activate($this->route_params['token']);

        $this->redirect('/signup/activated');
    }

    /**
     * Show the activation success page
     * 
     * @return void
     */
    public function activatedAction(): void
    {
        View::renderTemplate('Signup/activated.html');
    }
}

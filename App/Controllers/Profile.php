<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use App\Flash;

/**
 * Profile controller
 * 
 * PHP @version 7.4
 */
class Profile  extends Authenticated
{
    /**
     * Before filter - called before each action method
     */
    protected function before(): void
    {
        // To eliminate overriting
        parent::before();

        $this->user = Auth::getUser();
    }

    /**
     * Show the profile page
     * 
     * @return void
     */
    public function showAction(): void
    {
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user
        ]);
    }

    /**
     * Show the form for editing the profile
     * 
     * @return void
     */
    public function editAction(): void
    {
        View::renderTemplate('Profile/edit.html', [
            'user' => $this->user
        ]);
    }

    /**
     * Update the profile's data
     * 
     * @return void
     */
    public function updateAction(): void
    {
        if ($this->user->updateProfile($_POST)) {

            Flash::addMessage('Changes saved');
            $this->redirect('/profile/show');
        } else {
            View::renderTemplate('Profile/edit.html', [
                'user' => $this->user
            ]);
        }
    }
}

<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    /**
     * Go back to where you came from
     *
     * @return mixed
     */
    protected function redirectBack() {
        return $this->response->redirect($this->request->getHTTPReferer());
    }

    /**
     * Check if someone is logged in
     *
     */
    protected function loggedIn() {
        if ($this->session->has("id")) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if someone is an admin
     *
     */
    protected function isAdmin() {
        if ($this->session->has("id") && $this->session->get("admin") == 1) {
            return true;
        } else {
            return false;
        }
    }
}

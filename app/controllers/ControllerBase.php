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
}

<?php

class LogoutController extends ControllerBase
{

    public function indexAction()
    {
        $this->session->destroy();
        $this->redirectBack();
    }

}


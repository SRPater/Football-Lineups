<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        if ($this->session->has("id")) {
            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "profile"
            ]);

            return;
        }
    }

}


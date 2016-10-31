<?php

class LogoutController extends ControllerBase
{

    public function indexAction()
    {
        if (!$this->session->has("id")) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);
            return;
        }

        $this->session->remove("id");
        $this->session->remove("name");
        $this->session->remove("admin");
        $this->session->destroy();
        $this->dispatcher->forward([
            "controller" => "index",
            "action"     => "index"
        ]);
    }

}


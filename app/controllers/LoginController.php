<?php

class LoginController extends ControllerBase
{

    public function indexAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action" => "index"
            ]);

            return;
        }

        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");

        if ($username == "" || $password == "") {
            $this->flash->error("Please fill in your username and password.");
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);
            return;
        }

        $user = Users::findFirst("username = '" . $username . "'");

        if (!$user) {
            $this->flash->error("This username does not exist.");
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);
            return;
        }

        $security = new Phalcon\Security();
        if (!$security->checkHash($password, $user->password)) {
            $this->flash->error("Your password is incorrect.");
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);
            return;
        }

        $this->session->set("name", $user->first_name . " " . $user->last_name);
        $this->session->set("id", $user->id);
        $this->session->set("admin", $user->is_admin);
        $this->dispatcher->forward([
            "controller" => "users",
            "action"     => "profile"
        ]);
    }

}

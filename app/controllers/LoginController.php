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

        $username = $this->request->getPost("usernameLogin");
        $password = $this->request->getPost("passwordLogin");

        if ($username == "" || $password == "") {
            $this->session->set("error", "Fill in your username and password.");
            $this->redirectBack();
            return;
        }

        $user = Users::findFirst("username = '" . $username . "'");

        if (!$user) {
            $this->session->set("error", "This username does not exist.");
            $this->redirectBack();
            return;
        }

        $security = new Phalcon\Security();
        if (!$security->checkHash($password, $user->password)) {
            $this->session->set("error", "Your password is incorrect.");
            $this->redirectBack();
            return;
        }

        $this->session->set("name", $user->first_name . " " . $user->last_name);
        $this->session->set("id", $user->id);
        $this->redirectBack();
    }

}


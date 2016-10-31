<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class UsersController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        if (!$this->isAdmin()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);

            return;
        }

        if ($this->dispatcher->getParam("page")) {
            $numberPage = $this->dispatcher->getParam("page");
        } else {
            if ($this->request->isGet()) {
                $numberPage = $this->request->getQuery("page", "int");
            } else {
                $numberPage = 1;
            }
        }

        $users = Users::find();
        $paginator = new Paginator([
            'data' => $users,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Searches for users
     */
    public function searchAction()
    {
        if (!$this->isAdmin()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);

            return;
        }

        $numberPage = 1;
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "index"
            ]);

            return;
        }

        $param = $this->request->getPost("search");
        $users = Users::find([
            "username LIKE '%" . $param . "%' OR first_name LIKE '%" . $param . "%' OR last_name LIKE '%" . $param . "%' OR email LIKE '%" . $param . "%'",
            "order" => "last_name"
        ]);
        if (count($users) == 0) {
            $this->flash->notice("The search did not find any users.");

            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $users,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
        if ($this->loggedIn()) {
            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "profile"
            ]);
        }
    }

    /**
     * Edits a user
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->loggedIn()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);

            return;
    }

        if (!$this->request->isPost()) {

            $user = Users::findFirstByid($id);
            if (!$user) {
                $this->flash->error("User was not found.");

                $this->dispatcher->forward([
                    "controller" => "users",
                    "action"     => "index"
                ]);

                return;
            }

            $this->view->id = $user->id;

            $this->tag->setDefault("id", $user->id);
            $this->tag->setDefault("username", $user->username);
            $this->tag->setDefault("first_name", $user->first_name);
            $this->tag->setDefault("last_name", $user->last_name);
            $this->tag->setDefault("email", $user->email);
            
        }
    }

    /**
     * Creates a new user
     */
    public function createAction()
    {
        if ($this->loggedIn()) {
            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "profile"
            ]);

            return;
        }

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "new"
            ]);

            return;
        }

        $user = new Users();
        $user->username = $this->request->getPost("username");
        if ($this->request->getPost("password") != "") {
            $user->password = $this->security->hash($this->request->getPost("password"));
        }
        $user->first_name = $this->request->getPost("first_name");
        $user->last_name = $this->request->getPost("last_name");
        $user->email = $this->request->getPost("email");
        $user->is_admin = 0;
        
        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "new"
            ]);

            return;
        }

        $this->flash->success("You have succesfully created a profile. Welcome!");

        $this->session->set("id", $user->id);
        $this->session->set("name", $user->first_name . " " . $user->last_name);
        $this->session->set("admin", $user->is_admin);

        $this->dispatcher->forward([
            "controller" => "users",
            "action"     => "profile"
        ]);
    }

    /**
     * Saves a user edited
     *
     */
    public function saveAction()
    {
        if (!$this->loggedIn()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);

            return;
        }

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "index"
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $user = Users::findFirstByid($id);

        if (!$user) {
            $this->flash->error("User does not exist " . $id);

            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "index"
            ]);

            return;
        }

        $user->username = $this->request->getPost("username");
        $user->first_name = $this->request->getPost("first_name");
        $user->last_name = $this->request->getPost("last_name");
        $user->email = $this->request->getPost("email", "email");
        

        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "edit",
                "params"     => [$user->id]
            ]);

            return;
        }

        $this->flash->success("Your profile was edited successfully.");

        $this->dispatcher->forward([
            "controller" => "users",
            "action"     => "profile"
        ]);
    }

    /**
     * Deletes a user
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        if (!$this->isAdmin()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);

            return;
        }

        $user = Users::findFirstByid($id);
        if (!$user) {
            $this->flash->error("User was not found.");

            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "index"
            ]);

            return;
        }

        if (!$user->delete()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "index"
            ]);

            return;
        }

        $this->flash->success("User was deleted successfully.");

        $this->dispatcher->forward([
            "controller" => "users",
            "action"     => "index"
        ]);
    }

    /**
     * Switches a user's admin status
     *
     */
    public function adminAction()
    {
        if (!$this->isAdmin()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);

            return;
        }

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "index"
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $user = Users::findFirstByid($id);

        if(!$user) {
            $this->flash->error("The user was not found.");

            $this->dispatcher->forward([
                "controller" => "users",
                "action"     => "index"
            ]);

            return;
        }

        if ($user->is_admin == 0) {
            $user->is_admin = 1;
        } else {
            $user->is_admin = 0;
        }

        if (!$user->save()) {
            foreach($user->getMessages() as $message) {
                $this->flash->error($message);
            }
        }

        $this->dispatcher->forward([
            "controller" => "users",
            "action"     => "index",
            "params"     => ["page" => $this->request->getPost("page")]
        ]);
    }

    /**
     * Shows the user's profile
     *
     */
    public function profileAction() {
        if (!$this->loggedIn()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);
        }
    }
}

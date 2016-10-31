<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class PlayersController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        if (!$this->loggedIn()) {
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

        if ($this->isAdmin()) {
            $players = Players::find([
                "order" => "last_name"
            ]);
        } else {
            $players = Players::find([
                "is_approved = 1",
                "order" => "last_name"]);
        }

        if (count($players) == 0) {
            $this->flash->notice("There are no players.");

            $this->dispatcher->forward([
                "controller" => "players",
                "action"     => "new"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $players,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Searches for players
     */
    public function searchAction()
    {
        if (!$this->loggedIn()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);

            return;
        }

        $numberPage = 1;
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                "controller" => "players",
                "action"     => "index"
            ]);

            return;
        }

        $param = $this->request->getPost("search");
        $players = Players::find([
            "first_name LIKE '%" . $param . "%' OR last_name LIKE '%" . $param . "%'",
            "order" => "last_name"
        ]);
        if (count($players) == 0) {
            $this->flash->notice("The search did not find any players.");

            $this->dispatcher->forward([
                "controller" => "players",
                "action"     => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $players,
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
        if (!$this->loggedIn()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);
        }
    }

    /**
     * Edits a player
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->isAdmin()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);

            return;
        }

        if (!$this->request->isPost()) {

            $player = Players::findFirstByid($id);
            if (!$player) {
                $this->flash->error("The player was not found.");

                $this->dispatcher->forward([
                    "controller" => "players",
                    "action"     => "index"
                ]);

                return;
            }

            $this->view->id = $player->id;

            $this->tag->setDefault("id", $player->id);
            $this->tag->setDefault("first_name", $player->first_name);
            $this->tag->setDefault("last_name", $player->last_name);
            $this->tag->setDefault("nationality", $player->nationality);
            $this->tag->setDefault("club", $player->club);
            $this->tag->setDefault("position", $player->position);
            
        }
    }

    /**
     * Creates a new player
     */
    public function createAction()
    {
        if (!$this->loggedIn()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);
        }

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                "controller" => "players",
                "action"     => "index"
            ]);

            return;
        }

        $player = new Players();
        $player->first_name = $this->request->getPost("first_name");
        $player->last_name = $this->request->getPost("last_name");
        $player->nationality = $this->request->getPost("nationality");
        $player->club = $this->request->getPost("club");
        $player->position = $this->request->getPost("position");
        $player->is_approved = 0;
        

        if (!$player->save()) {
            foreach ($player->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                "controller" => "players",
                "action"     => "new"
            ]);

            return;
        }

        $this->flash->success("Player was added successfully.");

        $this->dispatcher->forward([
            "controller" => "players",
            "action" => "index"
        ]);
    }

    /**
     * Saves a player edited
     *
     */
    public function saveAction()
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
                "controller" => "players",
                "action"     => "index"
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $player = Players::findFirstByid($id);

        if (!$player) {
            $this->flash->error("Player does not exist " . $id);

            $this->dispatcher->forward([
                "controller" => "players",
                "action"     => "index"
            ]);

            return;
        }

        $player->first_name = $this->request->getPost("first_name");
        $player->last_name = $this->request->getPost("last_name");
        $player->nationality = $this->request->getPost("nationality");
        $player->club = $this->request->getPost("club");
        $player->position = $this->request->getPost("position");
        

        if (!$player->save()) {

            foreach ($player->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                "controller" => "players",
                "action"     => "edit",
                "params"     => [$player->id]
            ]);

            return;
        }

        $this->flash->success("Player was updated successfully.");

        $this->dispatcher->forward([
            "controller" => "players",
            "action"     => "index"
        ]);
    }

    /**
     * Deletes a player
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

        $player = Players::findFirstByid($id);
        if (!$player) {
            $this->flash->error("The player was not found.");

            $this->dispatcher->forward([
                "controller" => "players",
                "action" => "index"
            ]);

            return;
        }

        if (!$player->delete()) {

            foreach ($player->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                "controller" => "players",
                "action"     => "search"
            ]);

            return;
        }

        $this->flash->success("Player was deleted successfully.");

        $this->dispatcher->forward([
            "controller" => "players",
            "action"     => "index"
        ]);
    }

    /**
     * Approves a player
     *
     * @param string $id
     */
    public function approveAction()
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
                "controller" => "players",
                "action"     => "index"
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $player = Players::findFirstByid($id);

        if(!$player) {
            $this->flash->error("The player was not found.");

            $this->dispatcher->forward([
                "controller" => "players",
                "action"     => "index"
            ]);

            return;
        }

        if ($player->is_approved == 0) {
            $player->is_approved = 1;
        } else {
            $player->is_approved = 0;
        }

        if (!$player->save()) {
            foreach($player->getMessages() as $message) {
                $this->flash->error($message);
            }
        }

        $this->dispatcher->forward([
            "controller" => "players",
            "action"     => "index",
            "params"     => ["page" => $this->request->getPost("page")]
        ]);
    }

}

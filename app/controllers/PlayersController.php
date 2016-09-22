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
        $this->persistent->parameters = null;
    }

    /**
     * Searches for players
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Players', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "last_name";

        $players = Players::find($parameters);
        if (count($players) == 0) {
            $this->flash->notice("The search did not find any players.");

            $this->dispatcher->forward([
                "controller" => "players",
                "action" => "index"
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

    }

    /**
     * Edits a player
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $player = Players::findFirstByid($id);
            if (!$player) {
                $this->flash->error("Player was not found.");

                $this->dispatcher->forward([
                    'controller' => "players",
                    'action' => 'index'
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
            $this->tag->setDefault("is_approved", $player->is_approved);
            
        }
    }

    /**
     * Creates a new player
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "players",
                'action' => 'index'
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
                'controller' => "players",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("Player was added successfully.");

        $this->dispatcher->forward([
            'controller' => "players",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a player edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "players",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $player = Players::findFirstByid($id);

        if (!$player) {
            $this->flash->error("player does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "players",
                'action' => 'index'
            ]);

            return;
        }

        $player->first_name = $this->request->getPost("first_name");
        $player->last_name = $this->request->getPost("last_name");
        $player->nationality = $this->request->getPost("nationality");
        $player->club = $this->request->getPost("club");
        $player->position = $this->request->getPost("position");
        $player->is_approved = $this->request->getPost("is_approved");
        

        if (!$player->save()) {

            foreach ($player->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "players",
                'action' => 'edit',
                'params' => [$player->id]
            ]);

            return;
        }

        $this->flash->success("player was updated successfully");

        $this->dispatcher->forward([
            'controller' => "players",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a player
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $player = Players::findFirstByid($id);
        if (!$player) {
            $this->flash->error("player was not found");

            $this->dispatcher->forward([
                'controller' => "players",
                'action' => 'index'
            ]);

            return;
        }

        if (!$player->delete()) {

            foreach ($player->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "players",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("player was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "players",
            'action' => "index"
        ]);
    }

}
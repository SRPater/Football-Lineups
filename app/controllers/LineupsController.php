<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class LineupsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        if ($this->request->isGet()) {
            $numberPage = $this->request->getQuery("page", "int");
        } else {
            $numberPage = 1;
        }

        $lineups = Lineups::find(["order" => "average_rating DESC"]);

        if (count($lineups) == 0) {
            $this->flash->notice("There are no lineups at this moment.");

            $this->dispatcher->forward([
                "controller" => "lineups",
                "action" => "new"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $lineups,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Searches for lineups
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $user = Users::findFirst("username = '" . $_POST["username"] . "'");
            if ($user) {
                $criteria = new Criteria();
                $query = $criteria->where("user_id = '" . $user->id . "'");
                $this->persistent->parameters = $query->getParams();
            }
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "date_added DESC";

        $lineups = Lineups::find($parameters);
        if (count($lineups) == 0) {
            $this->flash->notice("This user does not exist or has not created any lineups yet.");

            $this->dispatcher->forward([
                "controller" => "lineups",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $lineups,
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
     * Edits a lineup
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $lineup = Lineups::findFirstByid($id);
            if (!$lineup) {
                $this->flash->error("lineup was not found");

                $this->dispatcher->forward([
                    'controller' => "lineups",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $lineup->id;

            $this->tag->setDefault("id", $lineup->id);
            $this->tag->setDefault("user_id", $lineup->user_id);
            $this->tag->setDefault("average_rating", $lineup->average_rating);
            $this->tag->setDefault("date_added", $lineup->date_added);
            
        }
    }

    /**
     * Creates a new lineup
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "lineups",
                'action' => 'index'
            ]);

            return;
        }

        if (
            ($this->request->getPost("right_back")              != $this->request->getPost("right_center_back"))        &&
            ($this->request->getPost("right_back")              != $this->request->getPost("left_center_back"))         &&
            ($this->request->getPost("right_back")              != $this->request->getPost("left_back"))                &&
            ($this->request->getPost("right_center_back")       != $this->request->getPost("left_center_back"))         &&
            ($this->request->getPost("right_center_back")       != $this->request->getPost("left_back"))                &&
            ($this->request->getPost("left_center_back")        != $this->request->getPost("left_back"))                &&
            ($this->request->getPost("right_center_midfielder") != $this->request->getPost("center_midfielder"))        &&
            ($this->request->getPost("right_center_midfielder") != $this->request->getPost("left_center_midfielder"))   &&
            ($this->request->getPost("center_midfielder")       != $this->request->getPost("left_center_midfielder"))   &&
            ($this->request->getPost("right_winger")            != $this->request->getPost("striker"))                  &&
            ($this->request->getPost("right_winger")            != $this->request->getPost("left_winger"))              &&
            ($this->request->getPost("striker")                 != $this->request->getPost("left_winger"))
        ) {
            $lineup = new Lineups();
            $lineup->user_id = $this->request->getPost("user_id");
            $positions = [
                1 => "goalkeeper",
                2 => "right_back",
                3 => "right_center_back",
                4 => "left_center_back",
                5 => "left_back",
                6 => "right_center_midfielder",
                7 => "center_midfielder",
                8 => "left_center_midfielder",
                9 => "right_winger",
                10=> "striker",
                11=> "left_winger"
            ];

            if (!$lineup->save()) {
                foreach ($lineup->getMessages() as $message) {
                    $this->flash->error($message);
                }

                $this->dispatcher->forward([
                    'controller' => "lineups",
                    'action' => 'new'
                ]);

                return;
            }

            foreach ($positions as $id => $position) {
                $pos = new Positions();
                $pos->id = $id;
                $pos->player_id = $this->request->getPost($position);
                $pos->lineup_id = $lineup->id;
                $pos->save();
            }

            $this->flash->success("Lineup was created successfully.");

            $this->dispatcher->forward([
                'controller' => "lineups",
                'action' => 'index'
            ]);
        } else {
            $this->flash->error("Check your lineup for double players!");

            $this->dispatcher->forward([
                'controller' => "lineups",
                'action' => 'new'
            ]);
        }
    }

    /**
     * Saves a lineup edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "lineups",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $lineup = Lineups::findFirstByid($id);

        if (!$lineup) {
            $this->flash->error("lineup does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "lineups",
                'action' => 'index'
            ]);

            return;
        }

        $lineup->user_id = $this->request->getPost("user_id");
        $lineup->average_rating = $this->request->getPost("average_rating");
        $lineup->date_added = $this->request->getPost("date_added");
        

        if (!$lineup->save()) {

            foreach ($lineup->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "lineups",
                'action' => 'edit',
                'params' => [$lineup->id]
            ]);

            return;
        }

        $this->flash->success("lineup was updated successfully");

        $this->dispatcher->forward([
            'controller' => "lineups",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a lineup
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $lineup = Lineups::findFirstByid($id);
        if (!$lineup) {
            $this->flash->error("lineup was not found");

            $this->dispatcher->forward([
                'controller' => "lineups",
                'action' => 'index'
            ]);

            return;
        }

        if (!$lineup->delete()) {

            foreach ($lineup->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "lineups",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("lineup was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "lineups",
            'action' => "index"
        ]);
    }

}

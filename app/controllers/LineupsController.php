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

        $lineups = Lineups::find(["order" => "average_rating DESC"]);

        if (count($lineups) == 0) {
            $this->flash->notice("There are no lineups to show at this moment.");

            $this->dispatcher->forward([
                "controller" => "lineups",
                "action"     => "new"
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
        if (!$this->loggedIn()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);
        }
    }

    /**
     * Creates a new lineup
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
                "controller" => "lineups",
                "action"     => "index"
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
            $lineup->user_id = $this->session->get("id");
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
                    "controller" => "lineups",
                    "action"     => "new"
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
                "controller" => "lineups",
                "action"     => "index"
            ]);
        } else {
            $this->flash->error("Check your lineup for double players!");

            $this->dispatcher->forward([
                "controller" => "lineups",
                "action"     => "new"
            ]);
        }
    }

    /**
     * Deletes a lineup
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        if (!$this->loggedIn()) {
            $this->dispatcher->forward([
                "controller" => "index",
                "action"     => "index"
            ]);
            return;
        }

        $lineup = Lineups::findFirstByid($id);
        if (!$lineup) {
            $this->flash->error("The lineup was not found!");

            $this->dispatcher->forward([
                "controller" => "lineups",
                "action"     => "index"
            ]);

            return;
        }

        if (!$lineup->delete()) {

            foreach ($lineup->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                "controller" => "lineups",
                "action"     => "index"
            ]);

            return;
        }

        $this->flash->success("Lineup was deleted successfully.");

        $this->dispatcher->forward([
            "controller" => "lineups",
            "action"     => "index"
        ]);
    }

    public function rateAction()
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
                "controller" => "lineups",
                "action"     => "index"
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $lineup = Lineups::findFirstByid($id);

        if (!$lineup) {
            $this->flash->error("The lineup was not found!");

            $this->dispatcher->forward([
                "controller" => "lineups",
                "action"     => "index"
            ]);

            return;
        }

        $rating = new Ratings();
        $rating->lineup_id = $id;
        $rating->rating = $this->request->getPost("ratingField");
        $rating->user_id = $this->session->get("id");

        if (!$rating->save()) {
            foreach($rating->getMessages() as $message) {
                $this->flash->error($message);
            }
        } else {
            $ratings = Ratings::find("lineup_id = '" . $id . "'");
            $totalRating = 0;

            foreach($ratings as $rate) {
                $totalRating += $rate->rating;
            }

            $lineup->average_rating = $totalRating / $ratings->count();

            if (!$lineup->save()) {
                foreach($lineup->getMessages() as $message) {
                    $this->flash->error($message);
                }
            }

            $this->flash->success("Your rating has been saved!");
        }

        $this->dispatcher->forward([
            "controller"=> "lineups",
            "action"    => "index",
            "params"    => ["page" => $this->request->getPost("page")]
        ]);
    }

}

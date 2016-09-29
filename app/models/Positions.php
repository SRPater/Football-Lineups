<?php

class Positions extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=4, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=4, nullable=false)
     */
    public $lineup_id;

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=4, nullable=false)
     */
    public $player_id;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('lineup_id', 'Lineups', 'id', ['alias' => 'Lineups']);
        $this->belongsTo('player_id', 'Players', 'id', ['alias' => 'Players']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'positions';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Positions[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Positions
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}

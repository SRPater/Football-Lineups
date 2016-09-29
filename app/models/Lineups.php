<?php

class Lineups extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=4, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $user_id;

    /**
     *
     * @var double
     * @Column(type="double", length=10, nullable=true)
     */
    public $average_rating;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $date_added;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'Positions', 'lineup_id', ['alias' => 'Positions']);
        $this->hasMany('id', 'Ratings', 'lineup_id', ['alias' => 'Ratings']);
        $this->belongsTo('user_id', 'Users', 'id', ['alias' => 'Users']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'lineups';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Lineups[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Lineups
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}

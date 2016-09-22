<?php

use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation;

class Players extends \Phalcon\Mvc\Model
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
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $first_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $last_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $nationality;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $club;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $position;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $is_approved;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();
        $validator->add(
            ["first_name", "last_name", "nationality"],
            new Uniqueness(
                [
                    "field"     => ["first_name", "last_name", "nationality"],
                    "message"   => "This player has already been added."
                ]
            )
        );
        $validator->add(
            "first_name",
            new PresenceOf(
                [
                    "field"     => "first_name",
                    "message"   => "Enter a first name."
                ]
            )
        );
        $validator->add(
            "last_name",
            new PresenceOf(
                [
                    "field"     => "last_name",
                    "message"   => "Enter a last name."
                ]
            )
        );
        $validator->add(
            "nationality",
            new PresenceOf(
                [
                    "field"     => "nationality",
                    "message"   => "Enter a nationality."
                ]
            )
        );
        $validator->add(
            "club",
            new PresenceOf(
                [
                    "field"     => "club",
                    "message"   => "Enter a club."
                ]
            )
        );

        return($this->validate($validator));
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'Positions', 'player_id', ['alias' => 'Positions']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'players';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Players[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Players
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}

<?php

use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation;

class Users extends \Phalcon\Mvc\Model
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
     * @Column(type="string", length=20, nullable=false)
     */
    public $username;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     */
    public $password;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $first_name;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     */
    public $last_name;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     */
    public $email;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $is_admin;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();
        $validator->add(
            "username",
            new Uniqueness(
                [
                    "field" => "username",
                    "message" => "This username is already in use."
                ]
            )
        );
        $validator->add(
            "username",
            new PresenceOf(
                [
                    "field" => "username",
                    "message" => "Enter a username."
                ]
            )
        );
        $validator->add(
            "password",
            new PresenceOf(
                [
                    "field" => "password",
                    "message" => "Enter a password."
                ]
            )
        );
        $validator->add(
            "first_name",
            new PresenceOf(
                [
                    "field" => "first_name",
                    "message" => "Enter your first name."
                ]
            )
        );
        $validator->add(
            "last_name",
            new PresenceOf(
                [
                    "field" => "last_name",
                    "message" => "Enter your last name."
                ]
            )
        );
        $validator->add(
            "email",
            new PresenceOf(
                [
                    "field" => "email",
                    "message" => "Enter your e-mail address."
                ]
            )
        );

        return($this->validate($validator));

//        if ($this->validationHasFailed() == true) {
//            return false;
//        }
//
//        return true;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setup(
            array('notNullValidations'=>false)
        );

        $this->hasMany('id', 'Lineups', 'user_id', ['alias' => 'Lineups']);
        $this->hasMany('id', 'Ratings', 'user_id', ['alias' => 'Ratings']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'users';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}

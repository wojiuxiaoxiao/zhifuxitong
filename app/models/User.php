<?php

class User extends Eloquent {

    protected $table = 'xyk_users';
    public $timestamps = false;

    public function inviter($user_id, $invite_code = null) {
    	if (!$invite_code) {
    		return false;
    	}

    	$first = self::where("invite", $invite_code)
    		->find();
    	if ($first) {
    		$first_id = $first->id;
			$second_id = Inviter::where("user_id", $first_id);
        }
    	
    }

}
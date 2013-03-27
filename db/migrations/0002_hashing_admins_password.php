<?php
class HashingAdminsPassword extends Atk14Migration{
	function up(){
		$admin = User::GetInstanceById(1);
		$admin->s("password",$admin->g("password"));
	}
}

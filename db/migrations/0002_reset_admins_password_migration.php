<?php
/**
 * This migration sets the default password to user admin
 *
 * The default password is admin.
 *
 * If you forgot admin password, you can set the default password by calling:
 * 
 * $ ./scripts/migrate -f 0002_reset_admins_password_migration.php
 */
class ResetAdminsPasswordMigration extends ApplicationMigration{
	function up(){
		$admin = User::GetInstanceById(1);
		$admin->s("password","admin"); // a transparent password hashing is involved
	}
}

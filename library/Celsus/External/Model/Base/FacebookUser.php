<?php

class Celsus_External_Model_Base_FacebookUser extends Celsus_Model_Base_Facebook {

	protected $_name = 'facebookUser';

	protected $_fields = array(
		"name",
		"first_name",
		"middle_name",
		"last_name",
		"email",
		"username",
		"gender",
		"locale",
		"updated_time",
		"hometown"
	);
}
<?php
class Gravatar
{
	__construct($email)
	{
		return 'http://www.gravatar.com/avatar/'.md5(strtolower(trim($email)));
	}
	
}
<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Help extends ORM
{

    public function deleteClear()
	{    
        // Common::deleteContentImage($this->body);
		 $this->delete();
	}
}
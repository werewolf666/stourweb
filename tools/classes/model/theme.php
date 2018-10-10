<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Theme extends ORM {

    /**
     * @function 删除主题
     * @throws Kohana_Exception
     */
	 public function deleteClear()
	 {
		 $this->delete();
	 }
}
<?php
/**
 * ZipFolder, PHP5 zip folder class
 * 
 * This class allows for an OOP approach to reading zip folders. This class 
 * was written for use on Microsoft Windows based machine using the ZZIPlib 
 * library.
 * 
 * @author Robert Guerin <robert@nuwebtech.com>
 * @version 1.0
 * @package ZipFolder
 */

class ZipFolder {
	
	private $loadPath;
	private $savePath;
	private $file;
	private $folder;

	 /**
     * Sets the path to the zip file being read.
     *
     * $loadPath must be the complete path to the file being read, 
     * including a trailing "\".  This is the path only the filename 
     * must be set through the setFile() method.
     *
     * @see setFile()
     * @param  string        $loadPath
     * @return void
     */
	public function setLoadPath($loadPath) {
		$this->loadPath = $loadPath;
	}
	
	 /**
     * Returns the path to the zip file being read.
     *
     * If the path is not set through the setLoadPath() method the 
     * path defaults to the directory the script is called from.
     * 
     * @see setLoadPath()
     * @return string
     */
	private function getLoadPath() {
		if ($this->loadPath == null) {
			$path = explode("/", $_SERVER['SCRIPT_FILENAME']);
			array_pop($path);
			$this->loadPath = implode("/", $path)."/";
		}
		return $this->loadPath;
	}

	 /**
     * Sets the path to the where the zip folder contents will be saved.
     *
     * $savePath must be the complete path to the file being read, 
     * including a trailing "\".  This is the path only the folder 
     * where the contents are saved has the same name as the zip folder
     * unless specified through the setFolder() method.
     *
     * @see setFolder()
     * @param  string        $savePath
     * @return void
     */
	public Function setSavePath($savePath) {
		$this->savePath = $savePath;
	}
	
	 /**
     * Returns the path to the where the zip file contents will be saved.
     *
     * If the path is not set through the setSavePath() method the 
     * path defaults to the directory the script is called from.
     * 
     * @see setSavePath
     * @return string
     */	
 	private function getSavePath() {
		if ($this->savePath == null) {
			$this->savePath = $this->getLoadPath();
		}
		return $this->savePath;
	}

	 /**
     * Sets the name of the zip file.
     *
     * $file is the name of the zip file to be read.  The path to the
     * file must be set through the setLoadPath() method
     *
     * @see setLoadPath()
     * @param  string        $file
     * @return void
     */	
	public function setFile($file) {
		$this->file = $file;
		$path = $this->getLoadPath().$this->file;
		$file_array = pathinfo($path);
		if($file_array['extension'] == 'zip') {
			$this->folder = substr($file, 0, -(strlen($file_array['extension'])+1));
		} else {
			throw new Exception('The file must be a Zip folder');
		}
	}

	/**
     * Returns the name of the zip file.
     * 
     * @return string
     */
	private function getFile() {
		return $this->file;
	}

	/**
     * Sets the name of the folder where the zip file contents
     * will be written.
     *
     * $folder is the name of the folder where the zip file contents 
     * will be be written. If the name is not specified, the name of the 
     * folder will be the same as the zip file 
     *
     * @param  string        $folder
     * @return void
     */
	public function setFolder($folder) {
		$this->folder = $folder;
	}

	/**
     * Returns the name of the folder where the zip file contents
     * will be written.
     * 
     * If the name is not specified through the setFolder() method the 
     * name of the folder will be the same as the zip file 
     * 
     * @see setFolder()
     * @return string
     */
	private function getFolder() {
		return $this->folder;
	}

	/**
     * Recursive method to delete directories
     * 
     * $directory is the path and name of the folder to delete. Such as
     * "C:/This/Is/A/Test/" where "Test" is the folder to be deleted.
     * This is a recursive function to delete directories and all 
     * sub-directories and files. 
     * 
     * @param  string        $directory
     * @return void
     */
	private function removeDirectory($directory) {
       $directory_contents = scandir($directory);
       foreach ($directory_contents as $item) {
           if (is_dir($directory.$item) && $item != '.' && $item != '..') {
               $this->removeDirectory($directory.$item.'/');
           }
           elseif (file_exists($directory.$item) && $item != '.' && $item != '..') {
               unlink($directory.$item);
           }
       }
       rmdir($directory);
   } 

	/**
     * Recursive method to create directories
     * 
     * $directory is the path and name of the folder to create. Such as
     * "C:/This/Is/A/Test/" where "Test" is the folder to be created.
     * This is a recursive function to create directories and all 
     * sub-directories and files. 
     * 
     * @param  string        $directory
     * @return void
     */
	private function makeDirectories($directory, $recursive = true) {
	  	if( is_null($directory) || $directory === "" ){
	   		return false;
	  	}
		if( is_dir($directory) || $directory === "c:/" ){
			return true;
		}
		if( $this->makeDirectories(dirname($directory), $recursive) ){
	 		return mkdir($directory);
		}
		return true;
	} 

	/**
     * This method actually uncompresses the zip folder and
     * saves its contents to the specefied directory.
     * 
     * This method actually uncompresses the zip folder and
     * saves its contents to the specefied directory.  The 
     * path and file where the contents are saved is set 
     * through the setSavePath() and setFolder() methods.
     * 
     * @see setSavePath()
     * @see setFolder()
     * @return boolean
     */
	public function openZip() {
		if ($zip = zip_open($this->getLoadPath().$this->getFile())) {
			if ($zip) {
				if (is_dir($this->getSavePath().$this->getFolder()."/")) {
					$this->removeDirectory($this->getSavePath().$this->getFolder()."/");
				} 
				$this->makeDirectories($this->getSavePath().$this->getFolder());
				while ($zip_entry = zip_read($zip)) {


					if (zip_entry_open($zip,$zip_entry,"r")) {
						$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                        if(empty($buf))
                        {
                            $d = $this->getSavePath().$this->getFolder()."/".zip_entry_name($zip_entry);
                            if(!file_exists($d))
                            {
                                mkdir($d);
                            }
                        }
						$directory_name = dirname(zip_entry_name($zip_entry));

						if ($directory_name != ".") {
							$dir_op = $this->getSavePath().$this->getFolder()."/";
							foreach ( explode("/",$directory_name) as $k) {
								$dir_op = $dir_op . $k;
                				if (is_file($dir_op)) unlink($dir_op);
                 				if (!is_dir($dir_op)) mkdir($dir_op);
                 				$dir_op = $dir_op . "/" ;
                 			}
						}


				$fp=fopen($this->getSavePath().$this->getFolder()."/".zip_entry_name($zip_entry),"w");
				fwrite($fp,$buf);
           		fclose($fp);
				zip_entry_close($zip_entry);
       			} else
           			return false;
       			}
       			zip_close($zip);
			}
		} else
    return false;
  	return true;
	}

	/**
     * This method deletes the zip file
     * 
     * This method deletes the zip file.  If called it must
     * be called after the openZip() method. 
     * 
     * @see openZip()
     * @return boolean
     */
	public function eraseZip() {
		unlink($this->getLoadPath().$this->getFile());
		return true;
	}
}
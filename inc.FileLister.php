<?php
/////////////////////////////////////////////////
//  programming by: Milos Veljkovic            //
// 	date: 06/14/2010                           //
//  version: 1.0.7                             //
//  compatible with PHP 5.x                    //
/**
 * FileLister
 *
 * class description: This class is able to list folders and files when pointed to the RootFolder. Based on the request it will be able to return any number of data pairs
 * Usage
 * 
 * @author Milos Veljkovic
 */
/////////class ///////////////////
class FileLister {
	//variables
	var $RootFolder; //set by the function call
	var $FolderListArr; //list of folders
	var $FolderList; //list of folders and names in a multidimensional array
	var $EXTENSIONS = array("jpg", "jpeg", "png", "gif");//default file array can be moded to show something else
	//var $CurrentPage = $this->getPage($_SERVER['PHP_SELF']);
	//function to list all the files in the folder
	public function getFolderList($getSub=false)
	{
		$this->FolderListArr = scandir($this->RootFolder, 1);
		//look for all the folders
		for ($i=0;$i<count($this->FolderListArr);$i++)
		{
			//loop through and avoid all the items that are not Folders
			if (($this->FolderListArr[$i] == ".") || ($this->FolderListArr[$i] == "..") || !is_dir($this->RootFolder . "/" .$this->FolderListArr[$i]))
			{
				//remove this item from the array
				$this->FolderListArr[$i]=null;
			}
		}
		//call sort function
		return $this->sortArray($getSub);
	}
	//function to check if it can pass variables inside of the class
	private function sortArray($getSub)
	{
		if (arsort($this->FolderListArr, SORT_NUMERIC))
		{
			reset($this->FolderListArr);
			$i=0;
			while (list($key, $val) = each($this->FolderListArr))
			{
				if($val)
				{
					//look for titile file if there is on to set the name of the folder/link
					if (is_file($this->RootFolder . "/" . $this->FolderListArr[$key] . "/title.txt"))
					{
						$FolderName = file_get_contents($this->RootFolder . "/" . $this->FolderListArr[$key] . "/title.txt");
						$this->FolderList[$i]['name'] = $FolderName;
					}else
					{
						$this->FolderList[$i]['name'] = $this->FolderListArr[$key];
					}
					//add the rest of the items in the array
					$this->FolderList[$i]['link'] = $this->FolderListArr[$key];
					$this->FolderList[$i]['elink'] = '?f=' . $this->FolderListArr[$key];
					//if there is a switch to display content for each folder do it
					//default false
					if ($getSub){
						$this->FolderList[$i]['content'] = $this->getContent($this->FolderListArr[$key]);
					}
					//increase a counter
					$i++;
				}
			}
		}
		//print_r($this->FolderList);
		return $this->FolderList;
	}
	public function getContent($Folder)
	{
		//allowed extensions for the file
		//$this->EXTENSIONS = array("jpg", "jpeg", "png", "gif");
		$i = 0;
		//list all the files inside the folder
		$FilesArray = scandir($this->RootFolder . "/" . $Folder, 0);
		while(list($key, $val) = each($FilesArray))
		{
			if ($val != "." && $val != "..")
			{
				//get the extension
				$tempExt = explode(".", $val);
				reset($this->EXTENSIONS);
				while(list($extKey, $extVal) = each($this->EXTENSIONS))
				{
					//check the extension if it comares
					if (!strcasecmp($extVal, $tempExt[count($tempExt)-1]))
					{
						//here is the file
						$ContentArray[$i]['name'] = $val;
						$ContentArray[$i]['link'] = $Folder . '/' . $val;
						$ContentArray[$i]['elink'] = '?f=' . $Folder . '&c=' . $val;
						$i++;
					}
				}
			}
		}
		
		return $ContentArray;
	}
	//public functions for creating lists
	public function makeLinks($FileArray, $class='')
	{
		//declare some constants
		$LINK_PARTS[0] = "<a href=\"";
		$LINK_PARTS[1] = "\" ";
		$LINK_PARTS[2] = "class=\"$class\"";
		$LINK_PARTS[3] = ">";
		$LINK_PARTS[4] = "</a>\n";
		//put the link together
		$collect_string = $LINK_PARTS[0] . $FileArray['elink'] . $LINK_PARTS[1] . $LINK_PARTS[2] . $LINK_PARTS[3] . $FileArray['name'] . $LINK_PARTS[4];
		//return value
		return $collect_string;
	}
	//strips the page
	public function getPage($URL)
	{
		$temp = explode("/", $URL);
		return $temp[(count($temp)-1)];
	}
	//setter for the folder value
	public function setFolder($FolderString)
	{
		$FolderString = trim($FolderString);
		$FolderString = str_replace("../", "", $FolderString);
		$this->RootFolder = str_replace("/", "", $FolderString);
		if (is_dir($this->RootFolder))
		{
			//folder found and everything is ok
			//echo ($this->RootFolder);
			return true;
		}else
		{
			//folder not found
			//echo ($this->RootFolder);
			return false;
			
		}
	}
	
}

////////end class/////////////////
//set the values
?>
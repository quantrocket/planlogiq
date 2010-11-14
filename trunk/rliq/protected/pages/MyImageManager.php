<?php
//example custom image manager class
class MyImageManager extends TPage
{
	/**
	 * File permissions, set $param->IsValid = false to deny access.
	 *
	 * @param ImageManager manager instance
	 * @param ImageAssetManagerEventParameter file access details.
	 */
	function check_files($sender, $param)
	{
		//e.g., ignore .svn files
		if(is_int(strpos($param->getFile()->getPathName(), ".svn")))
			$param->IsValid = false;
	}

	/**
	 * Event handler for create new folder request. Create the requested directory,
	 * using mkdir and sets the directory permission to 0777.
	 *
	 * @param ImageManager manager instance.
	 * @param IMCreateFolderEventParameter create new folder event parameter.
	 */
	function create_new_folder($sender,$param)
	{
		//only allow alphanum and underscores
		if(preg_match('/[a-zA-Z0-9_]+/', $param->Directory))
		{
			mkdir($param->Directory);
			chmod($param->Directory,0777);
		}
	}

	/**
	 * Save the uploaded file to the image asset directory. Further checks
	 * and logging logic can be imlemented here.
	 *
	 * @param ImageManager manager instance.
	 * @param IMFileUploadEventParameter event parameter
	 */
	function upload($sender, $param)
	{
		$ext = strtolower(substr($param->FileName, -3)); //use last 3 characters of the file name as extension
		if(in_array($ext, array('gif', 'jpg', 'bmp', 'png')))//only accept common image files.
		{
			if(is_array(@getimagesize($param->LocalName))) //use getImageSize to check for image file
			{
				$param->saveAs($param->Directory.$param->FileName);
			}
		}
	}

	/**
	 * Delete the image file and the thumbnail file.
	 *
	 * @param ImageManager manager instance.
	 * @param IMDeleteFileEventParameter delete event parameter.
	 */
	function delete_file($sender, $param)
	{
		if($param->isFile)
		{
			unlink($param->FilePath);
			unlink($param->Thumbnail);
		}
		else
			rmdir($param->FilePath);
	}
}

?>
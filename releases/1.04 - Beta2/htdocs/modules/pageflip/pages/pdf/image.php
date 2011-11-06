<?php
	ob_start();
	require_once('../../header.php');
	$GLOBALS['xoopsLogger']->activated = false;
	
	$bid = (isset($_GET['bid'])?intval($_GET['bid']):0);
	$pid = (isset($_GET['pid'])?intval($_GET['pid']):0);
	$page = (isset($_GET['page'])?intval($_GET['page']):0);
	$format = (isset($_GET['format'])?$_GET['format']:'jpg');
	
	$books_handler = xoops_getmodulehandler('books', 'pageflip');
	$pages_handler = xoops_getmodulehandler('pages', 'pageflip');

	$book = $pageflip_handler->get($bid);
	$pages = $pages_handler->get($pid);
	
	switch($pages->getVar('location')) {
		case '_MI_PAGEFLIP_PATH_XOOPS_UPLOAD_PATH':
			$path = XOOPS_UPLOAD_PATH;
			break;
		case '_MI_PAGEFLIP_PATH_XOOPS_VAR_PATH':
			$path = XOOPS_VAR_PATH;
			break;
		case '_MI_PAGEFLIP_PATH_OTHER':
			$path = '';
			break;
	}
	
	$path .= (substr($pages->getVar('path'), 0, 1)!=DIRECTORY_SEPARATOR?DIRECTORY_SEPARATOR:'').$pages->getVar('path');
	$path .= (substr($path, strlen($path)-1, 1)!=DIRECTORY_SEPARATOR?DIRECTORY_SEPARATOR:'');
	$image = $path.$pages->getVar('filename');
	$image = str_replace(array('/','\\'), DIRECTORY_SEPARATOR, $image);
	if (file_exists($image)) {
		switch($pages->getVar('filetype')) {
			default:
			case '_MI_PAGEFLIP_FILETYPE_JPG':
			case '_MI_PAGEFLIP_FILETYPE_PNG':
			case '_MI_PAGEFLIP_FILETYPE_GIF':
				require_once($GLOBALS['xoops']->path(_MI_PAGEFLIP_FRAMEWORK_WIDEIMAGE));
			
				switch ($pages->getVar('mode')) {
					case '_MI_PAGEFLIP_MODE_LANDSCAPE':
						$ratio_width = $pages->getVar('height') / $GLOBALS['xoopsModuleConfig']['print_width'];
						$ratio_height = $pages->getVar('width') / $GLOBALS['xoopsModuleConfig']['print_height']; 
						
						if ($ratio_width>$ratio_height)
							$scale = $ratio_height/$ratio_width;
						else 
							$scale = $ratio_width/$ratio_height;
						
						if (file_exists($image)) {
							$img = WideImage::load($image);
							
							if ($GLOBALS['xoopsModuleConfig']['scale_images']&&$GLOBALS['xoopsModuleConfig']['auto_crop_images']) {
								$newImage = $img->resize($GLOBALS['xoopsModuleConfig']['print_width']*$scale, $GLOBALS['xoopsModuleConfig']['print_height']*$scale)->crop("center", "middle", $GLOBALS['xoopsModuleConfig']['print_width'], $GLOBALS['xoopsModuleConfig']['print_height'])->rotate(90);
								ob_end_clean();
								$newImage->output($format, 99);
								exit(0);
							} elseif ($GLOBALS['xoopsModuleConfig']['scale_images']&&!$GLOBALS['xoopsModuleConfig']['auto_crop_images']) { 
								$newImage = $img->resize($GLOBALS['xoopsModuleConfig']['print_width'], $GLOBALS['xoopsModuleConfig']['print_height'])->rotate(90);
								ob_end_clean();
								$newImage->output($format, 99);
								exit(0);
							} else {
								$newImage = $img->rotate(90);
								ob_end_clean();
								$newImage->output($format, 99);
								exit(0);
							}
						}
						break;
					case '_MI_PAGEFLIP_MODE_PORTTRAIT':
						$ratio_width = $pages->getVar('width') / $GLOBALS['xoopsModuleConfig']['print_width'];
						$ratio_height = $pages->getVar('height') / $GLOBALS['xoopsModuleConfig']['print_height']; 
						
						if ($ratio_width>$ratio_height)
							$scale = $ratio_height/$ratio_width;
						else 
							$scale = $ratio_width/$ratio_height;
						
						if (file_exists($image)) {
							$img = WideImage::load($image);
							
							if ($GLOBALS['xoopsModuleConfig']['scale_images']&&$GLOBALS['xoopsModuleConfig']['auto_crop_images']) {
								$newImage = $img->resize($GLOBALS['xoopsModuleConfig']['print_width']*$scale, $GLOBALS['xoopsModuleConfig']['print_height']*$scale)->crop("center", "middle", $GLOBALS['xoopsModuleConfig']['print_width'], $GLOBALS['xoopsModuleConfig']['print_height']);
								ob_end_clean();
								$newImage->output($format, 99);
								exit(0);
							} elseif ($GLOBALS['xoopsModuleConfig']['scale_images']&&!$GLOBALS['xoopsModuleConfig']['auto_crop_images']) { 
								$newImage = $img->resize($GLOBALS['xoopsModuleConfig']['print_width'], $GLOBALS['xoopsModuleConfig']['print_height']);
								ob_end_clean();
								$newImage->output($format, 99);
								exit(0);
							} else {
								ob_end_clean();
								$img->output($format, 99);
								exit(0);
							}
						}
						break;		
				}
				break;					
			case '_MI_PAGEFLIP_FILETYPE_SWF':
				ob_end_clean();
				exit(0);
		}
	}
?>
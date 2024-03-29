<?php

/* PH File Manager
 *
 * @copyright (C) 2016 PrimeHover (Gustavo Fernandes)
 * @desc This extension enhances the current OpenCart file manager and adds a crop section to edit images.
 * @version 1.0.0
 *
 * To view the full documentation for this extension, visit:
 * http://primehover.gufernandes.com.br/ph-file-manager
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see [http://www.gnu.org/licenses/].
 */

class ControllerCommonFilemanager extends Controller {

	public function index() {

		$this->load->language('common/ph_filemanager');

		$data['heading_title'] 					= $this->language->get('heading_title');

		$data['text_confirm'] 					= $this->language->get('text_confirm');
		$data['text_details'] 					= $this->language->get('text_details');
		$data['text_details_none'] 				= $this->language->get('text_details_none');
		$data['text_library'] 					= $this->language->get('text_library_folder');
		$data['text_help']	 					= $this->language->get('text_help');

		$data['entry_search'] 					= $this->language->get('entry_search');
		$data['entry_image_editor'] 			= $this->language->get('entry_image_editor');
		$data['entry_image_editor_desc_1'] 		= str_replace('%s', '<button type="button" data-toggle="tooltip" title="' . $this->language->get('button_crop') . '" class="btn btn-primary"><i class="fa fa-crop"></i></button>', $this->language->get('entry_image_editor_desc_1'));
		$data['entry_image_editor_desc_2'] 		= str_replace('%s', '<label for="inputImageEditor" class="btn btn-primary" data-toggle="tooltip" title="' . $this->language->get('button_upload') . '"><i class="fa fa-upload"></i><input type="file" class="sr-only" id="inputImageEditor" name="fileEditor" accept="image/*"></label>', $this->language->get('entry_image_editor_desc_2'));

		$data['tab_gallery'] 					= $this->language->get('tab_gallery');
		$data['tab_editor'] 					= $this->language->get('tab_editor');

		$data['status_loading'] 				= $this->language->get('status_loading');
		$data['status_error'] 					= $this->language->get('status_error');
		$data['status_success'] 				= $this->language->get('status_success');

		$data['button_refresh'] 				= $this->language->get('button_refresh');
		$data['button_folder'] 					= $this->language->get('button_folder');
		$data['button_delete']					= $this->language->get('button_delete');
		$data['button_search']					= $this->language->get('button_search');
		$data['button_crop'] 					= $this->language->get('button_crop');
		$data['button_upload'] 					= $this->language->get('button_upload');
		$data['button_remote_upload'] 			= $this->language->get('button_remote_upload');
		$data['button_cancel_selection'] 		= $this->language->get('button_cancel_selection');

		$data['editor_drag'] 					= $this->language->get('editor_drag');
		$data['editor_crop'] 					= $this->language->get('editor_crop');
		$data['editor_flip_horizontal'] 		= $this->language->get('editor_flip_horizontal');
		$data['editor_flip_vertical'] 			= $this->language->get('editor_flip_vertical');
		$data['editor_rotate_left'] 			= $this->language->get('editor_rotate_left');
		$data['editor_rotate_right'] 			= $this->language->get('editor_rotate_right');
		$data['editor_zoom_in'] 				= $this->language->get('editor_zoom_in');
		$data['editor_zoom_out'] 				= $this->language->get('editor_zoom_out');
		$data['editor_save_crop'] 				= $this->language->get('editor_save_crop');
		$data['editor_cancel_crop'] 			= $this->language->get('editor_cancel_crop');
		$data['editor_reset_crop'] 				= $this->language->get('editor_reset_crop');
		$data['editor_upload_new_image'] 		= $this->language->get('editor_upload_new_image');

		$data['helpers'] = [];
		$data['helpers']['selecting_singular'] 		  = $this->language->get('helper_selecting_singular');
		$data['helpers']['selecting_plural']    	  = $this->language->get('helper_selecting_plural');
		$data['helpers']['uploading_plural']   		  = $this->language->get('helper_uploading_plural');
		$data['helpers']['uploading_singular']  	  = $this->language->get('helper_uploading_singular');
		$data['helpers']['uploaded_singular']  		  = $this->language->get('helper_uploaded_singular');
		$data['helpers']['uploaded_plural'] 		  = $this->language->get('helper_uploaded_plural');
		$data['helpers']['creating_folder'] 		  = $this->language->get('helper_creating_folder');
		$data['helpers']['renaming'] 		  		  = $this->language->get('helper_renaming');
		$data['helpers']['moving'] 		  			  = $this->language->get('helper_moving');
		$data['helpers']['deleting'] 		  	      = $this->language->get('helper_deleting');
		$data['helpers']['details_name'] 		 	  = $this->language->get('details_name');
		$data['helpers']['details_size'] 			  = $this->language->get('details_size');
		$data['helpers']['details_location'] 	 	  = $this->language->get('details_location');
		$data['helpers']['details_type'] 		      = $this->language->get('details_type');
		$data['helpers']['button_select'] 			  = $this->language->get('button_select');
		$data['helpers']['button_delete'] 			  = $this->language->get('button_delete');
		$data['helpers']['button_crop'] 			  = $this->language->get('button_crop');
		$data['helpers']['button_rename'] 			  = $this->language->get('button_rename');
		$data['helpers']['button_cancel'] 			  = $this->language->get('button_cancel');
		$data['helpers']['button_ok']    			  = $this->language->get('button_ok');
		$data['helpers']['remote_upload_title'] 	  = $this->language->get('remote_upload_title');
		$data['helpers']['remote_upload_placeholder'] = $this->language->get('remote_upload_placeholder');
		$data['helpers']['remote_upload_desc'] 		  = $this->language->get('remote_upload_desc');
		$data['helpers']['delete_title'] 		  	  = $this->language->get('delete_title');
		$data['helpers']['delete_desc_singular'] 	  = $this->language->get('delete_desc_singular');
		$data['helpers']['delete_desc_plural'] 		  = $this->language->get('delete_desc_plural');
		$data['helpers']['folder_title'] 		  	  = $this->language->get('folder_title');
		$data['helpers']['folder_desc'] 		  	  = $this->language->get('folder_desc');
		$data['helpers']['folder_placeholder'] 		  = $this->language->get('folder_placeholder');
		$data['helpers']['rename_title'] 		  	  = $this->language->get('rename_title');
		$data['helpers']['rename_desc'] 		  	  = $this->language->get('rename_desc');
		$data['helpers']['rename_placeholder'] 		  = $this->language->get('rename_placeholder');
		$data['helpers']['savecrop_title'] 		  	  = $this->language->get('savecrop_title');
		$data['helpers']['savecrop_desc'] 		  	  = $this->language->get('savecrop_desc');
		$data['helpers']['savecrop_placeholder'] 	  = $this->language->get('savecrop_placeholder');
		$data['helpers']['text_library_folder'] 	  = $this->language->get('text_library_folder');
		$data['helpers']['text_cropped'] 			  = $this->language->get('text_cropped');
		$data['helpers']['text_full_doc'] 			  = $this->language->get('text_full_doc');
		$data['helpers']['text_help_desc'] 			  = $this->language->get('text_help_desc');
		$data['helpers']['editor_width'] 			  = $this->language->get('editor_width');
		$data['helpers']['editor_height'] 			  = $this->language->get('editor_height');
		$data['helpers']['error_url'] 		    	  = $this->language->get('error_url');
		$data['helpers']['error_filetype'] 		      = $this->language->get('error_filetype');
		$data['helpers']['error_folder'] 		      = $this->language->get('error_folder');
		$data['helpers']['error_rename'] 		      = $this->language->get('error_rename');
		$data['helpers']['error_upload_blob'] 		  = $this->language->get('error_upload_blob');
		$data['helpers']['error_crop'] 		  		  = $this->language->get('error_crop');

		$data['token'] = $this->session->data['token'];

		// Return the target ID for the file manager to set the value
		if (isset($this->request->get['target'])) {
			$data['target'] = $this->request->get['target'];
		} else {
			$data['target'] = '';
		}

		// Return the thumbnail for the file manager to show a thumbnail
		if (isset($this->request->get['thumb'])) {
			$data['thumb'] = $this->request->get['thumb'];
		} else {
			$data['thumb'] = '';
		}

		$this->response->setOutput($this->load->view('common/ph_filemanager', $data));
	}

	public function load() {

		if (isset($this->request->post['searchTerm'])) {
			$filter_name = rtrim(str_replace(array('../', '..\\', '..', '*'), '', $this->request->post['searchTerm']), '/');
		} else {
			$filter_name = null;
		}

		// Make sure we have the correct directory
		if (isset($this->request->post['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', $this->request->post['directory']), '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		if (isset($this->request->post['page'])) {
			$page = $this->request->post['page'];
		} else {
			$page = 1;
		}

		$data['images'] = array();

		$this->load->model('tool/image');

		// Get directories
		$directories = glob($directory . '/' . $filter_name . '*', GLOB_ONLYDIR);

		if (!$directories) {
			$directories = array();
		}

		// Get files
		$files = glob($directory . '/' . $filter_name . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);

		if (!$files) {
			$files = array();
		}

		// Merge directories and files
		$images = array_merge($directories, $files);

		// Get total number of files and directories
		$image_total = count($images);

		// Split the array based on current page number and max number of items per page of 10
		$images = array_splice($images, ($page - 1) * 15, 15);

		foreach ($images as $image) {
			$name = str_split(basename($image), 14);

			$fullname = implode('', $name);

			if (is_dir($image)) {
				$data['images'][] = array(
					'thumb' => '',
					'name'  => $fullname,
					'type'  => 'directory',
					'size'  => $this->getDirectoryFilesize(DIR_IMAGE . utf8_substr($image, utf8_strlen(DIR_IMAGE))),
					'path'  => utf8_substr($image, utf8_strlen(DIR_IMAGE))
				);
			} elseif (is_file($image)) {
				// Find which protocol to use to pass the full image link back
				if ($this->request->server['HTTPS']) {
					$server = HTTPS_CATALOG;
				} else {
					$server = HTTP_CATALOG;
				}

				$data['images'][] = array(
					'thumb' => $this->model_tool_image->resize(utf8_substr($image, utf8_strlen(DIR_IMAGE)), 100, 100),
					'name'  => $fullname,
					'type'  => 'image',
					'size'  => filesize(DIR_IMAGE . utf8_substr($image, utf8_strlen(DIR_IMAGE))),
					'path'  => utf8_substr($image, utf8_strlen(DIR_IMAGE)),
					'href'  => $server . 'image/' . utf8_substr($image, utf8_strlen(DIR_IMAGE))
				);
			}
		}

		if (isset($this->request->post['directory'])) {
			$data['directory'] = urlencode($this->request->post['directory']);
		} else {
			$data['directory'] = '';
		}

		if (isset($this->request->post['filter_name'])) {
			$data['filter_name'] = $this->request->post['filter_name'];
		} else {
			$data['filter_name'] = '';
		}

		// Return the target ID for the file manager to set the value
		if (isset($this->request->post['target'])) {
			$data['target'] = $this->request->post['target'];
		} else {
			$data['target'] = '';
		}

		// Return the thumbnail for the file manager to show a thumbnail
		if (isset($this->request->post['thumb'])) {
			$data['thumb'] = $this->request->post['thumb'];
		} else {
			$data['thumb'] = '';
		}

		// Parent
		$parent = '';
		if (isset($this->request->post['directory'])) {
			$pos = strrpos($this->request->post['directory'], '/');
			if ($pos) {
				$parent = urlencode(substr($this->request->post['directory'], 0, $pos));
			}
		}

		$data['parent'] = $parent;

		$pagination = new Pagination();
		$pagination->total = $image_total;
		$pagination->page = $page;
		$pagination->limit = 15;
		$pagination->url = '#{page}';

		$data['pagination'] = $pagination->render();

		echo json_encode($data);

	}

	public function upload() {

		$this->load->language('common/ph_filemanager');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Make sure we have the correct directory
		if (isset($this->request->get['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', $this->request->get['directory']), '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		// Check its a directory
		if (!is_dir($directory)) {
			$json['error'] = $this->language->get('error_directory');
		}

		if (!$json && isset($this->request->files['file']))  {

			$count = count($this->request->files['file']['name']);

			for ($i = 0; $i < $count; $i++) {
				if (!empty($this->request->files['file']['name'][$i]) && is_file($this->request->files['file']['tmp_name'][$i])) {

					// Sanitize the filename
					$filename = basename(html_entity_decode($this->request->files['file']['name'][$i], ENT_QUOTES, 'UTF-8'));

					// Validate the filename length
					if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
						$json['error'] = $this->language->get('error_filename');
					}

					// Allowed file extension types
					$allowed = array('jpg', 'jpeg', 'gif', 'png');

					if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
						$json['error'] = $this->language->get('error_filetype');
					}

					// Allowed file mime types
					$allowed = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif');

					if (!in_array($this->request->files['file']['type'][$i], $allowed)) {
						$json['error'] = $this->language->get('error_filetype');
					}

					// Check to see if any PHP files are trying to be uploaded
					$content = file_get_contents($this->request->files['file']['tmp_name'][$i]);

					if (preg_match('/\<\?php/i', $content)) {
						$json['error'] = $this->language->get('error_filetype');
					}

					// Return any upload error
					if ($this->request->files['file']['error'][$i] != UPLOAD_ERR_OK) {
						$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error'][$i]);
					}

					if (!$json) {
						move_uploaded_file($this->request->files['file']['tmp_name'][$i], $directory . '/' . $filename);
					}

				} else {
					$json['error'] = $this->language->get('error_upload');
				}

			}

			if (!$json) {
				if ($count == 1) {
					$json['success'] = $this->language->get('helper_uploaded_singular');
				} else {
					$json['success'] = $this->language->get('helper_uploaded_plural');
				}
			}

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function remote() {

		$this->load->language('common/ph_filemanager');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Make sure we have the correct directory
		if (isset($this->request->post['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', $this->request->post['directory']), '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		if (!isset($this->request->post['url']) || (isset($this->request->post['url']) && filter_var($this->request->post['url'], FILTER_VALIDATE_URL) === false)) {
			$json['error'] = $this->language->get('error_url');
		}

		// Check its a directory
		if (!is_dir($directory)) {
			$json['error'] = $this->language->get('error_directory');
		}

		if (!$json) {

			$url = $this->request->post['url'];
			$filename = explode('/', $url);
			$count = count($filename);
			$filename = $filename[$count - 1];

			$headers = get_headers($url, 1);

			// Allowed file extension types
			$allowed = array('jpg', 'jpeg', 'gif', 'png');

			if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Allowed file mime types
			$allowed = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif');

			if (!in_array($headers['Content-Type'], $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($url);

			if (preg_match('/\<\?php/i', $content)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			if (!$json) {
				file_put_contents($directory . '/' . $filename, $content);
				$json['success'] = $this->language->get('helper_uploaded_singular');
			}

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	public function folder() {
		$this->load->language('common/ph_filemanager');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Make sure we have the correct directory
		if (isset($this->request->post['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', $this->request->post['directory']), '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		// Check its a directory
		if (!is_dir($directory)) {
			$json['error'] = $this->language->get('error_directory');
		}

		// Check if there is a folder variable
		if (!isset($this->request->post['name'])) {
			$json['error'] = $this->language->get('error_folder');
		}

		if (!$json) {
			// Sanitize the folder name
			$folder = str_replace(array('../', '..\\', '..'), '', basename(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($folder) < 3) || (utf8_strlen($folder) > 128)) {
				$json['error'] = $this->language->get('error_folder');
			}

			// Check if directory already exists or not
			if (is_dir($directory . '/' . $folder)) {
				$json['error'] = $this->language->get('error_exists');
			}

			if (!$json) {
				mkdir($directory . '/' . $folder, 0777);
				$json['success'] = $this->language->get('text_directory');
			}

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete() {
		$this->load->language('common/ph_filemanager');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['path'])) {
			$paths = $this->request->post['path'];
		} else {
			$paths = array();
		}

		// Loop through each path to run validations
		foreach ($paths as $path) {
			$path = rtrim(DIR_IMAGE . str_replace(array('../', '..\\', '..'), '', $path), '/');

			// Check path exsists
			if ($path == DIR_IMAGE . 'catalog') {
				$json['error'] = $this->language->get('error_delete');

				break;
			}
		}

		if (!$json) {
			// Loop through each path
			foreach ($paths as $path) {
				$path = rtrim(DIR_IMAGE . str_replace(array('../', '..\\', '..'), '', $path), '/');

				// If path is just a file delete it
				if (is_file($path)) {
					unlink($path);

				// If path is a directory beging deleting each file and sub folder
				} elseif (is_dir($path)) {
					$files = array();

					// Make path into an array
					$path = array($path . '*');

					// While the path array is still populated keep looping through
					while (count($path) != 0) {
						$next = array_shift($path);

						foreach (glob($next) as $file) {
							// If directory add to path array
							if (is_dir($file)) {
								$path[] = $file . '/*';
							}

							// Add the file to the files to be deleted array
							$files[] = $file;
						}
					}

					// Reverse sort the file array
					rsort($files);

					foreach ($files as $file) {
						// If file just delete
						if (is_file($file)) {
							unlink($file);

						// If directory use the remove directory function
						} elseif (is_dir($file)) {
							rmdir($file);
						}
					}
				}
			}

			$json['success'] = $this->language->get('text_delete');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function rename() {

		$this->load->language('common/ph_filemanager');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Make sure we have the correct directory
		if (isset($this->request->post['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', $this->request->post['directory']), '/');
		} else {
			$directory = '';
		}

		// Check its a directory
		if (!is_dir($directory)) {
			$json['error'] = $this->language->get('error_directory');
		}

		// Make sure we have two distinct names
		if (!isset($this->request->post['oldName']) || !isset($this->request->post['newName'])) {
			$json['error'] = $this->language->get('error_rename');
		}

		if (!$json) {

			$oldName = basename(html_entity_decode($this->request->post['oldName'], ENT_QUOTES, 'UTF-8'));
			$newName = basename(html_entity_decode($this->request->post['newName'], ENT_QUOTES, 'UTF-8'));
			$oldName = explode('.', $oldName);

			if (count($oldName) != 2) {
				$json['error'] = $this->language->get('error_rename');
			}

			if (trim($oldName[0]) == trim($newName)) {
				$json['error'] = $this->language->get('error_rename');
			}

			if (!$json) {
				rename($directory . '/' . $oldName[0] . '.' . $oldName[1], $directory . '/' . $newName . '.' . $oldName[1]);
				$json['success'] = $this->language->get('text_rename');
			}

		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	public function move() {

		$this->load->language('common/ph_filemanager');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Make sure we have the correct directories
		if (isset($this->request->post['oldDirectory']) && isset($this->request->post['newDirectory'])) {
			$oldDirectory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', str_replace('catalog/', '', $this->request->post['oldDirectory'])), '/');
			$newDirectory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', str_replace('catalog/', '', $this->request->post['newDirectory'])), '/');
		} else {
			$oldDirectory = '';
			$newDirectory = '';
		}

		// Check they are directories
		if (!is_dir($oldDirectory) || !is_dir($newDirectory)) {
			$json['error'] = $this->language->get('error_directory');
		}

		if (!isset($this->request->post['selected']) || !is_array($this->request->post['selected'])) {
			$json['error'] = $this->language->get('error_no_selection');
		}

		if (!$json) {
			$i = 0;
			foreach ($this->request->post['selected'] as $value) {
				$comparison = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', str_replace('catalog/', '', $value['path'])), '/');
				$file = $oldDirectory . '/' . $value['name'];
				if ((is_dir($file) || is_file($file)) && $oldDirectory != $comparison && $newDirectory != $comparison) {
					rename($file, $newDirectory . '/' . $value['name']);
					$i++;
				}
			}

			if ($i <= 0) {
				$json['error'] = $this->language->get('error_no_moved');
			} else if ($i == 1) {
				$json['success'] = $this->language->get('helper_moved_singular');
			} else if ($i >= 2) {
				$json['success'] = $this->language->get('helper_moved_plural');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	public function crop() {

		$this->load->language('common/ph_filemanager');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'common/filemanager')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Make sure we have the correct directory
		if (isset($this->request->post['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', $this->request->post['directory']), '/');
		} else {
			$directory = '';
		}

		// Check its a directory
		if (!is_dir($directory)) {
			$json['error'] = $this->language->get('error_directory');
		}

		// Validate the filename length
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 128)) {
			$json['error'] = $this->language->get('error_filename');
		}

		// Validate de image
		if (isset($this->request->post['image']) && !$json) {
			$image = explode(',', $this->request->post['image']);
			if (isset($image[1])) {
				$filename = $directory . '/' . $this->request->post['name'] . '.png';
				file_put_contents($filename, base64_decode($image[1]));
				$json['success'] = $this->language->get('text_cropped');
			} else {
				$json['error'] = $this->language->get('error_crop');
			}
		} else {
			$json['error'] = $this->language->get('error_crop');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	private function getDirectoryFilesize($dir) {
		$size = 0;
		foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
			$size += is_file($each) ? filesize($each) : $this->getDirectoryFilesize($each);
		}
		return $size;
	}

}
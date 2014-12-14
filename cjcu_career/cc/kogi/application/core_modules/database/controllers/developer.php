<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Copyright (c) 2011 Lonnie Ezell

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

//--------------------------------------------------------------------
// !Database Tools controller
//--------------------------------------------------------------------

class Developer extends Admin_Controller {
	
	private $backup_folder	= 'db/backups/';

	//---------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();
		
		$this->auth->restrict('Bonfire.Database.Manage');
						
		$this->backup_folder = APPPATH . $this->backup_folder;
				
		$this->lang->load('database');		
		
		Template::set_block('sub_nav', 'developer/_sub_nav');
		Template::set('sidebar', 'admin/sidebar');
	}

	//---------------------------------------------------------------

	/**
	 * Displays a list of tables in the database.
	 */
	public function index()
	{
		$hide_form = false;
				
		// Are we performing an action?
		if (isset($_POST['action']))
		{
			// Checked the checked() variable
			$_POST['checked'] = isset($_POST['checked']) ? $_POST['checked'] : '';
		
			switch(strtolower($_POST['action']))
			{
				case strtolower(lang('db_backup')):
					$hide_form = $this->backup($_POST['checked']);
					break;
				case strtolower(lang('db_repair')):
					$this->repair($_POST['checked']);
					break;
				case strtolower(lang('db_optimize')):
					$this->optimize();
					break;
				case strtolower(lang('db_drop')):
					$hide_form = $this->drop($_POST['checked']);
					break;
			}
		}
				
		if (!$hide_form)
		{
			$this->load->helper('number');
			Template::set('tables', $this->db->query('SHOW TABLE STATUS')->result());		
		} 	
		
		if (!Template::get('toolbar_title'))
		{
			Template::set('toolbar_title', lang('db_database_maintenance'));
		}
		Template::render();

	}

	//---------------------------------------------------------------
	
	public function backups() 
	{
		// Get a list of existing backup files
		$this->load->helper('file');
		Template::set('backups', get_dir_file_info($this->backup_folder));
	
		Template::set('toolbar_title', lang('db_database_backups'));
		Template::render();
	}
	
	//---------------------------------------------------------------
	
	/**
	 *	Performs the actual backup.
	 */
	public function backup($tables=null)
	{
		// Show the form
		if (!empty($tables) && is_array($tables) && count($tables) > 0)
		{
			Template::set_view('developer/backup');
			Template::set('tables', $tables);
			Template::set('file', ENVIRONMENT .'_backup_' . date('Y-m-j_His'));
			
			Template::set('toolbar_title', '建立新的備份');
			return true;		
		} else if (isset($_POST['submit']))
		{
			// Do the backup.
			$this->load->dbutil();
			
			$add_drop = ($_POST['drop_tables'] == 'Yes') ? true : false;
			$add_insert = ($_POST['add_inserts'] == 'Yes') ? true : false;
			$filename = $this->backup_folder . $_POST['file_name'] . '.' . $_POST['file_type'];
			
			$prefs = array(
							'tables' 		=> $_POST['tables'],
							'format'		=> $_POST['file_type'],
							'filename'		=> $filename,
							'add_drop'		=> $add_drop,
							'add_insert'	=> $add_insert
						);
			$backup =& $this->dbutil->backup($prefs);
			
			$this->load->helper('file');
			write_file($filename, $backup);
						
			if (file_exists($filename))
			{	
				Template::set_message('備份成功，您可在以下位置找到備份 <a href="/ncku/'. $filename .'">'. $filename .'</a>.', 'success');
			} else 
			{
				Template::set_message('備份出錯了，請聯繫系統管理員', 'error');
			}
			
			redirect(SITE_AREA .'/developer/database');
		}
		
		return false;
	}

	//---------------------------------------------------------------
	
	/**
	 * Do a force download on a backup file.
	 */
	public function get_backup($filename=null)
	{
		$this->load->helper('download');
		
		if (file_exists($this->backup_folder . $filename))
		{
			$data = file_get_contents($this->backup_folder . $filename);
			force_download($filename, $data);
			
			redirect(SITE_AREA .'/database/backups');
		} else 	// File doesn't exist
		{
			Template::set_message($filename . '找不到檔案', 'error');
			redirect(SITE_AREA .'/developer/database/backups');
		}
	}
	
	//---------------------------------------------------------------
	
	/**
	 * Perform a restore from a database backup.
	 */
	public function restore($filename=null)
	{
		Template::set('filename', $filename);
		
		if (!empty($filename) && isset($_POST['submit']))
		{
			// Load the file from disk.
			$this->load->helper('file');
			$file = file($this->backup_folder . $filename);
			
			$s = '';
			$templine = '';
			
			if (!empty($file))
			{
				// Loop through each line
				foreach ($file as $line)
				{	
					// Skip it if it's a comment
					if (substr(trim($line), 0, 1) != '#' || substr($line, 0 ,1) != '')
					{	
						// Add this line to the current segment
						$templine .= $line;
						// If it has a semicolon at the end, it's the end of a query.
						if (substr(trim($line), -1, 1) == ';')
						{
							// Perform the query...
							if($this->db->query($templine))
							{
								// Query Success
								$s .= "<strong style='color: green;'>Successfull Query</strong>: <span class='small'>$templine</span><br/>";
								
								// so reset our templine so we can start a new one
								$templine = '';
							} else {
								$s .= "<strong style='color:red'>Unsuccessful Query:</strong> $templine<br/><br/>";
								$templine = '';
							}
							
						}
					}
				}
				
				// Tell the results
				Template::set('results', $s);
			} else
			{
				// Couldn't read from file.
				Template::set_message('無法讀取檔案: /application/db/backups/' . $filename . '.', 'error');
				redirect(SITE_AREA .'/developer/database/backups');
			}
		}
		
		Template::set_view('developer/restore');
		Template::set('toolbar_title', 'Database Restore');
		Template::render();
	}
	
	//---------------------------------------------------------------
	
	/**
	 * Deletes a database table.
	 */
	public function delete()
	{	
		// Make sure we have something to delete
		if (isset($_POST['checked']) && is_array($_POST['checked']) && count($_POST['checked']) > 0)
		{		
			// Verify that we want to delete the files.
			Template::set('files', $_POST['checked']);
			
			Template::set('toolbar_title', 'Delete Backup Files');
			Template::render();
		} else if (isset($_POST['files']) && is_array($_POST['files']) && count($_POST['files']) > 0)
		{
			// Delete the files.
			$count = count($_POST['files']);
			
			$this->load->helper('file');
			
			foreach ($_POST['files'] as $file)
			{
				// Make sure the file is closed
				$fh = fopen($this->backup_folder . $file, 'w') or die("can't open file");
				fclose($fh);

				// Actually delete it.
				unlink($this->backup_folder . $file);
			}
			
			// Tell them it was good.
			Template::set_message($count . ' 檔案已刪除', 'success');
			redirect(SITE_AREA .'/developer/database/backups');
		}
	}
	
	//---------------------------------------------------------------
	
	/**
	 * Repairs database tables.
	 */
	public function repair($tables=null)
	{
		if (is_array($tables))
		{
			$count = count($tables);
			$failed = 0;
		
			$this->load->dbutil();
						
			foreach ($tables as $table)
			{
				if (!$this->dbutil->repair_table($table))
				{
					$failed += 1;
				}
			}
			
			// Tell them the results
			$quality = $failed == 0 ? 'success' : 'alert';
			
			Template::set_message(($count - $failed) .' / '. $count .' 資料表成功修復', $quality);
			redirect(SITE_AREA .'/developer/database');
		} 
		
		return;
	}
	
	//---------------------------------------------------------------
	
	/**
	 * Optimize the entire database
	 */
	public function optimize()
	{
		$this->load->dbutil();
					
		$result = $this->dbutil->optimize_database();
		
		if ($result == false)
		{
			$this->session->set_flashdata('message', 'alert::無法最佳化資料表');
		} else 
		{
			$this->session->set_flashdata('message', 'success::所選資料表最佳化成功');
		}
		
		redirect(SITE_AREA .'/developer/database', 'location');
	}
	
	//---------------------------------------------------------------
	
	/**
	 * Drop database tables.
	 */
	public function drop($tables=null)
	{	
		if (!empty($tables))
		{
			// Show our verification screen.
			Template::set('tables', $tables);
			
			Template::set_view('developer/drop');
			return true;
		} else if (is_array($_POST['tables']))
		{
			// Actually delete the files....
			$this->load->dbforge();
			
			foreach ($_POST['tables'] as $table)
			{
				@$this->dbforge->drop_table($table);
			}
			
			$grammar = count($_POST['tables'] == 1) ? ' table' : ' tables';
			Template::set_message(count($_POST['tables']) .$grammar.' 成功移除', 'success');
			redirect(SITE_AREA .'/developer/database');
		}
	}
	
	//---------------------------------------------------------------
	
	//--------------------------------------------------------------------
	// !MIGRATIONS
	//--------------------------------------------------------------------
	/*
	public function migrations() 
	{
		$this->load->library('Migrations');
	
		Template::set('installed_version', $this->migrations->get_schema_version());
		Template::set('latest_version', $this->migrations->get_latest_version());
	
		Template::render();
	}*/
	
	//--------------------------------------------------------------------
	
	/*public function migrate() 
	{
		$this->load->library('Migrations');
		
		if ($this->migrations->install())
		{
			Template::set_message('Database updated to the latest version.', 'success');
		} else
		{
			Template::set_message('Unable to update database schema: '. $this->migrations->error, 'error');
		}
		
		redirect(SITE_AREA .'/database/migrations');
	}*/
	
	//--------------------------------------------------------------------
	
	
}

// END ___ class

/* End of file ___.php */
/* Location: ./application/controllers/___.php */
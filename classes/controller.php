<?php 

class Controller
{
		private $_db,
			$_data,
			$_groupdata,
			$_sessionName,
			$_isLoggedIn = false;

	public function __construct($user = null)
	{
		$this->_db = DB::getInstance();

		$this->_sessionName = Config::get('session/session_name');

		if(!$user)
		{
			if(Session::exists($this->_sessionName))
			{
				$user = Session::get($this->_sessionName);
				if($this->findLogin($user))
				{
					$this->_isLoggedIn = true;
				}
			}
		}
		else
		{
			$this->findLogin($user);
		}

	}

	public function login($username = null, $password = null)
	{
		$user = $this->findLogin($username);
		if($user)
		{
			$passwordcheck = password_verify($password, $this->userdata()->password);

			if($passwordcheck)
			{
				Session::put($this->_sessionName, $this->userdata()->user_id);
				return true;
			}
		}
	}

	public function findLogin($user = null)
	{
		if($user)
		{
			$field = (is_numeric($user)) ? 'user_id' : 'username';
			$data = $this->_db->get('todo_users', array($field, '=', $user));
			if($data->count())
			{
				$this->_userdata = $data->GetFirst();
				
				if($this->userdata()->userrole == "admin")
				{
					$this->GetLocalListTodo($this->userdata()->userrole);
				}
				else
				{
					$this->GetUserListTodo($this->userdata()->user_id);
				}
				return true;
			}
		}
		return false;
	}

	public function find($todo_id = null)
	{
		if($todo_id)
		{
			$data = $this->_db->get('todo_table', array('todo_id', '=', $todo_id));
			if($data->count())
			{
				$this->_tododata = $data->GetFirst();
				return true;
			}
		}
		return false;
	}

	public function create($table, $fields = array())
	{
		if(!$this->_db->insert($table,$fields))
		{
			throw new Exception('There was a problem adding an todo.');
		}
	}
	public function edit($table, $id, $fields = array())
	{
		if(!$this->_db->update_todo($table, $id, $fields))
		{
			throw new Exception('There was a problem updating an todo.');
		}
	}

	public function remove($table, $fields)
	{

		if(!$this->_db->delete($table,$fields))
		{
			throw new Exception('There was a problem deleting an todo.');
		}
	}

	public function GetLocalListTodo($userrole = null)
	{
		if($userrole)
		{
			$data = $this->_db->select('todo_table');

			if($data->count())
			{
				$this->_data = $data->result();

				foreach ($data->result() as $value) 
				{
					$currentdate = strtotime('today');
					$date = strtotime($value->todo_datecreated);
					$days=round(($date-$currentdate)/86400);

					switch($days) 
					{
			            case '0';
			                 $value->todo_datecreated = 'Today';
			                break;
			            case '-1';
			                 $value->todo_datecreated = 'Yesterday';
			                break;
			            case '1';
			                 $value->todo_datecreated = 'Tomorrow';
			                break;
			            default:
			                break;
        			}
        			$field = (is_numeric($value->todo_userid)) ? 'user_id' : 'username';
					$data = $this->_db->get('todo_users', array($field, '=', $value->todo_userid));
					if($data->count())
					{
						$_set = $data->GetFirst();
						$value->todo_userid = $_set->username;
					}
				}
				return true;
			}
		}
		return false;
	}

	public function GetUserListTodo($user = null)
	{
		if($user)
		{
			$data = $this->_db->get('todo_table', array('todo_userid', '=', $user));

			if($data->count())
			{
				$this->_data = $data->result();

				foreach ($data->result() as $value) 
				{
					$currentdate = strtotime('today');
					$date = strtotime($value->todo_datecreated);
					$days=round(($date-$currentdate)/86400);

					switch($days) 
					{
			            case '0';
			                 $value->todo_datecreated = 'Today';
			                break;
			            case '-1';
			                 $value->todo_datecreated = 'Yesterday';
			                break;
			            case '1';
			                 $value->todo_datecreated = 'Tomorrow';
			                break;
			            default:
			                break;
        			}

        			
				}
				return true;
			}
		}
		return false;
	}

	public function exists()
	{
		return (!empty($this->_data)) ? true : false;
	}
	
	public function data()
	{
		return $this->_data;
	}
	public function userdata()
	{
		return $this->_userdata;
	}
	public function get_todo()
	{
		return $this->_tododata;
	}

	public function isLoggedIn()
	{
		return $this->_isLoggedIn;
	}

	public function Logout()
	{
		Session::delete($this->_sessionName);
	}




}
 ?>

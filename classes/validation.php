<?php 
class Validation
{
	private $_vpassed = false,
			$_verrors = array(),
			$_db = null;

	public function __construct()
	{
		$this->_db = DB::getInstance();
	}
	public function check($source, $items = array())
	{
		foreach ($items as $item => $rules) {

			$value = trim($source[$item]);

			foreach ($rules as $rule => $rule_value) {
				$valuename = $rules['name'];

				if ($rule === 'required' && empty($value)) {
					$this->addError("{$valuename} is required");
				}
				else if (!empty($value)) {
					switch ($rule) {
						
						case 'max':
							if(strlen($value) > $rule_value)
							{
								$this->addError("{$valuename} must be a maximum of {$rule_value} lenght");
							}
							break;
						case 'matches':
							{
								if($value != $source[$rule_value])
								{
									$this->addError("{$valuename} and Confirm Password doesn't match");
								}
							}
							break;
						case 'unique':
							{
								$checkusername = $this->_db->get("todo_users",array($item,'=',$value));

								if($checkusername->count())
								{
									$this->addError("{$valuename} is already exists");
								}
							}
							break;
						default:
							// code...
							break;
						}
					}
			}
		}

		if(empty($this->_verrors))
		{
			 $this->_vpassed = true;
		}
		return $this;
	}
	private function addError($error)
	{
		$this->_verrors[] =$error;
	}
	public function passed()
	{
		return $this->_vpassed;
	}
	public function errors()
	{
		return $this->_verrors;
	}
}
 ?>

<?php 
require_once 'core/init.php';
$controller = new Controller();

if($controller->isLoggedIn())
{
  Redirect::to('index.php');
}
	$_error = array();

if(Request::exists())
{
	$validate = new Validation();
	$validation = $validate->check($_POST, array(
		'fullname' => array (
		'required' => true,
		'min' => 3,
		'name' => 'Full Name'),

		'username' => array(
		'required' => true,
		'min' => 5,
		'name' => 'Username',
		'unique' => 'userid'),

		'password' => array(
		'required' => true,
		'min' => 5,	
		'name' => 'Password',
		'matches' => 'confirm-password'),

		'confirm-password' => array(
		'required' => true,
		'min' => 5,
		'name' => 'Confirm Password'),
	));

	if($validation->passed())
	{
	  $controller = new Controller();
	  $controller->create('todo_users',array(
	  	'username' => Request::get('username'),
	  	'fullname' => Request::get('fullname'),
	  	'password' => Hash::make(Request::get('password')),
	  	'userrole' => 'local'
	  ));
	  Session::flash('success','Your registered Successfully!');
	}
	else
	{
		$_error = $validation->errors();
	}

	if(Session::exists('success'))
	{
		  echo '<script type="text/javascript">',
       '  alert("'.Session::flash('success').'");
       	window.location.href = "registerAuthentication.php";',
       '</script>';
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>
	<link rel="stylesheet" href="css/style.css">

</head>
<body>
	<div class="wrapper">
		
		<form action="" method="post" accept-charset="utf-8">
			<div class="Register-form">

				<div class="todo-logo">
					<img src="css/default.png" alt="">
				</div>	
				<div class="error-form">
					<span>
						<?php 
							if($_error) 
							{
								foreach ($_error as $value) {
									echo $value.'<br>';
								}
							}
						?>
					</span>
				</div>

				<label>Full Name:</label>
				<input autocomplete="off" class="register-form-control"  type="text" name="fullname" value="<?php echo escape(Request::get('fullname')) ?>" placeholder="">

				<label>User Name:</label>
				<input autocomplete="off" class="register-form-control"  type="text" name="username" value="<?php echo escape(Request::get('username')) ?>" placeholder="">

				<label>Password:</label>
				<input autocomplete="off" class="register-form-control" type="password" name="password" value="" placeholder="">

				<label>Confirm Password:</label>
				<input autocomplete="off" class="register-form-control" type="password" name="confirm-password" value="" placeholder="">

				<div class="btn-form">
					<input type="submit" class="btn-control-primary" value="Register">
					<a href="LoginAuthentication.php" class="btn-control-warning">Back</a>
				</div>

			</div>
		</form>
	</div>
</body>
</html>

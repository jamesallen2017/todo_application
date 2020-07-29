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
		'username' => array(
			'required' => true,
			'name' => 'Username'
		),
		'password' => array(
			'required' => true,
			'name' => 'Password'
		)
	));

	if($validation->passed())
	{
		$login = $controller->login(Request::get('username'), Request::get('password'));
		if($login)
		{
			Redirect::to('index.php');
		}
		else
		{
			echo '<script type="text/javascript">',
	       'alert("Invalid username or password.");',
	       '</script>';
		}
	}
	else
	{
		$_error = $validation->errors();
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
				<label>User Name:</label>
				<input autocomplete="off" class="register-form-control"  type="text" name="username" value="<?php echo escape(Request::get('username')) ?>" placeholder="">

				<label>Password:</label>
				<input autocomplete="off" class="register-form-control" type="password" name="password" value="" placeholder="">

				<div class="btn-form">
					<input type="submit" class="btn-control-primary" value="Login">
					<a href="RegisterAuthentication.php" class="btn-control-warning">Register</a>
				</div>

			</div>
		</form>
	</div>
</body>
</html>
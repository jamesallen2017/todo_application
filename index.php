<?php require_once 'core/init.php';?>
<?php
$controller = new Controller();
if(!$controller->isLoggedIn())
{
  Redirect::to('loginAuthentication.php');
}

if(isset($_POST['btnSubmit']))
{
	//validate required field
	$validation = new Validation();
	$validate = $validation->check($_POST,array(
		'todo_name' => array(
			'required' => true,
			'name' => 'Title',
			'max' => 50	
		),
		'todo_datecreated' => array(
			'required' => true,
			'name' => 'Date',
		)
	));

	//pass validation
	 if($validate->passed())
	{
		$controller = new Controller();

		try {
			//update selected single row
			if(Request::get('update_id'))
			{
				$controller->edit('todo_table',Request::get('update_id'),array(
					'todo_name' => Request::get('todo_name'),
					'todo_datecreated' => Request::get('todo_datecreated')
				));
			}
			else
			{
				//insert data
				$controller->create('todo_table', array(
				'todo_name' => Request::get('todo_name'),
				'todo_datecreated' => Request::get('todo_datecreated'),
				'todo_status' => 'on-going',
				'todo_userid' => escape($controller->userdata()->user_id),
				'todo_userrole' => escape($controller->userdata()->userrole)
			));


			}
			
      		Redirect::to('index.php');

		} catch (Exception $e) {
			die ($e->getMessage());
		} 
	}
	else
	{
		foreach ($validate->errors() as $value) {
			echo '<script type="text/javascript">',
       '  alert("'.$value.'");',
       '</script>';
		}
	}
}

//delete multiple row
if(Request::get('multi_delete'))
{
	if(Request::get('chck_del'))
	{
		try {

			foreach (Request::get('chck_del') as $value) 
			{
				$controller = new Controller();
				$controller->remove('todo_table', array('todo_id','=', $value));
			}
			Redirect::to("index.php");
		} catch (Exception $e) {

			die ($e->getMessage());
			
		}
		
	}
}

//update status todo
if(Request::get('multi_status'))
{
	if(Request::get('chck_del'))
	{
		try {
			foreach (Request::get('chck_del') as $value) {
				$controller->edit('todo_table',$value,	array('todo_status' => Request::get('multi_status')));
			}
			Redirect::to("index.php");

		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	else
	{
		echo '<script type="text/javascript">',
       '  alert("You must select atleast one row.");',
       '</script>';
	}
}

//delete single row 
if(Request::get('delete_id'))
{
	$controller = new Controller();
	try {

		$controller->remove('todo_table', array('todo_id','=',Request::get('delete_id')));
		Redirect::to('index.php');

	} catch (Exception $e) {
			die ($e->getMessage());
	} 

}



//retrieve selected data in textbox
if(Request::get('update_id'))
{
	$data = new Controller();
	$data->find(Request::get('update_id'));
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
	<nav>
		<ul class="nav-menu">
			<li class="menu-list"></li>
			<li class="menu-list">
				<div class="menu-list-right">
					<ul>
						<li><?php echo escape($controller->userdata()->fullname) ?> (<?php echo escape($controller->userdata()->userrole) ?>)</li>
						<li><a href="Logout.php" onclick="return confirm('are you sure you want to logout?')" title="">Logout</a></li>
					</ul>
				</div>
				</li>
		</ul>
	</nav>
			<form action="" method="post" accept-charset="utf-8">
	<div class="wrapper">

		<div class="main-form">
				<input type="text" id="todo_name" class="form-control" value="<?php if(Request::get('update_id')) {echo escape($data->get_todo()->todo_name);} else {echo escape(Request::get('todo_name'));} ?>" name="todo_name"/>
				<?php 
					if(Request::get('update_id'))
						//start if
					{
				?>
						<button type="submit" name="btnSubmit" class="btnUpdate"> Update </button>
						<a href="index.php" class="btnCancel"> Cancel </a>
				<?php
					//end if
					}
					else
					{
					//start elseif
				?>
						<button type="submit" name="btnSubmit" id="btnAdd" class="btnAdd"> Add </button>
				<?php
					//end else if
					}
				?>
		<input type="date" name="todo_datecreated" value="<?php if(Request::get('update_id')) {echo escape($data->get_todo()->todo_datecreated);} else {echo escape(Request::get('todo_datecreated'));} ?>" placeholder="">

		</div>

		<?php 
			if($controller->data())
			{
				//start if
			?>
				<div class="tool-content">
				   	 <ul class="tool-menu">
				   	 	<li class="tool-list"><a class="tool-action" id="SelectAll" href="#" title="">select all</a></li>
				   	 	<li class="tool-list" id="MultiDelete" hidden><input type="submit" onclick="return confirm('are you sure you want to delete?')" value="Delete" name="multi_delete" class="tool-list-action"  placeholder=""></li>
				   	 	<li class="tool-list">
				   	 		<input type="submit" onclick="return confirm('are you sure you want to update into on-going?')" value="on-going" name="multi_status" class="tool-list-action"  placeholder="">
				   	 	</li>
				   	 	<li class="tool-list">
				   	 		<input type="submit" onclick="return confirm('are you sure you want to update into finished?')" value="finished" name="multi_status" class="tool-list-action"  placeholder="">
				   	 	</li>
				   	 	<li class="tool-list">
				   	 	</li>
				   	 </ul>
				</div>
			<?php
				//end if
			}
		 ?>
		<?php 
		if($controller->data())
		{
			//start if
			foreach (array_reverse($controller->data()) as $value) 
			{
			   ?>
			   <!-- start each -->
					<div class="sub-form">
						<label class="container">
							<input type="checkbox" name="chck_del[]" id="chck_del" value="<?php echo $value->todo_id ?>">
								<span class="checkmark"></span>
							</input>
						</label>
						<div class="list-content">
							<div class="title-content">
								<?php echo escape($value->todo_name); ?>
							</div>
							<div class="list-content-footer">
								<div class="date-created">
									<label><?php echo escape($value->todo_datecreated); ?>
									 <strong><?php if($controller->userdata()->userrole == "admin") echo '('. escape($value->todo_userid). ')'; ?></strong></label>
								</div>
								<?php 
									if($value->todo_status == "finished")
									{
								?>
										<div class="todo-status-success">
											<label><?php echo escape($value->todo_status); ?> </label>
										</div>
								<?php
									}
									else
									{
								?>
										<div class="todo-status-warning">
											<label><?php echo escape($value->todo_status); ?></label>
										</div>
								<?php
									}
								?>
								
							</div>
						</div>
						<a href="?delete_id=<?php echo $value->todo_id ?>" onclick="return confirm('are you sure you want to delete?')"><img class="remove_icn" src="css/delete_icn.png"></a>
						<a href="?update_id=<?php echo $value->todo_id ?>"><img class="edit_icn" src="css/edit_icn.png"></a>
					</div>
			   <!-- end each -->
			  <?php
			}
			//end if
		}

		 ?>
	
	</div>
	</form>

</body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"> </script>


<script type="text/javascript" charset="utf-8">

	$("#SelectAll").on('click',function(){
	var chk_arr =  document.getElementsByName("chck_del[]"); 
	if(checkAny())
	{
		for(k=0;k< chk_arr.length;k++)
	    {
	        chk_arr[k].checked = false;
	    } 
	    document.getElementById('SelectAll').innerHTML ='Select All';

	} 
	else
	{
		for(k=0;k< chk_arr.length;k++)
	    {
	        chk_arr[k].checked = true;
	    } 
	    document.getElementById('SelectAll').innerHTML ='deselect All';
	}      
		Check();
	})

	$("input[id*='chck_del']").on('click',function(){
		Check();
	})

	function checkAny(){
    var chk_arr =  document.getElementsByName("chck_del[]");             
    for(k=0;k< chk_arr.length;k++)
    {
        if(chk_arr[k].checked==true){
        return true;
        }
    } 
    return false;
}

	function showDeleteBtn()
	{
    	document.getElementById('MultiDelete').style.display = "block"; 
	    document.getElementById('SelectAll').innerHTML ='deselect All';

	}
	function HideDeleteBtn()
	{
		document.getElementById('MultiDelete').style.display= "none";
	    document.getElementById('SelectAll').innerHTML ='Select All';
	}

	function Check()
	{
		checkAny() ? showDeleteBtn():HideDeleteBtn();
	}

</script>
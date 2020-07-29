<?php
function escape($string)
{
	
	return htmlentities($string, ENT_HTML401, 'UTF-8');
}
 ?>
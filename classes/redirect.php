<?php 
class Redirect
{
    public static function to ($location = null)
    {
        if($location)
        {
            if(is_numeric($location))
            {
                switch ($location) {
                    case 404:
                        header('HTTP/1.0 404 not found');
                        include 'includes/errors/404.php';
                        break;
                    case 502:
                        // code...
                        break;
                    default:
                        // code...
                        break;
                }
            }
            header('location: ' . $location);
            exit();
        }
    }
}
 ?>

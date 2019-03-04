<?php
/**
 * Created by PhpStorm.
 * User: andersgj
 * Date: 3/1/19
 * Time: 10:55 AM
 */
/* This class is used throughout the application as a validation for all form input. */

class validator
{

    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;

    }
}

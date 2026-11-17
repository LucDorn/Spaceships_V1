<?php
abstract class UserID
{
    public function __construct()
    {
        echo rand(10, 100);
    }
}

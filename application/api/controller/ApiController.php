<?php
namespace app\api\controller;

class ApiController
{
    public function yoman()
    {
        return json(['message' => 'Hello World']);
    }

    public function finallyWorkingHuh()
    {
        return json(['message' => 'YEAHHHHHHHHHHHHHHHHHH!']);
    }
}
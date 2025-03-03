<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return "This is index page";
    }

    public function yoman()
    {
        return json(['message' => 'Hello World']);
    }

    public function finallyWorkingHuh()
    {
        return json(['message' => 'YEAHHHHHHHHHHHHHHHH']);
    }
}
<?php

namespace Ccs\Controllers;

class Index extends Controller
{
    public function index() {
        $this->render('front/home.php');
    }
}
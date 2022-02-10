<?php

namespace App\Http\Controllers;

class SinglePageController extends Controller {
    public function index() {
        return view('single-page-application');
    }
}

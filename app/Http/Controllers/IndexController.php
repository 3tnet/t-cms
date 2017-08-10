<?php

namespace App\Http\Controllers;


class IndexController extends Controller
{
    public function index()
    {
        return redirect()->route('category', 'company-news');
        // return $this->postList('company-news', $request, app(CategoryRepository::class))->render();
    }
}

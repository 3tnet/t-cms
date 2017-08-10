<?php

namespace App\Http\Controllers;

use App\Events\VisitedPostList;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function show($cateSlug, Request $request, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->findBySlug($cateSlug);

        event(new VisitedPostList($category));

        if ($category->isPostList()) {
            return $this->showList($category, $request);
        } else {
            return $this->showPage($category);
        }
    }

    private function showList(Category $category, Request $request)
    {
        $postList = $category->postListWithOrder($request->get('order'))->with('user')->paginate($this->perPage());
        $postList->appends($request->all());
        return theme_view(
            $category->list_template, [
                'postList' => $postList,
            ]
        );
    }

    private function showPage(Category $category)
    {
        $page = $category->page();
        if (is_null($page)) {
            //todo
            abort(404, '该单页还没有初始化');
        }
        return theme_view($category->page_template, ['pagePost' => $page]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Events\PostHasBeenRead;
use App\Repositories\CategoryRepository;
use App\Widgets\Alert;
use Illuminate\Http\Request;
use Auth;

class PostsController extends Controller
{
    /**
     * 正文
     *
     * @param  $cateSlug
     * @return \Illuminate\Contracts\View\View
     */
    public function show($cateSlug, $post, Request $request, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->findBySlug($cateSlug);
        $queryBuilder = $category->posts()->post()->where('id', $post);
        if (Auth::check() && Auth::user()->can('admin.post.show')) {
            $post = $queryBuilder->where(
                function ($query) {
                    $query->publishAndDraft();
                }
            )->withTrashed()->firstOrFail();
            if (!$post->isPublish() || $post->trashed()) {
                // 管理员预览草稿或未发布的文章
                app(Alert::class)->setDanger('当前文章未发布，此页面只有管理员可见!');
            }
        } else {
            $post = $queryBuilder->publish()->firstOrFail();
        }
        event(new PostHasBeenRead($category, $post, $request->getClientIp()));
        return theme_view($post->template, ['post' => $post]);
    }
}

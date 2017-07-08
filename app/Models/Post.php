<?php

namespace App\Models;

use App\Models\Presenters\PostPresenters;
use App\Models\Traits\Listable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laracasts\Presenter\PresentableTrait;
use Ty666\PictureManager\Traits\Picture;

class Post extends BaseModel
{
    use SoftDeletes, Picture, Listable, PresentableTrait;

    protected $presenter = PostPresenters::class;

    protected $fillable = ['title', 'user_id', 'author_info', 'excerpt', 'type', 'views_count', 'cover', 'status', 'template', 'top', 'published_at'];
    protected $dates = ['deleted_at', 'top', 'published_at', 'created_at', 'updated_at'];
    protected $hasDefaultValuesFields = ['order', 'views_count', 'status', 'type'];
    protected static $allowSearchFields = ['title', 'author_info', 'excerpt'];
    protected static $allowSortFields = ['title', 'status', 'views_count', 'top', 'order', 'published_at'];

    public function scopeRecent($query)
    {
        return $query->orderBy('published_at', 'desc')->orderBy('created_at', 'desc');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->ordered()->recent();
    }


    public function scopeApplyFilter($query, $data)
    {
        $data = $data->only('q', 'status', 'orders', 'only_trashed');
        $query->orderByTop();
        $query->post();

        if (isset($data['q'])) {
            $query->withSimpleSearch($data['q']);
        }

        if (isset($data['orders'])) {
            $query->withSort($data['orders']);
        }

        switch ($data['status']) {
        case 'publish':
            $query->publish();
            break;
        case 'draft':
            $query->draft();
        default:
            $query->publishAndDraft();
        }
        if (isset($data['only_trashed']) && $data['only_trashed']) {
            $query->onlyTrashed();
        }
        return $query->ordered()->recent();
    }

    public function scopePost($query)
    {
        return $query->where('type', 'post');
    }

    public function scopePage($query)
    {
        return $query->where('type', 'page');
    }

    public function scopePublish($query)
    {
        return $query->where('status', 'publish');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePublishAndDraft($query)
    {
        return $query->where('status', 'publish')->orWhere('status', 'draft');
    }

    public function scopeOrderByTop($query)
    {
        return $query->orderBy('top', 'DESC');
    }

    /*public function getViewsCountAttribute($viewsCount)
    {
        return intval(Cache::rememberForever('post_views_count_' . $this->id, function () use ($viewsCount) {
            return $viewsCount;
        }));
    }*/

    public function addViewCount()
    {

        Post::where($this->getKeyName(), $this->getKey())->increment('views_count');
        $this->views_count++;
    }

    public function content()
    {
        return $this->hasOne(PostContent::class);
    }

    public function saveCategories($categories)
    {
        if ($categories instanceof Collection) {
            $categories = $categories->pluck('id');
        } elseif (is_string($categories)) {
            $categories = explode(',', $categories);
        }
        $this->categories()->sync($categories);
    }

    public function getCoverUrlsAttribute()
    {
        //todo 换一个默认封面
        return $this->getPicure($this->cover, ['sm', 'md', 'lg', 'o'], asset('images/default_avatar.jpg'));
    }

    public function getCover($style, $defaultCover = '')
    {
        return $this->getPicure($this->cover, $style, $defaultCover);
    }

    /**
     * 添加附加表数据
     *
     * @param $data
     */
    public function addition($data)
    {
        if (isset($data['content'])) {
            $this->content()->updateOrCreate(
                [], [
                'content' => $data['content']
                ]
            );
        }

        // 处理分类
        if (!empty($data['category_ids'])) {
            $this->saveCategories($data['category_ids']);
        }
    }

    /**
     * 文章是否置顶
     */
    public function isTop()
    {
        return !is_null($this->top);
    }

    public function getNextPost(Category $category)
    {
        return $category->posts()->post()->publish()->where('post_id', '>', $this->id)->first();
    }

    public function isPublish()
    {
        return $this->status == 'publish';
    }
    public function isDraft()
    {
        return $this->type == 'draft';
    }
}

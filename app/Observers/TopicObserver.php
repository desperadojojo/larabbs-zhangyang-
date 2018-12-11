<?php

namespace App\Observers;

use App\Models\Topic;
use App\Jobs\TranslateSlug;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    

    public function saving(Topic $topic)
    {
	    // XSS 过滤
    	$topic->body = clean($topic->body, 'user_topic_body');

	    // 生成话题摘录
        $topic->excerpt = make_excerpt($topic->body);
        // $topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
	    
    }

    public function saved(Topic $topic)
    {
        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
        if ( ! $topic->slug) {

            // 推送任务到队列
            dispatch(new TranslateSlug($topic));//实例化任务类，推送到队列任务中。此时这个对象会在后台默默尝试执行几次，成功会更新数据表 topics，几次失败之后会把失败信息写进 failed_jobs 数据表，然后释放自己。
        }
    }
}
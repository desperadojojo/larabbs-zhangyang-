<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Handlers\SlugTranslateHandler;
use App\Models\Topic;

class TranslateSlug implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    //定义成员属性 `protected $topic;` 用于存等下要用的 $topic 对象
    protected $topic;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Topic $topic)
    {
        //构造函数 通常用来给成员属性赋值 （配置参数）
        //队列任务构造器中接收了 Eloquent 模型，将会只序列化模型的 ID
        $this->topic = $topic;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //请求百度 API 接口进行翻译
        $slug = app(SlugTranslateHandler::class)->translate($this->topic->title);

        //为了避免模型监控器死循环调用，我们用DB类直接对数据库进行操作
        // 这里必须用 \DB::table() 来读取表数据然后修改，而不能实例化模型
        \DB::table('topics')->where('id',$this->topic->id)->update(['slug'=>$slug]);
    }
}

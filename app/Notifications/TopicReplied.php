<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Reply;

class TopicReplied extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public $reply;

    public function __construct(Reply $reply)
    {
        //注入回复实体，方便toDatabase方法中的使用
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        //开启通知的频道
        return ['database', 'mail'];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     * toDatabase()这个方法接收 $notifiable 实例参数并返回一个普通的 PHP 数组。这个返回的数组将被转成 JSON 格式并存储到通知数据表的 data 字段中。
     */
    public function toDatabase($notifiable)
    {
        $topic = $this->reply->topic;
        $link = $topic->link(['#reply' . $this->reply->id]);
        // 这里的 `link()` 就是我们生成带 slug 地址的那个写在 User 模型中的函数，
        // 那个函数之前写了个参数默认为空数组的参数，这里就是拼它的第三参数，将最终的地址变成
        //  `http://larabbs.test/topics/{topic}?{slug}{#reply回复id}` => 这其实是个锚点，
        // 因为视图上装每条回复的div都给了个id: `id="reply{{ $reply->id }}"` 所以这样点击通知的链接的时候，
        // 会直接跳转到帖子的回复部分中的通知针对的那条回复。
        
        //存入数据库里的数据
        return [
            'reply_id' => $this->reply->id,
            'reply_content' => $this->reply->content,
            'user_id' => $this->reply->user->id,
            'user_name' => $this->reply->user->name,
            'user_avatar' => $this->reply->user->avatar,
            'topic_link' => $link,
            'topic_id' => $topic->id,
            'topic_title' => $topic->title,
            // `$notifications->data['字段']` => 因为其实 data 存的是 json，取出来不用管，
            // 直接用 `..->data['json属性即我们在 TopicReplied@toDatabase 方法中return 写进数据库的那些键名']` 即可读取具体数据。
        ];
    }

    public function toMail($notifiable)
    {
        $url = $this->reply->topic->link(['#reply' . $this->reply->id]);

        return (new MailMessage)
            ->line('你的话题有新回复！')
            ->action('查看回复', $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

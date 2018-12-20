@extends('layouts.app')

@section('title')
我的通知 
@stop

@section('content')
    <div class="container">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">

                <div class="panel-body">

                    <h3 class="text-center">
                        <span class="glyphicon glyphicon-bell" aria-hidden="true"></span> 我的通知
                    </h3>
                    <hr>

                    @if ($notifications->count())

                        <div class="notification-list">
                            @foreach ($notifications as $notification)
                                @include('notifications.types._' . snake_case(class_basename($notification->type)))
                                {{--  'notifications.types._' 这是说视图的两层文件夹 ../notifications/types/后面的_就是说视图以_开头
                                后面的 class_basename($notification->type) 是在读取 $notification 的 type 属性，即 notifications 表中的 type 字段
                                 => 这个字段存的就是通知类的命名空间（发送通知时自动读通知类的内容，写进数据库的）。如果用 `class_basename()` 去读命名空间，
                                 得到的只是那个类名。`class_basename('App\Notifications\TopicReplied') = TopicReplied` 
                                 `snake_case()` 函数就是把字符串全转小写加下划线的形式 `snake_case('TopicReplied') => topic_replied`
                                 最后生成的视图其实是 ../notifications/types/_topic_replied.blade.php --}}

                            @endforeach

                            {!! $notifications->render() !!}
                        </div>

                    @else
                        <div class="empty-block">没有消息通知！</div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
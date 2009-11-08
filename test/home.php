<?php
include dirname(dirname(__FILE__))
        .DIRECTORY_SEPARATOR.'code'
        .DIRECTORY_SEPARATOR.'ice'
        .DIRECTORY_SEPARATOR.'app.php';

app(array(
    '^/?$'           => 'Home',
    '^/post/(\d+)/?' => 'Post'
));


class Home
{
    public function get()
    {
        print 'Hello World!';
    }

    public function post()
    {
        print 'Recebi um POST';
    }
}

class Post
{
    public function get($id)
    {
        print "<h1>Post $id</h1><p>Loren ipsum</p>";
    }
}

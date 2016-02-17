<?php

class Controller_Todo extends Controller
{
    /**
     * アプリのメインページを表示する.
     */
    public function action_main()
    {
        $view = View::forge('../../../public/todo');

        return Response::forge($view);
    }

    public function action_404()
    {
        $view = View::forge('sentence');
        $view->set('content', 'お探しのページは見つかりませんでした');

        return Response::forge($view);
    }
}

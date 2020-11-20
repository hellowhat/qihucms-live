<?php

namespace Qihucms\Live\Admin\Controllers;

use Qihucms\Live\Models\Live;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LiveController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '直播';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Live());

        $grid->column('id', trans('qihu-live::live.Id'));
        $grid->column('user_id', trans('qihu-live::live.User id'))->display(function($user_id){
            return User::find($user_id)->username;
        });
        $grid->column('category.title', trans('qihu-live::live.Category id'));
        $grid->column('title', trans('qihu-live::live.Title'));
        $grid->column('screen', trans('qihu-live::live.Screen.label'))->editable('select',__('qihu-live::live.Screen.value'));
        $grid->column('cover', trans('qihu-live::live.Cover'))->image('', 100,100);
        $grid->column('hls', trans('qihu-live::live.Hls'));
        $grid->column('times', trans('qihu-live::live.Times'));
        $grid->column('product', trans('qihu-live::live.Product'));
        $grid->column('status', trans('qihu-live::live.Status.label'))->editable('select',__('qihu-live::live.Status.value'));
        $grid->column('created_at', trans('qihu-live::live.created_at'));
        $grid->column('updated_at', trans('qihu-live::live.updated_at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Live::findOrFail($id));

        $show->field('id', trans('qihu-live::live.Id'));
        $show->field('user_id', trans('qihu-live::live.User id'));
        $show->field('category_id', trans('qihu-live::live.Category id'));
        $show->field('title', trans('qihu-live::live.Title'));
        $show->field('screen', trans('qihu-live::live.Screen.label'))->using(trans('qihu-live::live.Screen.value'));
        $show->field('cover', trans('qihu-live::live.Cover'));
        $show->field('Hls', trans('qihu-live::live.Hls'));
        $show->field('times', trans('qihu-live::live.Times'));
        $show->field('product', trans('qihu-live::live.Product'));
        $show->field('status', trans('qihu-live::live.Status.label'))->using(trans('qihu-live::live.Status.value'));
        $show->field('created_at', trans('qihu-live::live.created_at'));
        $show->field('updated_at', trans('qihu-live::live.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Live());

        $form->select('user_id', trans('qihu-live::live.User id'))
            ->options(function ($use_id) {
                $model = User::find($use_id);
                if ($model) {
                    return [$model->id => $model->username];
                }
            })
            ->ajax(route('admin.api.users'))
            ->rules('required');
        $form->select('category_id', trans('qihu-live::live.Category id'))->options(route('live.wap.categories'));
        $form->text('title', trans('qihu-live::live.Title'));
        $form->select('screen', trans('qihu-live::live.Screen.label'))->options(trans('qihu-live::live.Screen.value'));
        $form->image('cover', trans('qihu-live::live.Cover'))
            ->removable()
            ->uniqueName()
            ->move('live/thumbnail');
        $form->url('hls', trans('qihu-live::live.Hls'));
        $form->number('times', trans('qihu-live::live.Times'));
        $form->text('product', trans('qihu-live::live.Product'));
        $form->select('status', trans('qihu-live::live.Status.label'))->options(trans('qihu-live::live.Status.value'));

        return $form;
    }
}

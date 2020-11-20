<?php

namespace Qihucms\Live\Admin\Controllers;

use Qihucms\Live\Models\LiveCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LiveCategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '直播分类';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new LiveCategory());

        $grid->column('id', trans('qihu-live::live.Id'));
        $grid->column('title', trans('qihu-live::live.category.Title'));
        $grid->column('sort', trans('qihu-live::live.category.Sort'));
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
        $show = new Show(LiveCategory::findOrFail($id));

        $show->field('id', trans('qihu-live::live.Id'));
        $show->field('title', trans('qihu-live::live.category.Title'));
        $show->field('sort', trans('qihu-live::live.category.Sort'));
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
        $form = new Form(new LiveCategory());

        $form->text('title', trans('qihu-live::live.category.Title'));
        $form->number('sort', trans('qihu-live::live.category.Sort'))->help('数字越大越靠前');

        return $form;
    }
}

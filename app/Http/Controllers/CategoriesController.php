<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
	/**
	 * @param Category $category
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function show(Category $category)
	{
    	//读取分类ID关联的话题，并按每20条分页
	    $topics = Topic::where('category_id',$category->id)->paginate(20);
	    //传参变量话题和分类到模板中
	    return view('topics.index', compact('topics','category'));
    }
}

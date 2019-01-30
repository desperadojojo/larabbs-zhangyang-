<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Transformers\CategoryTransformer;
use App\Models\Category;

class CategoriesController extends Controller
{
    public function index(){
        
        return $this->response->collection(Category::all(),new CategoryTransformer());
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Repositories\AuthorizationsRepository;
use App\Http\Requests\Api\AuthorizationRequest;


class AuthorizationsController extends Controller
{
    
    protected $repo;

    public function __construct(AuthorizationsRepository $repo){
        $this->repo = $repo;
    }
    
      
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        $token_array = $this->repo->weixinCreate($type,$request);
        return $token_array;
    }

    public function authStore(AuthorizationRequest $request)
    {
        $token_array = $this->repo->store($request);
        return $token_array;        
    }

    public function update()
    {
        return $this->repo->updateToken();
    }

    public function destroy()
    {
        return $this->repo->destroyToken();
    }
}

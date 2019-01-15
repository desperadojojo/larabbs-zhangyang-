<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Repositories\AuthorizationsRepository;

class AuthorizationsController extends Controller
{
    protected $repo;

    public function __construct(AuthorizationsRepository $repo){
        $this->repo = $repo;
    }
    
      
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        $token_userid = $this->repo->create($type,$request);
        return $token_userid;
    }
}

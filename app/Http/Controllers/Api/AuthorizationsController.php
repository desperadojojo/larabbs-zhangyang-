<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Repositories\AuthorizationsRepository;
use App\Http\Requests\Api\AuthorizationRequest;
use Zend\Diactoros\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\AuthorizationServer;
use Log;
use App\Traits\PassportToken;

class AuthorizationsController extends Controller
{
    use PassportToken;

    protected $repo;

    public function __construct(AuthorizationsRepository $repo){
        Log::info('Info',['msg'=>' 执行没有1']);
        $this->repo = $repo;
    }
    
    // Passport
    public function store(AuthorizationRequest $originRequest, AuthorizationServer $server, ServerRequestInterface $serverRequest)
    {
        try {
            Log::info('Info',['msg'=>' 执行没有2']);
        return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response)->withStatus(201);
        } catch(OAuthServerException $e) {
            
            return $this->response->errorUnauthorized($e->getMessage());
        }
    } 

    // JWT
    public function authStore(AuthorizationRequest $request)
    {
        Log::info('Info',['msg'=>' 执行没有3']);
        $token_array = $this->repo->store($request);
        return $token_array;        
    }
    
    // JWT
    // public function socialStore($type, SocialAuthorizationRequest $request)
    // {
    //     $token_array = $this->repo->weixinCreate($type,$request);
    //     return $token_array;
    // }

    // Passport
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        $token_array = $this->repo->weixinCreate($type,$request);
        return $token_array;
    }



    // JWT
    public function authupdate()
    {
        return $this->repo->updateToken();
    }
    
    // Passport
    public function update(AuthorizationServer $server, ServerRequestInterface $serverRequest) 
    {
        try {
        return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response);
        } catch(OAuthServerException $e) {
            return $this->response->errorUnauthorized($e->getMessage());
        }
    }

    // public function destroy()
    // {
    //     return $this->repo->destroyToken();
    // }

    public function destroy()
    {
        if (!empty($this->user())) {
            $this->user()->token()->revoke();
            return $this->response->noContent();
        } else {
            return $this->response->errorUnauthorized('The token is invalid.');
        }
    }
}

<?php

namespace Lxj\Laravel\Tars\controller;

use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Facade;
use Laravel\Lumen\Application;
use Lxj\Laravel\Tars\Boot;
use Lxj\Laravel\Tars\Controller;
use Lxj\Laravel\Tars\Request;
use Lxj\Laravel\Tars\Response;
use Lxj\Laravel\Tars\Server\LumenManager;
use Lxj\Laravel\Tars\Util;
use SwooleTW\Http\Server\Manager;
use SwooleTW\Http\Server\Sandbox;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LumenController extends Controller
{
    public function actionRoute()
    {
        try {
            /** @var \Illuminate\Http\Request $illuminateRequest */
            $illuminateRequest = Request::make($this->getRequest())->toIlluminate();
            

            /** @var LumenManager $manager */
            $manager = app(LumenManager::class);
            $manager->OnRequest($illuminateRequest, $this->getResponse());
        } catch (\Throwable $e) {
            $this->status(500);
            $this->sendRaw($e->getMessage() . '|' . $e->getTraceAsString());
        }
    }
    
}

<?php

namespace Lxj\Laravel\Tars\controller;

use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Facade;
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
    /** @var LumenManager */
    protected static $manager;
    public function __construct(\Tars\core\Request $request, \Tars\core\Response $response)
    {
        parent::__construct($request, $response);
        if (!static::$manager) {
            static::$manager = app(LumenManager::class);
        }
    }

    public function actionRoute()
    {
        $illuminateRequest = Request::make($this->getRequest())->toIlluminate();
        static::$manager->OnRequest($illuminateRequest,$this->getResponse());
    }

}

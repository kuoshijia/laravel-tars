<?php
namespace Lxj\Laravel\Tars\Server;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use SwooleTW\Http\Server\Sandbox;
use SwooleTW\Http\Transformers\Request;
use SwooleTW\Http\Transformers\Response;
use Throwable;

class LumenManager extends \SwooleTW\Http\Server\Manager
{
    public function __construct(Container $container = null, $framework = 'lumen', $basePath = null)
    {
        if (is_null($container)) {
            $container = app();
        }
        parent::__construct($container, $framework, $basePath);
    }

    protected $events = [];

    public function OnRequest($illuminateRequest, $swooleResponse)
    {
        $this->resetOnRequest();
        $sandbox = $this->app->make(Sandbox::class);

        try {

            // set current request to sandbox
            $sandbox->setRequest($illuminateRequest);

            // enable sandbox
            $sandbox->enable();

            // handle request via laravel/lumen's dispatcher
            $illuminateResponse = $sandbox->run($illuminateRequest);
            
            // send response
            Response::make($illuminateResponse, $swooleResponse)->send();

        } catch (Throwable $e) {
            try {
                $illuminateResponse = $this->app
                    ->make(ExceptionHandler::class)
                    ->render(
                        $illuminateRequest,
                        $this->normalizeException($e)
                    );
            } catch (Throwable $e) {
                $this->logServerError($e);
            }
        } finally {
            // disable and recycle sandbox resource
            $sandbox->disable();
        }
    }

}

<?php
namespace Lxj\Laravel\Tars\Server;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Lxj\Laravel\Tars\Response;
use SwooleTW\Http\Server\Sandbox;
use SwooleTW\Http\Transformers\Request;
use Throwable;

class LumenManager extends \SwooleTW\Http\Server\Manager
{
    protected static $application = null;
    public function __construct(Container $container = null, $framework = 'lumen', $basePath = null)
    {
        if (is_null($container)) {
            $container = app();
        }
        parent::__construct($container, $framework, $basePath);
        if ($this->app = static::$application) {
//            echo get_class($this->app);
        } else {
            $this->getApplication();
            static::$application = $this->app;
        }

        // bind after setting laravel app
        $this->bindToLaravelApp();

        // prepare websocket handler and routes
        if ($this->isServerWebsocket) {
            $this->prepareWebsocketHandler();
            $this->loadWebsocketRoutes();
        }

    }

    protected $events = [];

    public function OnRequest($illuminateRequest, $tarsResponse)
    {
        $this->resetOnRequest();
        /** @var Sandbox $sandbox */
        $sandbox = $this->app->make(Sandbox::class);

        try {

            // set current request to sandbox
            $sandbox->setRequest($illuminateRequest);

            // enable sandbox
            $sandbox->enable();

            // handle request via laravel/lumen's dispatcher
            /** @var \Illuminate\Http\Response $illuminateResponse */
            $illuminateResponse = $sandbox->run($illuminateRequest);

            // send response
            Response::make($illuminateResponse, $tarsResponse)->send();

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

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
    public function __construct(Container $container, $framework = 'lumen', $basePath = null)
    {
//        parent::__construct($container, $framework, $basePath);
    }

    /**
     * @Note:
     * @param \Illuminate\Http\Request $illuminateRequest
     * @param \Swoole\Http\Response $tarsResponse
     */
    public function OnRequest($illuminateRequest, $tarsResponse)
    {
        $this->resetOnRequest();

        /** @var Sandbox $sandbox */
        $sandbox = app(Sandbox::class);

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
            //清除context的数据
            $sandbox->disable();
        }
    }

}

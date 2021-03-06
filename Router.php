<?php
namespace codearchitect\codearklite;

use codearchitect\codearklite\exception\NotFoundException;

/**
 * Class Router
 *
 * @author: Indranil Samanta (code-architec)
 * @package codearchitect\codearklite
 */
class Router
{
    public Request $request;
    public Response $response;
    protected array $routes = [];

    /**
     * Router constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @param $path string Get the current path of the application
     * @param $callback '' Get the method this path corresponding to
     */
    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    /**
     * @param $path string Get the current path of the application
     * @param $callback '' Get the method this path corresponding to
     */
    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;

        if($callback === false)
        {
            throw new NotFoundException();
        }
        if(is_string($callback)){
            return Application::$app->view->renderView($callback);
        }
        if(is_array($callback))
        {
            // this is $callback[0] an instance of the controller
            /** @var Controller $controller  */
            $controller = new $callback[0]();
            Application::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;
            foreach ($controller->getMiddlewares() as $middleware)
            {
                $middleware->execute();
            }
        }
        return call_user_func($callback, $this->request, $this->response);
    }

}
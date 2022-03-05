<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\App;
use Cake\Http\Exception\MissingControllerException;
use Cake\Http\ServerRequest;
use Cake\Utility\Inflector;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;


class ActionFactory
{
    /**
     * Create an action class for a given request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request to build an action.
     */
    public function create(ServerRequestInterface $request): Action
    {
        $className = $this->getActionClass($request);
        if ($className === null) {
            throw $this->missingAction($request);
        }
        return new $className($request);
    }

    /**
     * Determine the action class name based on current request and controller and action params
     *
     * @param \Cake\Http\ServerRequest $request The request to build.
     * @return string|null
     */
    public function getActionClass(ServerRequest $request): ?string
    {
        $pluginPath = '';
        $namespace = 'Controller/Action';
        $controller = $request->getParam('controller', '');
        $action = $request->getParam('action', 'Index');
        $action = ucfirst($action);
        if ($request->getParam('plugin')) {
            $pluginPath = $request->getParam('plugin') . '.';
        }
        if ($request->getParam('prefix')) {
            $prefix = $request->getParam('prefix');

            $firstChar = substr($prefix, 0, 1);
            if ($firstChar !== strtoupper($firstChar)) {
                deprecationWarning(
                    "The `{$prefix}` prefix did not start with an upper case character. " .
                    'Routing prefixes should be defined as CamelCase values. ' .
                    'Prefix inflection will be removed in 5.0'
                );

                if (strpos($prefix, '/') === false) {
                    $namespace .= '/' . Inflector::camelize($prefix);
                } else {
                    $prefixes = array_map(
                        function ($val) {
                            return Inflector::camelize($val);
                        },
                        explode('/', $prefix)
                    );
                    $namespace .= '/' . implode('/', $prefixes);
                }
            } else {
                $namespace .= '/' . $prefix;
            }
        }
        $firstChar = substr($controller, 0, 1);

        // Disallow plugin short forms, / and \\ from
        // controller names as they allow direct references to
        // be created.
        if (
            strpos($controller, '\\') !== false ||
            strpos($controller, '/') !== false ||
            strpos($controller, '.') !== false ||
            strpos($action, '\\') !== false ||
            strpos($action, '/') !== false ||
            strpos($action, '.') !== false ||
            $firstChar === strtolower($firstChar)
        ) {
            throw $this->missingAction($request);
        }

        return App::className($pluginPath . $controller . '/' . $action, $namespace, 'Action');
    }

    /**
     * @param \Cake\Http\ServerRequest $request The request.
     * @return \Cake\Http\Exception\MissingControllerException
     */
    protected function missingAction(ServerRequest $request)
    {
        return new MissingControllerException([
            'class' => $request->getParam('controller'),
            'plugin' => $request->getParam('plugin'),
            'prefix' => $request->getParam('prefix'),
            '_ext' => $request->getParam('_ext'),
        ]);
    }
}

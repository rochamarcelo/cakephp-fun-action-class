<?php

namespace App\Controller;

use Cake\Controller\ComponentRegistry;
use Cake\Event\EventManagerInterface;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\View\ViewVarsTrait;

/**
 * @property false|mixed|string $name
 */
abstract class Action
{
    use LocatorAwareTrait;
    use ViewVarsTrait;

    public function __construct(
        protected ServerRequest $request,
        protected ?Response $response = null,
        protected ?string $name = null,
        protected ?EventManagerInterface $eventManager = null,
        protected ?ComponentRegistry $components = null
    )
    {
        $this->response = $this->response ?? new Response();
        $this->name = $request->getParam('controller');
    }

    /**
     * Get middleware to be applied for this controller.
     *
     * @return array
     */
    public function getMiddleware(): array
    {
        return [];
    }

    /**
     * @return ServerRequest|null
     */
    public function getRequest(): ?ServerRequest
    {
        return $this->request;
    }

    /**
     * @param ServerRequest|null $request
     */
    public function setRequest(?ServerRequest $request): void
    {
        $this->request = $request;
    }

    /**
     * @return Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * @param Response|null $response
     */
    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return EventManagerInterface|null
     */
    public function getEventManager(): ?EventManagerInterface
    {
        return $this->eventManager;
    }

    /**
     * @param EventManagerInterface|null $eventManager
     */
    public function setEventManager(?EventManagerInterface $eventManager): void
    {
        $this->eventManager = $eventManager;
    }

    public function execute(): Response
    {
        return $this->response;
    }


    /**
     * Instantiates the correct view class, hands it its data, and uses it to render the view output.
     *
     * @param string|null $template Template to use for rendering
     * @param string|null $layout Layout to use
     * @return \Cake\Http\Response A response object containing the rendered view.
     * @link https://book.cakephp.org/4/en/controllers.html#rendering-a-view
     */
    public function render(?string $template = null, ?string $layout = null): Response
    {
        $builder = $this->viewBuilder();
        if (!$builder->getTemplatePath()) {
            $builder->setTemplatePath($this->_templatePath());
        }

        $this->autoRender = false;

        if ($template !== null) {
            $builder->setTemplate($template);
        }

        if ($layout !== null) {
            $builder->setLayout($layout);
        }

        if ($builder->getTemplate() === null) {
            $builder->setTemplate($this->request->getParam('action'));
        }

        $view = $this->createView();
        $contents = $view->render();
        $this->setResponse($view->getResponse()->withStringBody($contents));

        return $this->response;
    }

    /**
     * Get the templatePath based on controller name and request prefix.
     *
     * @return string
     */
    protected function _templatePath(): string
    {
        $templatePath = $this->name;
        if ($this->request->getParam('prefix')) {
            $prefixes = array_map(
                'Cake\Utility\Inflector::camelize',
                explode('/', $this->request->getParam('prefix'))
            );
            $templatePath = implode(DIRECTORY_SEPARATOR, $prefixes) . DIRECTORY_SEPARATOR . $templatePath;
        }

        return $templatePath;
    }
}

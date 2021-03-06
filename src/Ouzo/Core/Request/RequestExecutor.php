<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;

use Ouzo\Controller;
use Ouzo\CookiesSetter;
use Ouzo\DownloadHandler;
use Ouzo\HeaderSender;
use Ouzo\OutputDisplayer;
use Ouzo\RedirectHandler;
use Ouzo\Uri;

class RequestExecutor
{
    /** @var HeaderSender */
    private $headerSender;
    /** @var CookiesSetter */
    private $cookiesSetter;
    /** @var RedirectHandler */
    private $redirectHandler;
    /** @var DownloadHandler */
    private $downloadHandler;
    /** @var OutputDisplayer */
    private $outputDisplayer;

    /**
     * @Inject
     */
    public function __construct(
        HeaderSender $headerSender,
        CookiesSetter $cookiesSetter,
        RedirectHandler $redirectHandler,
        DownloadHandler $downloadHandler,
        OutputDisplayer $outputDisplayer
    )
    {
        $this->headerSender = $headerSender;
        $this->cookiesSetter = $cookiesSetter;
        $this->redirectHandler = $redirectHandler;
        $this->downloadHandler = $downloadHandler;
        $this->outputDisplayer = $outputDisplayer;
    }

    /**
     * @param RequestContext $requestContext
     * @return void
     */
    public function execute(RequestContext $requestContext)
    {
        /** @var Controller $controller */
        $controller = $requestContext->getCurrentControllerObject();

        $this->invokeInit($controller);

        if ($this->invokeBeforeMethods($controller)) {
            $this->invokeAction($controller);
            $this->invokeAfterMethods($controller);
        }

        $this->doActionOnResponse($controller);
    }

    /** @return HeaderSender */
    public function getHeaderSender()
    {
        return $this->headerSender;
    }

    /** @return CookiesSetter */
    public function getCookiesSetter()
    {
        return $this->cookiesSetter;
    }

    /** @return RedirectHandler */
    public function getRedirectHandler()
    {
        return $this->redirectHandler;
    }

    /** @return DownloadHandler */
    public function getDownloadHandler()
    {
        return $this->downloadHandler;
    }

    /** @return OutputDisplayer */
    public function getOutputDisplayer()
    {
        return $this->outputDisplayer;
    }

    /**
     * @param Controller $controller
     * @return void
     */
    private function invokeInit(Controller $controller)
    {
        if (method_exists($controller, 'init')) {
            $controller->init();
        }
    }

    /**
     * @param Controller $controller
     * @return bool
     */
    private function invokeBeforeMethods(Controller $controller)
    {
        foreach ($controller->before as $callback) {
            if (!$this->callCallback($callback, $controller)) {
                return false;
            }
            if ($this->isRedirect($controller)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Controller $controller
     * @return void
     */
    private function invokeAction(Controller $controller)
    {
        $currentAction = $controller->currentAction;

        call_user_func_array([$controller, $currentAction], $controller->getRouteRule()->getParameters());
    }

    /**
     * @param Controller $controller
     * @return void
     */
    private function invokeAfterMethods(Controller $controller)
    {
        foreach ($controller->after as $callback) {
            $this->callCallback($callback, $controller);
        }
    }

    /**
     * @param Controller $controller
     * @return void
     */
    private function doActionOnResponse(Controller $controller)
    {
        $this->headerSender->send($controller->getHeaders());
        $this->cookiesSetter->setCookies($controller->getNewCookies());

        switch ($controller->getStatusResponse()) {
            case 'show':
                $this->renderOutput($controller);
                break;
            case 'redirect':
                $this->redirect($controller->getRedirectLocation());
                break;
            case 'redirectOld':
                $this->redirectHandler->redirect($controller->getRedirectLocation());
                break;
            case 'file':
                session_write_close();
                $this->downloadHandler->downloadFile($controller->getFileData());
                break;
            case 'stream':
                session_write_close();
                $this->downloadHandler->streamMediaFile($controller->getFileData());
                break;
        }
    }

    /**
     * @param mixed $callback
     * @param Controller $controller
     * @return mixed
     */
    private function callCallback($callback, Controller $controller)
    {
        if (is_string($callback)) {
            $callback = [$controller, $callback];
        }

        return call_user_func($callback, $controller);
    }

    /**
     * @param Controller $controller
     * @return bool
     */
    private function isRedirect(Controller $controller)
    {
        return in_array($controller->getStatusResponse(), ['redirect', 'redirectOld']);
    }

    /**
     * @param Controller $controller
     * @return void
     */
    private function renderOutput(Controller $controller)
    {
        ob_start();
        $controller->display();
        $page = ob_get_contents();
        ob_end_clean();
        $this->outputDisplayer->display($page);
    }

    /**
     * @param string $url
     * @return void
     */
    private function redirect($url)
    {
        $this->redirectHandler->redirect(Uri::addPrefixIfNeeded($url));
    }
}

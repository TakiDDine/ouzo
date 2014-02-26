<?php
use Ouzo\Config;
use Ouzo\ControllerUrl;
use Ouzo\I18n;
use Ouzo\Utilities\Date;
use Ouzo\Utilities\Objects;
use Ouzo\Utilities\Strings;
use Ouzo\View;

function url($params)
{
    return ControllerUrl::createUrl($params);
}

function renderWidget($widgetName)
{
    $className = ucfirst($widgetName);
    $viewWidget = new View($className . '/' . $widgetName);

    $classLoad = '\Widget\\' . $className;
    $widget = new $classLoad($viewWidget);

    return $widget->render();
}

function renderPartial($name, array $values = array())
{
    $view = new View($name, $values);
    return $view->render();
}

function addFile(array $fileInfo = array(), $stringToRemove = '')
{
    if (!empty($fileInfo)) {
        $prefixSystem = Config::getValue('global', 'prefix_system');
        $suffixCache = Config::getValue('global', 'suffix_cache');
        $suffixCache = !empty($suffixCache) ? '?' . $suffixCache : '';

        $url = $prefixSystem . $fileInfo['params']['url'] . $suffixCache;
        $url = Strings::remove($url, $stringToRemove);

        return _getHtmlFileTag($fileInfo['type'], $url);
    }
    return null;
}

function _getHtmlFileTag($type, $url)
{
    switch ($type) {
        case 'link':
            return '<link rel="stylesheet" href="' . $url . '" type="text/css" />' . PHP_EOL;
        case 'script':
            return '<script type="text/javascript" src="' . $url . '"></script>' . PHP_EOL;
    }
    return null;
}

function showErrors($errors)
{
    if ($errors) {
        $errorView = new View('error_alert');
        $errorView->errors = $errors;
        return $errorView->render();
    }
}

function showNotices()
{
    if (isset($_SESSION['messages'])) {
        $noticeView = new View('notice_alert');
        $noticeView->notices = $_SESSION['messages'];
        return $noticeView->render();
    }
}

function formatDate($date, $format = 'Y-m-d')
{
    return Date::formatDate($date, $format);
}

function formatDateTime($date, $format = 'Y-m-d H:i')
{
    return Date::formatDateTime($date, $format);
}

function formatDateTimeWithSeconds($date)
{
    return Date::formatDateTime($date, 'Y-m-d H:i:s');
}

function pluralise($count, $words)
{
    return $words[$count == 1 ? 'singular' : 'plural'];
}

function t($textKey, $params = array())
{
    return I18n::t($textKey, $params);
}

function toString($object)
{
    return Objects::toString($object);
}
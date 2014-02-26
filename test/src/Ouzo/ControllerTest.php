<?php
use Ouzo\Controller;
use Ouzo\ControllerFactory;
use Ouzo\Routing\Route;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\ControllerTestCase;
use Ouzo\Utilities\Arrays;

class SimpleTestController extends Controller
{
    public function download()
    {
        $this->downloadFile('file.txt', 'text/plain', '/tmp/file.txt');
    }

    public function params()
    {
        $this->view->params = $this->params;
    }

    public function keep()
    {
        $this->notice(array('Keep this'), true);
    }

    public function read_kept()
    {
        $this->layout->renderAjax(Arrays::firstOrNull($_SESSION['messages']));
        $this->layout->unsetLayout();
    }
}

class ControllerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_frontController->controllerFactory = new ControllerFactory('\\');
        Route::$routes = array();
    }

    /**
     * @test
     */
    public function shouldReturnClassNameInUnderscoreAsDefaultTab()
    {
        //when
        $tab = SimpleTestController::getTab();

        //then
        $this->assertEquals('simple_test', $tab);
    }

    /**
     * @test
     * @covers \Ouzo\DownloadHandler
     */
    public function shouldDownloadFile()
    {
        //given
        Route::get('/simple_test/download', 'simple_test#download');

        //when
        $this->get('/simple_test/download');

        //then
        $this->assertDownloadFile('file.txt');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfMethodDoesNotExist()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');

        //when
        CatchException::when($this)->get('/simple_test/invalid');

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\NoControllerActionException');
    }

    /**
     * @test
     */
    public function shouldParseQueryString()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');

        //when
        $this->get('/simple_test/params?p1=v1&p2=v2');

        //then
        $this->assertEquals(array('p1' => 'v1', 'p2' => 'v2'), $this->getAssigned('params'));
    }

    /**
     * @test
     */
    public function shouldParseQueryStringIfParamHasNoValue()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');

        //when
        $this->get('/simple_test/params?p1');

        //then
        $this->assertEquals(array('p1' => null), $this->getAssigned('params'));
    }

    /**
     * @test
     */
    public function shouldSetEmptyParamsIfNoParameters()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');

        //when
        $this->get('/simple_test/params');

        //then
        $this->assertEmpty($this->getAssigned('params'));
    }

    /**
     * @test
     */
    public function shouldKeepNoticeToNextRequest()
    {
        //given
        Route::allowAll('/simple_test', 'simple_test');
        $this->get('/simple_test/keep');

        //when
        $this->get('/simple_test/read_kept');

        //then
        $this->assertRendersContent('Keep this');
    }
}
<?php
use Ouzo\Controller;
use Ouzo\Tests\ControllerTestCase;

class SampleControllerException extends Exception
{
}

class SampleController extends Controller
{
    function __construct()
    {
        parent::__construct('action');
    }

    public function init()
    {
        $this->before[] = 'beforeAction';
    }

    public function beforeAction()
    {
        return false;
    }

    public function action()
    {
        throw new SampleControllerException("This action shouldn't be called!");
    }
}

class MockControllerResolver
{
    public function getCurrentController()
    {
        return new SampleController();
    }
}

class BeforeFilterTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_frontController->controllerResolver = new MockControllerResolver();
        $this->_frontController->redirectHandler = $this->getMock('\Ouzo\RedirectHandler', array('redirect'));
    }

    /**
     * @test
     */
    public function shouldNotInvokeActionWhenBeforeFilterReturnFalse()
    {
        //when
        try {
            $this->get('/sample/action');
        } catch (SampleControllerException $exception) {
            $this->fail();
        }
    }
}
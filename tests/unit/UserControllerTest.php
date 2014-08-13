<?php

class UserControllerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->mock = Mockery::mock('Eloquent','User');
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    public function test_signup(){

        $input = [
            'email'     => '',
            'password'  => 'password',
            'fname'     => 'First',
            'lname'     => 'Last'
        ];

        $this->mock->shouldReceive('create')->once()->with($input);

        $app = $this->getService('app');

        $app->instance('User', $this->mock);

        $this->call('POST', 'user/signup');

        $this->assertRedirectedToRoute('user/login', ['message']);
    }

}
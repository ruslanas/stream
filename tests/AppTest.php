<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

use Stream\Exception\ForbiddenException;
use Stream\Exception\NotFoundException;

use Stream\App;
use Stream\Cache;
use Stream\Acl;
use Stream\Request;

class AppTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {
        
        $this->app = new App();

        $this->acl = $this->getMockBuilder(Acl::class)->getMock();

        $this->app->uses(['Acl', $this->acl]);
        
        $this->req = $this->getMockBuilder(Request::class)->getMock();
        $this->app->uses(['Request', $this->req]);
        $this->app->uses(new Cache);
    }

    public function testApp() {
        
        App::deleteInstance();

        $app = App::getInstance();
        $this->assertInstanceOf(App::class, $app);
        $this->app = new App(new Cache);
        $this->assertEquals(NULL, $this->app->non_existing_property);
    
    }

    public function testSerialize() {
    
        $res = $this->app->serialize((object)['id'=>1]);
        $this->assertJsonStringEqualsJsonString('{"id":1}', $res);
    
    }

    public function testConnect() {

        $this->app = App::getInstance();
        $this->app->loadConfig();

        $this->assertInstanceOf(App::class, $this->app);

        // expects correct default database setup
        try {
            $this->app->connect();
        } catch(\PDOException $e) {
            $this->markTestIncomplete($e->getMessage());
        }

        $this->assertInstanceOf(PDO::class, $this->app->pdo);

        $this->app->connect('test_stream');
        $this->assertInstanceOf(PDO::class, $this->app->pdo);

        $this->expectException(Exception::class);
        $this->app->connect('not_existing_database_configuration_key');

    }
    
    public function testHook() {
        $this->app->hook('hook.notFound', function() {});
        $this->assertTrue(is_callable($this->app->hook('hook.notFound')), 'Should return callable');
    }

    public function testDispatchNotFoundHook() {
        $ret = '';
        
        $this->app->hook('hook.notFound', function($uri) use (&$ret) {
            $ret = $uri;
        });

        $this->req->method('getMethod')->willReturn('GET');
        $this->acl->method('allow')->willReturn(TRUE);
        $this->app->dispatch('/not_found');

        $this->assertEquals('/not_found', $ret);
    }

    public function testLoadConfig() {

        $conf = $this->app->loadConfig();
        $this->assertArrayHasKey('template_path', $conf);
    
    }

    public function testRest() {
        $this->expectException(Exception::class);
        $this->app->rest('/endpoint', (object)[]);
    }

    public function testDispatchException() {

        $this->expectException(ForbiddenException::class);

        $this->app->dispatch('/');

    }

    public function testDispatchPostMethod() {
        
        $uri = '/controller/action/books/7';
        $data = ['key' => 'value'];

        $this->req->method('getMethod')->willReturn('POST');
        $this->req->method('getPostData')->willReturn($data);

        $this->acl->method('allow')
            ->with('POST', $uri)
            ->willReturn(TRUE);
        
        $this->app->rest(['/controller/action/:category/:id'], \Stream\Test\DummyController::class);

        $output = $this->app->dispatch($uri);

        $decoded = json_decode($output);

        $this->assertEquals('value', $decoded->key);
        $this->assertEquals('books', $decoded->params->category);
        $this->assertEquals('7', $decoded->params->id);

    }

    public function testDispatchPageController() {

        $this->acl->method('allow')->with('GET', '/module/index.html')->willReturn(TRUE);
        $this->req->method('getMethod')->willReturn('GET');

        $this->app->domain('/module/:action.html', \Stream\Test\DummyController::class);
        $output = $this->app->dispatch('/module/index.html');
        $this->assertContains('<!DOCTYPE html>', $output);
    
    }

    public function testDispatchThrowsNotFoundException() {
        
        $this->acl->method('allow')->with('GET', '/module/__construct')->willReturn(TRUE);
        $this->req->method('getMethod')->willReturn('GET');

        $this->app->domain('/module/:action', \Stream\Test\DummyController::class);

        $this->expectException(NotFoundException::class);
        $out = $this->app->dispatch('/module/__construct');

    }

    public function testDispatchThrowsIfNotRestController() {
        $this->acl->method('allow')->with('GET', '/api/books/2')->willReturn(TRUE);
        $this->req->method('getMethod')->willReturn('GET');

        // Passing any existing not RestApi class
        $this->app->rest('/api/books/:id', \Stream\App::class);

        $this->expectException(\Exception::class);
        $this->app->dispatch('/api/books/2');
    }

    public function testDispatchExceptionMessageContainsPath() {

        $this->acl->method('allow')->with('GET', '/module/dispatch')->willReturn(TRUE);
        $this->req->method('getMethod')->willReturn('GET');

        $this->app->domain('/module/:action', \Stream\Test\DummyController::class);

        try {

            $out = $this->app->dispatch('/module/dispatch');
        
        } catch (NotFoundException $e) {
            $msg = $e->getMessage();
        }

        $this->assertContains('`/module/dispatch`', $msg);
    }

    public function testRestSecurity() {

        $this->acl->method('allow')
            ->with('notStandardRequestMethod', '/pi/31415926')
            ->willReturn(TRUE);

        $this->req->method('getMethod')->willReturn('notStandardRequestMethod');

        $this->app->rest('/pi/:number', \Stream\Test\DummyController::class);
        
        $this->expectException(\Stream\Exception\UnknownMethodException::class);
        $this->app->dispatch('/pi/31415926');

        $this->markTestIncomplete("REST security not checked");

    }

    public function testCreateDomainControllerThrowsNotFoundException() {

        $this->expectException(\Stream\Exception\NotFoundException::class);

        $this->app->domain('/foo/bar', NotExisting::class);

        $this->acl->method('allow')->willReturn(TRUE);

        $this->app->dispatch('/foo/bar');
    
    }

    public function testCreateController() {
        
        $this->expectException(\Stream\Exception\NotFoundException::class);
        
        $this->acl->method('allow')->willReturn(TRUE);
        $this->req->expects($this->once())->method('getMethod')->willReturn('GET');

        $this->app->rest('/magic/:number.xml', NotExisting::class);
        $this->app->dispatch('/magic/126.xml');
    }

    /**
     * Uses ReflectionClass to invoke protected method Stream\App::match
     */
    public function testMatch() {
        
        $cls = new ReflectionClass(App::class);

        $meth = $cls->getMethod('match');
        $meth->setAccessible('true');

        $params = $meth->invokeArgs($this->app, ['/foo/:foo/bar/:bar/baz/:baz', '/foo/10/bar/20/baz/baz?boo=boo']);
        
        $this->assertTrue(is_array($params), 'Should return associative array of URL parameters');

        $this->assertEquals($params['foo'], 10);
        $this->assertEquals($params['bar'], 20);
        $this->assertEquals($params['baz'], 'baz');

        $params = $meth->invokeArgs($this->app, ['/foo/:foo/bar/:bar/baz/:baz', '/foo/10/bar/20/baz/baz/']);
        $this->assertTrue(is_array($params));

        $params = $meth->invokeArgs($this->app, ['/foo/:foo/bar/', '/foo/10/bar/20/baz/baz?boo=boo']);
        $this->assertEquals(FALSE, $params);

        $params = $meth->invokeArgs($this->app, ['/', '?boo=boo']);
        $this->assertTrue(is_array($params), 'Array expected');

        $params = $meth->invokeArgs($this->app, ['~:user', '~Tom']);
        $this->assertEquals('Tom', $params['user']);

    }

    public function testGet() {

        $ret = [];
        
        $this->acl->method('allow')->willReturn(TRUE);

        $this->req->method('getHeaders')->willReturn([]);
        $this->req->method('getMethod')->willReturn('GET');

        $this->app->get('/client/:name', function($params) use (&$ret) {
            $ret = $params;
        });

        $this->app->dispatch('/client/Name');

        $this->assertEquals('Name', $ret['name']);

    }
}

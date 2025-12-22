<?php

use PHPUnit\Framework\TestCase;

class SessionManagerTest extends TestCase
{
    private $session;

    protected function setUp(): void
    {
        // Reset the singleton instance if possible, or just use it
        // Note: Real sessions are tricky in CLI, but our SessionManager
        // uses the $_SESSION superglobal which we can mock in the bootstrap.
        $this->session = SessionManager::getInstance();
        $_SESSION = []; // Clear mock session before each test
    }

    public function testSingletonInstance()
    {
        $instance1 = SessionManager::getInstance();
        $instance2 = SessionManager::getInstance();
        $this->assertSame($instance1, $instance2);
    }

    public function testSetAndGet()
    {
        $this->session->set('user_id', 123);
        $this->assertEquals(123, $this->session->get('user_id'));
        $this->assertEquals(123, $_SESSION['user_id']);
    }

    public function testHas()
    {
        $this->assertFalse($this->session->has('key'));
        $this->session->set('key', 'value');
        $this->assertTrue($this->session->has('key'));
    }

    public function testRemove()
    {
        $this->session->set('key', 'value');
        $this->session->remove('key');
        $this->assertFalse($this->session->has('key'));
        $this->assertNull($this->session->get('key'));
    }

    public function testFlashMessaging()
    {
        // Flash messages should use the FlashMessage class, but SessionManager 
        // is the underlying storage.
        $this->session->set('flash_msg', 'Hello');
        $this->assertEquals('Hello', $this->session->get('flash_msg'));
    }
}

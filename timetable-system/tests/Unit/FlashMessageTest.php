<?php

use PHPUnit\Framework\TestCase;

class FlashMessageTest extends TestCase
{
    private $flash;
    private $session;

    protected function setUp(): void
    {
        $this->session = SessionManager::getInstance();
        $_SESSION = [];
        $this->flash = new FlashMessage($this->session);
    }

    public function testSetAndGet()
    {
        $this->flash->set('Operation successful', 'success');
        $flash = $this->flash->get();
        
        $this->assertNotNull($flash);
        $this->assertEquals('Operation successful', $flash['message']);
        $this->assertEquals('success', $flash['type']);
        
        // After get, flash message should be cleared
        $this->assertNull($this->flash->get());
    }

    public function testHasFlash()
    {
        $this->assertFalse($this->flash->hasFlash());
        $this->flash->set('Alert!', 'warning');
        $this->assertTrue($this->flash->hasFlash());
    }

    public function testGetReturnsNullIfNone()
    {
        $this->assertNull($this->flash->get());
    }
}

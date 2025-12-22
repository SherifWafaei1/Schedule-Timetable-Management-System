<?php

use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    private $auth;
    private $db;
    private $session;

    protected function setUp(): void
    {
        $this->db = $this->createMock(PDO::class);
        $this->session = SessionManager::getInstance();
        $_SESSION = [];
        $this->auth = new AuthService($this->db);
    }

    public function testLoginWithInvalidUser()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(false);

        $this->db->method('prepare')->willReturn($stmt);

        $this->assertFalse($this->auth->login('wronguser', 'password'));
    }

    public function testLogout()
    {
        $_SESSION['user_id'] = 1;
        $this->auth->logout();
        $this->assertNull($this->session->get('user_id'));
    }
}

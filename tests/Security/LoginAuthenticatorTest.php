<?php

namespace App\Tests\Security;

use App\Security\LoginAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class LoginAuthenticatorTest extends TestCase
{
    private $urlGenerator;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
    }

    public function testAuthenticate(): void
    {
        $request = new Request([], ['email' => 'test@example.com', 'password' => 'password']);
        $request->setSession(new Session(new MockArraySessionStorage()));

        $loginAuthenticator = new LoginAuthenticator($this->urlGenerator);
        $passport = $loginAuthenticator->authenticate($request);

        $this->assertInstanceOf(Passport::class, $passport);
        $this->assertInstanceOf(UserBadge::class, $passport->getBadge(UserBadge::class));
        $this->assertEquals('test@example.com', $passport->getBadge(UserBadge::class)->getUserIdentifier());
    }
}
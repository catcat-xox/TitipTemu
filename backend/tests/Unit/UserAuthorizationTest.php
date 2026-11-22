<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class UserAuthorizationTest extends TestCase
{
    public function test_user_role_is_not_admin(): void
    {
        $user = User::factory()->make();

        $this->assertTrue($user->isUser());
        $this->assertFalse($user->isAdmin());
        $this->assertTrue(Gate::forUser($user)->allows('user'));
        $this->assertFalse(Gate::forUser($user)->allows('admin'));
    }

    public function test_admin_accounts_are_separate_from_user_accounts(): void
    {
        $firstAdmin = Admin::factory()->make();
        $secondAdmin = Admin::factory()->make();
        $user = User::factory()->make();

        $this->assertInstanceOf(Admin::class, $firstAdmin);
        $this->assertNotInstanceOf(User::class, $firstAdmin);
        $this->assertNotSame($firstAdmin->email, $secondAdmin->email);
        $this->assertFalse(method_exists($firstAdmin, 'isUser'));
    }

    public function test_legacy_user_admin_role_remains_backward_compatible(): void
    {
        $firstAdmin = User::factory()->admin()->make();
        $secondAdmin = User::factory()->admin()->make();

        $this->assertTrue($firstAdmin->isAdmin());
        $this->assertTrue($secondAdmin->isAdmin());
        $this->assertTrue(Gate::forUser($firstAdmin)->allows('admin'));
        $this->assertTrue(Gate::forUser($secondAdmin)->allows('admin'));
        $this->assertFalse(Gate::forUser($firstAdmin)->allows('user'));
    }
}

<?php

namespace Tests\Unit;

use Tests\TestCase;

class ApplicationConfigTest extends TestCase
{
    public function test_application_name_is_configured(): void
    {
        $this->assertSame('TitipTemu', config('app.name'));
    }

    public function test_application_uses_wita_timezone(): void
    {
        $this->assertSame('Asia/Makassar', config('app.timezone'));
        $this->assertSame('WITA', now()->format('T'));
    }

    public function test_application_locale_is_indonesian(): void
    {
        $this->assertSame('id', app()->getLocale());
    }
}

<?php

namespace Tests\Unit\App\Models;

use Tests\TestCase;
use App\Models\FundamentalsSpecificFundamentals;
use Spatie\Activitylog\LogOptions;

class FundamentalsSpecificFundamentalsTest extends TestCase
{
    /**
     * A basic unit test relation getActivitylogOptions.
     *
     * @return void
     */
    public function test_get_activitylog_options()
    {
        $user = new FundamentalsSpecificFundamentals();
        $this->assertInstanceOf(LogOptions::class, $user->getActivitylogOptions());
    }
}
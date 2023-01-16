<?php

namespace Tests\Unit\App\Observers;

use App\Models\Training;
use App\Observers\TrainingObserver;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;

class TrainingObserverTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        config(['database.redis.default.connection' => 'disable']);
    }
    /**
     * Test created method
     *
     * @test
     *
     * @return void
     */
    public function created()
    {
        $trainingMock = $this->mock(Training::class, function ($mock) {
            $mock->shouldReceive('sendNotificationPlayers')
                ->once()
                ->andReturn(true);
        });
        Mail::fake();
        $trainingObserver = new TrainingObserver();
        $trainingObserver->created($trainingMock);
    }

    /**
     * Test updated method
     *
     * @test
     *
     * @return void
     */
    public function updated()
    {
        $trainingMock = $this->mock(Training::class, function ($mock) {
            $mock->shouldReceive('sendNotificationPlayers')
                ->once()
                ->andReturn(true);
        });
        Mail::fake();

        $trainingObserver = new TrainingObserver();
        $trainingObserver->updated($trainingMock);
    }
}
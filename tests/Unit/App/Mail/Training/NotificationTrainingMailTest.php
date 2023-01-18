<?php

namespace Tests\Unit\App\Mail\Training;

use Tests\TestCase;
use App\Mail\Training\NotificationTrainingMail;
use App\Models\Training;
use App\Models\User;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class NotificationTrainingMailTest extends TestCase
{
    /**
     * A method to test the envelope.
     * @test
     * @return void
     */
    public function envelope()
    {
        $trainingMock = $this->mock(Training::class, function ($mock) {
            $mock->shouldReceive('getAttribute')->with('date_start')->andReturn(
                \Carbon\Carbon::parse('2020-01-01 00:00:00')
            );
            $mock->shouldReceive('getAttribute')->with('date_end')->andReturn(
                \Carbon\Carbon::parse('2020-01-01 00:00:00')
            );
            $mock->shouldReceive('getAttribute')->with('name')->andReturn(
                'Test name'
            );
        });

        $userMock = $this->createMock(User::class);
        $mail = new NotificationTrainingMail($trainingMock, $userMock);
        $envelope = $mail->envelope();

        $this->assertInstanceOf(Envelope::class, $envelope);
    }

    /**
     * A method to test the content.
     * @test
     * @return void
     */
    public function content()
    {
        $trainingMock = $this->mock(Training::class, function ($mock) {
            $mock->shouldReceive('getAttribute')->with('date_start')->andReturn(
                \Carbon\Carbon::parse('2020-01-01 00:00:00')
            );
            $mock->shouldReceive('getAttribute')->with('date_end')->andReturn(
                \Carbon\Carbon::parse('2020-01-01 00:00:00')
            );
            $mock->shouldReceive('getAttribute')->with('name')->andReturn(
                'Test name'
            );
        });

        $userMock = $this->createMock(User::class);
        $mail = new NotificationTrainingMail($trainingMock, $userMock);
        $content = $mail->content();

        $this->assertInstanceOf(Content::class, $content);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

class DailyScheduleTest extends TestCase
{
    public function test_welcome_page_renders_daily_schedule_mount_point(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Daily Schedule');
        $response->assertSee('data-component="daily-schedule"', false);
    }

    public function test_daily_schedule_api_returns_expected_shape(): void
    {
        $response = $this->getJson(
            route('api.schedule.daily', [
                'date' => '2026-04-08',
                'user_id' => 7,
            ]),
        );

        $response->assertOk();
        $response->assertJson([
            'date' => '2026-04-08',
            'userId' => 7,
        ]);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'date',
            'userId',
            'data' => [
                '*' => [
                    'id',
                    'patientName',
                    'status',
                    'appointmentTime',
                    'timeLabel',
                ],
            ],
        ]);
    }
}

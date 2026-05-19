<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyScheduleController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'user_id' => ['nullable', 'integer'],
        ]);

        $scheduleDate = CarbonImmutable::createFromFormat(
            'Y-m-d',
            $validated['date'] ?? now()->format('Y-m-d'),
            config('app.timezone'),
        );

        $appointments = collect([
            [
                'id' => 1,
                'patientName' => 'Alice Johnson',
                'status' => 'scheduled',
                'time' => '09:00',
            ],
            [
                'id' => 2,
                'patientName' => 'Bruno Silva',
                'status' => 'checked-in',
                'time' => '10:30',
            ],
            [
                'id' => 3,
                'patientName' => 'Carla Souza',
                'status' => 'completed',
                'time' => '14:00',
            ],
        ])->map(function (array $appointment) use ($scheduleDate) {
            $appointmentDateTime = $scheduleDate->setTimeFromTimeString(
                $appointment['time'],
            );

            return [
                'id' => $appointment['id'],
                'patientName' => $appointment['patientName'],
                'status' => $appointment['status'],
                'appointmentTime' => $appointmentDateTime->toIso8601String(),
                'timeLabel' => $appointmentDateTime->format('H:i'),
            ];
        })->values();

        return response()->json([
            'date' => $scheduleDate->format('Y-m-d'),
            'userId' => isset($validated['user_id'])
                ? (int) $validated['user_id']
                : null,
            'data' => $appointments,
        ]);
    }
}

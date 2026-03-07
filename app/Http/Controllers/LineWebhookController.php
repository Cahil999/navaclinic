<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $events = $request->input('events', []);

        foreach ($events as $event) {
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $replyToken = $event['replyToken'];
                $text = trim($event['message']['text']);
                $lineId = $event['source']['userId'];

                $user = User::where('line_id', $lineId)->first();

                if ($user) {
                    // Linked user
                    if ($text === 'วันนัด') {
                        $this->sendBookings($replyToken, $user);
                    } elseif ($text === 'จอง' || $text === 'จองคิว') {
                        $this->replyMessage($replyToken, "สามารถจองคิวได้ที่ลิงก์นี้เลยครับ 👇\n" . url('/booking'));
                    }
                } else {
                    // Not linked user
                    if ($text === 'วันนัด') {
                        $this->replyMessage($replyToken, 'ยังไม่มีข้อมูลในระบบ กรุณากรอกเบอร์โทรศัพท์เพื่อเชื่อมบัญชีที่มีในระบบ');
                    } elseif ($text === 'จอง' || $text === 'จองคิว') {
                        $this->replyMessage($replyToken, "สามารถจองคิวได้ที่ลิงก์นี้เลยครับ 👇\n" . url('/booking'));
                    } else {
                        // Check if text could be a phone number (e.g., 9-10 digits)
                        $phone = preg_replace('/[^0-9]/', '', $text);
                        if (strlen($phone) >= 9) {
                            $foundUser = User::where('phone_number', $phone)
                                ->orWhere('phone_number', $text)
                                ->first();

                            if ($foundUser) {
                                $foundUser->line_id = $lineId;
                                $foundUser->save();
                                // Bind existing guest bookings to this user
                                Booking::where('customer_phone', $phone)
                                    ->orWhere('customer_phone', $text)
                                    ->whereNull('user_id')
                                    ->update(['user_id' => $foundUser->id]);

                                $this->replyMessage($replyToken, 'ลงทะเบียนไลน์เรียบร้อย กรุณาพิมพ์ "วันนัด" อีกรอบ');
                            } else {
                                // Check if this phone number exists in guest bookings
                                $booking = Booking::where('customer_phone', $phone)
                                    ->orWhere('customer_phone', $text)
                                    ->orderBy('id', 'desc')
                                    ->first();

                                if ($booking) {
                                    // Create a new user for this guest
                                    $newUser = User::create([
                                        'name' => $booking->customer_name ?? 'LINE User',
                                        'phone_number' => $phone,
                                        'email' => 'line_' . $lineId . '@navaclinic.local',
                                        'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                                        'line_id' => $lineId,
                                    ]);

                                    // Map old guest bookings to this new user
                                    Booking::where('customer_phone', $phone)
                                        ->orWhere('customer_phone', $text)
                                        ->update(['user_id' => $newUser->id]);

                                    $this->replyMessage($replyToken, 'ลงทะเบียนไลน์เรียบร้อย กรุณาพิมพ์ "วันนัด" อีกรอบ');
                                } else {
                                    $this->replyMessage($replyToken, 'ไม่พบเบอร์โทรศัพท์นี้ในระบบ กรุณาตรวจสอบอีกครั้ง หรือติดต่อเจ้าหน้าที่ครับ');
                                }
                            }
                        }
                    }
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function sendBookings($replyToken, User $user)
    {
        // Fetch upcoming bookings for this user
        $bookings = Booking::where('user_id', $user->id)
            ->where('appointment_date', '>=', now()->format('Y-m-d'))
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        if ($bookings->isEmpty()) {
            $this->replyMessage($replyToken, "คุณไม่มีวันนัดที่กำลังจะมาถึงครับ\n\nสามารถจองคิวใหม่ได้ที่ลิงก์นี้เลยครับ 👇\n" . url('/booking'));
            return;
        }

        $contents = [];

        foreach ($bookings as $index => $booking) {
            if ($index > 0) {
                // Add a separator for multiple bookings
                $contents[] = [
                    'type' => 'separator',
                    'margin' => 'lg',
                    'color' => '#E5E7EB'
                ];
            }

            // Convert to Thai Buddhist Era year
            $date = \Carbon\Carbon::parse($booking->appointment_date)->addYears(543)->format('d/m/Y');
            $time = \Carbon\Carbon::parse($booking->start_time)->format('H:i');

            $statusText = 'รอการยืนยัน';
            $statusColor = '#F59E0B'; // Amber
            if ($booking->status === 'confirmed') {
                $statusText = 'ยืนยันแล้ว';
                $statusColor = '#10B981'; // Emerald
            }

            $contents[] = [
                'type' => 'box',
                'layout' => 'vertical',
                'margin' => 'lg',
                'spacing' => 'md',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => "🗓️ วันที่ {$date}",
                        'weight' => 'bold',
                        'size' => 'md',
                        'color' => '#1F2937'
                    ],
                    [
                        'type' => 'box',
                        'layout' => 'baseline',
                        'spacing' => 'sm',
                        'contents' => [
                            [
                                'type' => 'text',
                                'text' => 'เวลา',
                                'color' => '#6B7280',
                                'size' => 'sm',
                                'flex' => 1
                            ],
                            [
                                'type' => 'text',
                                'text' => "{$time} น.",
                                'color' => '#111827',
                                'size' => 'sm',
                                'flex' => 3,
                                'weight' => 'bold'
                            ]
                        ]
                    ],
                    [
                        'type' => 'box',
                        'layout' => 'baseline',
                        'spacing' => 'sm',
                        'contents' => [
                            [
                                'type' => 'text',
                                'text' => 'สถานะ',
                                'color' => '#6B7280',
                                'size' => 'sm',
                                'flex' => 1
                            ],
                            [
                                'type' => 'text',
                                'text' => $statusText,
                                'color' => $statusColor,
                                'size' => 'sm',
                                'flex' => 3,
                                'weight' => 'bold'
                            ]
                        ]
                    ]
                ]
            ];
        }

        $flexMessage = [
            'type' => 'flex',
            'altText' => 'ข้อมูลวันนัดหมายของคุณ ขอดูได้ในแชทครับ',
            'contents' => [
                'type' => 'bubble',
                'body' => [
                    'type' => 'box',
                    'layout' => 'vertical',
                    'paddingAll' => '0px',
                    'contents' => [
                        [
                            'type' => 'box',
                            'layout' => 'vertical',
                            'backgroundColor' => '#0284C7', // Sky Blue 600
                            'paddingAll' => '20px',
                            'contents' => [
                                [
                                    'type' => 'text',
                                    'text' => 'Nava Clinic',
                                    'color' => '#E0F2FE',
                                    'size' => 'sm',
                                    'weight' => 'bold'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => '📅 วันนัดหมายของคุณ',
                                    'color' => '#FFFFFF',
                                    'size' => 'xl',
                                    'weight' => 'bold',
                                    'margin' => 'md'
                                ]
                            ]
                        ],
                        [
                            'type' => 'box',
                            'layout' => 'vertical',
                            'paddingAll' => '20px',
                            'contents' => $contents
                        ]
                    ]
                ],
                'footer' => [
                    'type' => 'box',
                    'layout' => 'vertical',
                    'spacing' => 'sm',
                    'contents' => [
                        [
                            'type' => 'button',
                            'style' => 'primary',
                            'height' => 'sm',
                            'color' => '#0284C7',
                            'action' => [
                                'type' => 'uri',
                                'label' => 'จองคิว',
                                'uri' => url('/booking')
                            ]
                        ]
                    ],
                    'flex' => 0
                ]
            ]
        ];

        $this->replyFlexMessage($replyToken, $flexMessage);
    }

    private function replyFlexMessage($replyToken, $flexMessage)
    {
        $accessToken = env('LINE_CHANNEL_ACCESS_TOKEN');

        if (!$accessToken) {
            Log::error('LINE_CHANNEL_ACCESS_TOKEN is not set.');
            return;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post('https://api.line.me/v2/bot/message/reply', [
                    'replyToken' => $replyToken,
                    'messages' => [$flexMessage]
                ]);

        if ($response->failed()) {
            Log::error('LINE Flex Reply Failed: ' . $response->body());
        }
    }

    private function replyMessage($replyToken, $text)
    {
        $accessToken = env('LINE_CHANNEL_ACCESS_TOKEN');

        if (!$accessToken) {
            Log::error('LINE_CHANNEL_ACCESS_TOKEN is not set.');
            return;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post('https://api.line.me/v2/bot/message/reply', [
                    'replyToken' => $replyToken,
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => $text,
                        ]
                    ]
                ]);

        if ($response->failed()) {
            Log::error('LINE Reply Failed: ' . $response->body());
        }
    }
}

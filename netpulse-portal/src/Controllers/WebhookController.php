<?php

namespace App\Controllers;

use App\Services\TicketService;
use Exception;

class WebhookController 
{
    private TicketService $ticketService;

    // حقن الاعتماديات (Dependency Injection) لخدمة التذاكر
    public function __construct(TicketService $ticketService) 
    {
        $this->ticketService = $ticketService;
    }

    /**
     * معالجة طلب الـ Webhook الوارد من نظام الـ Java (NOC)
     */
    public function handle(): void 
    {
        // 1. التأكد من أن الطلب يستخدم طريقة POST فقط
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(405, [
                "status" => "error", 
                "message" => "Method Not Allowed. Only POST requests are accepted."
            ]);
        }

        // 2. قراءة بيانات الـ JSON القادمة من الطلب
        $jsonPayload = file_get_contents("php://input");
        $data = json_decode($jsonPayload, true);

        // التحقق من صحة فك التشفير وأن البيانات مصفوفة صالحة
        if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
            $this->respond(400, [
                "status" => "error", 
                "message" => "Invalid JSON payload provided."
            ]);
        }

        // 3. التحقق من وجود الحقول الأساسية المطلوبة
        if (empty($data['title']) || empty($data['description']) || empty($data['priority'])) {
            $this->respond(400, [
                "status" => "error", 
                "message" => "Validation Error: Missing required fields (title, description, priority)."
            ]);
        }

        try {
            // 4. تنقية وتجهيز البيانات
            $payload = [
                'title'       => trim($data['title']),
                'description' => trim($data['description']),
                'priority'    => strtoupper(trim($data['priority'])),
                'incident_id' => !empty($data['incident_id']) ? (int)$data['incident_id'] : null
            ];

            // 5. استدعاء طبقة الخدمة لإنشاء التذكرة آلياً
            $newTicketId = $this->ticketService->generateAutomatedTicket($payload);
            
            // جلب بيانات التذكرة لإرجاع رقم التذكرة للمصدر
            $ticketDetails = $this->ticketService->getTicketDetails($newTicketId);
            $ticketNumber = $ticketDetails ? $ticketDetails->ticket_number : "TKT-UNKNOWN";

            // 6. الرد بنجاح العملية (HTTP 201 Created)
            $this->respond(201, [
                "status"        => "success",
                "ticket_number" => $ticketNumber,
                "message"       => "Automated ticket generated successfully."
            ]);

        } catch (Exception $e) {
            // 7. التعامل مع الاستثناءات وتسجيل الأخطاء في سجلات الخادم
            error_log("Webhook Error: " . $e->getMessage());
            
            $this->respond(500, [
                "status"  => "error", 
                "message" => "Internal Server Error while processing the webhook."
            ]);
        }
    }

    /**
     * دالة مساعدة لتوحيد تنسيق استجابات الـ API بصيغة JSON
     */
    private function respond(int $statusCode, array $data): void 
    {
        http_response_code($statusCode);
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($data);
        exit;
    }
}
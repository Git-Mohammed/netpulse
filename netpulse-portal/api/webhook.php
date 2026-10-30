<?php
/**
 * NetPulse Webhook API
 * Receives automated alerts from the central Java (NOC) engine.
 */

// 1. إعداد ترويسات الاستجابة (Headers) لقبول JSON فقط
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// 2. التأكد من أن الطلب هو POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed"]);
    exit;
}

// 3. قراءة البيانات القادمة من Java
$jsonPayload = file_get_contents("php://input");
$data = json_decode($jsonPayload, true);

// 4. التحقق من وجود الحقول الأساسية المطلوبة
if (empty($data['title']) || empty($data['description']) || empty($data['priority'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error", 
        "message" => "Missing required fields (title, description, priority)"
    ]);
    exit;
}

// 5. استدعاء طبقة الخدمات (Service Layer) لمعالجة الإدخال
require_once __DIR__ . '/../src/Services/TicketService.php';

try {
    $ticketService = new TicketService();

    // تجهيز مصفوفة البيانات (Payload) بنفس الطريقة المستخدمة في TicketController
    $payload = [
        'title'       => trim($data['title']),
        'description' => trim($data['description']),
        'priority'    => strtoupper(trim($data['priority'])),
        'incident_id' => !empty($data['incident_id']) ? (int)$data['incident_id'] : null
    ];

    // 6. إنشاء التذكرة 
    $newTicketId = $ticketService->generateAutomatedTicket($payload);
    
    // جلب تفاصيل التذكرة للحصول على رقم التذكرة المقروء (ticket_number)
    $ticketDetails = $ticketService->getTicketDetails($newTicketId);
    $ticketNumber = $ticketDetails ? $ticketDetails->ticket_number : "TKT-UNKNOWN";

    // 7. إرسال استجابة النجاح (HTTP 201 Created) إلى نظام الـ Java
    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "ticket_number" => $ticketNumber,
        "message" => "Ticket generated successfully from central NOC engine."
    ]);
    
} catch (Exception $e) {
    // 8. التعامل مع الأخطاء (HTTP 500)
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Internal Server Error: " . $e->getMessage()
    ]);
}


```yaml
---
title: NetPulse Hardware Alert Console (Physical Desk Alarm)
project: NetPulse Enterprise NOC
type: Hardware Extension Specification
date: 2026-08-20
tags: [NOC, IoT, ESP32, NetPulse, SystemDesign, Embedded]
status: Architecture Approved
---
```

# وحدة التنبيه المادية المكتبية (NetPulse Physical Desk Alarm)

## 📌 1. نظرة عامة ورؤية وحدة التنبيه (Concept Overview)

تُعتبر **وحدة التنبيه المادية المكتبية (NetPulse Physical Desk Alarm)** ملحقاً مادياً (Hardware Companion) ينقل مراقبة الأحداث الحرجة من الشاشات البرمجية إلى بيئة مهندس الشبكات المادية مباشرة.


بدلاً من الاعتماد الحصري على فتح شاشات المراقب (Dashboards)، تعمل هذه الوحدة المعتمدة على شريحة **ESP32** كجهاز إنذار مكتبي مادي (Ambient Desk Alert Node) يتصل لاسلكياً بالباك إند المركزي (Java Receiver Engine)، ويترجم الأعطال وسقوط الأجهزة أو ارتفاع الحرارة الافتراضية داخل **Cisco Packet Tracer** إلى استجابات مادية فورية (مرئية وصوتية).
  
## 🏗️ 2. معمارية وتدفق البيانات (Data Flow Architecture)

يعتمد التصميم على نمط **التبريد/السحب الهابط (Downstream Polling/Stream)**، حيث يعمل الأردوينو/ESP32 كمستهلك بيانات فقط (Telemetry Consumer) دون الحاجة لربط مستشعرات مادية به.

  

```
┌────────────────────────────────┐
│   Cisco Packet Tracer Environment│
│   (Virtual Routers / Switches) │
└───────────────┬────────────────┘
                │ Telemetry Stream (Python Client)
                ▼
┌────────────────────────────────┐
│      NetPulse Java Engine      │
│   (Central Processing & Rules) │
└───────────────┬────────────────┘
                │ REST API / Json State (HTTP GET)
                ▼
┌────────────────────────────────┐
│   ESP32 Physical Desk Alarm    │
│  (LCD + RGB LED + Active Buzzer)│
└────────────────────────────────┘
```

1. **الالتقاط الافتراضي (Ingestion):** يراقب سكريبت Python شبكة Packet Tracer ويرسل مؤشرات الأداء (الـ Latency والحرارة وحالة الاتصال) إلى خادم Java.
    
      
    
2. **المعالجة والتقييم (Evaluation):** يحلل خادم Java البيانات ويحدد مستوى الخطر الحركي (Normal, Warning, Critical).
    
      
    
3. **الاستهلاك المادي (Consumption):** يستعلم جهاز ESP32 عبر الـ Wi-Fi دورياً (كل 1-2 ثانية) عن ملخص حالة النظام من خادم Java.
    
      
    
4. **التفعيل الفيزيائي (Physical Actuation):** يترجم ESP32 الاستجابة فوراً إلى الشاشة المادية، الأضواء، والإنذار الصوتي.
    
      
    

## 🚦 3. مصفوفة حالات الإنذار والاستجابة (Alert Matrix)

|**حالة النظام (System State)**|**الشرط داخل Packet Tracer**|**مؤشر LED**|**جرس الإنذار (Buzzer)**|**شاشة الـ LCD المادية**|
|---|---|---|---|---|
|**NORMAL**|جميع الأجهزة `UP` والحرارة $< 34^\circ\text{C}$|🟢 أخضر ثابت|صامت (OFF)|`SYS: OPERATIONAL`<br><br>  <br>  <br><br>`All Nodes: OK`|
|**WARNING**|ارتفاع Latency $> 20\text{ms}$ أو الحرارة $\ge 34^\circ\text{C}$|🟡 أصفر ثابت|نغمة متقطعة بطيئة (Beep... Beep)|`WARN: Router_R1`<br><br>  <br>  <br><br>`High Temp: 36.5C`|
|**CRITICAL**|سقوط جهاز `Status: DOWN`|🔴 أحمر وامض|إنذار متصل سريع (Alarm)|`CRITICAL ALERT!`<br><br>  <br>  <br><br>`Node: MultiSW_1 DOWN`|

## 🛠️ 4. مواصفات العتاد والبدائل (Hardware Specifications)

> [!tip] تصميم مبسط وخالٍ من التعقيد
> 
> تم الاستغناء عن مستشعرات الحرارة المادية على اللوحة، لتركيز دور العتاد كشاشة تنبيه وتفاعل فقط.
> 
>   

### القطع الأساسية (Primary Hardware)

- **المتحكم:** **ESP32 NodeMCU** (بسبب وجود Wi-Fi مدمج ومعالج مزدوج بسعر اقتصادي).
    
- **شاشة العرض:** **LCD 16x2 مع محول I2C** (لعرض النصوص وتفاصيل العطل بأقل عدد من الأسلاك).
    
- **التنبيه الصوتي:** **Active Buzzer 5V** (للتنبيه الصوتي المباشر عند الأعطال).
    
- **التنبيه البصري:** **RGB LED** أو 3 لمبات LED منفصلة (أخضر، أصفر، أحمر) مع مقاومات حماية $220\,\Omega$.
    

### جدول البدائل العتادية المرنة (Pluggable Alternatives)

|**المكون الأساسي**|**البديل المقترح**|**دواعي استخدام البديل**|
|---|---|---|
|**ESP32**|NodeMCU ESP8266|خيار أرخص ومتاح بشكل أوسع، يفي بنفس الغرض بنفس كفاءة الاتصال بـ Wi-Fi.|
|**ESP32**|Arduino Uno + W5100 Ethernet Shield|في حال رغبت بإنشاء وحدة تنبيه مكتبي **سلكية (Wired LAN)** بدلاً من Wi-Fi.|
|**LCD 16x2 I2C**|OLED Display 0.96" (I2C)|إذا كنت تفضل حجماً أصغر بدقة عرض أعلى ومظهر مدرن (Modern Minimalist).|

## 📐 5. القرارات الهندسية والتبريرات (Design Decisions)

### 1. فصل مصادر البيانات عن آليات التنبيه (Separation of Concerns)

- **القرار:** عدم تركيب أي مستشعرات بيئية حقيقية على وحدة الأردوينو/ESP32.
    
      
    
- **التبرير:** العقدة المادية هدفها التفاعل مع أحداث شبكة Cisco الافتراضية؛ لذا فربط إنذار الميكروكنترولر بقراءات Packet Tracer يضمن اتساق منطق النظام (Logical Consistency)، بحيث تكون شاشة المكتب انعكاساً دقيقاً لما يحدث داخل التوبولوجيا الافتراضية دون خلط مع بيئة الغرفة الحقيقية.
    
      
    

### 2. بروتوكول I2C لشاشة العرض

- **القرار:** اعتماد شاشة LCD مدعومة بمحول I2C.
    
      
    
- **التبرير:** يقلل عدد خطوط الاتصال باللوحة من 8-12 سلكاً إلى خطين فقط (`SDA` و `SCL`) بالإضافة لخطوط الطاقة. هذا يمنح الجهاز المكتبي حجماً مدمجاً ومظهراً هندسياً منظماً (Clean Wiring Layout).
    
      
    

### 3. الاعتماد على C++ لتطوير البرمجيات الدقيقة (Firmware)

- **القرار:** كتابة كود اللوحة بلغة **C++** عبر Arduino IDE / PlatformIO.
    
      
    
- **التبرير:** تضمن C++ إدارة ممتازة لذاكرة الـ RAM المحدودة في اللوحة، وتمنع انقطاع الاتصال بالنظام (Connection Timeout) أثناء طلبات الـ HTTP المستمرة، مما يحافظ على استجابة الإنذار الصوتي والمرئي في زمن حقيقي (Real-Time Responsiveness).
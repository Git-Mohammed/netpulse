package com.netpulse.controller;

import com.netpulse.dto.EventProcessingResponse;
import com.netpulse.dto.TelemetryEventRequest;
import com.netpulse.service.EventProcessingService;

import jakarta.validation.Valid;

import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

/**
 * REST entry point used by the Python Bridge.
 */
@RestController
@RequestMapping("/api/events")
public class EventController {

    private final EventProcessingService eventProcessingService;

    public EventController(EventProcessingService eventProcessingService) {
        this.eventProcessingService = eventProcessingService;
    }

    @PostMapping
    public ResponseEntity<EventProcessingResponse> receiveEvent(
            @Valid @RequestBody TelemetryEventRequest request) {

        return ResponseEntity.ok(eventProcessingService.process(request));
    }
}

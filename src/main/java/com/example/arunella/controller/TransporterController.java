package com.example.arunella.controller;

import com.example.arunella.entity.Transporter;
import com.example.arunella.service.TransporterService;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/transporters")
public class TransporterController {

    private final TransporterService transporterService;

    public TransporterController(TransporterService transporterService) {
        this.transporterService = transporterService;
    }

    // CREATE
    @PostMapping
    public ResponseEntity<Transporter> createTransporter(@RequestBody Transporter transporter) {
        Transporter saved = transporterService.saveTransporter(transporter);
        return new ResponseEntity<>(saved, HttpStatus.CREATED);
    }

    // READ ALL
    @GetMapping
    public ResponseEntity<List<Transporter>> getAllTransporters() {
        List<Transporter> transporters = transporterService.getAllTransporters();
        return ResponseEntity.ok(transporters);
    }

    // READ ONE
    @GetMapping("/{id}")
    public ResponseEntity<Transporter> getTransporterById(@PathVariable Long id) {
        Transporter transporter = transporterService.getTransporterById(id);
        return ResponseEntity.ok(transporter);
    }

    // UPDATE
    @PutMapping("/{id}")
    public ResponseEntity<Transporter> updateTransporter(@PathVariable Long id, @RequestBody Transporter transporterData) {
        Transporter updated = transporterService.updateTransporter(id, transporterData);
        return ResponseEntity.ok(updated);
    }

    // DELETE
    @DeleteMapping("/{id}")
    public ResponseEntity<Void> deleteTransporter(@PathVariable Long id) {
        transporterService.deleteTransporter(id);
        return ResponseEntity.noContent().build();
    }
}

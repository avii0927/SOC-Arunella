package com.example.arunella.controller;

import com.example.arunella.entity.Crop;
import com.example.arunella.service.CropService;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/crops")
public class CropController {

    private final CropService cropService;

    public CropController(CropService cropService) {
        this.cropService = cropService;
    }

    // CREATE
    @PostMapping
    public ResponseEntity<Crop> createCrop(@RequestBody Crop crop) {
        Crop saved = cropService.saveCrop(crop);
        return new ResponseEntity<>(saved, HttpStatus.CREATED);
    }

    // READ ALL
    @GetMapping
    public ResponseEntity<List<Crop>> getAllCrops() {
        List<Crop> crops = cropService.getAllCrops();
        return ResponseEntity.ok(crops);
    }

    // READ ONE
    @GetMapping("/{id}")
    public ResponseEntity<Crop> getCropById(@PathVariable Long id) {
        Crop crop = cropService.getCropById(id);
        return ResponseEntity.ok(crop);
    }

    // READ by Status
    @GetMapping("/status/{status}")
    public ResponseEntity<List<Crop>> getCropsByStatus(@PathVariable String status) {
        List<Crop> crops = cropService.getCropsByStatus(status);
        return ResponseEntity.ok(crops);
    }

    // READ by Farmer
    @GetMapping("/farmer/{farmerId}")
    public ResponseEntity<List<Crop>> getCropsByFarmer(@PathVariable Long farmerId) {
        List<Crop> crops = cropService.getCropsByFarmer(farmerId);
        return ResponseEntity.ok(crops);
    }

    // SEARCH by name
    @GetMapping("/search")
    public ResponseEntity<List<Crop>> searchCrops(@RequestParam String name) {
        List<Crop> crops = cropService.searchCropsByName(name);
        return ResponseEntity.ok(crops);
    }

    // UPDATE
    @PutMapping("/{id}")
    public ResponseEntity<Crop> updateCrop(@PathVariable Long id, @RequestBody Crop cropData) {
        Crop updated = cropService.updateCrop(id, cropData);
        return ResponseEntity.ok(updated);
    }

    // DELETE
    @DeleteMapping("/{id}")
    public ResponseEntity<Void> deleteCrop(@PathVariable Long id) {
        cropService.deleteCrop(id);
        return ResponseEntity.noContent().build();
    }
}

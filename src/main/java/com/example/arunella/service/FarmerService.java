package com.example.arunella.service;

import com.example.arunella.entity.Farmer;
import com.example.arunella.repository.FarmerRepository;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class FarmerService {

    private final FarmerRepository farmerRepository;

    public FarmerService(FarmerRepository farmerRepository) {
        this.farmerRepository = farmerRepository;
    }

    public Farmer saveFarmer(Farmer farmer) {
        return farmerRepository.save(farmer);
    }

    public List<Farmer> getAllFarmers() {
        return farmerRepository.findAll();
    }

    public Farmer getFarmerById(Long id) {
        return farmerRepository.findById(id).orElse(null);
    }

    public List<Farmer> getFarmersByDistrict(String district) {
        return farmerRepository.findByDistrict(district);
    }

    public Farmer updateFarmer(Long id, Farmer farmerData) {
        Farmer existing = farmerRepository.findById(id).orElse(null);
        if (existing != null) {
            existing.setName(farmerData.getName());
            existing.setEmail(farmerData.getEmail());
            existing.setPassword(farmerData.getPassword());
            existing.setNic(farmerData.getNic());
            existing.setContactNo(farmerData.getContactNo());
            existing.setDistrict(farmerData.getDistrict());
            existing.setLocation(farmerData.getLocation());
            existing.setWallet(farmerData.getWallet());
            existing.setBankAccountNo(farmerData.getBankAccountNo());
            existing.setRating(farmerData.getRating());
            return farmerRepository.save(existing);
        }
        return null;
    }

    public void deleteFarmer(Long id) {
        farmerRepository.deleteById(id);
    }
}

package com.example.arunella.service;

import com.example.arunella.entity.Buyer;
import com.example.arunella.repository.BuyerRepository;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class BuyerService {

    private final BuyerRepository buyerRepository;

    public BuyerService(BuyerRepository buyerRepository) {
        this.buyerRepository = buyerRepository;
    }

    public Buyer saveBuyer(Buyer buyer) {
        return buyerRepository.save(buyer);
    }

    public List<Buyer> getAllBuyers() {
        return buyerRepository.findAll();
    }

    public Buyer getBuyerById(Long id) {
        return buyerRepository.findById(id).orElse(null);
    }

    public Buyer updateBuyer(Long id, Buyer buyerData) {
        Buyer existing = buyerRepository.findById(id).orElse(null);
        if (existing != null) {
            existing.setName(buyerData.getName());
            existing.setEmail(buyerData.getEmail());
            existing.setPassword(buyerData.getPassword());
            existing.setNic(buyerData.getNic());
            existing.setContactNo(buyerData.getContactNo());
            existing.setDistrict(buyerData.getDistrict());
            existing.setBusinessRegNo(buyerData.getBusinessRegNo());
            existing.setMarketLocation(buyerData.getMarketLocation());
            existing.setRating(buyerData.getRating());
            return buyerRepository.save(existing);
        }
        return null;
    }

    public void deleteBuyer(Long id) {
        buyerRepository.deleteById(id);
    }
}

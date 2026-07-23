package com.example.arunella.service;

import com.example.arunella.entity.Transporter;
import com.example.arunella.repository.TransporterRepository;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class TransporterService {

    private final TransporterRepository transporterRepository;

    public TransporterService(TransporterRepository transporterRepository) {
        this.transporterRepository = transporterRepository;
    }

    public Transporter saveTransporter(Transporter transporter) {
        return transporterRepository.save(transporter);
    }

    public List<Transporter> getAllTransporters() {
        return transporterRepository.findAll();
    }

    public Transporter getTransporterById(Long id) {
        return transporterRepository.findById(id).orElse(null);
    }

    public Transporter updateTransporter(Long id, Transporter transporterData) {
        Transporter existing = transporterRepository.findById(id).orElse(null);
        if (existing != null) {
            existing.setName(transporterData.getName());
            existing.setEmail(transporterData.getEmail());
            existing.setPassword(transporterData.getPassword());
            existing.setNic(transporterData.getNic());
            existing.setContactNo(transporterData.getContactNo());
            existing.setDistrict(transporterData.getDistrict());
            existing.setVehiclePlateNo(transporterData.getVehiclePlateNo());
            existing.setMaxCapacity(transporterData.getMaxCapacity());
            existing.setRating(transporterData.getRating());
            return transporterRepository.save(existing);
        }
        return null;
    }

    public void deleteTransporter(Long id) {
        transporterRepository.deleteById(id);
    }
}

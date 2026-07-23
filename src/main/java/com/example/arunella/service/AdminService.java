package com.example.arunella.service;

import com.example.arunella.entity.Admin;
import com.example.arunella.repository.AdminRepository;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class AdminService {

    private final AdminRepository adminRepository;

    public AdminService(AdminRepository adminRepository) {
        this.adminRepository = adminRepository;
    }

    public Admin saveAdmin(Admin admin) {
        return adminRepository.save(admin);
    }

    public List<Admin> getAllAdmins() {
        return adminRepository.findAll();
    }

    public Admin getAdminById(Long id) {
        return adminRepository.findById(id).orElse(null);
    }

    public Admin updateAdmin(Long id, Admin adminData) {
        Admin existing = adminRepository.findById(id).orElse(null);
        if (existing != null) {
            existing.setName(adminData.getName());
            existing.setEmail(adminData.getEmail());
            existing.setPassword(adminData.getPassword());
            return adminRepository.save(existing);
        }
        return null;
    }

    public void deleteAdmin(Long id) {
        adminRepository.deleteById(id);
    }
}

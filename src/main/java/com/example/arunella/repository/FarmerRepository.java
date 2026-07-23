package com.example.arunella.repository;

import com.example.arunella.entity.Farmer;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface FarmerRepository extends JpaRepository<Farmer, Long> {

    List<Farmer> findByDistrict(String district);
}

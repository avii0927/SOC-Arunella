package com.example.arunella.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.math.BigDecimal;

@Data
@Entity
@Table(name = "transporter")
@AllArgsConstructor
@NoArgsConstructor
public class Transporter {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long userId;

    @ManyToOne
    @JoinColumn(name = "admin_id")
    private Admin admin;

    private String name;
    private String email;
    private String password;
    private String nic;
    private String contactNo;
    private String district;
    private BigDecimal rating;
    private String role;
    private String vehiclePlateNo;
    private Double maxCapacity;
}

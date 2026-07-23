package com.example.arunella.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.math.BigDecimal;
import java.time.LocalDate;

@Data
@Entity
@Table(name = "crop")
@AllArgsConstructor
@NoArgsConstructor
public class Crop {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long productId;

    private String productName;

    @ManyToOne
    @JoinColumn(name = "user_id")
    private Farmer farmer;

    private BigDecimal pricePerKg;
    private Integer stock;
    private String status;
    private LocalDate uploadedDate;
    private LocalDate expDate;
    private BigDecimal minPrice;
    private String description;

    @Lob
    private byte[] image;
}

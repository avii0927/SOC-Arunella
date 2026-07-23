package com.example.arunella.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.time.LocalDate;

@Data
@Entity
@Table(name = "delivery")
@AllArgsConstructor
@NoArgsConstructor
public class Delivery {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long deliveryId;

    @ManyToOne
    @JoinColumn(name = "user_id")
    private Transporter transporter;

    @OneToOne
    @JoinColumn(name = "order_id")
    private Order order;

    private String pickupLocation;
    private String deliveryLocation;
    private String status;

    @Lob
    private byte[] confirmationImg;

    private LocalDate date;
}

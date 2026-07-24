package com.example.arunella.controller;

import com.example.arunella.service.*;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

@Controller
public class WebViewController {

    private final FarmerService farmerService;
    private final BuyerService buyerService;
    private final TransporterService transporterService;
    private final CropService cropService;
    private final OrderService orderService;
    private final DeliveryService deliveryService;
    private final AdminService adminService;

    public WebViewController(FarmerService farmerService,
                             BuyerService buyerService,
                             TransporterService transporterService,
                             CropService cropService,
                             OrderService orderService,
                             DeliveryService deliveryService,
                             AdminService adminService) {
        this.farmerService = farmerService;
        this.buyerService = buyerService;
        this.transporterService = transporterService;
        this.cropService = cropService;
        this.orderService = orderService;
        this.deliveryService = deliveryService;
        this.adminService = adminService;
    }

    @GetMapping("/")
    public String dashboard(Model model) {
        model.addAttribute("farmerCount", farmerService.getAllFarmers().size());
        model.addAttribute("buyerCount", buyerService.getAllBuyers().size());
        model.addAttribute("transporterCount", transporterService.getAllTransporters().size());
        model.addAttribute("cropCount", cropService.getAllCrops().size());
        model.addAttribute("orderCount", orderService.getAllOrders().size());
        model.addAttribute("deliveryCount", deliveryService.getAllDeliveries().size());
        model.addAttribute("recentCrops", cropService.getAllCrops());
        return "dashboard";
    }

    @GetMapping("/users")
    public String users(Model model) {
        model.addAttribute("farmers", farmerService.getAllFarmers());
        model.addAttribute("buyers", buyerService.getAllBuyers());
        model.addAttribute("transporters", transporterService.getAllTransporters());
        model.addAttribute("admins", adminService.getAllAdmins());
        model.addAttribute("totalUsersCount",
                farmerService.getAllFarmers().size() +
                buyerService.getAllBuyers().size() +
                transporterService.getAllTransporters().size());
        return "users";
    }

    @GetMapping("/crops")
    public String crops(Model model) {
        model.addAttribute("crops", cropService.getAllCrops());
        model.addAttribute("totalCropsCount", cropService.getAllCrops().size());
        return "crops";
    }

    @GetMapping("/deliveries")
    public String deliveries(Model model) {
        model.addAttribute("deliveries", deliveryService.getAllDeliveries());
        model.addAttribute("orders", orderService.getAllOrders());
        model.addAttribute("activeShipmentsCount", deliveryService.getAllDeliveries().size());
        return "deliveries";
    }
}

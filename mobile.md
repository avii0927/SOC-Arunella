# Arunella Mobile App — React Native (Expo)

A full mobile app for Farmers, Buyers, and Transporters built with React Native + Expo,
connecting to the existing Spring Boot REST API at `localhost:8085`.

## Proposed Changes

The mobile app is a **brand new separate project** created alongside the Spring Boot backend.
Location: `e:\UNI\CSC 313 1.5 Service Oriented Computing\Project\Anti\arunella-mobile\`

---

### Backend Change (Spring Boot)

#### [NEW] CorsConfig.java
Add CORS support so the mobile app can call the REST API.

---

### Mobile App Project — `arunella-mobile/`

Built with **Expo** (the easiest way to run React Native — no Android Studio or Xcode needed to start).

#### Structure

```
arunella-mobile/
├── app.json
├── package.json
├── src/
│   ├── api/
│   │   └── apiService.js         ← all fetch() calls to Spring Boot
│   ├── context/
│   │   └── AuthContext.js        ← stores logged-in user globally
│   ├── screens/
│   │   ├── WelcomeScreen.js      ← role selector (Farmer/Buyer/Transporter)
│   │   ├── LoginScreen.js        ← login by email lookup
│   │   ├── RegisterScreen.js     ← register (role-aware)
│   │   │
│   │   ├── farmer/
│   │   │   ├── FarmerHomeScreen.js   ← dashboard
│   │   │   ├── MyCropsScreen.js      ← GET /api/crops/farmer/{id}
│   │   │   └── AddCropScreen.js      ← POST /api/crops
│   │   │
│   │   ├── buyer/
│   │   │   ├── BuyerHomeScreen.js    ← dashboard
│   │   │   ├── MarketplaceScreen.js  ← GET /api/crops
│   │   │   ├── CropDetailScreen.js   ← GET /api/crops/{id}
│   │   │   ├── PlaceOrderScreen.js   ← POST /api/orders
│   │   │   └── MyOrdersScreen.js     ← GET /api/orders/buyer/{id}
│   │   │
│   │   └── transporter/
│   │       ├── TransporterHomeScreen.js   ← dashboard
│   │       └── MyDeliveriesScreen.js      ← GET /api/deliveries/transporter/{id}
│   │                                         PUT /api/deliveries/{id}
│   └── navigation/
│       └── AppNavigator.js       ← React Navigation stack/tab setup
└── App.js
```

#### Key packages
| Package | Purpose |
|---|---|
| `@react-navigation/native` | Screen navigation |
| `@react-navigation/stack` | Stack (push/pop) navigation |
| `@react-navigation/bottom-tabs` | Tab bar per role |
| `expo-async-storage` | Persist login session |
| `axios` | HTTP client for Spring Boot API |

---

## API Endpoints Used

| Screen | Method | Endpoint |
|---|---|---|
| Register Farmer | POST | `/api/farmers` |
| Register Buyer | POST | `/api/buyers` |
| Register Transporter | POST | `/api/transporters` |
| Marketplace | GET | `/api/crops` |
| Search crops | GET | `/api/crops/search?name=X` |
| Farmer's crops | GET | `/api/crops/farmer/{id}` |
| Add crop | POST | `/api/crops` |
| Place order | POST | `/api/orders` |
| My orders | GET | `/api/orders/buyer/{id}` |
| My deliveries | GET | `/api/deliveries/transporter/{id}` |
| Update delivery status | PUT | `/api/deliveries/{id}` |

---

## Open Questions

> [!IMPORTANT]
> **Do you have Node.js installed?** The setup requires Node.js + npm.
> Check by running: `node -v` in your terminal.

> [!IMPORTANT]
> **How will you test?** Options:
> - **Expo Go app** on your phone (scan QR code — easiest, no extra setup)
> - **Android Emulator** via Android Studio
> - **Web browser** (Expo supports web preview too)

> [!NOTE]
> Since there's no login/auth system in the backend yet, the app will use
> **email lookup** to find a user — just enter your registered email to "log in".
> Full auth (JWT tokens) can be added later.

## Verification Plan

### Manual Verification
- Run `npx expo start` → scan QR code in Expo Go app on phone
- OR press `w` to open in web browser
- Test: Register a farmer → Add a crop → Switch to buyer → See crop in marketplace → Place order → Switch to transporter → Update delivery status

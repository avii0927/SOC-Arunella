# Arunella SOA Project: Comprehensive Group Task & Git Division Plan

Since we have 3 members, multiple technologies (Spring Boot, Web Admin, React Native Mobile), and a Service-Oriented Architecture (SOA) requirement, we are using a **Vertical Split (Domain-Driven)** strategy. 

In this approach, each member owns a feature from front-to-back (Database ➔ Backend Service ➔ Mobile UI ➔ Web UI). This is the truest form of SOA, as each person manages their own microservice independently.

---

## 1. Task Delegation & Architecture Ownership

### 👤 Member 1: User Identity & Profiles
- **Microservice:** `user-service` (Port 8081)
- **Database:** `arunella_users_db` (Manages `Farmer`, `Buyer`, `Transporter` tables)
- **Service Responsibilities:** User registration, profile updates, and providing user verification endpoints for other services.
- **Mobile App Tasks:** Builds `WelcomeScreen.js`, `LoginScreen.js`, `RegisterScreen.js`, and sets up `AuthContext.js`.
- **Web Admin Tasks:** Builds the "Users" dashboard page (`users.html`).

### 🛒 Member 2: Marketplace & Orders
- **Microservice:** `market-service` (Port 8082)
- **Database:** `arunella_market_db` (Manages `Crop` and `Order` tables)
- **Service Responsibilities:** Crop listings, marketplace search, order placement.
- **Inter-service Communication:** Uses `RestTemplate` or `FeignClient` to call `user-service` to verify the buyer and farmer exist when an order is placed.
- **Mobile App Tasks:** Builds `FarmerHomeScreen.js`, `MyCropsScreen.js`, `AddCropScreen.js`, `BuyerHomeScreen.js`, `MarketplaceScreen.js`, `CropDetailScreen.js`, `PlaceOrderScreen.js`, `MyOrdersScreen.js`.
- **Web Admin Tasks:** Builds the "Products" dashboard page (`crops.html`).

### 🚚 Member 3: Logistics & Deliveries
- **Microservice:** `logistics-service` (Port 8083)
- **Database:** `arunella_logistics_db` (Manages `Delivery` table)
- **Service Responsibilities:** Delivery assignment and tracking (Pending ➔ In Transit ➔ Delivered).
- **Inter-service Communication:** Uses `RestTemplate` to call `market-service` for order details, and `user-service` for transporter details.
- **Mobile App Tasks:** Builds `TransporterHomeScreen.js`, `MyDeliveriesScreen.js`.
- **Web Admin Tasks:** Builds the "Deliveries" dashboard page (`deliveries.html`).

---

## 2. Frontend Integration (How the UI talks to 3 Backends)

Since there are 3 separate Spring Boot applications running on 3 different ports, the mobile app and web admin need a way to route requests correctly. You have two options (decide as a group):

- **Option A (Simpler): Multiple Base URLs.** Inside `apiService.js`, configure 3 different Axios instances:
  - `const userApi = axios.create({ baseURL: 'http://localhost:8081/api' })`
  - `const marketApi = axios.create({ baseURL: 'http://localhost:8082/api' })`
  - `const logisticsApi = axios.create({ baseURL: 'http://localhost:8083/api' })`
- **Option B (True SOA): API Gateway.** Member 1 or 2 creates a 4th Spring Boot app using **Spring Cloud Gateway** running on port `8080`. It routes `/api/users/**` to 8081, `/api/crops/**` to 8082, etc. The frontend only talks to port 8080.

---

## 3. Git Repository Structure

We will use a **Monorepo** (one main Git repository) containing all the sub-projects.

```text
arunella-project/ (Main Git Repo)
├── backend/
│   ├── user-service/       (Member 1 exclusively edits here)
│   ├── market-service/     (Member 2 exclusively edits here)
│   └── logistics-service/  (Member 3 exclusively edits here)
├── mobile/                 (Shared: Everyone edits their own screen files)
└── admin-web/              (Shared: Everyone edits their own HTML files)
```

---

## 4. Git Branching Strategy & Workflow

To prevent merge conflicts, **no one should push directly to the `main` branch.**

1. **Main Branch:** `main` (always deployable, working code).
2. **Develop Branch:** `dev` (integration branch where everyone merges their work).
3. **Feature Branches:** Each member creates a branch for their specific task based off `dev`.

### Daily Workflow Step-by-Step
1. **Pull Latest:** `git checkout dev` ➔ `git pull origin dev`
2. **Create Branch:** `git checkout -b feature/memberX-task-name` (e.g., `feature/user-login`)
3. **Write Code:** Work in your designated files.
4. **Commit:** `git add .` ➔ `git commit -m "Added user login endpoint"`
5. **Push:** `git push origin feature/memberX-task-name`
6. **Pull Request (PR):** Go to GitHub/GitLab and create a Pull Request to merge your feature branch into `dev`. **Another team member must review and approve it before merging.**

---

## 5. Handling Git Merge Conflicts in Shared Files

Because the frontend apps (`mobile/` and `admin-web/`) are shared, you will occasionally need to edit the same file (e.g., `AppNavigator.js` where all mobile screens are registered, or `apiService.js` where all API calls are written).

**Rules for Shared Files:**
1. Announce to the group in chat: *"I am adding my routes to AppNavigator.js right now."*
2. If you get a merge conflict when pulling `dev`, **DO NOT overwrite someone else's code.** Read the conflict markers (`<<<<<<< HEAD`), keep their routes, and add your routes below them.
3. Keep shared files as clean as possible. Keep all your heavy logic inside your own `Screen.js` files, not in the navigator.

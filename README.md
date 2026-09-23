# AI-Powered Cafeteria Management System (Laravel)

A modern, full-featured web application designed to manage a cafeteria's food and beverage menu while providing intelligent, personalized recommendations using Generative AI and semantic matching.

---

## 🌟 Key Features

### 1. Dual Roles & Strict RBAC (Role-Based Access Control)
- **Admin**:
  - Full CRUD operations on Food Items, Beverages, Categories, Orders, and Customers.
  - Live analytics dashboard: Today's sales, all-time revenue, orders today, low stock alerts (< 5 units), top 5 selling items, category distribution.
  - Order status workflow: `Pending` &rarr; `Preparing` &rarr; `Ready` &rarr; `Completed` / `Cancelled`.
  - Admin AI Assistant for operational insights and business metrics.
- **Customer**:
  - Registration, Login, and Session management.
  - Personal taste profile: Favorite categories, preferred food types, favorite beverages, preferred taste (savory, spicy, sweet, etc.), dietary restrictions, price preference (budget), spicy heat level (0 to 3), favorite & disliked ingredients.
  - Interactive menu with live multi-filtering (category pills, price slider, spicy heat, calories, food vs drink).
  - AI Natural-Language Search (e.g. *"spicy meal with cheese under 150 EGP"*).
  - Favorites & Rating system.
  - Cart with line calculations, combo discounts, and kitchen notes.
  - Order tracking with a 4-step live progress stepper (`Pending` &rarr; `Preparing` &rarr; `Ready` &rarr; `Completed`).
  - Order cancellation (strictly protected: only allowed while order is `Pending`).

### 2. Generative AI & Semantic Matching Engine
- **Match Percentage (0% - 99%)**:
  Calculates affinity between customer preferences and menu items based on:
  - Category & food type affinity (+20-25%)
  - Spiciness heat alignment (+15%)
  - Favorite ingredients bonus (+5% per ingredient, up to +15%)
  - Budget compatibility (+10%)
  - Previous order frequency signals (+12%)
  - **Disliked Ingredients & Allergen Penalty**: If a dish contains any disliked ingredient, the score drops drastically (-40%), protecting customers from unwanted items!
- **Explain Recommendation**: Explains in plain language *why* a dish was recommended.
- **Smart Meal / Combo Recommendation**: Pairs complementary main dishes and drinks (or trio feasts with dessert) with 12% to 15% bundled savings.
- **Budget Explorer**: Filter dishes and drinks strictly within chosen budget limits (e.g. Under 60 EGP, Under 100 EGP, 100-150 EGP).
- **AI Food Comparison Tool**: Side-by-side comparison of 2 dishes (Price, Calories, Spiciness, Ingredients, Match %, and AI Verdict).
- **Surprise Me**: One-click intelligent recommendation taking into account customer taste and meal time (Morning, Afternoon, Evening).

### 3. Role-Aware AI Chatbot & Security Pipeline
```
Request ──▶ Authentication ──▶ Authorization ──▶ User Role ──▶ Allowed Scope ──▶ AI Engine ──▶ Response
```
- **Admin Inquiries**:
  - "What is today's total sales?"
  - "How many orders were placed today?"
  - "Which products have low stock?"
  - "What is the most ordered food?"
  - "What are the top 5 selling items?"
  - "Which food items have never been ordered?"
- **Customer Inquiries**:
  - "What food do you recommend for me?"
  - "Recommend something spicy."
  - "I want something under 100 EGP."
  - "What drinks are suitable for me?"
  - "What is the healthiest meal available?"
  - "Compare Spicy Chicken Pizza and Beef Burger."
- **Enforced Security Barrier**: If a customer queries administrative or financial metrics (e.g. *"What are today's total sales?"* or *"List all customers"*), the system immediately blocks the request with a **403 Access Denied** response.

---

## 🚀 Quick Start Guide

### 1. Clone or Open the Workspace
```powershell
cd C:\Users\pc\.gemini\antigravity\scratch\cafeteria-system
```

### 2. Start the Local Server
```powershell
php artisan serve
```
Open your browser at: **`http://127.0.0.1:8000`**

---

## 🔑 Pre-Configured Demo Accounts

| Role | Email | Password | Details |
|---|---|---|---|
| **Admin** | `admin@cafeteria.com` | `password` | Full system CRUD, cafeteria statistics, low stock monitor |
| **Customer** | `customer@cafeteria.com` | `password` | Profile tuned for spicy chicken, budget 150 EGP, dislikes mushrooms |
| **Customer 2** | `sara@cafeteria.com` | `password` | Vegetarian sweet tooth profile, dislikes beef |

*(A quick one-click login switcher is also embedded directly on the login page for instant testing).*

---

## ⚙️ Configuration & Database

### SQLite (Default - Out of the Box)
The system is configured to run out-of-the-box using SQLite (`database/database.sqlite`), requiring zero server setup or MySQL configuration.

### MySQL (Optional via Laragon)
If you wish to switch to MySQL:
1. Start MySQL in Laragon.
2. In `.env`, change:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cafeteria
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Run: `php artisan migrate:fresh --seed`

### Google Gemini API (Optional)
The system has a built-in semantic analyzer that works 100% offline. To enable extended conversational generative capabilities:
1. Obtain a key from [Google AI Studio](https://aistudio.google.com/).
2. Add to `.env`:
   ```env
   GEMINI_API_KEY=your_actual_gemini_api_key
   ```

---

## 🧪 Automated Testing
Run the automated test suite to verify the AI matching engine, security barriers, and RBAC guards:
```powershell
php artisan test --filter=CafeteriaAiTest
```
Result: **4 passed, 15 assertions (100% success)**.

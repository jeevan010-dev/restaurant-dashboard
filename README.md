🍽️ Restaurant Analytics Dashboard

A full-stack analytics dashboard built with PHP (backend) and React.js + Vite (frontend).  
It analyzes restaurant performance using mock data (`restaurants.json`, `orders.json`).

 ✨ Features
- 📋 List, search, sort, and filter restaurants
- 📊 Per-restaurant analytics:
  - Daily Orders count
  - Daily Revenue
  - Average Order Value (AOV)
  - Peak Order Hour
- 🏆 Top 3 Restaurants by Revenue
- 🎛 Filters: restaurant, date range, amount range, hour range

🧭 Quick Start

1️⃣ Clone

git clone https://github.com/jeevan010-dev/restaurant-dashboard.git
cd restaurant-dashboard


2️⃣ Backend (PHP API)


cd backend
php -S localhost:8000

This runs API endpoints at [http://localhost:8000/api/](http://localhost:8000/api/)

api/restaurants.php` → Restaurant list (search/sort/filter)
api/analytics.php` → Metrics (orders, revenue, AOV, peak hour)
api/top_restaurants.php` → Top restaurants by revenue
api/helpers.php` → Shared logic

Data files:


backend/api/data/restaurants.json
backend/api/data/orders.json

3️⃣ Frontend (React + Vite)


cd frontend
npm install
npm run dev


Open:[http://localhost:5173](http://localhost:5173)

> By default, the frontend fetches data from `http://localhost:8000/api/`.
> Update in `src/config.js` if you change the backend port.


🗂 Project Structure

backend/
 └─ api/
     ├─ data/                 # JSON mock data
     ├─ analytics.php         # Metrics endpoint
     ├─ restaurants.php       # Restaurant list endpoint
     ├─ top_restaurants.php   # Top 3 restaurants endpoint
     └─ helpers.php           # Shared PHP functions

frontend/
 ├─ src/
 │   ├─ assets/
 │   ├─ App.jsx
 │   ├─ index.css
 │   └─ main.jsx
 ├─ package.json
 ├─ vite.config.js
 └─ index.html

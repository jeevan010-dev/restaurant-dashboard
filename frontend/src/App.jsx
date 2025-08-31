import React, { useState, useEffect } from "react";
import axios from "axios";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from "chart.js";
import { Line, Bar } from "react-chartjs-2";
import "./App.css";

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend
);

// Change this to match your backend
const API = "http://localhost:8000/api";

export default function App() {
  const [restaurants, setRestaurants] = useState([]);
  const [selected, setSelected] = useState(null);
  const [data, setData] = useState([]);
  const [top, setTop] = useState([]);
  const [filters, setFilters] = useState({
    start: "",
    end: "",
    min_amount: "",
    max_amount: "",
    min_hour: "",
    max_hour: "",
  });
  const [search, setSearch] = useState("");

  // fetch restaurants
  useEffect(() => {
    axios
      .get(`${API}/restaurants.php`, { params: { q: search } })
      .then((r) => setRestaurants(r.data.data));
  }, [search]);

  // fetch analytics for selected restaurant
  useEffect(() => {
    if (selected) {
      axios
        .get(`${API}/analytics.php`, {
          params: { restaurant_id: selected, ...filters },
        })
        .then((r) => setData(r.data.data));
    }
  }, [selected, filters]);

  // fetch top restaurants
  useEffect(() => {
    axios
      .get(`${API}/top_restaurants.php`, { params: filters })
      .then((r) => setTop(r.data.data));
  }, [filters]);

  return (
    <div className="app">
      {/* Sidebar */}
      <aside className="sidebar">
        <h2>Filters</h2>
        <div className="filter-group">
          <label>Date Range</label>
          <input
            type="date"
            value={filters.start}
            onChange={(e) => setFilters((f) => ({ ...f, start: e.target.value }))}
          />
          <input
            type="date"
            value={filters.end}
            onChange={(e) => setFilters((f) => ({ ...f, end: e.target.value }))}
          />
        </div>

        <div className="filter-group">
          <label>Amount Range</label>
          <input
            type="number"
            placeholder="Min"
            value={filters.min_amount}
            onChange={(e) =>
              setFilters((f) => ({ ...f, min_amount: e.target.value }))
            }
          />
          <input
            type="number"
            placeholder="Max"
            value={filters.max_amount}
            onChange={(e) =>
              setFilters((f) => ({ ...f, max_amount: e.target.value }))
            }
          />
        </div>

        <div className="filter-group">
          <label>Hour Range</label>
          <input
            type="number"
            placeholder="Min Hour"
            min="0"
            max="23"
            value={filters.min_hour}
            onChange={(e) =>
              setFilters((f) => ({ ...f, min_hour: e.target.value }))
            }
          />
          <input
            type="number"
            placeholder="Max Hour"
            min="0"
            max="23"
            value={filters.max_hour}
            onChange={(e) =>
              setFilters((f) => ({ ...f, max_hour: e.target.value }))
            }
          />
        </div>

        <h2>Restaurants</h2>
        <input
          type="text"
          placeholder="Search..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="search-box"
        />
        <div className="restaurant-list">
          {restaurants.map((r) => (
            <div
              key={r.id}
              className={`restaurant-item ${
                selected === r.id ? "active" : ""
              }`}
              onClick={() => setSelected(r.id)}
            >
              <strong>{r.name}</strong>
              <span>{r.cuisine}</span>
            </div>
          ))}
        </div>

        <h2>Top 3 by Revenue</h2>
        <ol className="top-list">
          {top.map((r) => (
            <li key={r.id}>
              {r.name} – <strong>₹{r.revenue}</strong>
            </li>
          ))}
        </ol>
      </aside>

      {/* Charts */}
      <main className="dashboard">
        {selected ? (
          <>
            <h1>
              Analytics –{" "}
              {restaurants.find((r) => r.id === selected)?.name || selected}
            </h1>
            <ChartBlock
              title="Daily Orders"
              labels={data.map((d) => d.date)}
              values={data.map((d) => d.orders_count)}
              type="line"
            />
            <ChartBlock
              title="Daily Revenue"
              labels={data.map((d) => d.date)}
              values={data.map((d) => d.revenue)}
              type="bar"
            />
            <ChartBlock
              title="Average Order Value"
              labels={data.map((d) => d.date)}
              values={data.map((d) => d.avg_order_value)}
              type="line"
            />
            <ChartBlock
              title="Peak Hour"
              labels={data.map((d) => d.date)}
              values={data.map((d) => d.peak_hour)}
              type="bar"
            />
          </>
        ) : (
          <p className="empty">Select a restaurant to view analytics.</p>
        )}
      </main>
    </div>
  );
}

function ChartBlock({ title, labels, values, type }) {
  const chartData = { labels, datasets: [{ label: title, data: values }] };
  return (
    <div className="chart-block">
      <h3>{title}</h3>
      {type === "line" ? <Line data={chartData} /> : <Bar data={chartData} />}
    </div>
  );
}

import React from "react";
import { createRoot } from "react-dom/client";
import App from "./App";
import "./admin.css";

const container = document.getElementById("all-woo-addons-admin");
if (container) {
  const root = createRoot(container);
  root.render(<App />);
}

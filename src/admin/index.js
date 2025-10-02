import React from "react";
import { createRoot } from "react-dom/client";

function AdminApp() {
  return <h2>Hello from React Admin 🚀</h2>;
}

const container = document.getElementById("ultimate-woo-addons-admin");
if (container) {
  const root = createRoot(container);
  root.render(<AdminApp />);
}

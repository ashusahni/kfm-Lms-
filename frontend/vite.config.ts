import { defineConfig, loadEnv } from "vite";
import react from "@vitejs/plugin-react-swc";
import path from "path";
import { componentTagger } from "lovable-tagger";

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), "");
  const apiUrl = env.VITE_API_URL || "http://localhost:8000";
  // When building for Laravel backend (SERVE_REACT_FROM_BACKEND), assets must be under /spa/
  const base = env.VITE_APP_BASE || "/";

  return {
    base,
    server: {
      host: "::",
      port: 8080, // Frontend dev server – open http://localhost:8080 (backend API runs on 8000)
      hmr: {
        overlay: false,
      },
      proxy: {
        "/api": {
          target: apiUrl,
          changeOrigin: true,
        },
      },
    },
    plugins: [react(), mode === "development" && componentTagger()].filter(Boolean),
    resolve: {
      alias: {
        "@": path.resolve(__dirname, "./src"),
      },
    },
  };
});

import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route, useLocation } from "react-router-dom";
import { ConfigProvider } from "@/context/ConfigContext";
import { AuthProvider } from "@/context/AuthContext";
import Index from "./pages/Index";
import Login from "./pages/Login";
import Register from "./pages/Register";
import ForgotPassword from "./pages/ForgotPassword";
import ResetPassword from "./pages/ResetPassword";
import VerifyAccount from "./pages/VerifyAccount";
import Programs from "./pages/Programs";
import ProgramDetail from "./pages/ProgramDetail";
import Panel from "./pages/Panel";
import Dashboard from "./pages/panel/Dashboard";
import PanelPrograms from "./pages/panel/PanelPrograms";
import PanelMeetings from "./pages/panel/PanelMeetings";
import PanelCart from "./pages/panel/PanelCart";
import PanelCheckout from "./pages/panel/PanelCheckout";
import HealthLogOverview from "./pages/panel/HealthLogOverview";
import HealthLogForm from "./pages/panel/HealthLogForm";
import ProgressPage from "./pages/panel/ProgressPage";
import NotificationsPage from "./pages/panel/NotificationsPage";
import NotFound from "./pages/NotFound";
import { ProtectedRoute } from "./components/ProtectedRoute";

const queryClient = new QueryClient();

/** Keyed routes so navigation always updates the visible page (avoids stale view when URL changes). */
function AppRoutes() {
  const location = useLocation();
  return (
    <Routes location={location} key={location.pathname}>
      <Route path="/" element={<Index />} />
      <Route path="/login" element={<Login />} />
      <Route path="/register" element={<Register />} />
      <Route path="/forgot-password" element={<ForgotPassword />} />
      <Route path="/reset-password/:token" element={<ResetPassword />} />
      <Route path="/verify" element={<VerifyAccount />} />
      <Route path="/programs" element={<Programs />} />
      <Route path="/programs/:id" element={<ProgramDetail />} />
      <Route
        path="/panel"
        element={
          <ProtectedRoute>
            <Panel />
          </ProtectedRoute>
        }
      >
        <Route index element={<Dashboard />} />
        <Route path="programs" element={<PanelPrograms />} />
        <Route path="programs/:id" element={<PanelPrograms />} />
        <Route path="health-log" element={<HealthLogOverview />} />
        <Route path="health-log/new" element={<HealthLogForm />} />
        <Route path="health-log/edit/:id" element={<HealthLogForm />} />
        <Route path="health-log/weekly" element={<HealthLogOverview />} />
        <Route path="progress" element={<ProgressPage />} />
        <Route path="notifications" element={<NotificationsPage />} />
        <Route path="meetings" element={<PanelMeetings />} />
        <Route path="cart" element={<PanelCart />} />
        <Route path="checkout" element={<PanelCheckout />} />
      </Route>
      <Route path="*" element={<NotFound />} />
    </Routes>
  );
}

const App = () => (
  <QueryClientProvider client={queryClient}>
    <ConfigProvider>
      <AuthProvider>
        <TooltipProvider>
          <Toaster />
          <Sonner />
          <BrowserRouter>
            <AppRoutes />
          </BrowserRouter>
        </TooltipProvider>
      </AuthProvider>
    </ConfigProvider>
  </QueryClientProvider>
);

export default App;

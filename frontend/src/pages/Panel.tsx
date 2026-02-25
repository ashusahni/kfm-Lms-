import { Routes, Route } from "react-router-dom";
import { PanelLayout } from "@/components/PanelLayout";
import Dashboard from "@/pages/panel/Dashboard";
import PanelPrograms from "@/pages/panel/PanelPrograms";
import PanelMeetings from "@/pages/panel/PanelMeetings";
import PanelCart from "@/pages/panel/PanelCart";
import PanelCheckout from "@/pages/panel/PanelCheckout";
import HealthLogOverview from "@/pages/panel/HealthLogOverview";
import HealthLogForm from "@/pages/panel/HealthLogForm";
import ProgressPage from "@/pages/panel/ProgressPage";
import NotificationsPage from "@/pages/panel/NotificationsPage";

const Panel = () => (
  <PanelLayout>
    <Routes>
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
    </Routes>
  </PanelLayout>
);

export default Panel;

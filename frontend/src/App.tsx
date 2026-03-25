import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route, useLocation, useParams } from "react-router-dom";
import { ConfigProvider } from "@/context/ConfigContext";
import { AuthProvider } from "@/context/AuthContext";
import Index from "./pages/Index";
import Login from "./pages/Login";
import Register from "./pages/Register";
import Onboarding from "./pages/Onboarding";
import ForgotPassword from "./pages/ForgotPassword";
import ResetPassword from "./pages/ResetPassword";
import VerifyAccount from "./pages/VerifyAccount";
import Programs from "./pages/Programs";
import ProgramDetail from "./pages/ProgramDetail";
import Panel from "./pages/Panel";
import Dashboard from "./pages/panel/Dashboard";
import PanelPrograms from "./pages/panel/PanelPrograms";
import PanelCourseLearn from "./pages/panel/PanelCourseLearn";
import PanelRecommendedMeals from "./pages/panel/PanelRecommendedMeals";
import PanelMeetings from "./pages/panel/PanelMeetings";
import PanelCart from "./pages/panel/PanelCart";
import PanelCheckout from "./pages/panel/PanelCheckout";
import PanelCheckoutPayment from "./pages/panel/PanelCheckoutPayment";
import PanelCartSuccess from "./pages/panel/PanelCartSuccess";
import PanelCartFailed from "./pages/panel/PanelCartFailed";
import HealthLogOverview from "./pages/panel/HealthLogOverview";
import HealthProfilePage from "./pages/panel/HealthProfilePage";
import PanelMyBatches from "./pages/panel/PanelMyBatches";
import CourseIntakeForm from "./pages/panel/CourseIntakeForm";
import DieticianPanel from "./pages/dietician/DieticianPanel";
import DieticianDashboard from "./pages/dietician/DieticianDashboard";
import DieticianMyCourses from "./pages/dietician/DieticianMyCourses";
import DieticianCourseBatches from "./pages/dietician/DieticianCourseBatches";
import DieticianHealthLogs from "./pages/dietician/DieticianHealthLogs";
import DieticianStudentHealthProfile from "./pages/dietician/DieticianStudentHealthProfile";
import DieticianStudents from "./pages/dietician/DieticianStudents";
import DieticianHealthCare from "./pages/dietician/DieticianHealthCare";
import DieticianMeetings from "./pages/dietician/DieticianMeetings";
import DieticianAssignments from "./pages/dietician/DieticianAssignments";
import DieticianRecipes from "./pages/dietician/DieticianRecipes";
import DieticianCertificates from "./pages/dietician/DieticianCertificates";
import DieticianProfile from "./pages/dietician/DieticianProfile";
import HealthLogForm from "./pages/panel/HealthLogForm";
import ProgressPage from "./pages/panel/ProgressPage";
import NotificationsPage from "./pages/panel/NotificationsPage";
import NotFound from "./pages/NotFound";
import { ProtectedRoute } from "./components/ProtectedRoute";
import { StudentRoute } from "./components/StudentRoute";
import { DieticianRoute } from "./components/DieticianRoute";
import { Navigate } from "react-router-dom";

const queryClient = new QueryClient();

/** Redirects /instructor/... to /dietician/... for legacy bookmarks. */
function RedirectInstructorToDietician() {
  const { "*": splat } = useParams<{ "*"?: string }>();
  const to = splat ? `/dietician/${splat}` : "/dietician";
  return <Navigate to={to} replace />;
}

/** Keyed routes so navigation always updates the visible page (avoids stale view when URL changes). */
function AppRoutes() {
  const location = useLocation();
  return (
    <Routes location={location} key={location.pathname}>
      <Route path="/" element={<Index />} />
      <Route path="/login" element={<Login />} />
      <Route path="/register" element={<Register />} />
      <Route path="/onboarding" element={<ProtectedRoute><Onboarding /></ProtectedRoute>} />
      <Route path="/forgot-password" element={<ForgotPassword />} />
      <Route path="/reset-password/:token" element={<ResetPassword />} />
      <Route path="/verify" element={<VerifyAccount />} />
      <Route path="/programs" element={<Programs />} />
      <Route path="/programs/:id" element={<ProgramDetail />} />
      <Route
        path="/panel"
        element={
          <StudentRoute>
            <Panel />
          </StudentRoute>
        }
      >
        <Route index element={<Dashboard />} />
        <Route path="programs" element={<PanelPrograms />} />
        <Route path="programs/:id" element={<PanelPrograms />} />
        <Route path="learn/:id" element={<PanelCourseLearn />} />
        <Route path="learn/:id/intake" element={<CourseIntakeForm />} />
        <Route path="recommended-meals" element={<PanelRecommendedMeals />} />
        <Route path="recommended-meals/:assignmentId" element={<PanelRecommendedMeals />} />
        <Route path="my-batches" element={<PanelMyBatches />} />
        <Route path="health-log" element={<HealthLogOverview />} />
        <Route path="health-log/new" element={<HealthLogForm />} />
        <Route path="health-log/edit/:id" element={<HealthLogForm />} />
        <Route path="health-log/weekly" element={<HealthLogOverview />} />
        <Route path="health-profile" element={<HealthProfilePage />} />
        <Route path="progress" element={<ProgressPage />} />
        <Route path="notifications" element={<NotificationsPage />} />
        <Route path="meetings" element={<PanelMeetings />} />
        <Route path="cart" element={<PanelCart />} />
        <Route path="cart/success" element={<PanelCartSuccess />} />
        <Route path="cart/failed" element={<PanelCartFailed />} />
        <Route path="checkout" element={<PanelCheckout />} />
        <Route path="checkout/payment" element={<PanelCheckoutPayment />} />
      </Route>
      <Route
        path="/dietician"
        element={
          <DieticianRoute>
            <DieticianPanel />
          </DieticianRoute>
        }
      >
        <Route index element={<DieticianDashboard />} />
        <Route path="students" element={<DieticianStudents />} />
        <Route path="my-courses" element={<DieticianMyCourses />} />
        <Route path="my-courses/:webinarId/batches" element={<DieticianCourseBatches />} />
        <Route path="health-logs" element={<DieticianHealthLogs />} />
        <Route path="health-care" element={<DieticianHealthCare />} />
        <Route path="students/:userId/health-profile" element={<DieticianStudentHealthProfile />} />
        <Route path="meetings" element={<DieticianMeetings />} />
        <Route path="assignments" element={<DieticianAssignments />} />
        <Route path="recipes" element={<DieticianRecipes />} />
        <Route path="certificates" element={<DieticianCertificates />} />
        <Route path="profile" element={<DieticianProfile />} />
      </Route>
      <Route path="*" element={<NotFound />} />
      {/* Legacy: instructor panel is now dietician panel */}
      <Route path="/instructor" element={<Navigate to="/dietician" replace />} />
      <Route path="/instructor/*" element={<RedirectInstructorToDietician />} />
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

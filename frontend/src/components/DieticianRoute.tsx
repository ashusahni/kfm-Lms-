import { Navigate, useLocation } from "react-router-dom";
import { useAuth } from "@/context/AuthContext";

interface DieticianRouteProps {
  children: React.ReactNode;
}

/**
 * Protects dietician-only routes. Redirects to student panel when not a dietician.
 * Backend role remains "teacher"; UI is rebranded as Dietician.
 */
export function DieticianRoute({ children }: DieticianRouteProps) {
  const { isAuthenticated, isInstructor, initialized } = useAuth();
  const location = useLocation();

  if (!initialized) {
    return (
      <div className="min-h-screen bg-background flex items-center justify-center">
        <div className="animate-pulse text-muted-foreground">Loading…</div>
      </div>
    );
  }

  if (!isAuthenticated) {
    return <Navigate to="/login" state={{ from: location }} replace />;
  }

  if (!isInstructor) {
    return <Navigate to="/panel" replace />;
  }

  return <>{children}</>;
}

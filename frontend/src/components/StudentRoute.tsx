import { Navigate, useLocation } from "react-router-dom";
import { useAuth } from "@/context/AuthContext";

interface StudentRouteProps {
  children: React.ReactNode;
}

/**
 * Protects student panel routes. Redirects dieticians to dietician panel.
 */
export function StudentRoute({ children }: StudentRouteProps) {
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

  if (isInstructor) {
    return <Navigate to="/dietician" replace />;
  }

  return <>{children}</>;
}

import { Link, NavLink, Outlet } from "react-router-dom";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { ErrorBoundary } from "@/components/ErrorBoundary";
import { LayoutDashboard, Layers, Heart, LogOut } from "lucide-react";

const navItems = [
  { to: "/dietician", end: true, label: "Dashboard", icon: LayoutDashboard },
  { to: "/dietician/my-courses", end: false, label: "My programs & batches", icon: Layers },
  { to: "/dietician/health-logs", end: true, label: "Student health logs", icon: Heart },
];

export function InstructorLayout() {
  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <div className="pt-20 flex">
        <aside className="hidden lg:block w-64 shrink-0 border-r border-border bg-card/50 min-h-[calc(100vh-5rem)]">
          <nav className="p-4 space-y-1 sticky top-20">
            <div className="px-4 py-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
              Dietician
            </div>
            {navItems.map(({ to, end, label, icon: Icon }) => (
              <NavLink
                key={to}
                to={to}
                end={end}
                className={({ isActive }) =>
                  `flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors ${
                    isActive
                      ? "bg-primary/10 text-primary"
                      : "text-muted-foreground hover:bg-muted hover:text-foreground"
                  }`
                }
              >
                <Icon size={18} />
                {label}
              </NavLink>
            ))}
            <div className="pt-4 mt-4 border-t border-border">
              <Link
                to="/panel"
                className="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-muted-foreground hover:bg-muted hover:text-foreground"
              >
                <LogOut size={18} />
                View as student
              </Link>
            </div>
          </nav>
        </aside>

        <main className="flex-1 pb-16">
          <div className="container mx-auto px-4 lg:px-8 py-8">
            <ErrorBoundary
              fallback={
                <div className="rounded-xl border border-destructive/50 bg-destructive/10 p-6">
                  <h2 className="text-lg font-semibold text-foreground mb-2">Something went wrong</h2>
                  <p className="text-sm text-muted-foreground mb-4">
                    Check the browser console for details.
                  </p>
                  <button
                    type="button"
                    onClick={() => window.location.reload()}
                    className="px-4 py-2 rounded-lg bg-primary text-primary-foreground text-sm font-medium"
                  >
                    Reload
                  </button>
                </div>
              }
            >
              <Outlet />
            </ErrorBoundary>
          </div>
        </main>
      </div>
      <Footer />
    </div>
  );
}

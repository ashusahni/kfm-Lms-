import { Link, NavLink, Outlet } from "react-router-dom";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { ErrorBoundary } from "@/components/ErrorBoundary";
import { useAuth } from "@/context/AuthContext";
import {
  LayoutDashboard,
  BookOpen,
  ClipboardList,
  TrendingUp,
  Bell,
  ShoppingCart,
  Video,
  Layers,
  Heart,
  Utensils,
} from "lucide-react";
import { useQuery } from "@tanstack/react-query";
import { notificationsService } from "@/services/notifications";
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";

const navItems = [
  { to: "/panel", end: true, label: "Dashboard", icon: LayoutDashboard },
  { to: "/panel/health-profile", end: true, label: "Student health profile", icon: Heart },
  { to: "/panel/programs", end: false, label: "My Programs", icon: BookOpen },
  { to: "/panel/recommended-meals", end: false, label: "Recommended Meals", icon: Utensils },
  { to: "/panel/my-batches", end: true, label: "My batches", icon: Layers },
  { to: "/panel/meetings", end: true, label: "Meetings", icon: Video },
  { to: "/panel/cart", end: true, label: "Cart", icon: ShoppingCart },
  { to: "/panel/health-log", end: false, label: "Daily Log", icon: ClipboardList },
  { to: "/panel/progress", end: true, label: "Progress & Adherence", icon: TrendingUp },
  { to: "/panel/notifications", end: true, label: "Notifications", icon: Bell },
];

function useCartCount(): number {
  const { data } = useQuery({
    queryKey: ["panel-cart"],
    queryFn: () => api.get<unknown>(paths.panel.cart.list),
    staleTime: 60 * 1000,
  });
  if (!data || typeof data !== "object") return 0;
  const cart = (data as { cart?: { items?: unknown[] } }).cart;
  const items = cart?.items ?? (data as { items?: unknown[] }).items;
  return Array.isArray(items) ? items.length : 0;
}

export function PanelLayout() {
  const { fullName } = useAuth();
  const { data: notifications } = useQuery({
    queryKey: ["panel-notifications"],
    queryFn: () => notificationsService.list(),
  });
  const cartCount = useCartCount();
  const notificationList = notifications?.notifications ?? [];
  const unreadCount = notificationList.filter(
    (n) => (n as { status?: string }).status !== "read" && !(n as { read_at?: number }).read_at
  ).length;

  return (
    <div className="min-h-screen bg-background">
      <Navbar />
      <div className="pt-20 flex">
        <aside className="hidden lg:block w-64 shrink-0 border-r border-border bg-card/50 min-h-[calc(100vh-5rem)]">
          <nav className="p-4 space-y-1 sticky top-20">
            <div className="px-4 py-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
              Student
            </div>
            {fullName && (
              <div className="px-4 py-2 text-sm text-foreground/90 border-b border-border/60 mb-2">
                Logged in as: <span className="font-medium">{fullName}</span>
              </div>
            )}
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
                {label === "Cart" && cartCount > 0 && (
                  <span className="ml-auto bg-primary text-primary-foreground text-xs font-bold rounded-full min-w-[1.25rem] h-5 px-1.5 flex items-center justify-center">
                    {cartCount > 99 ? "99+" : cartCount}
                  </span>
                )}
                {label === "Notifications" && unreadCount > 0 && (
                  <span className="ml-auto bg-accent text-accent-foreground text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                    {unreadCount > 99 ? "99+" : unreadCount}
                  </span>
                )}
              </NavLink>
            ))}
          </nav>
        </aside>

        <main className="flex-1 pb-16">
          <div className="container mx-auto px-4 lg:px-8 py-8">
            <ErrorBoundary
              fallback={
                <div className="rounded-xl border border-destructive/50 bg-destructive/10 p-6">
                  <h2 className="text-lg font-semibold text-foreground mb-2">Panel error</h2>
                  <p className="text-sm text-muted-foreground mb-4">
                    Something went wrong loading this page. Check the browser console for details.
                  </p>
                  <button
                    type="button"
                    onClick={() => window.location.reload()}
                    className="px-4 py-2 rounded-lg bg-primary text-primary-foreground text-sm font-medium"
                  >
                    Reload page
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

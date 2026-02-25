import { Link, NavLink, Outlet } from "react-router-dom";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import {
  LayoutDashboard,
  BookOpen,
  ClipboardList,
  TrendingUp,
  Bell,
  ShoppingCart,
  Video,
} from "lucide-react";
import { useQuery } from "@tanstack/react-query";
import { notificationsService } from "@/services/notifications";

const navItems = [
  { to: "/panel", end: true, label: "Dashboard", icon: LayoutDashboard },
  { to: "/panel/programs", end: false, label: "My Programs", icon: BookOpen },
  { to: "/panel/meetings", end: true, label: "Meetings", icon: Video },
  { to: "/panel/cart", end: true, label: "Cart", icon: ShoppingCart },
  { to: "/panel/health-log", end: false, label: "Daily Log", icon: ClipboardList },
  { to: "/panel/progress", end: true, label: "Progress & Adherence", icon: TrendingUp },
  { to: "/panel/notifications", end: true, label: "Notifications", icon: Bell },
];

export function PanelLayout() {
  const { data: notifications } = useQuery({
    queryKey: ["panel-notifications"],
    queryFn: () => notificationsService.list(),
  });
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
            <Outlet />
          </div>
        </main>
      </div>
      <Footer />
    </div>
  );
}

import { useState } from "react";
import { Link, useLocation } from "react-router-dom";
import { Menu, User, LogOut, Bell, BookOpen, Calendar, MessageCircle, FileText, LayoutDashboard } from "lucide-react";
import { useConfig } from "@/context/ConfigContext";
import { useAuth } from "@/context/AuthContext";
import { useQuery } from "@tanstack/react-query";
import { notificationsService } from "@/services/notifications";
import { Button } from "@/components/ui/button";
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from "@/components/ui/sheet";
import { cn } from "@/lib/utils";

const navLinkIcons: Record<string, React.ComponentType<{ size?: number; className?: string }>> = {
  "/programs": BookOpen,
  "/#cohorts": Calendar,
  "/#coaches": User,
  "/#stories": MessageCircle,
  "/#blog": FileText,
};

const Navbar = () => {
  const [open, setOpen] = useState(false);
  const { t, isDisabled } = useConfig();
  const { isAuthenticated, logout } = useAuth();
  const location = useLocation();
  const { data: notifications } = useQuery({
    queryKey: ["panel-notifications"],
    queryFn: () => notificationsService.list(),
    enabled: isAuthenticated,
  });
  const unreadCount = isAuthenticated && Array.isArray(notifications)
    ? notifications.filter((n) => !n.read_at).length
    : 0;

  const programsLabel = t("courses");
  const navLinks = [
    { label: programsLabel, href: "/programs" },
    { label: "Upcoming Cohorts", href: "/#cohorts" },
    { label: "Coaches", href: "/#coaches" },
    { label: "Stories", href: "/#stories" },
    { label: "Blog", href: "/#blog" },
  ].filter((link) => {
    if (link.label === "Blog" && isDisabled("instructor_blog")) return false;
    return true;
  });

  const isActive = (href: string) => {
    if (href === "/programs") return location.pathname === "/programs";
    if (href.startsWith("/#")) return location.pathname === "/" && location.hash === href.slice(1);
    return false;
  };

  return (
    <header className="fixed top-0 left-0 right-0 z-50 px-4 pt-4 md:px-6 md:pt-5">
      <nav
        className={cn(
          "rounded-2xl px-4 py-2.5 md:px-5 md:py-3 max-w-[1400px] mx-auto",
          "bg-card/80 backdrop-blur-xl border border-white/20 dark:border-white/10",
          "shadow-[0_1px_0_0_rgba(255,255,255,0.06)_inset,0_8px_32px_-4px_rgba(0,0,0,0.1)]",
          "transition-all duration-300"
        )}
      >
        <div className="flex items-center justify-between gap-4">
          {/* Logo */}
          <Link
            to="/"
            className="flex items-center gap-2.5 shrink-0 outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 rounded-xl"
          >
            <div className="relative flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-cta shadow-lg shadow-primary/25 transition-all duration-300 hover:shadow-primary/30 hover:scale-[1.02]">
              <span className="font-display text-lg font-bold text-primary-foreground">FK</span>
            </div>
            <span className="font-display text-xl font-bold tracking-tight text-foreground hidden sm:block">
              Fit Karnataka
            </span>
          </Link>

          {/* Desktop nav links - pill group */}
          <div className="hidden md:flex items-center gap-0.5 rounded-full bg-muted/60 p-1 border border-border/60">
            {navLinks.map((link) => {
              const active = isActive(link.href);
              return (
                <Link
                  key={link.label}
                  to={link.href}
                  className={cn(
                    "relative px-4 py-2 rounded-full text-sm font-medium transition-all duration-200",
                    active
                      ? "text-foreground bg-background shadow-sm"
                      : "text-muted-foreground hover:text-foreground hover:bg-background/60"
                  )}
                >
                  {link.label}
                </Link>
              );
            })}
          </div>

          {/* Desktop actions */}
          <div className="hidden md:flex items-center gap-2">
            {isAuthenticated ? (
              <>
                <Link to="/panel/notifications" className="relative">
                  <Button
                    variant="ghost"
                    size="icon"
                    className="relative h-9 w-9 rounded-full text-muted-foreground hover:text-foreground hover:bg-muted/80 transition-colors"
                    aria-label="Notifications"
                  >
                    <Bell size={18} />
                    {unreadCount > 0 && (
                      <span className="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-accent text-[10px] font-bold text-accent-foreground px-1">
                        {unreadCount > 99 ? "99+" : unreadCount}
                      </span>
                    )}
                  </Button>
                </Link>
                <Link to="/panel">
                  <Button
                    variant="ghost"
                    size="sm"
                    className="h-9 rounded-full gap-2 px-4 font-medium text-muted-foreground hover:text-foreground hover:bg-muted/80"
                  >
                    <LayoutDashboard size={16} />
                    Dashboard
                  </Button>
                </Link>
                <Button
                  variant="ghost"
                  size="sm"
                  className="h-9 rounded-full gap-2 px-4 text-muted-foreground hover:text-destructive hover:bg-destructive/10"
                  onClick={() => logout()}
                >
                  <LogOut size={16} />
                  <span className="hidden lg:inline">Logout</span>
                </Button>
              </>
            ) : (
              <>
                <Link to="/login">
                  <Button
                    variant="ghost"
                    size="sm"
                    className="h-9 rounded-full px-4 font-medium text-muted-foreground hover:text-foreground"
                  >
                    Sign in
                  </Button>
                </Link>
                <Link to="/register">
                  <Button
                    variant="outline"
                    size="sm"
                    className="h-9 rounded-full border-2 px-4 font-medium"
                  >
                    Register
                  </Button>
                </Link>
                <Link to="/programs">
                  <Button
                    size="sm"
                    className="h-9 rounded-full bg-gradient-cta text-primary-foreground px-5 font-semibold shadow-lg shadow-primary/20 hover:opacity-90 hover:shadow-primary/25 transition-all"
                  >
                    Join a {t("course")}
                  </Button>
                </Link>
              </>
            )}
          </div>

          {/* Mobile menu trigger */}
          <Sheet open={open} onOpenChange={setOpen}>
            <SheetTrigger asChild>
              <Button
                variant="ghost"
                size="icon"
                className="md:hidden h-10 w-10 rounded-xl text-foreground hover:bg-muted/80"
                aria-label="Open menu"
              >
                <Menu size={22} />
              </Button>
            </SheetTrigger>
            <SheetContent
              side="right"
              className="w-[min(340px,100vw)] flex flex-col border-l border-border/80 bg-card/95 backdrop-blur-xl p-0"
            >
              {/* Mobile header strip */}
              <div className="h-1.5 w-full bg-gradient-to-r from-primary via-primary to-accent rounded-b-full" />
              <SheetHeader className="px-6 pt-6 pb-4">
                <SheetTitle className="text-left font-display text-xl">Menu</SheetTitle>
              </SheetHeader>
              <nav className="flex flex-col gap-0.5 px-4 pb-8 overflow-auto">
                {navLinks.map((link) => {
                  const Icon = navLinkIcons[link.href];
                  const active = isActive(link.href);
                  return (
                    <Link
                      key={link.label}
                      to={link.href}
                      onClick={() => setOpen(false)}
                      className={cn(
                        "flex items-center gap-3 rounded-xl px-4 py-3.5 text-[15px] font-medium transition-colors",
                        active
                          ? "bg-primary/10 text-primary"
                          : "text-muted-foreground hover:bg-muted hover:text-foreground"
                      )}
                    >
                      {Icon && <Icon size={20} className="shrink-0 opacity-70" />}
                      {link.label}
                    </Link>
                  );
                })}
                <div className="my-2 h-px bg-border" />
                {isAuthenticated ? (
                  <>
                    <Link
                      to="/panel/notifications"
                      onClick={() => setOpen(false)}
                      className="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[15px] font-medium text-muted-foreground hover:bg-muted hover:text-foreground"
                    >
                      <Bell size={20} className="shrink-0 opacity-70" />
                      Notifications
                      {unreadCount > 0 && (
                        <span className="ml-auto rounded-full bg-accent px-2.5 py-0.5 text-xs font-bold text-accent-foreground">
                          {unreadCount}
                        </span>
                      )}
                    </Link>
                    <Link to="/panel" onClick={() => setOpen(false)}>
                      <div className="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[15px] font-medium text-muted-foreground hover:bg-muted hover:text-foreground">
                        <LayoutDashboard size={20} className="shrink-0 opacity-70" />
                        Dashboard
                      </div>
                    </Link>
                    <button
                      type="button"
                      onClick={() => { setOpen(false); logout(); }}
                      className="flex w-full items-center gap-3 rounded-xl px-4 py-3.5 text-left text-[15px] font-medium text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                    >
                      <LogOut size={20} className="shrink-0 opacity-70" />
                      Logout
                    </button>
                  </>
                ) : (
                  <>
                    <Link to="/login" onClick={() => setOpen(false)}>
                      <div className="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[15px] font-medium text-muted-foreground hover:bg-muted hover:text-foreground">
                        Sign in
                      </div>
                    </Link>
                    <Link to="/register" onClick={() => setOpen(false)}>
                      <div className="flex items-center gap-3 rounded-xl px-4 py-3.5 text-[15px] font-medium text-muted-foreground hover:bg-muted hover:text-foreground">
                        Register
                      </div>
                    </Link>
                    <Link to="/programs" onClick={() => setOpen(false)} className="mt-2">
                      <Button className="w-full rounded-xl bg-gradient-cta text-primary-foreground font-semibold shadow-lg shadow-primary/20 py-6">
                        Join a {t("course")}
                      </Button>
                    </Link>
                  </>
                )}
              </nav>
            </SheetContent>
          </Sheet>
        </div>
      </nav>
    </header>
  );
};

export default Navbar;

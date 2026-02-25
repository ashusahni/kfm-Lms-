import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { useConfig } from "@/context/ConfigContext";
import { progressService } from "@/services/progress";
import { healthService } from "@/services/health";
import { programsService } from "@/services/programs";
import {
  BookOpen,
  Target,
  Flame,
  AlertCircle,
  Calendar,
  MessageSquare,
  Wallet,
  Headphones,
  MessageCircle,
  Award,
} from "lucide-react";
import { motion } from "framer-motion";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Progress } from "@/components/ui/progress";
import { Skeleton } from "@/components/ui/skeleton";
import { format, startOfDay, subDays } from "date-fns";

function useDashboardData() {
  const quickInfo = useQuery({
    queryKey: ["panel-quick-info"],
    queryFn: () => progressService.getQuickInfo(),
  });
  const healthLogs = useQuery({
    queryKey: ["panel-health-logs", "dashboard"],
    queryFn: () =>
      healthService.list({
        from_date: format(subDays(new Date(), 30), "yyyy-MM-dd"),
        to_date: format(new Date(), "yyyy-MM-dd"),
        per_page: 31,
      }),
  });
  const assignments = useQuery({
    queryKey: ["panel-assignments"],
    queryFn: () => programsService.getMyAssignments(),
  });

  const logs = healthLogs.data?.data ?? [];
  const todayStr = format(new Date(), "yyyy-MM-dd");
  const todayLog = logs.find((l) => l.log_date === todayStr);
  const last7 = logs.slice(0, 7);
  const adherence7 =
    last7.length > 0
      ? Math.round(
          (last7.filter((l) => (l.water_ml ?? 0) > 0 || (l.calories ?? 0) > 0).length / last7.length) * 100
        )
      : 0;
  let streak = 0;
  const sortedByDate = [...logs].sort(
    (a, b) => new Date(b.log_date).getTime() - new Date(a.log_date).getTime()
  );
  for (const log of sortedByDate) {
    const d = format(startOfDay(new Date(log.log_date)), "yyyy-MM-dd");
    const expected = format(subDays(new Date(), streak), "yyyy-MM-dd");
    if (d === expected && ((log.water_ml ?? 0) > 0 || (log.calories ?? 0) > 0)) streak++;
    else break;
  }

  const eventCount = quickInfo.data?.unread_notifications?.count ?? 0;
  return {
    quickInfo: quickInfo.data,
    quickInfoLoading: quickInfo.isLoading,
    activeProgramsCount: quickInfo.data?.webinarsCount ?? 0,
    reserveMeetingsCount: quickInfo.data?.reserveMeetingsCount ?? 0,
    balance: quickInfo.data?.balance ?? 0,
    supportsCount: quickInfo.data?.supportsCount ?? 0,
    commentsCount: quickInfo.data?.commentsCount ?? 0,
    badges: quickInfo.data?.badges,
    eventCount,
    todayLog,
    todayStr,
    adherence7,
    streak,
    assignments: Array.isArray(assignments.data) ? assignments.data : [],
    assignmentsLoading: assignments.isLoading,
  };
}

function formatBalance(value: number, currencySign = "₹") {
  return `${currencySign}${Number(value).toLocaleString("en-IN", { minimumFractionDigits: 0, maximumFractionDigits: 2 })}`;
}

export default function Dashboard() {
  const { t, appConfig } = useConfig();
  const {
    quickInfo,
    quickInfoLoading,
    activeProgramsCount,
    todayLog,
    adherence7,
    streak,
    todayStr,
    reserveMeetingsCount,
    balance,
    supportsCount,
    commentsCount,
    badges,
    eventCount,
    assignments,
    assignmentsLoading,
  } = useDashboardData();

  const todayChallenge = assignments[0];
  const currencySign = appConfig?.currency?.sign ?? "₹";
  const badgePercent = badges?.percent ?? 0;
  const nextBadge = badges?.next_badge ?? "New User";

  return (
    <>
      <motion.div
        initial={{ opacity: 0, y: -8 }}
        animate={{ opacity: 1, y: 0 }}
        className="mb-8"
      >
        <h1 className="text-2xl font-display font-bold text-foreground mb-1">
          Dashboard
        </h1>
        <p className="text-muted-foreground">
          Hi, {quickInfo?.full_name ?? "there"} — You have {eventCount} new event{eventCount !== 1 ? "s" : ""}.
        </p>
        {eventCount > 0 && (
          <Link to="/panel/notifications" className="text-sm text-primary font-medium mt-1 inline-block hover:underline">
            View all events
          </Link>
        )}
      </motion.div>

      <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        <motion.div
          initial={{ opacity: 0, y: 12 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.02 }}
        >
          <Card className="border border-border h-full">
            <CardHeader className="pb-2">
              <CardTitle className="text-base font-medium flex items-center gap-2">
                <Wallet size={18} className="text-primary" />
                Account Balance
              </CardTitle>
            </CardHeader>
            <CardContent>
              {quickInfoLoading ? (
                <Skeleton className="h-10 w-20 rounded" />
              ) : (
                <p className="text-2xl font-bold text-foreground">
                  {formatBalance(balance, currencySign)}
                </p>
              )}
              <span className="text-sm text-muted-foreground mt-1 block">Manage in Financial</span>
            </CardContent>
          </Card>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 12 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.05 }}
        >
          <Link to="/panel/programs">
            <Card className="border border-border hover:shadow-card-hover transition-shadow h-full">
              <CardHeader className="pb-2">
                <CardTitle className="text-base font-medium flex items-center gap-2">
                  <BookOpen size={18} className="text-primary" />
                  Purchased {t("courses")}
                </CardTitle>
              </CardHeader>
              <CardContent>
                {quickInfoLoading ? (
                  <Skeleton className="h-10 w-16 rounded" />
                ) : (
                  <p className="text-2xl font-bold text-foreground">
                    {activeProgramsCount}
                  </p>
                )}
                <p className="text-sm text-muted-foreground mt-1">
                  View your programs
                </p>
              </CardContent>
            </Card>
          </Link>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 12 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.07 }}
        >
          <Card className="border border-border h-full">
            <CardHeader className="pb-2">
              <CardTitle className="text-base font-medium flex items-center gap-2">
                <Headphones size={18} className="text-primary" />
                Support Messages
              </CardTitle>
            </CardHeader>
            <CardContent>
              {quickInfoLoading ? (
                <Skeleton className="h-10 w-12 rounded" />
              ) : (
                <p className="text-2xl font-bold text-foreground">{supportsCount}</p>
              )}
            </CardContent>
          </Card>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 12 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.1 }}
        >
          <Card className="border border-border h-full">
            <CardHeader className="pb-2">
              <CardTitle className="text-base font-medium flex items-center gap-2">
                <Target size={18} className="text-primary" />
                Today&apos;s Health Challenge
              </CardTitle>
            </CardHeader>
            <CardContent>
              {assignmentsLoading ? (
                <div className="space-y-2">
                  <Skeleton className="h-5 w-3/4 rounded" />
                  <Skeleton className="h-9 w-24 rounded" />
                </div>
              ) : todayChallenge ? (
                <>
                  <p className="font-semibold text-foreground line-clamp-2">
                    {todayChallenge.title ?? "Daily Challenge"}
                  </p>
                  <Link to="/panel/health-log">
                    <Button variant="outline" size="sm" className="mt-2">
                      Log progress
                    </Button>
                  </Link>
                </>
              ) : (
                <p className="text-muted-foreground text-sm">
                  No challenge for today. Keep up your daily log!
                </p>
              )}
            </CardContent>
          </Card>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 12 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.15 }}
        >
          <Card className="border border-border h-full">
            <CardHeader className="pb-2">
              <CardTitle className="text-base font-medium flex items-center gap-2">
                <Target size={18} className="text-primary" />
                Adherence Score (7 days)
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex items-center gap-4">
                <Progress value={adherence7} className="h-3 flex-1" />
                <span className="text-2xl font-bold text-foreground shrink-0">
                  {adherence7}%
                </span>
              </div>
            </CardContent>
          </Card>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 12 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
        >
          <Card className="border border-border h-full">
            <CardHeader className="pb-2">
              <CardTitle className="text-base font-medium flex items-center gap-2">
                <Flame size={18} className="text-accent" />
                Current Streak
              </CardTitle>
            </CardHeader>
            <CardContent className="pt-0">
              <p className="text-2xl font-bold text-foreground">{streak} days</p>
            </CardContent>
          </Card>
        </motion.div>

        {!todayLog && (
          <motion.div
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.25 }}
          >
            <Link to="/panel/health-log/new">
              <Card className="border-accent/50 bg-accent/5 hover:shadow-card-hover transition-shadow h-full">
                <CardHeader className="pb-2">
                  <CardTitle className="text-base font-medium flex items-center gap-2 text-accent">
                    <AlertCircle size={18} />
                    Missed Log Alert
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <p className="text-sm text-muted-foreground">
                    You haven&apos;t logged for {todayStr}. Log your day to keep your streak.
                  </p>
                  <Button size="sm" className="mt-2 bg-gradient-cta text-primary-foreground">
                    Add today&apos;s log
                  </Button>
                </CardContent>
              </Card>
            </Link>
          </motion.div>
        )}

        <motion.div
          initial={{ opacity: 0, y: 12 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
        >
          <Card className="border border-border h-full">
            <CardHeader className="pb-2">
              <CardTitle className="text-base font-medium flex items-center gap-2">
                <Calendar size={18} className="text-primary" />
                Upcoming Live Class
              </CardTitle>
            </CardHeader>
            <CardContent>
              {reserveMeetingsCount > 0 ? (
                <p className="text-foreground font-medium">
                  {reserveMeetingsCount} session{reserveMeetingsCount !== 1 ? "s" : ""} scheduled
                </p>
              ) : (
                <p className="text-muted-foreground text-sm">
                  No live sessions scheduled right now.
                </p>
              )}
              <Link to="/panel/meetings" className="text-sm text-primary font-medium mt-1 inline-block hover:underline">
                View meetings
              </Link>
            </CardContent>
          </Card>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 12 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.22 }}
        >
          <Card className="border border-border h-full">
            <CardHeader className="pb-2">
              <CardTitle className="text-base font-medium flex items-center gap-2">
                <MessageCircle size={18} className="text-primary" />
                Comments
              </CardTitle>
            </CardHeader>
            <CardContent>
              {quickInfoLoading ? (
                <Skeleton className="h-10 w-12 rounded" />
              ) : (
                <p className="text-2xl font-bold text-foreground">{commentsCount}</p>
              )}
            </CardContent>
          </Card>
        </motion.div>

        <motion.div
          initial={{ opacity: 0, y: 12 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.25 }}
        >
          <Card className="border border-border h-full">
            <CardHeader className="pb-2">
              <CardTitle className="text-base font-medium flex items-center gap-2">
                <Award size={18} className="text-primary" />
                Next Badge
              </CardTitle>
            </CardHeader>
            <CardContent>
              {quickInfoLoading ? (
                <Skeleton className="h-16 w-full rounded" />
              ) : (
                <>
                  <div className="flex items-center gap-4">
                    <Progress value={badgePercent} className="h-3 flex-1" />
                    <span className="text-xl font-bold text-foreground shrink-0">
                      {badgePercent}%
                    </span>
                  </div>
                  <p className="text-sm text-muted-foreground mt-2">
                    Next Badge: {nextBadge}
                  </p>
                </>
              )}
            </CardContent>
          </Card>
        </motion.div>
      </div>

      <motion.div
        initial={{ opacity: 0, y: 12 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: 0.35 }}
      >
        <Card className="mt-8 border border-border">
          <CardHeader>
            <CardTitle className="text-base font-medium flex items-center gap-2">
              <MessageSquare size={18} />
              Noticeboard &amp; Announcements
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground text-sm">
              Check your program noticeboards and notifications for updates from your coaches.
            </p>
            <Link to="/panel/notifications">
              <Button variant="outline" size="sm" className="mt-3">
                View all notifications
              </Button>
            </Link>
          </CardContent>
        </Card>
      </motion.div>
    </>
  );
}

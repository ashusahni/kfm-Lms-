import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { healthService } from "@/services/health";
import { programsService } from "@/services/programs";
import { format, subDays } from "date-fns";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Progress } from "@/components/ui/progress";
import {
  Droplets,
  Utensils,
  Activity,
  TrendingUp,
  Flame,
  Calendar,
  ChevronLeft,
  ChevronRight,
} from "lucide-react";
import { useState, useMemo } from "react";
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  AreaChart,
  Area,
} from "recharts";
import type { HealthLog } from "@/types/api";

const RANGE_OPTIONS = [
  { label: "7 days", days: 7 },
  { label: "14 days", days: 14 },
  { label: "30 days", days: 30 },
] as const;

function computeStreak(logs: HealthLog[]): number {
  const normalize = (log: HealthLog): string => {
    const d = log.log_date;
    if (typeof d === "number") return format(new Date(d * 1000), "yyyy-MM-dd");
    try {
      return format(new Date(d), "yyyy-MM-dd");
    } catch {
      return "";
    }
  };
  const sorted = [...logs].sort(
    (a, b) => new Date(normalize(b)).getTime() - new Date(normalize(a)).getTime()
  );
  const todayStr = format(new Date(), "yyyy-MM-dd");
  let streak = 0;
  for (const log of sorted) {
    const d = normalize(log);
    const expected = format(subDays(new Date(), streak), "yyyy-MM-dd");
    if (d === expected && ((log.water_ml ?? 0) > 0 || (log.calories ?? 0) > 0 || (log.activity_minutes ?? 0) > 0))
      streak++;
    else break;
  }
  return streak;
}

export default function HealthLogOverview() {
  const [rangeDays, setRangeDays] = useState<number>(14);
  const [courseFilter, setCourseFilter] = useState<number | "">("");
  const [page, setPage] = useState(1);
  const perPage = 14;

  const toDate = new Date();
  const fromDate = subDays(toDate, rangeDays);
  const from = format(fromDate, "yyyy-MM-dd");
  const to = format(toDate, "yyyy-MM-dd");

  const { data, isLoading } = useQuery({
    queryKey: ["health-logs-overview", from, to, courseFilter, page],
    queryFn: () =>
      healthService.list({
        from_date: from,
        to_date: to,
        per_page: perPage,
        page,
        ...(courseFilter !== "" ? { webinar_id: courseFilter } : {}),
      }),
  });

  const rawList = data;
  const logs: HealthLog[] = Array.isArray(rawList)
    ? rawList
    : (rawList && typeof rawList === "object" && "data" in rawList && Array.isArray((rawList as { data: unknown[] }).data)
      ? (rawList as { data: HealthLog[] }).data
      : []);
  const totalPages =
    rawList && typeof rawList === "object" && "last_page" in rawList
      ? Number((rawList as { last_page: number }).last_page) || 1
      : 1;
  const todayStr = format(new Date(), "yyyy-MM-dd");
  const normalizeLogDate = (log: HealthLog): string => {
    const d = log.log_date;
    if (typeof d === "number") return format(new Date(d * 1000), "yyyy-MM-dd");
    try {
      return format(new Date(d), "yyyy-MM-dd");
    } catch {
      return "";
    }
  };
  const todayLog = logs.find((l) => normalizeLogDate(l) === todayStr);

  const { data: programs } = useQuery({
    queryKey: ["panel-program-purchases"],
    queryFn: () => programsService.getMyPrograms(),
  });
  const programList = ((): { id: number; title: string }[] => {
    if (!programs) return [];
    if (Array.isArray(programs)) return programs;
    const p = programs as { data?: { id: number; title: string }[] };
    return p.data ?? [];
  })();

  const stats = useMemo(() => {
    const withData = logs.filter(
      (l) =>
        (l.water_ml ?? 0) > 0 || (l.calories ?? 0) > 0 || (l.activity_minutes ?? 0) > 0
    );
    const count = withData.length;
    const avgWater =
      count > 0
        ? Math.round(
            withData.reduce((s, l) => s + (l.water_ml ?? 0), 0) / count
          )
        : 0;
    const avgCalories =
      count > 0
        ? Math.round(
            withData.reduce((s, l) => s + (l.calories ?? 0), 0) / count
          )
        : 0;
    const totalActivity = logs.reduce((s, l) => s + (l.activity_minutes ?? 0), 0);
    const adherence7 =
      count > 0
        ? Math.round(
            (withData.filter((l) => (l.adherence_score ?? 0) >= 50).length / count) * 100
          )
        : 0;
    const scores = logs.map((l) => l.adherence_score).filter((s): s is number => s != null && s >= 0);
    const avgAdherence =
      scores.length > 0
        ? Math.round(scores.reduce((a, b) => a + b, 0) / scores.length)
        : 0;
    const streak = computeStreak(logs);
    return {
      avgWater,
      avgCalories,
      totalActivity,
      adherence7,
      avgAdherence,
      streak,
      daysLogged: count,
    };
  }, [logs]);

  const chartData = useMemo(() => {
    const reversed = [...logs].reverse();
    return reversed.map((log) => ({
      date: format(new Date(normalizeLogDate(log)), "MMM d"),
      fullDate: normalizeLogDate(log),
      water: log.water_ml ?? 0,
      calories: log.calories ?? 0,
      activity: log.activity_minutes ?? 0,
      adherence: log.adherence_score ?? 0,
    }));
  }, [logs]);

  return (
    <>
      <div className="flex flex-wrap items-center justify-between gap-4 mb-8">
        <h1 className="text-2xl font-display font-bold text-foreground">
          Daily Log
        </h1>
        <div className="flex gap-2 flex-wrap items-center">
          {programList.length > 0 && (
            <select
              className="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm max-w-[200px]"
              value={courseFilter}
              onChange={(e) => {
                setCourseFilter(e.target.value === "" ? "" : Number(e.target.value));
                setPage(1);
              }}
            >
              <option value="">All programs</option>
              {programList.map((p) => (
                <option key={p.id} value={p.id}>{p.title}</option>
              ))}
            </select>
          )}
          <div className="flex rounded-lg border border-input bg-muted/50 p-0.5">
            {RANGE_OPTIONS.map(({ label, days }) => (
              <button
                key={days}
                type="button"
                onClick={() => {
                  setRangeDays(days);
                  setPage(1);
                }}
                className={`px-3 py-1.5 text-sm font-medium rounded-md transition-colors ${
                  rangeDays === days
                    ? "bg-background text-foreground shadow-sm"
                    : "text-muted-foreground hover:text-foreground"
                }`}
              >
                {label}
              </button>
            ))}
          </div>
          <Link to="/panel/health-log/weekly">
            <Button variant="outline" size="sm">Weekly summary</Button>
          </Link>
          <Link to="/panel/health-log/new">
            <Button className="bg-gradient-cta">Add log</Button>
          </Link>
        </div>
      </div>

      {/* Stats cards */}
      <div className="grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-6 mb-8">
        <Card className="border border-border">
          <CardContent className="pt-4 pb-4">
            <div className="flex items-center gap-2 text-muted-foreground mb-1">
              <Droplets size={16} />
              <span className="text-xs font-medium">Avg water</span>
            </div>
            <p className="text-xl font-bold text-foreground">{stats.avgWater} ml</p>
          </CardContent>
        </Card>
        <Card className="border border-border">
          <CardContent className="pt-4 pb-4">
            <div className="flex items-center gap-2 text-muted-foreground mb-1">
              <Utensils size={16} />
              <span className="text-xs font-medium">Avg calories</span>
            </div>
            <p className="text-xl font-bold text-foreground">{stats.avgCalories}</p>
          </CardContent>
        </Card>
        <Card className="border border-border">
          <CardContent className="pt-4 pb-4">
            <div className="flex items-center gap-2 text-muted-foreground mb-1">
              <Activity size={16} />
              <span className="text-xs font-medium">Activity</span>
            </div>
            <p className="text-xl font-bold text-foreground">{stats.totalActivity} min</p>
          </CardContent>
        </Card>
        <Card className="border border-border">
          <CardContent className="pt-4 pb-4">
            <div className="flex items-center gap-2 text-muted-foreground mb-1">
              <Flame size={16} />
              <span className="text-xs font-medium">Streak</span>
            </div>
            <p className="text-xl font-bold text-foreground">{stats.streak} days</p>
          </CardContent>
        </Card>
        <Card className="border border-border">
          <CardContent className="pt-4 pb-4">
            <div className="flex items-center gap-2 text-muted-foreground mb-1">
              <TrendingUp size={16} />
              <span className="text-xs font-medium">Adherence</span>
            </div>
            <p className="text-xl font-bold text-foreground">{stats.avgAdherence}%</p>
          </CardContent>
        </Card>
        <Card className="border border-border">
          <CardContent className="pt-4 pb-4">
            <div className="flex items-center gap-2 text-muted-foreground mb-1">
              <Calendar size={16} />
              <span className="text-xs font-medium">Days logged</span>
            </div>
            <p className="text-xl font-bold text-foreground">{stats.daysLogged}</p>
          </CardContent>
        </Card>
      </div>

      {/* Adherence summary bar */}
      <Card className="mb-8 border border-border">
        <CardHeader>
          <CardTitle className="text-base">Adherence score (selected period)</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex items-center gap-4">
            <Progress value={stats.adherence7} className="h-4 flex-1" />
            <span className="text-2xl font-bold text-foreground shrink-0">{stats.adherence7}%</span>
          </div>
        </CardContent>
      </Card>

      {/* Trend charts */}
      {chartData.length > 0 && (
        <div className="grid gap-6 md:grid-cols-2 mb-8">
          <Card className="border border-border">
            <CardHeader>
              <CardTitle className="text-base">Water & calories trend</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="h-[200px] w-full">
                <ResponsiveContainer width="100%" height="100%">
                  <LineChart data={chartData}>
                    <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                    <XAxis dataKey="date" tick={{ fontSize: 11 }} className="text-muted-foreground" />
                    <YAxis yAxisId="left" tick={{ fontSize: 11 }} />
                    <YAxis yAxisId="right" orientation="right" tick={{ fontSize: 11 }} />
                    <Tooltip
                      contentStyle={{ borderRadius: "8px" }}
                      labelFormatter={(_, payload) => payload[0]?.payload?.fullDate}
                      formatter={(value: number, name: string) => [
                        name === "water" ? `${value} ml` : `${value} cal`,
                        name === "water" ? "Water" : "Calories",
                      ]}
                    />
                    <Line
                      yAxisId="left"
                      type="monotone"
                      dataKey="water"
                      name="water"
                      stroke="hsl(var(--primary))"
                      strokeWidth={2}
                      dot={{ r: 3 }}
                    />
                    <Line
                      yAxisId="right"
                      type="monotone"
                      dataKey="calories"
                      name="calories"
                      stroke="hsl(var(--accent))"
                      strokeWidth={2}
                      dot={{ r: 3 }}
                    />
                  </LineChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>
          <Card className="border border-border">
            <CardHeader>
              <CardTitle className="text-base">Adherence & activity</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="h-[200px] w-full">
                <ResponsiveContainer width="100%" height="100%">
                  <AreaChart data={chartData}>
                    <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                    <XAxis dataKey="date" tick={{ fontSize: 11 }} />
                    <YAxis yAxisId="left" tick={{ fontSize: 11 }} domain={[0, 100]} />
                    <YAxis yAxisId="right" orientation="right" tick={{ fontSize: 11 }} />
                    <Tooltip
                      contentStyle={{ borderRadius: "8px" }}
                      formatter={(value: number, name: string) => [
                        name === "adherence" ? `${value}%` : `${value} min`,
                        name === "adherence" ? "Adherence" : "Activity",
                      ]}
                    />
                    <Area
                      yAxisId="left"
                      type="monotone"
                      dataKey="adherence"
                      name="adherence"
                      fill="hsl(var(--primary))"
                      fillOpacity={0.3}
                      stroke="hsl(var(--primary))"
                      strokeWidth={2}
                    />
                    <Line
                      yAxisId="right"
                      type="monotone"
                      dataKey="activity"
                      name="activity"
                      stroke="hsl(var(--accent))"
                      strokeWidth={2}
                      dot={{ r: 3 }}
                    />
                  </AreaChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>
        </div>
      )}

      {!todayLog && (
        <Card className="mb-8 border-accent/50 bg-accent/5">
          <CardContent className="py-6">
            <p className="text-foreground font-medium mb-2">You haven&apos;t logged today.</p>
            <Link to="/panel/health-log/new">
              <Button size="sm" className="bg-gradient-cta">Add today&apos;s log</Button>
            </Link>
          </CardContent>
        </Card>
      )}

      <div className="flex items-center justify-between mb-4">
        <h2 className="text-lg font-semibold text-foreground">Recent logs</h2>
        {totalPages > 1 && (
          <div className="flex items-center gap-1">
            <Button
              variant="outline"
              size="icon"
              className="h-8 w-8"
              onClick={() => setPage((p) => Math.max(1, p - 1))}
              disabled={page <= 1}
            >
              <ChevronLeft size={16} />
            </Button>
            <span className="text-sm text-muted-foreground px-2">
              Page {page} of {totalPages}
            </span>
            <Button
              variant="outline"
              size="icon"
              className="h-8 w-8"
              onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
              disabled={page >= totalPages}
            >
              <ChevronRight size={16} />
            </Button>
          </div>
        )}
      </div>

      {isLoading && (
        <div className="space-y-3">
          {[1, 2, 3].map((i) => (
            <div key={i} className="h-20 bg-muted rounded-xl animate-pulse" />
          ))}
        </div>
      )}
      {!isLoading && logs.length === 0 && (
        <Card className="border border-border">
          <CardContent className="py-12 text-center text-muted-foreground">
            No logs in this range. Add a log or change the date range.
          </CardContent>
        </Card>
      )}
      {!isLoading && logs.length > 0 && (
        <div className="space-y-3">
          {logs.map((log) => (
            <Link key={log.id} to={`/panel/health-log/edit/${log.id}`}>
              <Card className="border border-border hover:shadow-card-hover transition-shadow">
                <CardContent className="py-4 flex flex-wrap items-center gap-x-6 gap-y-2">
                  <span className="font-medium text-foreground">
                    {format(new Date(normalizeLogDate(log)), "EEE, MMM d, yyyy")}
                  </span>
                  {log.webinar && (
                    <span className="text-xs text-muted-foreground bg-muted px-2 py-0.5 rounded">
                      {log.webinar.title}
                    </span>
                  )}
                  <div className="flex flex-wrap gap-x-4 gap-y-1 text-sm text-muted-foreground">
                    {log.water_ml != null && log.water_ml > 0 && (
                      <span className="flex items-center gap-1">
                        <Droplets size={14} /> {log.water_ml} ml
                      </span>
                    )}
                    {log.calories != null && log.calories > 0 && (
                      <span className="flex items-center gap-1">
                        <Utensils size={14} /> {log.calories} cal
                      </span>
                    )}
                    {(log.protein != null && log.protein > 0) ||
                    (log.carbs != null && log.carbs > 0) ||
                    (log.fat != null && log.fat > 0) ? (
                      <span className="flex items-center gap-1">
                        P:{log.protein ?? 0} C:{log.carbs ?? 0} F:{log.fat ?? 0}
                      </span>
                    ) : null}
                    {log.activity_minutes != null && log.activity_minutes > 0 && (
                      <span className="flex items-center gap-1">
                        <Activity size={14} /> {log.activity_minutes} min
                      </span>
                    )}
                  </div>
                  {log.adherence_score != null && (
                    <span className="ml-auto text-sm font-semibold text-primary">
                      Score: {log.adherence_score}%
                    </span>
                  )}
                </CardContent>
              </Card>
            </Link>
          ))}
        </div>
      )}
    </>
  );
}

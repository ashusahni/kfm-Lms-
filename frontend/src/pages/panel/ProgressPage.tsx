import { useQuery } from "@tanstack/react-query";
import { healthService } from "@/services/health";
import { format, subDays } from "date-fns";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Progress } from "@/components/ui/progress";
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
} from "recharts";
import type { HealthLog } from "@/types/api";

function normalizeLogDate(log: HealthLog): string {
  const d = log.log_date;
  if (typeof d === "number") return format(new Date(d * 1000), "yyyy-MM-dd");
  try {
    return format(new Date(d), "yyyy-MM-dd");
  } catch {
    return "";
  }
}

export default function ProgressPage() {
  const from = format(subDays(new Date(), 30), "yyyy-MM-dd");
  const to = format(new Date(), "yyyy-MM-dd");
  const { data, isLoading } = useQuery({
    queryKey: ["health-logs-progress", from, to],
    queryFn: () => healthService.list({ from_date: from, to_date: to, per_page: 30 }),
  });

  const rawList = data;
  const logs: HealthLog[] = Array.isArray(rawList)
    ? rawList
    : (rawList && typeof rawList === "object" && "data" in rawList && Array.isArray((rawList as { data: unknown[] }).data)
      ? (rawList as { data: HealthLog[] }).data
      : []);
  const last7 = logs.slice(0, 7);
  const last30 = logs.slice(0, 30);
  const adherence7 =
    last7.length > 0
      ? Math.round(
          (last7.filter((l) => (l.water_ml ?? 0) > 0 || (l.calories ?? 0) > 0).length / last7.length) * 100
        )
      : 0;
  const adherence30 =
    last30.length > 0
      ? Math.round(
          (last30.filter((l) => (l.water_ml ?? 0) > 0 || (l.calories ?? 0) > 0).length / last30.length) * 100
        )
      : 0;

  let streak = 0;
  const sorted = [...logs].sort(
    (a, b) => new Date(normalizeLogDate(b)).getTime() - new Date(normalizeLogDate(a)).getTime()
  );
  for (const log of sorted) {
    const d = normalizeLogDate(log);
    const expected = format(subDays(new Date(), streak), "yyyy-MM-dd");
    if (d === expected && ((log.water_ml ?? 0) > 0 || (log.calories ?? 0) > 0)) streak++;
    else break;
  }

  const chartData = [...last30].reverse().map((l) => ({
    date: format(new Date(normalizeLogDate(l)), "MM/dd"),
    score: l.adherence_score ?? 0,
    water: (l.water_ml ?? 0) / 1000,
    calories: l.calories ?? 0,
  }));

  const missedDays = 30 - last30.filter((l) => (l.water_ml ?? 0) > 0 || (l.calories ?? 0) > 0).length;

  return (
    <>
      <h1 className="text-2xl font-display font-bold text-foreground mb-8">
        Progress & Adherence
      </h1>

      {isLoading && (
        <div className="space-y-6">
          <div className="h-32 bg-muted rounded-xl animate-pulse" />
          <div className="h-64 bg-muted rounded-xl animate-pulse" />
        </div>
      )}

      {!isLoading && (
        <>
          <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-4 mb-8">
            <Card className="border border-border">
              <CardHeader className="pb-2">
                <CardTitle className="text-base">Weekly adherence</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="flex items-center gap-4">
                  <Progress value={adherence7} className="h-3 flex-1" />
                  <span className="text-xl font-bold">{adherence7}%</span>
                </div>
              </CardContent>
            </Card>
            <Card className="border border-border">
              <CardHeader className="pb-2">
                <CardTitle className="text-base">Monthly adherence</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="flex items-center gap-4">
                  <Progress value={adherence30} className="h-3 flex-1" />
                  <span className="text-xl font-bold">{adherence30}%</span>
                </div>
              </CardContent>
            </Card>
            <Card className="border border-border">
              <CardHeader className="pb-2">
                <CardTitle className="text-base">Current streak</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-2xl font-bold text-foreground">{streak} days</p>
              </CardContent>
            </Card>
            <Card className="border border-border">
              <CardHeader className="pb-2">
                <CardTitle className="text-base">Missed days (30d)</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-2xl font-bold text-foreground">{missedDays}</p>
              </CardContent>
            </Card>
          </div>

          <Card className="border border-border">
            <CardHeader>
              <CardTitle className="text-base">Adherence trend</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="h-64">
                <ResponsiveContainer width="100%" height="100%">
                  <LineChart data={chartData}>
                    <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                    <XAxis dataKey="date" className="text-xs" />
                    <YAxis domain={[0, 100]} className="text-xs" />
                    <Tooltip />
                    <Line
                      type="monotone"
                      dataKey="score"
                      name="Adherence %"
                      stroke="hsl(var(--primary))"
                      strokeWidth={2}
                    />
                  </LineChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>
        </>
      )}
    </>
  );
}

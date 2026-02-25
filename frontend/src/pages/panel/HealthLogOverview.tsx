import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { healthService } from "@/services/health";
import { programsService } from "@/services/programs";
import { format, subDays } from "date-fns";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Progress } from "@/components/ui/progress";
import { Droplets, Utensils, Activity } from "lucide-react";
import { useState } from "react";

export default function HealthLogOverview() {
  const [courseFilter, setCourseFilter] = useState<number | "">("");
  const from = format(subDays(new Date(), 14), "yyyy-MM-dd");
  const to = format(new Date(), "yyyy-MM-dd");
  const { data, isLoading } = useQuery({
    queryKey: ["health-logs-overview", from, to, courseFilter],
    queryFn: () =>
      healthService.list({
        from_date: from,
        to_date: to,
        per_page: 14,
        ...(courseFilter !== "" ? { webinar_id: courseFilter } : {}),
      }),
  });

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

  const logs = data?.data ?? [];
  const todayStr = format(new Date(), "yyyy-MM-dd");
  const todayLog = logs.find((l) => l.log_date === todayStr);
  const last7 = logs.slice(0, 7);
  const adherence7 =
    last7.length > 0
      ? Math.round(
          (last7.filter((l) => (l.water_ml ?? 0) > 0 || (l.calories ?? 0) > 0).length / last7.length) * 100
        )
      : 0;

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
              onChange={(e) => setCourseFilter(e.target.value === "" ? "" : Number(e.target.value))}
            >
              <option value="">All programs</option>
              {programList.map((p) => (
                <option key={p.id} value={p.id}>{p.title}</option>
              ))}
            </select>
          )}
          <Link to="/panel/health-log/weekly">
            <Button variant="outline">Weekly summary</Button>
          </Link>
          <Link to="/panel/health-log/new">
            <Button className="bg-gradient-cta">Add log</Button>
          </Link>
        </div>
      </div>

      <Card className="mb-8 border border-border">
        <CardHeader>
          <CardTitle className="text-base">Adherence score (last 7 days)</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex items-center gap-4">
            <Progress value={adherence7} className="h-4 flex-1" />
            <span className="text-2xl font-bold text-foreground">{adherence7}%</span>
          </div>
        </CardContent>
      </Card>

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

      <h2 className="text-lg font-semibold text-foreground mb-4">Recent logs</h2>
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
            No logs yet. Add your first log to track your daily health.
          </CardContent>
        </Card>
      )}
      {!isLoading && logs.length > 0 && (
        <div className="space-y-3">
          {logs.map((log) => (
            <Link key={log.id} to={`/panel/health-log/edit/${log.id}`}>
              <Card className="border border-border hover:shadow-card-hover transition-shadow">
                <CardContent className="py-4 flex flex-wrap items-center gap-4">
                  <span className="font-medium text-foreground">
                    {format(new Date(log.log_date), "EEE, MMM d, yyyy")}
                  </span>
                  {log.webinar && (
                    <span className="text-xs text-muted-foreground bg-muted px-2 py-0.5 rounded">
                      {log.webinar.title}
                    </span>
                  )}
                  <div className="flex gap-4 text-sm text-muted-foreground">
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

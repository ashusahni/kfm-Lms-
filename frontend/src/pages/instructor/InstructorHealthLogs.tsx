import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { format, subDays } from "date-fns";
import { healthService } from "@/services/health";
import { instructorCoursesService } from "@/services/batches";
import {
  Droplets,
  Utensils,
  TrendingUp,
  Calendar,
  ChevronLeft,
  ChevronRight,
  Heart,
} from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import type { HealthLog } from "@/types/api";
import type { WebinarBrief } from "@/types/api";

const RANGE_OPTIONS = [
  { label: "7 days", days: 7 },
  { label: "14 days", days: 14 },
  { label: "30 days", days: 30 },
] as const;

function normalizeLogDate(log: HealthLog): string {
  const d = log.log_date;
  if (typeof d === "number") return format(new Date(d * 1000), "yyyy-MM-dd");
  try {
    return format(new Date(d), "yyyy-MM-dd");
  } catch {
    return "";
  }
}

export default function InstructorHealthLogs() {
  const [rangeDays, setRangeDays] = useState<number>(14);
  const [courseFilter, setCourseFilter] = useState<number | "">("");
  const [page, setPage] = useState(1);
  const perPage = 15;

  const toDate = new Date();
  const fromDate = subDays(toDate, rangeDays);
  const from = format(fromDate, "yyyy-MM-dd");
  const to = format(toDate, "yyyy-MM-dd");

  const { data: courses } = useQuery({
    queryKey: ["instructor-my-courses"],
    queryFn: () => instructorCoursesService.getMyClasses(),
  });
  const courseList: WebinarBrief[] = Array.isArray(courses) ? courses : [];

  const { data: listData, isLoading: listLoading } = useQuery({
    queryKey: ["instructor-health-logs", from, to, courseFilter, page],
    queryFn: () =>
      healthService.list({
        from_date: from,
        to_date: to,
        per_page: perPage,
        page,
        ...(courseFilter !== "" ? { webinar_id: courseFilter } : {}),
      }),
  });

  const { data: summaryData } = useQuery({
    queryKey: ["instructor-health-logs-summary", from, to, courseFilter],
    queryFn: () =>
      healthService.summary({
        from_date: from,
        to_date: to,
        ...(courseFilter !== "" ? { webinar_id: courseFilter } : {}),
      }),
  });

  const rawList = listData;
  const logs: HealthLog[] = Array.isArray(rawList)
    ? rawList
    : (rawList &&
      typeof rawList === "object" &&
      "data" in rawList &&
      Array.isArray((rawList as { data: unknown[] }).data)
      ? (rawList as { data: HealthLog[] }).data
      : []);
  const totalPages =
    rawList && typeof rawList === "object" && "last_page" in rawList
      ? Number((rawList as { last_page: number }).last_page) || 1
      : 1;

  const summary = summaryData ?? null;

  return (
    <div className="space-y-8">
      <div className="flex flex-wrap items-center justify-between gap-4">
        <div className="flex items-center gap-3">
          <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
            <Heart className="h-6 w-6" />
          </div>
          <div>
            <h1 className="text-2xl font-display font-bold text-foreground">
              Student health logs
            </h1>
            <p className="text-sm text-muted-foreground">
              Monitor health logs for students in your programs (read-only).
            </p>
          </div>
        </div>
        <div className="flex gap-2 flex-wrap items-center">
          {courseList.length > 0 && (
            <select
              className="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm max-w-[220px]"
              value={courseFilter}
              onChange={(e) => {
                setCourseFilter(e.target.value === "" ? "" : Number(e.target.value));
                setPage(1);
              }}
            >
              <option value="">All programs</option>
              {courseList.map((c) => (
                <option key={c.id} value={c.id}>
                  {c.title}
                </option>
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
        </div>
      </div>

      {/* Summary cards */}
      {summary && (
        <div className="grid gap-4 grid-cols-2 md:grid-cols-4">
          <Card className="border border-border">
            <CardContent className="pt-4 pb-4">
              <div className="flex items-center gap-2 text-muted-foreground mb-1">
                <Calendar size={16} />
                <span className="text-xs font-medium">Total entries</span>
              </div>
              <p className="text-xl font-bold text-foreground">
                {summary.total_entries}
              </p>
            </CardContent>
          </Card>
          <Card className="border border-border">
            <CardContent className="pt-4 pb-4">
              <div className="flex items-center gap-2 text-muted-foreground mb-1">
                <Droplets size={16} />
                <span className="text-xs font-medium">Avg water (ml)</span>
              </div>
              <p className="text-xl font-bold text-foreground">
                {summary.avg_water_ml ?? "—"}
              </p>
            </CardContent>
          </Card>
          <Card className="border border-border">
            <CardContent className="pt-4 pb-4">
              <div className="flex items-center gap-2 text-muted-foreground mb-1">
                <Utensils size={16} />
                <span className="text-xs font-medium">Avg calories</span>
              </div>
              <p className="text-xl font-bold text-foreground">
                {summary.avg_calories ?? "—"}
              </p>
            </CardContent>
          </Card>
          <Card className="border border-border">
            <CardContent className="pt-4 pb-4">
              <div className="flex items-center gap-2 text-muted-foreground mb-1">
                <TrendingUp size={16} />
                <span className="text-xs font-medium">Avg adherence</span>
              </div>
              <p className="text-xl font-bold text-foreground">
                {summary.avg_adherence_score != null
                  ? `${summary.avg_adherence_score}%`
                  : "—"}
              </p>
            </CardContent>
          </Card>
        </div>
      )}

      {/* Logs table */}
      <Card className="border border-border overflow-hidden">
        <CardHeader>
          <CardTitle className="text-base">Recent logs</CardTitle>
        </CardHeader>
        <CardContent className="p-0">
          {listLoading && (
            <div className="p-8 space-y-3">
              {[1, 2, 3, 4, 5].map((i) => (
                <div
                  key={i}
                  className="h-14 bg-muted rounded-lg animate-pulse"
                />
              ))}
            </div>
          )}
          {!listLoading && logs.length === 0 && (
            <div className="py-16 text-center text-muted-foreground">
              No student health logs in this period. Try a different date range
              or program.
            </div>
          )}
          {!listLoading && logs.length > 0 && (
            <>
              <div className="overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-border bg-muted/30">
                      <th className="text-left py-3 px-4 font-medium text-foreground">
                        Student
                      </th>
                      <th className="text-left py-3 px-4 font-medium text-foreground">
                        Program
                      </th>
                      <th className="text-left py-3 px-4 font-medium text-foreground">
                        Date
                      </th>
                      <th className="text-right py-3 px-4 font-medium text-foreground">
                        Water
                      </th>
                      <th className="text-right py-3 px-4 font-medium text-foreground">
                        Calories
                      </th>
                      <th className="text-right py-3 px-4 font-medium text-foreground">
                        Activity
                      </th>
                      <th className="text-right py-3 px-4 font-medium text-foreground">
                        Adherence
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    {logs.map((log) => (
                      <tr
                        key={log.id}
                        className="border-b border-border/70 hover:bg-muted/20 transition-colors"
                      >
                        <td className="py-3 px-4 font-medium text-foreground">
                          {log.user?.full_name ?? `User #${log.user_id}`}
                        </td>
                        <td className="py-3 px-4 text-muted-foreground">
                          {log.webinar?.title ?? "—"}
                        </td>
                        <td className="py-3 px-4 text-muted-foreground">
                          {format(
                            new Date(normalizeLogDate(log)),
                            "MMM d, yyyy"
                          )}
                        </td>
                        <td className="py-3 px-4 text-right">
                          {log.water_ml != null && log.water_ml > 0
                            ? `${log.water_ml} ml`
                            : "—"}
                        </td>
                        <td className="py-3 px-4 text-right">
                          {log.calories != null && log.calories > 0
                            ? log.calories
                            : "—"}
                        </td>
                        <td className="py-3 px-4 text-right">
                          {log.activity_minutes != null &&
                          log.activity_minutes > 0
                            ? `${log.activity_minutes} min`
                            : "—"}
                        </td>
                        <td className="py-3 px-4 text-right">
                          {log.adherence_score != null
                            ? `${log.adherence_score}%`
                            : "—"}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
              {totalPages > 1 && (
                <div className="flex items-center justify-end gap-2 py-3 px-4 border-t border-border bg-muted/20">
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
                    onClick={() =>
                      setPage((p) => Math.min(totalPages, p + 1))
                    }
                    disabled={page >= totalPages}
                  >
                    <ChevronRight size={16} />
                  </Button>
                </div>
              )}
            </>
          )}
        </CardContent>
      </Card>
    </div>
  );
}

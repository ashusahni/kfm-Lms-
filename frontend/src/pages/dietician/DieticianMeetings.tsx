import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import {
  dieticianMeetingsService,
  type MeetingReservation,
  type DieticianMeetingsData,
} from "@/services/dietician";
import { Calendar, Video, CheckCircle, Loader2 } from "lucide-react";
import { motion } from "framer-motion";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import { format } from "date-fns";
import { toast } from "sonner";

function formatDayTime(day?: string, time?: string, created?: number): string {
  if (day || time) return [day, time].filter(Boolean).join(" ");
  if (created) return format(new Date(created * 1000), "MMM d, yyyy HH:mm");
  return "—";
}

export default function DieticianMeetings() {
  const queryClient = useQueryClient();
  const { data, isLoading, error } = useQuery({
    queryKey: ["dietician-meetings"],
    queryFn: () => dieticianMeetingsService.get(),
  });

  const finishMutation = useMutation({
    mutationFn: (id: number) => dieticianMeetingsService.finish(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["dietician-meetings"] });
      toast.success("Meeting marked as finished.");
    },
    onError: (e) => toast.error(e instanceof Error ? e.message : "Failed to finish"),
  });

  const meetingsData: DieticianMeetingsData = data ?? {
    reservations: { count: 0, meetings: [] },
    requests: { count: 0, meetings: [] },
  };
  const requests = meetingsData.requests?.meetings ?? [];
  const reservations = meetingsData.reservations?.meetings ?? [];

  return (
    <div className="space-y-8">
      <motion.div
        initial={{ opacity: 0, y: 8 }}
        animate={{ opacity: 1, y: 0 }}
        className="relative rounded-2xl bg-gradient-to-br from-primary/15 via-primary/5 to-transparent dark:from-primary/20 dark:via-primary/10 p-8 border border-primary/10"
      >
        <div className="flex items-center gap-4">
          <div className="flex h-14 w-14 items-center justify-center rounded-xl bg-primary/10 text-primary">
            <Video className="h-7 w-7" />
          </div>
          <div>
            <h1 className="text-3xl font-display font-bold tracking-tight text-foreground">
              Meetings & consultations
            </h1>
            <p className="mt-1 text-muted-foreground">
              View meeting requests from students and your scheduled reservations.
            </p>
          </div>
        </div>
      </motion.div>

      {error && (
        <Card className="border-destructive/50">
          <CardContent className="py-8 text-center text-muted-foreground">
            {(error as Error).message}
          </CardContent>
        </Card>
      )}

      {isLoading && (
        <div className="grid gap-6 md:grid-cols-2">
          <Skeleton className="h-48 rounded-xl" />
          <Skeleton className="h-48 rounded-xl" />
        </div>
      )}

      {!isLoading && !error && (
        <div className="grid gap-8 md:grid-cols-2">
          <Card className="border border-border overflow-hidden">
            <CardHeader className="pb-2">
              <CardTitle className="text-lg flex items-center gap-2">
                <Calendar className="h-5 w-5" />
                Meeting requests
                <span className="text-sm font-normal text-muted-foreground">
                  ({requests.length})
                </span>
              </CardTitle>
            </CardHeader>
            <CardContent className="pt-0">
              {requests.length === 0 ? (
                <p className="text-sm text-muted-foreground py-4">
                  No pending meeting requests.
                </p>
              ) : (
                <ul className="space-y-3">
                  {requests.map((m: MeetingReservation) => (
                    <li
                      key={m.id ?? 0}
                      className="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-border bg-muted/30 p-3"
                    >
                      <div>
                        <p className="font-medium text-foreground">
                          {m.user?.full_name ?? "Student"}
                        </p>
                        <p className="text-xs text-muted-foreground">
                          {m.meeting?.title ?? "Consultation"} ·{" "}
                          {formatDayTime(m.day, m.time, m.created_at)}
                        </p>
                      </div>
                      <Button
                        size="sm"
                        onClick={() => m.id && finishMutation.mutate(m.id)}
                        disabled={finishMutation.isPending}
                      >
                        {finishMutation.isPending ? (
                          <Loader2 className="h-4 w-4 animate-spin" />
                        ) : (
                          <>
                            <CheckCircle className="h-4 w-4 mr-1" />
                            Mark finished
                          </>
                        )}
                      </Button>
                    </li>
                  ))}
                </ul>
              )}
            </CardContent>
          </Card>

          <Card className="border border-border overflow-hidden">
            <CardHeader className="pb-2">
              <CardTitle className="text-lg flex items-center gap-2">
                <Video className="h-5 w-5" />
                My reservations
                <span className="text-sm font-normal text-muted-foreground">
                  ({reservations.length})
                </span>
              </CardTitle>
            </CardHeader>
            <CardContent className="pt-0">
              {reservations.length === 0 ? (
                <p className="text-sm text-muted-foreground py-4">
                  No upcoming reservations.
                </p>
              ) : (
                <ul className="space-y-3">
                  {reservations.map((m: MeetingReservation) => (
                    <li
                      key={m.id ?? 0}
                      className="rounded-lg border border-border bg-muted/30 p-3"
                    >
                      <p className="font-medium text-foreground">
                        {m.meeting?.title ?? "Consultation"}
                      </p>
                      <p className="text-xs text-muted-foreground">
                        {formatDayTime(m.day, m.time, m.created_at)}
                      </p>
                    </li>
                  ))}
                </ul>
              )}
            </CardContent>
          </Card>
        </div>
      )}
    </div>
  );
}

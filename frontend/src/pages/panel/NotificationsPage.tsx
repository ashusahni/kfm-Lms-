import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { notificationsService } from "@/services/notifications";
import type { NotificationItem } from "@/types/api";
import { format } from "date-fns";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Bell } from "lucide-react";

export default function NotificationsPage() {
  const queryClient = useQueryClient();
  const { data: notifications, isLoading } = useQuery({
    queryKey: ["panel-notifications"],
    queryFn: () => notificationsService.list(),
  });

  const list = notifications?.notifications ?? [];
  const isUnread = (n: (typeof list)[0]) =>
    (n as { status?: string }).status !== "read" && !(n as NotificationItem).read_at;
  const markSeen = useMutation({
    mutationFn: (id: number) => notificationsService.markSeen(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["panel-notifications"] });
      queryClient.invalidateQueries({ queryKey: ["panel-quick-info"] });
    },
  });

  return (
    <>
      <h1 className="text-2xl font-display font-bold text-foreground mb-8">
        Notifications
      </h1>

      {isLoading && (
        <div className="space-y-3">
          {[1, 2, 3].map((i) => (
            <div key={i} className="h-20 bg-muted rounded-xl animate-pulse" />
          ))}
        </div>
      )}

      {!isLoading && list.length === 0 && (
        <Card className="border border-border">
          <CardContent className="py-12 text-center text-muted-foreground">
            <Bell className="mx-auto mb-4 h-12 w-12 opacity-50" />
            <p>No notifications yet.</p>
          </CardContent>
        </Card>
      )}

      {!isLoading && list.length > 0 && (
        <div className="space-y-3">
          {list.map((n) => (
            <Card
              key={n.id}
              className={`border ${isUnread(n) ? "border-primary/30 bg-primary/5" : "border-border"}`}
            >
              <CardContent className="py-4 flex flex-wrap items-start justify-between gap-2">
                <div>
                  <p className="font-medium text-foreground">{n.title ?? "Notification"}</p>
                  {n.message && (
                    <p className="text-sm text-muted-foreground mt-1">{n.message}</p>
                  )}
                  {n.created_at && (
                    <p className="text-xs text-muted-foreground mt-2">
                      {format(
                        new Date(typeof n.created_at === "number" ? n.created_at * 1000 : n.created_at),
                        "MMM d, yyyy HH:mm"
                      )}
                    </p>
                  )}
                </div>
                {isUnread(n) && (
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => markSeen.mutate(n.id)}
                    disabled={markSeen.isPending}
                  >
                    Mark read
                  </Button>
                )}
              </CardContent>
            </Card>
          ))}
        </div>
      )}
    </>
  );
}

import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { progressService } from "@/services/progress";
import { Calendar } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";
import { format } from "date-fns";

export default function PanelMeetings() {
  const { data, isLoading } = useQuery({
    queryKey: ["panel-meetings"],
    queryFn: () => progressService.getMeetings(),
  });
  const list = Array.isArray(data) ? data : [];

  return (
    <>
      <div className="mb-8 flex items-center gap-4">
        <Link to="/panel" className="text-sm text-muted-foreground hover:text-foreground">
          ← Dashboard
        </Link>
      </div>
      <h1 className="text-2xl font-display font-bold text-foreground mb-8">
        Live sessions & meetings
      </h1>
      {isLoading && (
        <div className="space-y-3">
          {[1, 2].map((i) => (
            <div key={i} className="h-20 bg-muted rounded-xl animate-pulse" />
          ))}
        </div>
      )}
      {!isLoading && list.length === 0 && (
        <Card className="border border-border">
          <CardContent className="py-12 text-center text-muted-foreground">
            <Calendar className="mx-auto mb-4 h-12 w-12 opacity-50" />
            <p>No upcoming live sessions.</p>
          </CardContent>
        </Card>
      )}
      {!isLoading && list.length > 0 && (
        <div className="space-y-3">
          {list.map((m: { id?: number; start_at?: number; title?: string; join_url?: string }, i) => (
            <Card key={(m as { id?: number }).id ?? i} className="border border-border">
              <CardContent className="py-4 flex flex-wrap items-center justify-between gap-2">
                <div>
                  <p className="font-medium text-foreground">{m.title ?? "Session"}</p>
                  {m.start_at && (
                    <p className="text-sm text-muted-foreground">
                      {format(new Date(m.start_at * 1000), "EEE, MMM d, yyyy HH:mm")}
                    </p>
                  )}
                </div>
                {m.join_url && (
                  <a
                    href={m.join_url}
                    target="_blank"
                    rel="noreferrer"
                    className="text-sm font-medium text-primary hover:underline"
                  >
                    Join live
                  </a>
                )}
              </CardContent>
            </Card>
          ))}
        </div>
      )}
    </>
  );
}

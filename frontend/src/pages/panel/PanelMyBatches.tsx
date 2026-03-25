import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import { Layers, BookOpen } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";

interface BatchEnrollment {
  sale_id: number;
  webinar_id: number;
  webinar_title: string | null;
  learning_url?: string | null;
  batch_id: number;
  batch_name: string | null;
  batch_code: string | null;
}

export default function PanelMyBatches() {
  const { data, isLoading, error } = useQuery({
    queryKey: ["panel-my-batches"],
    queryFn: async () => {
      const res = await api.get<{ enrollments?: BatchEnrollment[] }>(paths.panel.myBatches);
      return (res && typeof res === "object" && "enrollments" in res
        ? (res as { enrollments: BatchEnrollment[] }).enrollments
        : []) as BatchEnrollment[];
    },
  });

  const list: BatchEnrollment[] = Array.isArray(data) ? data : [];

  return (
    <div className="space-y-10">
      <div>
        <h1 className="text-3xl font-display font-bold tracking-tight text-foreground">
          My batches
        </h1>
        <p className="mt-2 text-muted-foreground">
          Batches (cohorts) you are enrolled in. View-only.
        </p>
      </div>

      {isLoading && (
        <div className="space-y-4">
          <Skeleton className="h-20 rounded-xl" />
          <Skeleton className="h-20 rounded-xl" />
          <Skeleton className="h-20 rounded-xl" />
        </div>
      )}

      {error && (
        <Card className="border-destructive/50">
          <CardContent className="py-8 text-center text-muted-foreground">
            {(error as Error).message}
          </CardContent>
        </Card>
      )}

      {!isLoading && !error && list.length === 0 && (
        <Card className="border border-border overflow-hidden">
          <div className="px-6 py-16 text-center">
            <Layers className="mx-auto h-14 w-14 text-muted-foreground/60" />
            <p className="mt-4 font-medium text-foreground">No batch enrollments</p>
            <p className="mt-1 text-sm text-muted-foreground">
              When you enroll in a course that uses batches, your batch will appear here.
            </p>
            <Link to="/programs" className="inline-block mt-6 text-primary font-medium hover:underline">
              Browse programs
            </Link>
          </div>
        </Card>
      )}

      {!isLoading && !error && list.length > 0 && (
        <div className="space-y-4">
          {list.map((enrollment) => (
            <Card key={enrollment.sale_id} className="border border-border">
              <CardContent className="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div className="flex items-center gap-4">
                  <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
                    <BookOpen className="h-6 w-6" />
                  </div>
                  <div>
                    <h2 className="font-semibold text-foreground">
                      {enrollment.webinar_title ?? `Course #${enrollment.webinar_id}`}
                    </h2>
                    <p className="text-sm text-muted-foreground mt-1">
                      Batch: {enrollment.batch_name ?? `#${enrollment.batch_id}`}
                      {enrollment.batch_code ? ` (${enrollment.batch_code})` : ""}
                    </p>
                  </div>
                </div>
                <Link
                  to={`/panel/learn/${enrollment.webinar_id}`}
                  className="text-sm font-medium text-primary hover:underline shrink-0"
                >
                  View course →
                </Link>
              </CardContent>
            </Card>
          ))}
        </div>
      )}
    </div>
  );
}

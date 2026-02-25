import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { programsService } from "@/services/programs";
import { useConfig } from "@/context/ConfigContext";
import { BookOpen } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";

function isPaginated<T>(d: unknown): d is { data: T[] } {
  return typeof d === "object" && d !== null && "data" in d && Array.isArray((d as { data: unknown }).data);
}

function isWebinarsList<T>(d: unknown): d is { webinars: T[] } {
  return typeof d === "object" && d !== null && "webinars" in d && Array.isArray((d as { webinars: unknown }).webinars);
}

export default function PanelPrograms() {
  const { t } = useConfig();
  const { data, isLoading, error } = useQuery({
    queryKey: ["panel-program-purchases"],
    queryFn: () => programsService.getMyPrograms(),
  });

  const list = !data
    ? []
    : isPaginated(data)
      ? data.data
      : isWebinarsList(data)
        ? data.webinars
        : Array.isArray(data)
          ? data
          : [];

  return (
    <>
      <h1 className="text-2xl font-display font-bold text-foreground mb-8">
        My {t("courses")}
      </h1>
      {isLoading && (
        <div className="grid md:grid-cols-2 gap-4">
          {[1, 2, 3].map((i) => (
            <div key={i} className="h-32 bg-muted rounded-xl animate-pulse" />
          ))}
        </div>
      )}
      {error && (
        <p className="text-destructive">
          {(error as Error).message}
        </p>
      )}
      {!isLoading && !error && list.length === 0 && (
        <Card className="border border-border">
          <CardContent className="py-12 text-center text-muted-foreground">
            <BookOpen className="mx-auto mb-4 h-12 w-12 opacity-50" />
            <p>You are not enrolled in any {t("courses").toLowerCase()} yet.</p>
            <Link to="/programs" className="text-primary font-medium mt-2 inline-block hover:underline">
              Browse programs
            </Link>
          </CardContent>
        </Card>
      )}
      {!isLoading && list.length > 0 && (
        <div className="grid md:grid-cols-2 gap-6">
          {list.map((prog) => (
            <Link key={prog.id} to={`/programs/${prog.id}`}>
              <Card className="border border-border hover:shadow-card-hover transition-shadow h-full overflow-hidden">
                <div className="aspect-video bg-muted relative">
                  <img
                    src={prog.image}
                    alt={prog.title}
                    className="w-full h-full object-cover"
                  />
                </div>
                <CardContent className="p-4">
                  <h2 className="font-semibold text-foreground line-clamp-2">
                    {prog.title}
                  </h2>
                  <span className="text-sm text-primary font-medium mt-2 inline-block">
                    Open program →
                  </span>
                </CardContent>
              </Card>
            </Link>
          ))}
        </div>
      )}
    </>
  );
}

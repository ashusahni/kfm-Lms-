import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { instructorCoursesService } from "@/services/batches";
import { Layers, BookOpen, ArrowRight } from "lucide-react";
import { motion } from "framer-motion";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";

export default function InstructorDashboard() {
  const { data: courses, isLoading } = useQuery({
    queryKey: ["panel-my-courses"],
    queryFn: () => instructorCoursesService.getMyClasses(),
    retry: (_, err) => {
      const msg = err instanceof Error ? err.message : "";
      if (msg.includes("403") || msg.includes("Forbidden")) return false;
      return true;
    },
  });
  const list = Array.isArray(courses) ? courses : [];
  const count = list.length;

  return (
    <div className="space-y-10">
      <motion.div
        initial={{ opacity: 0, y: 8 }}
        animate={{ opacity: 1, y: 0 }}
        className="relative rounded-2xl bg-gradient-to-br from-primary/15 via-primary/5 to-transparent dark:from-primary/20 dark:via-primary/10 p-8 border border-primary/10"
      >
        <div className="flex items-center gap-4">
          <div className="flex h-14 w-14 items-center justify-center rounded-xl bg-primary/10 text-primary">
            <Layers className="h-7 w-7" />
          </div>
          <div>
            <h1 className="text-3xl font-display font-bold tracking-tight text-foreground">
              Dietician dashboard
            </h1>
            <p className="mt-1 text-muted-foreground">
              Manage your programs and batches (cohorts).
            </p>
          </div>
        </div>
      </motion.div>

      <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <Card className="border border-border overflow-hidden">
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-muted-foreground">Your programs</p>
                {isLoading ? (
                  <Skeleton className="mt-2 h-8 w-16" />
                ) : (
                  <p className="mt-2 text-3xl font-bold text-foreground">{count}</p>
                )}
              </div>
              <BookOpen className="h-10 w-10 text-muted-foreground/60" />
            </div>
            <Link to="/dietician/my-courses" className="mt-4 block">
              <Button variant="outline" size="sm" className="w-full">
                Manage programs & batches
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
            </Link>
          </CardContent>
        </Card>
      </div>

      {!isLoading && count === 0 && (
        <Card className="border border-border">
          <CardContent className="py-12 text-center text-muted-foreground">
            <p>You don’t have any assigned programs yet.</p>
            <p className="mt-1 text-sm">Contact the admin to be assigned as a dietician.</p>
          </CardContent>
        </Card>
      )}
    </div>
  );
}

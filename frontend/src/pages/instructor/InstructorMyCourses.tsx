import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { instructorCoursesService } from "@/services/batches";
import { BookOpen, Layers, ArrowRight } from "lucide-react";
import { motion } from "framer-motion";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";

const BASE = "/dietician";

export default function InstructorMyCourses() {
  const { data: courses, isLoading, error } = useQuery({
    queryKey: ["instructor-my-courses"],
    queryFn: () => instructorCoursesService.getMyClasses(),
    retry: (_, err) => {
      const message = err instanceof Error ? err.message : "";
      if (message.includes("403") || message.includes("Forbidden")) return false;
      return true;
    },
  });

  const list = Array.isArray(courses) ? courses : [];

  if (error && list.length === 0) {
    return (
      <div className="space-y-8">
        <div>
          <h1 className="text-3xl font-display font-bold tracking-tight text-foreground">
            My programs
          </h1>
          <p className="mt-2 text-muted-foreground">
            Manage batches for programs you run.
          </p>
        </div>
        <Card className="border border-border overflow-hidden">
          <div className="bg-muted/50 px-6 py-16 text-center">
            <BookOpen className="mx-auto h-14 w-14 text-muted-foreground/60" />
            <p className="mt-4 text-muted-foreground font-medium">
              Unable to load your programs.
            </p>
            <Link to={BASE}>
              <Button variant="outline" className="mt-6">
                Back to dashboard
              </Button>
            </Link>
          </div>
        </Card>
      </div>
    );
  }

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
              My programs
            </h1>
            <p className="mt-1 text-muted-foreground">
              Select a program to create and manage batches (cohorts).
            </p>
          </div>
        </div>
      </motion.div>

      {isLoading && (
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {[1, 2, 3].map((i) => (
            <Skeleton key={i} className="h-[200px] rounded-xl" />
          ))}
        </div>
      )}

      {!isLoading && list.length === 0 && (
        <Card className="border border-border overflow-hidden">
          <div className="bg-muted/30 px-6 py-16 text-center">
            <BookOpen className="mx-auto h-14 w-14 text-muted-foreground/60" />
            <p className="mt-4 font-medium text-foreground">No programs yet</p>
            <p className="mt-1 text-sm text-muted-foreground">
              You don’t have any assigned programs. Contact the admin to be assigned as a dietician.
            </p>
            <Link to={BASE}>
              <Button variant="outline" className="mt-6">
                Back to dashboard
              </Button>
            </Link>
          </div>
        </Card>
      )}

      {!isLoading && list.length > 0 && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ delay: 0.05 }}
          className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
          {list.map((course, i) => (
            <motion.div
              key={course.id}
              initial={{ opacity: 0, y: 12 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.03 * i }}
            >
              <Link
                to={`${BASE}/my-courses/${course.id}/batches`}
                state={{ title: course.title }}
              >
                <Card className="group h-full overflow-hidden border border-border bg-card transition-all duration-200 hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5">
                  <div className="aspect-video w-full overflow-hidden bg-muted">
                    <img
                      src={course.image}
                      alt={course.title}
                      className="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                    />
                  </div>
                  <CardContent className="p-5">
                    <h2 className="font-semibold text-foreground line-clamp-2 leading-snug">
                      {course.title}
                    </h2>
                    <div className="mt-4 flex items-center justify-between">
                      <span className="text-sm text-muted-foreground">
                        Manage batches
                      </span>
                      <span className="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-primary transition-colors group-hover:bg-primary group-hover:text-primary-foreground">
                        <ArrowRight className="h-4 w-4" />
                      </span>
                    </div>
                  </CardContent>
                </Card>
              </Link>
            </motion.div>
          ))}
        </motion.div>
      )}
    </div>
  );
}

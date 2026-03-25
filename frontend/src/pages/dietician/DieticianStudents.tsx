import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { dieticianStudentsService, type DieticianStudent } from "@/services/dietician";
import { Users, Heart, FileText } from "lucide-react";
import { motion } from "framer-motion";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import { Badge } from "@/components/ui/badge";

export default function DieticianStudents() {
  const { data: students, isLoading, error } = useQuery({
    queryKey: ["dietician-students"],
    queryFn: () => dieticianStudentsService.list(),
  });

  const list: DieticianStudent[] = Array.isArray(students) ? students : [];

  return (
    <div className="space-y-8">
      <motion.div
        initial={{ opacity: 0, y: 8 }}
        animate={{ opacity: 1, y: 0 }}
        className="relative rounded-2xl bg-gradient-to-br from-primary/15 via-primary/5 to-transparent dark:from-primary/20 dark:via-primary/10 p-8 border border-primary/10"
      >
        <div className="flex items-center gap-4">
          <div className="flex h-14 w-14 items-center justify-center rounded-xl bg-primary/10 text-primary">
            <Users className="h-7 w-7" />
          </div>
          <div>
            <h1 className="text-3xl font-display font-bold tracking-tight text-foreground">
              My students
            </h1>
            <p className="mt-1 text-muted-foreground">
              Students enrolled in your programs. View health profiles and health logs.
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
        <div className="space-y-4">
          {[1, 2, 3, 4].map((i) => (
            <Skeleton key={i} className="h-24 rounded-xl" />
          ))}
        </div>
      )}

      {!isLoading && !error && list.length === 0 && (
        <Card className="border border-border overflow-hidden">
          <div className="px-6 py-16 text-center">
            <Users className="mx-auto h-14 w-14 text-muted-foreground/60" />
            <p className="mt-4 font-medium text-foreground">No students yet</p>
            <p className="mt-1 text-sm text-muted-foreground">
              Students who enroll in your programs will appear here.
            </p>
          </div>
        </Card>
      )}

      {!isLoading && !error && list.length > 0 && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          className="space-y-4"
        >
          <Card className="border border-border overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b border-border bg-muted/30">
                    <th className="text-left py-3 px-4 font-medium text-foreground">
                      Student
                    </th>
                    <th className="text-left py-3 px-4 font-medium text-foreground">
                      Email
                    </th>
                    <th className="text-left py-3 px-4 font-medium text-foreground">
                      Programs
                    </th>
                    <th className="text-right py-3 px-4 font-medium text-foreground">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody>
                  {list.map((s) => (
                    <tr
                      key={s.id}
                      className="border-b border-border/70 hover:bg-muted/20 transition-colors"
                    >
                      <td className="py-3 px-4">
                        <div className="flex items-center gap-3">
                          {s.avatar ? (
                            <img
                              src={s.avatar}
                              alt=""
                              className="h-10 w-10 rounded-full object-cover"
                            />
                          ) : (
                            <div className="h-10 w-10 rounded-full bg-muted flex items-center justify-center">
                              <Users className="h-5 w-5 text-muted-foreground" />
                            </div>
                          )}
                          <span className="font-medium text-foreground">
                            {s.full_name}
                          </span>
                        </div>
                      </td>
                      <td className="py-3 px-4 text-muted-foreground">
                        {s.email ?? "—"}
                      </td>
                      <td className="py-3 px-4">
                        <div className="flex flex-wrap gap-1">
                          {(s.programs || []).slice(0, 3).map((title, i) => (
                            <Badge key={i} variant="secondary" className="text-xs">
                              {title}
                            </Badge>
                          ))}
                          {(s.programs?.length ?? 0) > 3 && (
                            <Badge variant="outline">+{(s.programs?.length ?? 0) - 3}</Badge>
                          )}
                        </div>
                      </td>
                      <td className="py-3 px-4 text-right">
                        <div className="flex items-center justify-end gap-2">
                          <Link
                            to={`/dietician/students/${s.id}/health-profile`}
                            className="inline-flex items-center gap-1.5 text-primary hover:underline text-sm font-medium"
                          >
                            <FileText className="h-4 w-4" />
                            Health profile
                          </Link>
                          <Link
                            to="/dietician/health-logs"
                            className="inline-flex items-center gap-1.5 text-muted-foreground hover:text-foreground text-sm font-medium"
                          >
                            <Heart className="h-4 w-4" />
                            Health logs
                          </Link>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </Card>
        </motion.div>
      )}
    </div>
  );
}

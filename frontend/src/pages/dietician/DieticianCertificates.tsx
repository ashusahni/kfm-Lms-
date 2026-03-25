import { useQuery } from "@tanstack/react-query";
import { dieticianCertificatesService } from "@/services/dietician";
import { Award, GraduationCap } from "lucide-react";
import { motion } from "framer-motion";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";

export default function DieticianCertificates() {
  const { data: created, isLoading: loadingCreated } = useQuery({
    queryKey: ["dietician-certificates-created"],
    queryFn: () => dieticianCertificatesService.created(),
  });
  const { data: students, isLoading: loadingStudents } = useQuery({
    queryKey: ["dietician-certificates-students"],
    queryFn: () => dieticianCertificatesService.students(),
  });

  const createdList = Array.isArray(created) ? created : [];
  const studentsList = Array.isArray(students) ? students : [];

  return (
    <div className="space-y-8">
      <motion.div
        initial={{ opacity: 0, y: 8 }}
        animate={{ opacity: 1, y: 0 }}
        className="relative rounded-2xl bg-gradient-to-br from-primary/15 via-primary/5 to-transparent dark:from-primary/20 dark:via-primary/10 p-8 border border-primary/10"
      >
        <div className="flex items-center gap-4">
          <div className="flex h-14 w-14 items-center justify-center rounded-xl bg-primary/10 text-primary">
            <Award className="h-7 w-7" />
          </div>
          <div>
            <h1 className="text-3xl font-display font-bold tracking-tight text-foreground">
              Certificates
            </h1>
            <p className="mt-1 text-muted-foreground">
              Certificates for your programs and students who have earned them.
            </p>
          </div>
        </div>
      </motion.div>

      <div className="grid gap-8 md:grid-cols-2">
        <Card className="border border-border overflow-hidden">
          <CardHeader>
            <CardTitle className="text-lg flex items-center gap-2">
              <Award className="h-5 w-5" />
              Certificates (by quiz)
            </CardTitle>
          </CardHeader>
          <CardContent className="pt-0">
            {loadingCreated && (
              <div className="space-y-2">
                <Skeleton className="h-12 rounded-lg" />
                <Skeleton className="h-12 rounded-lg" />
              </div>
            )}
            {!loadingCreated && createdList.length === 0 && (
              <p className="text-sm text-muted-foreground py-4">
                No certificates configured for your programs yet.
              </p>
            )}
            {!loadingCreated && createdList.length > 0 && (
              <ul className="space-y-2">
                {createdList.map((c: { id?: number; title?: string }, i: number) => (
                  <li key={(c as { id?: number }).id ?? i} className="rounded-lg border border-border bg-muted/30 px-4 py-3">
                    <p className="font-medium text-foreground">{(c as { title?: string }).title ?? "Certificate"}</p>
                  </li>
                ))}
              </ul>
            )}
          </CardContent>
        </Card>

        <Card className="border border-border overflow-hidden">
          <CardHeader>
            <CardTitle className="text-lg flex items-center gap-2">
              <GraduationCap className="h-5 w-5" />
              Students who earned certificates
            </CardTitle>
          </CardHeader>
          <CardContent className="pt-0">
            {loadingStudents && (
              <div className="space-y-2">
                <Skeleton className="h-12 rounded-lg" />
                <Skeleton className="h-12 rounded-lg" />
              </div>
            )}
            {!loadingStudents && studentsList.length === 0 && (
              <p className="text-sm text-muted-foreground py-4">
                No student has earned a certificate yet.
              </p>
            )}
            {!loadingStudents && studentsList.length > 0 && (
              <ul className="space-y-2">
                {studentsList.slice(0, 20).map((s: { user_id?: number; user?: { full_name?: string }; quiz_title?: string }, i: number) => (
                  <li key={i} className="rounded-lg border border-border bg-muted/30 px-4 py-3">
                    <p className="font-medium text-foreground">
                      {(s as { user?: { full_name?: string } }).user?.full_name ?? `Student #${(s as { user_id?: number }).user_id}`}
                    </p>
                    <p className="text-xs text-muted-foreground">{(s as { quiz_title?: string }).quiz_title}</p>
                  </li>
                ))}
                {studentsList.length > 20 && (
                  <li className="text-sm text-muted-foreground py-2">
                    +{studentsList.length - 20} more
                  </li>
                )}
              </ul>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}

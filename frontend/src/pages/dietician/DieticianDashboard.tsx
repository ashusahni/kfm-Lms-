import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { dieticianCoursesService } from "@/services/batches";
import {
  dieticianStudentsService,
  dieticianMeetingsService,
  dieticianAssignmentsService,
} from "@/services/dietician";
import { Layers, BookOpen, ArrowRight, Users, Video, ClipboardList, Heart } from "lucide-react";
import { motion } from "framer-motion";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";

export default function DieticianDashboard() {
  const { data: courses, isLoading: loadingCourses } = useQuery({
    queryKey: ["panel-my-courses"],
    queryFn: () => dieticianCoursesService.getMyClasses(),
    retry: (_, err) => {
      const msg = err instanceof Error ? err.message : "";
      if (msg.includes("403") || msg.includes("Forbidden")) return false;
      return true;
    },
  });
  const { data: students, isLoading: loadingStudents } = useQuery({
    queryKey: ["dietician-students"],
    queryFn: () => dieticianStudentsService.list(),
  });
  const { data: meetings, isLoading: loadingMeetings } = useQuery({
    queryKey: ["dietician-meetings"],
    queryFn: () => dieticianMeetingsService.get(),
  });
  const { data: assignmentsData, isLoading: loadingAssignments } = useQuery({
    queryKey: ["dietician-assignments"],
    queryFn: () => dieticianAssignmentsService.list(),
  });

  const list = Array.isArray(courses) ? courses : [];
  const count = list.length;
  const studentList = Array.isArray(students) ? students : [];
  const pendingRequests = meetings?.requests?.meetings?.length ?? 0;
  const pendingReview = assignmentsData?.pendingCount ?? 0;

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
              Manage students, programs, batches, health logs, meetings, and more.
            </p>
          </div>
        </div>
      </motion.div>

      <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <Link to="/dietician/students">
          <Card className="border border-border overflow-hidden h-full transition-colors hover:border-primary/30">
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">My students</p>
                  {loadingStudents ? (
                    <Skeleton className="mt-2 h-8 w-12" />
                  ) : (
                    <p className="mt-2 text-3xl font-bold text-foreground">{studentList.length}</p>
                  )}
                </div>
                <Users className="h-10 w-10 text-muted-foreground/60" />
              </div>
              <Button variant="outline" size="sm" className="mt-4 w-full">
                Manage students
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
            </CardContent>
          </Card>
        </Link>

        <Card className="border border-border overflow-hidden">
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-muted-foreground">Your programs</p>
                {loadingCourses ? (
                  <Skeleton className="mt-2 h-8 w-16" />
                ) : (
                  <p className="mt-2 text-3xl font-bold text-foreground">{count}</p>
                )}
              </div>
              <BookOpen className="h-10 w-10 text-muted-foreground/60" />
            </div>
            <Link to="/dietician/my-courses" className="mt-4 block">
              <Button variant="outline" size="sm" className="w-full">
                Programs & batches
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
            </Link>
          </CardContent>
        </Card>

        <Link to="/dietician/health-logs">
          <Card className="border border-border overflow-hidden h-full transition-colors hover:border-primary/30">
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Student health logs</p>
                  <p className="mt-2 text-lg font-bold text-foreground">View</p>
                </div>
                <Heart className="h-10 w-10 text-muted-foreground/60" />
              </div>
              <Button variant="outline" size="sm" className="mt-4 w-full">
                Health logs
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
            </CardContent>
          </Card>
        </Link>

        <Link to="/dietician/meetings">
          <Card className="border border-border overflow-hidden h-full transition-colors hover:border-primary/30">
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Meeting requests</p>
                  {loadingMeetings ? (
                    <Skeleton className="mt-2 h-8 w-12" />
                  ) : (
                    <p className="mt-2 text-3xl font-bold text-foreground">{pendingRequests}</p>
                  )}
                </div>
                <Video className="h-10 w-10 text-muted-foreground/60" />
              </div>
              <Button variant="outline" size="sm" className="mt-4 w-full">
                Meetings
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
            </CardContent>
          </Card>
        </Link>

        <Link to="/dietician/assignments" className="sm:col-span-2 lg:col-span-1">
          <Card className="border border-border overflow-hidden h-full transition-colors hover:border-primary/30">
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-muted-foreground">Assignments pending</p>
                  {loadingAssignments ? (
                    <Skeleton className="mt-2 h-8 w-12" />
                  ) : (
                    <p className="mt-2 text-3xl font-bold text-foreground">{pendingReview}</p>
                  )}
                </div>
                <ClipboardList className="h-10 w-10 text-muted-foreground/60" />
              </div>
              <Button variant="outline" size="sm" className="mt-4 w-full">
                Assignments
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
            </CardContent>
          </Card>
        </Link>
      </div>

      {!loadingCourses && count === 0 && (
        <Card className="border border-border">
          <CardContent className="py-12 text-center text-muted-foreground">
            <p>You don't have any assigned programs yet.</p>
            <p className="mt-1 text-sm">Contact the admin to be assigned as a dietician.</p>
          </CardContent>
        </Card>
      )}
    </div>
  );
}

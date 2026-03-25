import { useState } from "react";
import { Link } from "react-router-dom";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import {
  dieticianAssignmentsService,
  type DieticianAssignment,
  type DieticianAssignmentHistory,
} from "@/services/dietician";
import { ClipboardList, Loader2, Award } from "lucide-react";
import { motion } from "framer-motion";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Skeleton } from "@/components/ui/skeleton";
import { Badge } from "@/components/ui/badge";
import { toast } from "sonner";

export default function DieticianAssignments() {
  const queryClient = useQueryClient();
  const [selectedAssignmentId, setSelectedAssignmentId] = useState<number | null>(null);
  const [gradingId, setGradingId] = useState<number | null>(null);
  const [gradeValue, setGradeValue] = useState("");

  const { data, isLoading, error } = useQuery({
    queryKey: ["dietician-assignments"],
    queryFn: () => dieticianAssignmentsService.list(),
  });

  const { data: histories, isLoading: loadingHistories } = useQuery({
    queryKey: ["dietician-assignment-students", selectedAssignmentId],
    queryFn: () => dieticianAssignmentsService.getStudents(selectedAssignmentId!),
    enabled: selectedAssignmentId != null,
  });

  const gradeMutation = useMutation({
    mutationFn: ({ historyId, grade }: { historyId: number; grade: number }) =>
      dieticianAssignmentsService.setGrade(historyId, grade),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["dietician-assignment-students", selectedAssignmentId] });
      queryClient.invalidateQueries({ queryKey: ["dietician-assignments"] });
      setGradingId(null);
      setGradeValue("");
      toast.success("Grade saved.");
    },
    onError: (e) => toast.error(e instanceof Error ? e.message : "Failed to save grade"),
  });

  const assignments: DieticianAssignment[] = data?.assignments ?? [];
  const pendingCount = data?.pendingCount ?? 0;
  const historyList: DieticianAssignmentHistory[] = Array.isArray(histories) ? histories : [];

  return (
    <div className="space-y-8">
      <motion.div
        initial={{ opacity: 0, y: 8 }}
        animate={{ opacity: 1, y: 0 }}
        className="relative rounded-2xl bg-gradient-to-br from-primary/15 via-primary/5 to-transparent dark:from-primary/20 dark:via-primary/10 p-8 border border-primary/10"
      >
        <div className="flex items-center gap-4">
          <div className="flex h-14 w-14 items-center justify-center rounded-xl bg-primary/10 text-primary">
            <ClipboardList className="h-7 w-7" />
          </div>
          <div>
            <h1 className="text-3xl font-display font-bold tracking-tight text-foreground">
              Assignments
            </h1>
            <p className="mt-1 text-muted-foreground">
              Review and grade student assignment submissions.
            </p>
            {pendingCount > 0 && (
              <Badge className="mt-2" variant="secondary">
                {pendingCount} pending review
              </Badge>
            )}
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
          <Skeleton className="h-20 rounded-xl" />
          <Skeleton className="h-20 rounded-xl" />
        </div>
      )}

      {!isLoading && !error && assignments.length === 0 && (
        <Card className="border border-border">
          <CardContent className="py-16 text-center text-muted-foreground">
            <ClipboardList className="mx-auto h-14 w-14 opacity-50" />
            <p className="mt-4 font-medium">No assignments</p>
            <p className="mt-1 text-sm">Assignments from your programs will appear here.</p>
          </CardContent>
        </Card>
      )}

      {!isLoading && !error && assignments.length > 0 && (
        <div className="grid gap-8 lg:grid-cols-2">
          <Card className="border border-border overflow-hidden">
            <CardHeader>
              <CardTitle className="text-lg">Your assignments</CardTitle>
            </CardHeader>
            <CardContent className="pt-0">
              <ul className="space-y-2">
                {assignments.map((a) => (
                  <li key={a.id}>
                    <button
                      type="button"
                      onClick={() => setSelectedAssignmentId(selectedAssignmentId === a.id ? null : a.id)}
                      className={`w-full text-left rounded-lg border px-4 py-3 transition-colors ${
                        selectedAssignmentId === a.id
                          ? "border-primary bg-primary/10"
                          : "border-border hover:bg-muted/50"
                      }`}
                    >
                      <p className="font-medium text-foreground">{a.title ?? "Assignment"}</p>
                      <p className="text-xs text-muted-foreground">
                        {a.webinar?.title ?? "Program"}
                      </p>
                    </button>
                  </li>
                ))}
              </ul>
            </CardContent>
          </Card>

          <Card className="border border-border overflow-hidden">
            <CardHeader>
              <CardTitle className="text-lg">Submissions</CardTitle>
              {selectedAssignmentId == null && (
                <p className="text-sm text-muted-foreground">Select an assignment</p>
              )}
            </CardHeader>
            <CardContent className="pt-0">
              {selectedAssignmentId == null && (
                <p className="text-sm text-muted-foreground py-8 text-center">
                  Select an assignment to see student submissions.
                </p>
              )}
              {selectedAssignmentId != null && loadingHistories && (
                <div className="space-y-2">
                  <Skeleton className="h-16 rounded-lg" />
                  <Skeleton className="h-16 rounded-lg" />
                </div>
              )}
              {selectedAssignmentId != null && !loadingHistories && historyList.length === 0 && (
                <p className="text-sm text-muted-foreground py-8 text-center">
                  No submissions yet.
                </p>
              )}
              {selectedAssignmentId != null && !loadingHistories && historyList.length > 0 && (
                <ul className="space-y-3">
                  {historyList.map((h) => (
                    <li
                      key={h.id}
                      className="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-border bg-muted/30 p-3"
                    >
                      <div>
                        <p className="font-medium text-foreground">
                          {h.student?.full_name ?? `Student #${h.student_id}`}
                        </p>
                        <div className="flex gap-2 mt-1">
                          <Badge variant={h.status === "pending" ? "secondary" : "outline"}>
                            {h.status ?? "—"}
                          </Badge>
                          {h.grade != null && (
                            <span className="text-xs text-muted-foreground">Grade: {h.grade}</span>
                          )}
                        </div>
                      </div>
                      {h.status === "pending" && (
                        <div className="flex items-center gap-2">
                          <Input
                            type="number"
                            min={0}
                            max={100}
                            placeholder="Grade"
                            className="w-20"
                            value={gradingId === h.id ? gradeValue : ""}
                            onChange={(e) => {
                              setGradingId(h.id);
                              setGradeValue(e.target.value);
                            }}
                          />
                          <Button
                            size="sm"
                            onClick={() => {
                              const grade = parseInt(gradeValue, 10);
                              if (!Number.isFinite(grade) || grade < 0 || grade > 100) {
                                toast.error("Enter a grade 0–100");
                                return;
                              }
                              gradeMutation.mutate({ historyId: h.id, grade });
                            }}
                            disabled={gradeMutation.isPending || !gradeValue}
                          >
                            {gradeMutation.isPending && gradingId === h.id ? (
                              <Loader2 className="h-4 w-4 animate-spin" />
                            ) : (
                              "Save"
                            )}
                          </Button>
                        </div>
                      )}
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

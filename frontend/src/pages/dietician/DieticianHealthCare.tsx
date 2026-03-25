import { Link } from "react-router-dom";
import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { healthCareService, type HealthCareCourse, type HealthCareStudent } from "@/services/dietician";
import { Stethoscope, Users, Lock, LockOpen, MessageCircle, FileText, ExternalLink, ClipboardList } from "lucide-react";
import { motion } from "framer-motion";
import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import { Badge } from "@/components/ui/badge";
import { toast } from "sonner";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";

export default function DieticianHealthCare() {
  const queryClient = useQueryClient();
  const [saleIdForIntake, setSaleIdForIntake] = useState<number | null>(null);

  const { data: courses, isLoading, error } = useQuery({
    queryKey: ["health-care"],
    queryFn: () => healthCareService.list(),
  });

  const markConversationMutation = useMutation({
    mutationFn: (saleId: number) => healthCareService.markInitialConversation(saleId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["health-care"] });
      toast.success("Initial conversation marked complete. Student can now access course content.");
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : "Failed to update"),
  });

  const { data: intakeData, isLoading: loadingIntake } = useQuery({
    queryKey: ["health-care-intake", saleIdForIntake],
    queryFn: () => healthCareService.getIntake(saleIdForIntake!),
    enabled: saleIdForIntake != null,
  });

  const list: HealthCareCourse[] = Array.isArray(courses) ? courses : [];

  return (
    <div className="space-y-8">
      <motion.div
        initial={{ opacity: 0, y: 8 }}
        animate={{ opacity: 1, y: 0 }}
        className="relative rounded-2xl bg-gradient-to-br from-primary/15 via-primary/5 to-transparent dark:from-primary/20 dark:via-primary/10 p-8 border border-primary/10"
      >
        <div className="flex items-center gap-4">
          <div className="flex h-14 w-14 items-center justify-center rounded-xl bg-primary/10 text-primary">
            <Stethoscope className="h-7 w-7" />
          </div>
          <div>
            <h1 className="text-3xl font-display font-bold tracking-tight text-foreground">
              Health Care
            </h1>
            <p className="mt-1 text-muted-foreground">
              Review students who purchased your courses. Mark &quot;Initial conversation done&quot; to unlock their video content (or wait 48 hours after purchase).
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
          {[1, 2, 3].map((i) => (
            <Skeleton key={i} className="h-40 rounded-xl" />
          ))}
        </div>
      )}

      {!isLoading && !error && list.length === 0 && (
        <Card className="border border-border overflow-hidden">
          <div className="px-6 py-16 text-center">
            <Stethoscope className="mx-auto h-14 w-14 text-muted-foreground/60" />
            <p className="mt-4 font-medium text-foreground">No course purchases yet</p>
            <p className="mt-1 text-sm text-muted-foreground">
              When students purchase your video courses, they will appear here. You can review their info and mark the initial conversation when done.
            </p>
          </div>
        </Card>
      )}

      {!isLoading && !error && list.length > 0 && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          className="space-y-6"
        >
          {list.map((course) => (
            <Card key={course.webinar_id} className="border border-border overflow-hidden">
              <CardHeader className="pb-2">
                <h2 className="text-lg font-semibold text-foreground flex items-center gap-2">
                  {course.title}
                  <Badge variant="secondary" className="text-xs">
                    {course.students.length} student{course.students.length !== 1 ? "s" : ""}
                  </Badge>
                </h2>
              </CardHeader>
              <CardContent className="pt-0">
                <div className="overflow-x-auto">
                  <table className="w-full text-sm">
                    <thead>
                      <tr className="border-b border-border bg-muted/30">
                        <th className="text-left py-3 px-4 font-medium text-foreground">Student</th>
                        <th className="text-left py-3 px-4 font-medium text-foreground">Purchased</th>
                        <th className="text-left py-3 px-4 font-medium text-foreground">Content access</th>
                        <th className="text-right py-3 px-4 font-medium text-foreground">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      {course.students.map((s: HealthCareStudent) => (
                        <tr
                          key={s.sale_id}
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
                              <div>
                                <span className="font-medium text-foreground">{s.full_name}</span>
                                {s.email && (
                                  <div className="text-xs text-muted-foreground">{s.email}</div>
                                )}
                              </div>
                            </div>
                          </td>
                          <td className="py-3 px-4 text-muted-foreground">
                            {s.purchased_at_formatted ?? "—"}
                          </td>
                          <td className="py-3 px-4">
                            {s.content_unlocked ? (
                              <Badge variant="default" className="gap-1 bg-green-600">
                                <LockOpen className="h-3 w-3" />
                                Unlocked
                              </Badge>
                            ) : (
                              <Badge variant="secondary" className="gap-1">
                                <Lock className="h-3 w-3" />
                                Locked
                              </Badge>
                            )}
                          </td>
                          <td className="py-3 px-4 text-right">
                            <div className="flex items-center justify-end gap-2 flex-wrap">
                              <button
                                type="button"
                                onClick={() => setSaleIdForIntake(s.sale_id)}
                                className="inline-flex items-center gap-1.5 text-primary hover:underline text-sm font-medium"
                              >
                                <ClipboardList className="h-4 w-4" />
                                View intake form
                              </button>
                              <Link
                                to={`/dietician/students/${s.user_id}/health-profile`}
                                className="inline-flex items-center gap-1.5 text-primary hover:underline text-sm font-medium"
                              >
                                <FileText className="h-4 w-4" />
                                Health profile
                              </Link>
                              {!s.initial_conversation_done ? (
                                <Button
                                  size="sm"
                                  variant="secondary"
                                  className="gap-1.5"
                                  onClick={() => markConversationMutation.mutate(s.sale_id)}
                                  disabled={markConversationMutation.isPending}
                                >
                                  <MessageCircle className="h-4 w-4" />
                                  Mark initial conversation done
                                </Button>
                              ) : (
                                <Badge variant="outline" className="gap-1 text-green-600 border-green-600/50">
                                  <MessageCircle className="h-3 w-3" />
                                  Conversation done
                                </Badge>
                              )}
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </CardContent>
            </Card>
          ))}
        </motion.div>
      )}

      <Dialog open={saleIdForIntake != null} onOpenChange={(open) => !open && setSaleIdForIntake(null)}>
        <DialogContent className="max-w-2xl max-h-[85vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>Course intake form</DialogTitle>
          </DialogHeader>
          {loadingIntake && (
            <div className="space-y-2">
              <Skeleton className="h-4 w-full" />
              <Skeleton className="h-4 w-3/4" />
              <Skeleton className="h-20 w-full" />
            </div>
          )}
          {!loadingIntake && intakeData && (
            <div className="space-y-4 text-sm">
              {intakeData.webinar && (
                <p className="text-muted-foreground">
                  Course: <span className="font-medium text-foreground">{intakeData.webinar.title}</span>
                </p>
              )}
              {intakeData.student && (
                <p className="text-muted-foreground">
                  Student: <span className="font-medium text-foreground">{intakeData.student.full_name}</span>
                  {intakeData.student.email && (
                    <> · {intakeData.student.email}</>
                  )}
                </p>
              )}
              {!intakeData.intake ? (
                <p className="text-muted-foreground italic py-4">Student has not submitted the course intake form yet.</p>
              ) : (
                <>
                  <div className="grid gap-3 border rounded-lg p-4 bg-muted/30 space-y-2">
                    <p className="font-medium text-foreground border-b pb-2">Questionnaire</p>
                    {intakeData.intake.weight_history && (
                      <div>
                        <span className="font-medium text-foreground">Weight history:</span>
                        <p className="text-muted-foreground whitespace-pre-wrap mt-0.5">{String(intakeData.intake.weight_history)}</p>
                      </div>
                    )}
                    {intakeData.intake.past_dieting_attempts && (
                      <div>
                        <span className="font-medium text-foreground">Past dieting attempts:</span>
                        <p className="text-muted-foreground whitespace-pre-wrap mt-0.5">{String(intakeData.intake.past_dieting_attempts)}</p>
                      </div>
                    )}
                    {intakeData.intake.digestive_issues && (
                      <div>
                        <span className="font-medium text-foreground">Digestive issues:</span>
                        <p className="text-muted-foreground whitespace-pre-wrap mt-0.5">{String(intakeData.intake.digestive_issues)}</p>
                      </div>
                    )}
                    {(intakeData.intake.sleep_quality || intakeData.intake.stress_level) && (
                      <div className="flex gap-4 flex-wrap">
                        {intakeData.intake.sleep_quality && (
                          <span><span className="font-medium text-foreground">Sleep:</span> {String(intakeData.intake.sleep_quality)}</span>
                        )}
                        {intakeData.intake.stress_level && (
                          <span><span className="font-medium text-foreground">Stress:</span> {String(intakeData.intake.stress_level)}</span>
                        )}
                      </div>
                    )}
                    {intakeData.intake.food_preference && (
                      <div>
                        <span className="font-medium text-foreground">Food preference:</span>
                        <p className="text-muted-foreground whitespace-pre-wrap mt-0.5">{String(intakeData.intake.food_preference)}</p>
                      </div>
                    )}
                    {intakeData.intake.meal_timings && (
                      <div>
                        <span className="font-medium text-foreground">Meal timings:</span>
                        <p className="text-muted-foreground mt-0.5">{String(intakeData.intake.meal_timings)}</p>
                      </div>
                    )}
                    {intakeData.intake.daily_schedule && (
                      <div>
                        <span className="font-medium text-foreground">Daily schedule:</span>
                        <p className="text-muted-foreground whitespace-pre-wrap mt-0.5">{String(intakeData.intake.daily_schedule)}</p>
                      </div>
                    )}
                  </div>
                  {(intakeData.intake.blood_reports_urls?.length || intakeData.intake.body_measurements_url || intakeData.intake.body_photos_urls?.length) ? (
                    <div className="border rounded-lg p-4 bg-muted/30 space-y-2">
                      <p className="font-medium text-foreground border-b pb-2">Uploads</p>
                      <div className="space-y-1.5">
                        {intakeData.intake.blood_reports_urls?.length ? (
                          <p>
                            <span className="font-medium text-foreground">Blood reports:</span>{" "}
                            {intakeData.intake.blood_reports_urls.map((url, i) => (
                              <a key={i} href={url} target="_blank" rel="noopener noreferrer" className="text-primary hover:underline inline-flex items-center gap-1">
                                Link <ExternalLink className="h-3 w-3" />
                              </a>
                            ))}
                          </p>
                        ) : null}
                        {intakeData.intake.body_measurements_url && (
                          <p>
                            <span className="font-medium text-foreground">Body measurements:</span>{" "}
                            <a href={intakeData.intake.body_measurements_url} target="_blank" rel="noopener noreferrer" className="text-primary hover:underline inline-flex items-center gap-1">
                              View file <ExternalLink className="h-3 w-3" />
                            </a>
                          </p>
                        )}
                        {intakeData.intake.body_photos_urls?.length ? (
                          <p>
                            <span className="font-medium text-foreground">Body photos:</span>{" "}
                            {intakeData.intake.body_photos_urls.map((url, i) => (
                              <a key={i} href={url} target="_blank" rel="noopener noreferrer" className="text-primary hover:underline inline-flex items-center gap-1">
                                Photo {i + 1} <ExternalLink className="h-3 w-3" />
                              </a>
                            ))}
                          </p>
                        ) : null}
                      </div>
                    </div>
                  ) : null}
                </>
              )}
            </div>
          )}
        </DialogContent>
      </Dialog>
    </div>
  );
}

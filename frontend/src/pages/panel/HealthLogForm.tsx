import { useParams, useNavigate, Link } from "react-router-dom";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useEffect, useState, useMemo } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { healthService } from "@/services/health";
import { programsService } from "@/services/programs";
import { format } from "date-fns";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import type { HealthLogCreatePayload } from "@/types/api";
import type { CourseHealthLogSetting } from "@/types/api";

const schema = z.object({
  log_date: z.string().min(1, "Date required"),
  webinar_id: z.union([z.number(), z.nan()]).optional(),
  water_ml: z.coerce.number().min(0).optional(),
  calories: z.coerce.number().min(0).optional(),
  protein: z.coerce.number().min(0).optional(),
  carbs: z.coerce.number().min(0).optional(),
  fat: z.coerce.number().min(0).optional(),
  medicines: z.string().optional(),
  activity_minutes: z.coerce.number().min(0).optional(),
  activity_notes: z.string().optional(),
  adherence_score: z.coerce.number().min(0).max(100).optional(),
});

type FormValues = z.infer<typeof schema>;

export default function HealthLogForm() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const isEdit = !!id;
  const [customData, setCustomData] = useState<Record<string, string | number | null>>({});

  const { data: log, isLoading } = useQuery({
    queryKey: ["health-log", id],
    queryFn: () => healthService.get(id!),
    enabled: isEdit,
  });

  const { data: programs } = useQuery({
    queryKey: ["panel-program-purchases"],
    queryFn: () => programsService.getMyPrograms(),
  });
  const programList = ((): { id: number; title: string }[] => {
    if (!programs) return [];
    if (Array.isArray(programs)) return programs;
    const p = programs as { data?: { id: number; title: string }[] };
    return p.data ?? [];
  })();

  const form = useForm<FormValues>({
    resolver: zodResolver(schema),
    defaultValues: {
      log_date: format(new Date(), "yyyy-MM-dd"),
    },
  });
  const webinarId = form.watch("webinar_id");

  const { data: courseSetting } = useQuery({
    queryKey: ["course-health-log-setting", webinarId],
    queryFn: () => healthService.getCourseSetting(webinarId as number),
    enabled: typeof webinarId === "number" && !Number.isNaN(webinarId),
  });

  const setting = useMemo(() => {
    if (!courseSetting) return null;
    const d = courseSetting as unknown as CourseHealthLogSetting;
    return d;
  }, [courseSetting]);

  useEffect(() => {
    if (!setting?.custom_fields?.length) {
      setCustomData({});
      return;
    }
    setCustomData((prev) => {
      const next: Record<string, string | number | null> = {};
      setting.custom_fields.forEach((f) => {
        next[f.key] = prev[f.key] ?? null;
      });
      return next;
    });
  }, [setting?.webinar_id, setting?.custom_fields?.length]);

  useEffect(() => {
    if (isEdit && log) {
      if (log.custom_data && typeof log.custom_data === "object") {
        setCustomData({ ...log.custom_data } as Record<string, string | number | null>);
      }
      form.reset({
        log_date: log.log_date,
        webinar_id: log.webinar_id ?? undefined,
        water_ml: log.water_ml ?? undefined,
        calories: log.calories ?? undefined,
        protein: log.protein ?? undefined,
        carbs: log.carbs ?? undefined,
        fat: log.fat ?? undefined,
        medicines: log.medicines ?? undefined,
        activity_minutes: log.activity_minutes ?? undefined,
        activity_notes: log.activity_notes ?? undefined,
        adherence_score: log.adherence_score ?? undefined,
      });
    } else if (!isEdit) {
      form.reset({ log_date: format(new Date(), "yyyy-MM-dd") });
    }
  }, [isEdit, log, form.reset]);

  const mutation = useMutation({
    mutationFn: (payload: HealthLogCreatePayload) => healthService.save(payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["health-logs"] });
      queryClient.invalidateQueries({ queryKey: ["panel-health-logs"] });
      navigate("/panel/health-log");
    },
  });

  const { register, handleSubmit, formState: { errors } } = form;

  if (isEdit && isLoading) {
    return (
      <div className="animate-pulse space-y-4">
        <div className="h-10 bg-muted rounded w-48" />
        <div className="h-64 bg-muted rounded" />
      </div>
    );
  }

  const onSubmit = (values: FormValues) => {
    const payload: HealthLogCreatePayload = {
      log_date: values.log_date,
      water_ml: values.water_ml ?? null,
      calories: values.calories ?? null,
      protein: values.protein ?? null,
      carbs: values.carbs ?? null,
      fat: values.fat ?? null,
      medicines: values.medicines || null,
      activity_minutes: values.activity_minutes ?? null,
      activity_notes: values.activity_notes || null,
      adherence_score: values.adherence_score ?? null,
    };
    if (values.webinar_id) payload.webinar_id = values.webinar_id;
    const hasCustom = setting?.custom_fields?.length && Object.keys(customData).some((k) => customData[k] !== undefined && customData[k] !== null && customData[k] !== "");
    if (hasCustom) {
      const cleaned: Record<string, string | number | null> = {};
      Object.entries(customData).forEach(([k, v]) => {
        if (v !== undefined && v !== null && v !== "") cleaned[k] = v;
      });
      if (Object.keys(cleaned).length) payload.custom_data = cleaned;
    }
    mutation.mutate(payload);
  };

  return (
    <>
      <div className="mb-8 flex items-center gap-4">
        <Link to="/panel/health-log" className="text-sm text-muted-foreground hover:text-foreground">
          ← Back to Daily Log
        </Link>
      </div>
      <h1 className="text-2xl font-display font-bold text-foreground mb-8">
        {isEdit ? "Edit log" : "Add daily log"}
      </h1>

      <Card className="border border-border max-w-2xl">
        <CardHeader>
          <CardTitle className="text-base">Water, meals & activity</CardTitle>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
            <div className="grid gap-4 sm:grid-cols-2">
              <div>
                <Label htmlFor="log_date">Date</Label>
                <Input
                  id="log_date"
                  type="date"
                  {...register("log_date")}
                  disabled={isEdit}
                />
                {errors.log_date && (
                  <p className="text-sm text-destructive">{errors.log_date.message}</p>
                )}
              </div>
              {programList.length > 0 && (
                <div>
                  <Label htmlFor="webinar_id">Program / course (optional)</Label>
                  <select
                    id="webinar_id"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...register("webinar_id", { valueAsNumber: true })}
                  >
                    <option value="">None</option>
                    {programList.map((p) => (
                      <option key={p.id} value={p.id}>{p.title}</option>
                    ))}
                  </select>
                </div>
              )}
            </div>

            {setting?.tracking_notes && (
              <div className="rounded-lg border border-border bg-muted/50 p-4 text-sm text-muted-foreground">
                <p className="font-medium text-foreground mb-1">What to log for this course</p>
                <p className="whitespace-pre-wrap">{setting.tracking_notes}</p>
              </div>
            )}

            <div>
              <Label htmlFor="water_ml">Water (ml)</Label>
              <Input id="water_ml" type="number" min={0} {...register("water_ml")} />
            </div>

            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <div>
                <Label htmlFor="calories">Calories</Label>
                <Input id="calories" type="number" min={0} {...register("calories")} />
              </div>
              <div>
                <Label htmlFor="protein">Protein (g)</Label>
                <Input id="protein" type="number" min={0} {...register("protein")} />
              </div>
              <div>
                <Label htmlFor="carbs">Carbs (g)</Label>
                <Input id="carbs" type="number" min={0} {...register("carbs")} />
              </div>
              <div>
                <Label htmlFor="fat">Fat (g)</Label>
                <Input id="fat" type="number" min={0} {...register("fat")} />
              </div>
            </div>

            <div>
              <Label htmlFor="medicines">Supplements / medicines</Label>
              <Textarea id="medicines" rows={2} {...register("medicines")} />
            </div>

            <div className="grid gap-4 sm:grid-cols-2">
              <div>
                <Label htmlFor="activity_minutes">Activity (minutes)</Label>
                <Input id="activity_minutes" type="number" min={0} {...register("activity_minutes")} />
              </div>
              <div>
                <Label htmlFor="adherence_score">Adherence score (0–100)</Label>
                <Input id="adherence_score" type="number" min={0} max={100} {...register("adherence_score")} />
              </div>
            </div>

            <div>
              <Label htmlFor="activity_notes">Activity notes</Label>
              <Textarea id="activity_notes" rows={2} {...register("activity_notes")} />
            </div>

            {setting?.custom_fields?.length ? (
              <div className="space-y-3">
                <p className="text-sm font-medium text-foreground">Course-specific fields</p>
                <div className="grid gap-4 sm:grid-cols-2">
                  {setting.custom_fields.map((f) => (
                    <div key={f.key}>
                      <Label htmlFor={`custom_${f.key}`}>{f.label}</Label>
                      {f.type === "number" ? (
                        <Input
                          id={`custom_${f.key}`}
                          type="number"
                          value={customData[f.key] ?? ""}
                          onChange={(e) =>
                            setCustomData((prev) => ({
                              ...prev,
                              [f.key]: e.target.value === "" ? null : Number(e.target.value),
                            }))
                          }
                        />
                      ) : (
                        <Input
                          id={`custom_${f.key}`}
                          type="text"
                          value={customData[f.key] ?? ""}
                          onChange={(e) =>
                            setCustomData((prev) => ({
                              ...prev,
                              [f.key]: e.target.value || null,
                            }))
                          }
                        />
                      )}
                    </div>
                  ))}
                </div>
              </div>
            ) : null}

            <div className="flex gap-3">
              <Button type="submit" className="bg-gradient-cta" disabled={mutation.isPending}>
                {mutation.isPending ? "Saving…" : isEdit ? "Update log" : "Save log"}
              </Button>
              <Link to="/panel/health-log">
                <Button type="button" variant="outline">Cancel</Button>
              </Link>
            </div>
            {mutation.isError && (
              <p className="text-sm text-destructive">
                {(mutation.error as Error).message}
              </p>
            )}
          </form>
        </CardContent>
      </Card>
    </>
  );
}

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
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Slider } from "@/components/ui/slider";
import { Progress } from "@/components/ui/progress";
import { Droplets, Utensils, Activity, FileText, Plus, Trash2 } from "lucide-react";
import type { HealthLogCreatePayload, HealthLogMeal } from "@/types/api";
import type { CourseHealthLogSetting } from "@/types/api";

const WATER_PRESETS = [250, 500, 1000, 2000] as const;
const WATER_GOAL_ML = 2000;
const ACTIVITY_TYPES = [
  "",
  "Walk",
  "Run",
  "Gym",
  "Yoga",
  "Cycling",
  "Swimming",
  "Sports",
  "Other",
] as const;

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

function adherenceLabel(value: number): string {
  if (value <= 25) return "Poor";
  if (value <= 50) return "Fair";
  if (value <= 75) return "Good";
  return "Great";
}

export default function HealthLogForm() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const isEdit = !!id;
  const [customData, setCustomData] = useState<Record<string, string | number | null>>({});
  const [meals, setMeals] = useState<HealthLogMeal[]>([]);
  const [activityType, setActivityType] = useState<string>("");

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
      adherence_score: 70,
    },
  });
  const webinarId = form.watch("webinar_id");
  const waterMl = form.watch("water_ml");

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
      if (log.meals && Array.isArray(log.meals)) {
        setMeals(log.meals as HealthLogMeal[]);
      } else {
        setMeals([]);
      }
      const logDate =
        typeof log.log_date === "number"
          ? format(new Date(log.log_date * 1000), "yyyy-MM-dd")
          : typeof log.log_date === "string"
            ? log.log_date
            : format(new Date(), "yyyy-MM-dd");
      form.reset({
        log_date: logDate,
        webinar_id: log.webinar_id ?? undefined,
        water_ml: log.water_ml ?? undefined,
        calories: log.calories ?? undefined,
        protein: log.protein ?? undefined,
        carbs: log.carbs ?? undefined,
        fat: log.fat ?? undefined,
        medicines: log.medicines ?? undefined,
        activity_minutes: log.activity_minutes ?? undefined,
        activity_notes: log.activity_notes ?? undefined,
        adherence_score: log.adherence_score ?? 70,
      });
    } else if (!isEdit) {
      form.reset({
        log_date: format(new Date(), "yyyy-MM-dd"),
        adherence_score: 70,
      });
      setMeals([]);
    }
  }, [isEdit, log, form.reset]);

  const mutation = useMutation({
    mutationFn: (payload: HealthLogCreatePayload) => healthService.save(payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["health-logs"] });
      queryClient.invalidateQueries({ queryKey: ["panel-health-logs"] });
      queryClient.invalidateQueries({ queryKey: ["health-logs-overview"] });
      navigate("/panel/health-log");
    },
  });

  const { register, handleSubmit, formState: { errors }, setValue, watch } = form;
  const adherenceScore = watch("adherence_score") ?? 0;

  const addMeal = () => {
    setMeals((prev) => [...prev, { type: "Meal", name: "", time: "", calories: undefined }]);
  };

  const updateMeal = (index: number, field: keyof HealthLogMeal, value: string | number | undefined) => {
    setMeals((prev) => {
      const next = [...prev];
      next[index] = { ...next[index], [field]: value };
      return next;
    });
  };

  const removeMeal = (index: number) => {
    setMeals((prev) => prev.filter((_, i) => i !== index));
  };

  if (isEdit && isLoading) {
    return (
      <div className="animate-pulse space-y-4">
        <div className="h-10 bg-muted rounded w-48" />
        <div className="h-64 bg-muted rounded" />
      </div>
    );
  }

  const onSubmit = (values: FormValues) => {
    let activityNotes = values.activity_notes || "";
    if (activityType && activityType.trim()) {
      const prefix = `${activityType.trim()} – `;
      activityNotes = activityNotes ? `${prefix}${activityNotes}` : prefix.trim();
    }
    const payload: HealthLogCreatePayload = {
      log_date: values.log_date,
      water_ml: values.water_ml ?? null,
      calories: values.calories ?? null,
      protein: values.protein ?? null,
      carbs: values.carbs ?? null,
      fat: values.fat ?? null,
      medicines: values.medicines || null,
      activity_minutes: values.activity_minutes ?? null,
      activity_notes: activityNotes || null,
      adherence_score: values.adherence_score ?? null,
    };
    if (values.webinar_id) payload.webinar_id = values.webinar_id;
    const cleanedMeals = meals.filter((m) => (m.name && m.name.trim()) || (m.calories != null && m.calories > 0));
    if (cleanedMeals.length > 0) {
      payload.meals = cleanedMeals.map((m) => ({
        type: m.type || "Meal",
        name: m.name?.trim() || "",
        time: m.time || "",
        calories: m.calories ?? undefined,
        notes: m.notes,
      }));
    }
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

  const waterProgress = waterMl != null && waterMl > 0 ? Math.min(100, (waterMl / WATER_GOAL_ML) * 100) : 0;

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
        <CardContent className="pt-6">
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
                  <Label htmlFor="webinar_id">Program (optional)</Label>
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

            <Tabs defaultValue="nutrition" className="w-full">
              <TabsList className="grid w-full grid-cols-3">
                <TabsTrigger value="nutrition" className="gap-2">
                  <Utensils size={16} />
                  Nutrition
                </TabsTrigger>
                <TabsTrigger value="activity" className="gap-2">
                  <Activity size={16} />
                  Activity
                </TabsTrigger>
                <TabsTrigger value="notes" className="gap-2">
                  <FileText size={16} />
                  Notes
                </TabsTrigger>
              </TabsList>

              <TabsContent value="nutrition" className="space-y-6 mt-6">
                <div>
                  <Label className="flex items-center gap-2 mb-2">
                    <Droplets size={16} />
                    Water (ml)
                  </Label>
                  <div className="flex flex-wrap gap-2 mb-2">
                    {WATER_PRESETS.map((ml) => (
                      <Button
                        key={ml}
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={() => setValue("water_ml", (form.getValues("water_ml") || 0) + ml)}
                      >
                        +{ml}
                      </Button>
                    ))}
                  </div>
                  <Input
                    id="water_ml"
                    type="number"
                    min={0}
                    placeholder="Total ml"
                    {...register("water_ml")}
                  />
                  {WATER_GOAL_ML > 0 && (
                    <div className="mt-2">
                      <div className="flex justify-between text-xs text-muted-foreground mb-1">
                        <span>Daily goal: {WATER_GOAL_ML} ml</span>
                        {(waterMl ?? 0) > 0 && (
                          <span>{waterMl} ml</span>
                        )}
                      </div>
                      <Progress value={waterProgress} className="h-2" />
                    </div>
                  )}
                </div>

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                  <div>
                    <Label htmlFor="calories">Calories</Label>
                    <Input id="calories" type="number" min={0} placeholder="kcal" {...register("calories")} />
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
                  <div className="flex items-center justify-between mb-2">
                    <Label>Meals / snacks</Label>
                    <Button type="button" variant="outline" size="sm" onClick={addMeal} className="gap-1">
                      <Plus size={14} /> Add
                    </Button>
                  </div>
                  {meals.length === 0 && (
                    <p className="text-sm text-muted-foreground py-2">Optional: add meals for detail.</p>
                  )}
                  <div className="space-y-3">
                    {meals.map((meal, index) => (
                      <div key={index} className="flex flex-wrap items-end gap-2 p-3 rounded-lg border border-border bg-muted/30">
                        <select
                          className="flex h-9 w-24 rounded-md border border-input bg-background px-2 text-sm"
                          value={meal.type || "Meal"}
                          onChange={(e) => updateMeal(index, "type", e.target.value)}
                        >
                          <option value="Breakfast">Breakfast</option>
                          <option value="Lunch">Lunch</option>
                          <option value="Dinner">Dinner</option>
                          <option value="Snack">Snack</option>
                          <option value="Meal">Meal</option>
                        </select>
                        <Input
                          placeholder="Name"
                          className="flex-1 min-w-[120px]"
                          value={meal.name ?? ""}
                          onChange={(e) => updateMeal(index, "name", e.target.value)}
                        />
                        <Input
                          type="time"
                          className="w-28"
                          value={meal.time ?? ""}
                          onChange={(e) => updateMeal(index, "time", e.target.value)}
                        />
                        <Input
                          type="number"
                          min={0}
                          placeholder="Cal"
                          className="w-20"
                          value={meal.calories ?? ""}
                          onChange={(e) => updateMeal(index, "calories", e.target.value === "" ? undefined : Number(e.target.value))}
                        />
                        <Button
                          type="button"
                          variant="ghost"
                          size="icon"
                          className="shrink-0 text-muted-foreground hover:text-destructive"
                          onClick={() => removeMeal(index)}
                        >
                          <Trash2 size={16} />
                        </Button>
                      </div>
                    ))}
                  </div>
                </div>
              </TabsContent>

              <TabsContent value="activity" className="space-y-6 mt-6">
                <div className="grid gap-4 sm:grid-cols-2">
                  <div>
                    <Label htmlFor="activity_type">Activity type</Label>
                    <select
                      id="activity_type"
                      className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                      value={activityType}
                      onChange={(e) => setActivityType(e.target.value)}
                    >
                      {ACTIVITY_TYPES.map((t) => (
                        <option key={t || "none"} value={t}>{t || "— Select —"}</option>
                      ))}
                    </select>
                  </div>
                  <div>
                    <Label htmlFor="activity_minutes">Duration (minutes)</Label>
                    <Input
                      id="activity_minutes"
                      type="number"
                      min={0}
                      placeholder="e.g. 30"
                      {...register("activity_minutes")}
                    />
                  </div>
                </div>
                <div>
                  <Label htmlFor="activity_notes">Activity notes</Label>
                  <Textarea
                    id="activity_notes"
                    rows={2}
                    placeholder="e.g. route, intensity, how you felt"
                    {...register("activity_notes")}
                  />
                </div>
                <div>
                  <Label className="mb-3 block">
                    Adherence score: <span className="font-semibold text-primary">{adherenceScore}% — {adherenceLabel(adherenceScore)}</span>
                  </Label>
                  <Slider
                    min={0}
                    max={100}
                    step={5}
                    value={[adherenceScore]}
                    onValueChange={([v]) => setValue("adherence_score", v)}
                    className="w-full"
                  />
                  <div className="flex justify-between text-xs text-muted-foreground mt-1">
                    <span>Poor</span>
                    <span>Fair</span>
                    <span>Good</span>
                    <span>Great</span>
                  </div>
                </div>
              </TabsContent>

              <TabsContent value="notes" className="space-y-6 mt-6">
                <div>
                  <Label htmlFor="medicines">Supplements / medicines</Label>
                  <Textarea id="medicines" rows={3} placeholder="What you took today" {...register("medicines")} />
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
              </TabsContent>
            </Tabs>

            <div className="flex gap-3 pt-4 border-t border-border">
              <Button type="submit" className="bg-gradient-cta" disabled={mutation.isPending}>
                {mutation.isPending ? "Saving…" : isEdit ? "Update log" : "Save log"}
              </Button>
              <Link to="/panel/health-log">
                <Button type="button" variant="outline">Cancel</Button>
              </Link>
            </div>
            {mutation.isError && (
              <p className="text-sm text-destructive">
                {(() => {
                  const msg = (mutation.error as Error).message;
                  if (msg === "auth.unauthorized" || msg.toLowerCase().includes("unauthorized")) {
                    return "You must be logged in as a student to save your daily log. Please log out and log in again with your student account.";
                  }
                  return msg;
                })()}
              </p>
            )}
          </form>
        </CardContent>
      </Card>
    </>
  );
}

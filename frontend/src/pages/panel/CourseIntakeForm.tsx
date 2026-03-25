import { useState, useEffect } from "react";
import { useParams, Link } from "react-router-dom";
import { useQuery, useQueryClient } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { programsService } from "@/services/programs";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { ArrowLeft, Upload, FileText } from "lucide-react";
import { toast } from "sonner";

type IntakeFormData = {
  weight_history: string;
  past_dieting_attempts: string;
  digestive_issues: string;
  sleep_quality: string;
  stress_level: string;
  food_preference: string;
  meal_timings: string;
  daily_schedule: string;
};

const SLEEP_OPTIONS = ["Poor", "Fair", "Good", "Very good", "Excellent"];
const STRESS_OPTIONS = ["Low", "Moderate", "High", "Very high"];

export default function CourseIntakeForm() {
  const { id } = useParams<{ id: string }>();
  const queryClient = useQueryClient();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [uploadSuccess, setUploadSuccess] = useState(false);
  const [questionnaireSaved, setQuestionnaireSaved] = useState(false);
  const [courseTitle, setCourseTitle] = useState<string>("");
  const [bloodReportFiles, setBloodReportFiles] = useState<File[]>([]);
  const [bodyMeasurementsFile, setBodyMeasurementsFile] = useState<File | null>(null);
  const [bodyPhotoFiles, setBodyPhotoFiles] = useState<File[]>([]);

  const { data, isLoading: loadingIntake, error: intakeError } = useQuery({
    queryKey: ["course-intake", id],
    queryFn: () => programsService.getIntake(id!),
    enabled: !!id,
  });
  const intake = (data as { intake?: IntakeFormData })?.intake;
  const webinar = (data as { webinar?: { id: number; title: string } })?.webinar;

  useEffect(() => {
    if (webinar?.title) setCourseTitle(webinar.title);
  }, [webinar]);

  const form = useForm<IntakeFormData>({
    defaultValues: {
      weight_history: "",
      past_dieting_attempts: "",
      digestive_issues: "",
      sleep_quality: "",
      stress_level: "",
      food_preference: "",
      meal_timings: "",
      daily_schedule: "",
    },
  });

  useEffect(() => {
    if (intake) {
    form.reset({
      weight_history: (intake as IntakeFormData).weight_history ?? "",
      past_dieting_attempts: (intake as IntakeFormData).past_dieting_attempts ?? "",
      digestive_issues: (intake as IntakeFormData).digestive_issues ?? "",
      sleep_quality: (intake as IntakeFormData).sleep_quality ?? "",
      stress_level: (intake as IntakeFormData).stress_level ?? "",
      food_preference: (intake as IntakeFormData).food_preference ?? "",
      meal_timings: (intake as IntakeFormData).meal_timings ?? "",
      daily_schedule: (intake as IntakeFormData).daily_schedule ?? "",
    });
    }
  }, [intake, form]);

  const onSaveForm = async (values: IntakeFormData) => {
    if (!id) return;
    setError(null);
    setQuestionnaireSaved(false);
    setLoading(true);
    try {
      await programsService.saveIntake(id, values);
      setError(null);
      setQuestionnaireSaved(true);
      queryClient.invalidateQueries({ queryKey: ["course-intake", id] });
      toast.success("Questionnaire saved successfully. You can now upload files below if needed.");
    } catch (e) {
      setError(e instanceof Error ? e.message : "Failed to save");
    } finally {
      setLoading(false);
    }
  };

  const onUploadFiles = async () => {
    if (!id) return;
    setError(null);
    setUploadSuccess(false);
    setLoading(true);
    try {
      const formData = new FormData();
      bloodReportFiles.forEach((f) => formData.append("blood_reports[]", f));
      if (bodyMeasurementsFile) formData.append("body_measurements", bodyMeasurementsFile);
      bodyPhotoFiles.forEach((f) => formData.append("body_photos[]", f));
      const res = await programsService.uploadIntakeFiles(id, formData);
      if (res?.data) {
        setUploadSuccess(true);
        setBloodReportFiles([]);
        setBodyMeasurementsFile(null);
        setBodyPhotoFiles([]);
      }
    } catch (e) {
      setError(e instanceof Error ? e.message : "Failed to upload files");
    } finally {
      setLoading(false);
    }
  };

  if (intakeError || (data && !(data as { webinar?: unknown }).webinar && (data as { status?: string }).status === "not_purchased")) {
    return (
      <div className="space-y-4">
        <Link to={`/panel/learn/${id}`} className="inline-flex items-center gap-2 text-sm text-primary hover:underline">
          <ArrowLeft size={16} /> Back to course
        </Link>
        <Card className="border-destructive/50 bg-destructive/5">
          <CardContent className="py-8 text-center">
            <p className="text-muted-foreground">
              You need to purchase this course to access the intake form.
            </p>
            <Link to={`/panel/programs/${id}`}>
              <Button variant="outline" className="mt-4">View program</Button>
            </Link>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Link
          to={`/panel/learn/${id}`}
          className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
        >
          <ArrowLeft size={16} /> Back to course
        </Link>
      </div>
      <div>
        <h1 className="text-2xl font-display font-bold text-foreground">Course intake form</h1>
        <p className="text-muted-foreground mt-1">
          {courseTitle ? `Complete this form for: ${courseTitle}` : "Questionnaire and uploads for your enrolled course."}
        </p>
      </div>

      {error && (
        <div className="rounded-lg border border-destructive/50 bg-destructive/10 px-4 py-3 text-sm text-destructive">
          {error}
        </div>
      )}
      {questionnaireSaved && (
        <div className="rounded-lg border border-green-500/50 bg-green-500/10 px-4 py-3 text-sm text-green-700 dark:text-green-400">
          Questionnaire saved successfully. You can upload files in the section below.
        </div>
      )}
      {uploadSuccess && (
        <div className="rounded-lg border border-green-500/50 bg-green-500/10 px-4 py-3 text-sm text-green-700 dark:text-green-400">
          Files uploaded successfully.
        </div>
      )}

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <FileText size={20} /> Questionnaire
          </CardTitle>
          <CardDescription>
            Weight history, dieting attempts, digestive issues, sleep, stress, food preference, meal timings, daily schedule.
          </CardDescription>
        </CardHeader>
        <CardContent>
          {loadingIntake ? (
            <p className="text-muted-foreground">Loading…</p>
          ) : (
            <form onSubmit={form.handleSubmit(onSaveForm)} className="space-y-5">
              <div className="space-y-2">
                <Label>Weight history</Label>
                <textarea
                  className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  placeholder="e.g. Current weight, past weight fluctuations, when you gained/lost"
                  {...form.register("weight_history")}
                />
              </div>
              <div className="space-y-2">
                <Label>Past dieting attempts</Label>
                <textarea
                  className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  placeholder="What diets have you tried? What worked or didn't?"
                  {...form.register("past_dieting_attempts")}
                />
              </div>
              <div className="space-y-2">
                <Label>Digestive issues</Label>
                <textarea
                  className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  placeholder="Bloating, constipation, acidity, etc."
                  {...form.register("digestive_issues")}
                />
              </div>
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label>Sleep quality</Label>
                  <select
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...form.register("sleep_quality")}
                  >
                    <option value="">Select</option>
                    {SLEEP_OPTIONS.map((o) => (
                      <option key={o} value={o}>{o}</option>
                    ))}
                  </select>
                </div>
                <div className="space-y-2">
                  <Label>Stress level</Label>
                  <select
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...form.register("stress_level")}
                  >
                    <option value="">Select</option>
                    {STRESS_OPTIONS.map((o) => (
                      <option key={o} value={o}>{o}</option>
                    ))}
                  </select>
                </div>
              </div>
              <div className="space-y-2">
                <Label>Food preference</Label>
                <textarea
                  className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  placeholder="Likes, dislikes, allergies, vegetarian/vegan, etc."
                  {...form.register("food_preference")}
                />
              </div>
              <div className="space-y-2">
                <Label>Meal timings</Label>
                <input
                  className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  placeholder="e.g. Breakfast 8am, Lunch 1pm, Dinner 8pm"
                  {...form.register("meal_timings")}
                />
              </div>
              <div className="space-y-2">
                <Label>Daily schedule</Label>
                <textarea
                  className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                  placeholder="Wake time, work hours, exercise time, etc."
                  {...form.register("daily_schedule")}
                />
              </div>
              <Button type="submit" disabled={loading}>
                {loading ? "Saving…" : "Save questionnaire"}
              </Button>
            </form>
          )}
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Upload size={20} /> Uploads
          </CardTitle>
          <CardDescription>
            Blood reports, body measurements, and body photos (optional).
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-6">
          <div className="space-y-2">
            <Label>Blood reports</Label>
            <input
              type="file"
              accept=".pdf,image/*"
              multiple
              className="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-md file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-medium file:text-primary-foreground"
              onChange={(e) => setBloodReportFiles(e.target.files ? Array.from(e.target.files) : [])}
            />
          </div>
          <div className="space-y-2">
            <Label>Body measurements (file)</Label>
            <input
              type="file"
              accept=".pdf,image/*"
              className="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-md file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-medium file:text-primary-foreground"
              onChange={(e) => setBodyMeasurementsFile(e.target.files?.[0] ?? null)}
            />
          </div>
          <div className="space-y-2">
            <Label>Body photos (optional)</Label>
            <input
              type="file"
              accept="image/*"
              multiple
              className="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-md file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-medium file:text-primary-foreground"
              onChange={(e) => setBodyPhotoFiles(e.target.files ? Array.from(e.target.files) : [])}
            />
          </div>
          <Button
            type="button"
            onClick={onUploadFiles}
            disabled={loading || (bloodReportFiles.length === 0 && !bodyMeasurementsFile && bodyPhotoFiles.length === 0)}
          >
            {loading ? "Uploading…" : "Upload files"}
          </Button>
        </CardContent>
      </Card>
    </div>
  );
}

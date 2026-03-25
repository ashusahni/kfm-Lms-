import { Link, useParams } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { onboardingApi } from "@/services/onboarding";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import {
  User,
  Heart,
  Pill,
  UtensilsCrossed,
  Moon,
  Target,
  FileText,
  ExternalLink,
  ArrowLeft,
} from "lucide-react";

function Section({
  title,
  icon: Icon,
  children,
}: {
  title: string;
  icon: React.ElementType;
  children: React.ReactNode;
}) {
  return (
    <Card className="border border-border">
      <CardHeader className="pb-2">
        <CardTitle className="text-base font-medium flex items-center gap-2">
          <Icon size={18} className="text-primary shrink-0" />
          {title}
        </CardTitle>
      </CardHeader>
      <CardContent className="text-sm text-muted-foreground space-y-2">
        {children}
      </CardContent>
    </Card>
  );
}

function LabelValue({ label, value }: { label: string; value?: string | number | null }) {
  if (value === undefined || value === null || value === "") return null;
  return (
    <p>
      <span className="font-medium text-foreground">{label}:</span>{" "}
      <span>{String(value)}</span>
    </p>
  );
}

export default function DieticianStudentHealthProfile() {
  const { userId } = useParams<{ userId: string }>();

  const { data, isLoading, error } = useQuery({
    queryKey: ["dietician-student-health-profile", userId],
    queryFn: () => onboardingApi.getStudentProfileForDietician(userId!),
    enabled: !!userId,
  });

  if (!userId) {
    return (
      <div className="space-y-4">
        <p className="text-muted-foreground">Missing student.</p>
        <Link to="/dietician/health-logs">
          <Button variant="outline">Back to Student health logs</Button>
        </Link>
      </div>
    );
  }

  if (isLoading) {
    return (
      <div className="space-y-6">
        <Skeleton className="h-10 w-64" />
        <div className="grid gap-4 md:grid-cols-2">
          {[1, 2, 3, 4].map((i) => (
            <Skeleton key={i} className="h-40 rounded-lg" />
          ))}
        </div>
      </div>
    );
  }

  if (error || !data) {
    return (
      <div className="space-y-4">
        <h1 className="text-2xl font-display font-bold text-foreground">Student health profile</h1>
        <Card className="border-destructive/50 bg-destructive/5">
          <CardContent className="py-6">
            <p className="text-muted-foreground">
              {error instanceof Error ? error.message : "Could not load this student's health profile. They may not be enrolled in your courses, or they may not have completed onboarding."}
            </p>
            <Link to="/dietician/health-logs" className="inline-block mt-4">
              <Button variant="outline">Back to Student health logs</Button>
            </Link>
          </CardContent>
        </Card>
      </div>
    );
  }

  const user = data.user;
  const profile = data.health_profile as Record<string, unknown> | undefined;
  const conditions = data.health_conditions ?? [];
  const medical = data.medical_data as Record<string, unknown> | undefined;
  const diet = data.diet_pattern as Record<string, unknown> | undefined;
  const lifestyle = data.lifestyle_assessment as Record<string, unknown> | undefined;
  const goals = data.body_goals ?? [];
  const files = data.file_uploads;

  return (
    <div className="space-y-6">
      <div>
        <Link
          to="/dietician/health-logs"
          className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground mb-4"
        >
          <ArrowLeft className="h-4 w-4" />
          Back to Student health logs
        </Link>
        <h1 className="text-2xl font-display font-bold text-foreground">
          Student health profile: {user?.full_name ?? `#${userId}`}
        </h1>
        <p className="text-muted-foreground mt-1">
          Use this to guide the student. Details they shared during signup and onboarding.
        </p>
      </div>

      <div className="grid gap-4 md:grid-cols-2">
        <Section title="Personal info" icon={User}>
          <LabelValue label="Name" value={user?.full_name} />
          <LabelValue label="Email" value={user?.email} />
          <LabelValue label="Age" value={profile?.age} />
          <LabelValue label="Gender" value={profile?.gender as string} />
          <LabelValue label="Height (cm)" value={profile?.height} />
          <LabelValue label="Weight (kg)" value={profile?.weight} />
          <LabelValue label="City" value={profile?.city as string} />
          <LabelValue label="Occupation" value={profile?.occupation as string} />
          <LabelValue label="Lifestyle" value={profile?.lifestyle_type as string} />
          <LabelValue label="Language" value={profile?.language as string} />
          {!profile && !user?.full_name && (
            <p className="text-muted-foreground italic">No personal info saved yet.</p>
          )}
        </Section>

        <Section title="Health conditions" icon={Heart}>
          {conditions.length > 0 ? (
            <ul className="list-disc list-inside space-y-1 text-foreground">
              {conditions.map((c) => (
                <li key={c.id}>{c.name}</li>
              ))}
            </ul>
          ) : (
            <p className="italic">None selected.</p>
          )}
        </Section>

        <Section title="Medical data" icon={Pill}>
          <LabelValue label="Current medications" value={medical?.current_medications as string} />
          <LabelValue label="Past surgeries" value={medical?.past_surgeries as string} />
          <LabelValue label="Food allergies" value={medical?.food_allergies as string} />
          <LabelValue label="Menstrual history" value={medical?.menstrual_history as string} />
          {!medical && (
            <p className="italic">No medical data saved yet.</p>
          )}
        </Section>

        <Section title="Diet pattern" icon={UtensilsCrossed}>
          <LabelValue label="Diet type" value={diet?.diet_type as string} />
          <LabelValue label="Meal pattern" value={diet?.meal_pattern as string} />
          <LabelValue label="Breakfast" value={diet?.breakfast as string} />
          <LabelValue label="Lunch" value={diet?.lunch as string} />
          <LabelValue label="Dinner" value={diet?.dinner as string} />
          <LabelValue label="Food cravings" value={diet?.food_cravings as string} />
          <LabelValue label="Outside food frequency" value={diet?.outside_food_frequency as string} />
          {!diet && <p className="italic">No diet pattern saved yet.</p>}
        </Section>

        <Section title="Lifestyle" icon={Moon}>
          <LabelValue label="Sleep (hours)" value={lifestyle?.sleep_hours} />
          <LabelValue label="Stress level" value={lifestyle?.stress_level as string} />
          <LabelValue label="Water intake" value={lifestyle?.water_intake as string} />
          <LabelValue label="Physical activity" value={lifestyle?.physical_activity_level as string} />
          {!lifestyle && <p className="italic">No lifestyle data saved yet.</p>}
        </Section>

        <Section title="Body goals" icon={Target}>
          {goals.length > 0 ? (
            <ul className="list-disc list-inside space-y-1 text-foreground">
              {goals.map((g) => (
                <li key={g.id}>{g.name}</li>
              ))}
            </ul>
          ) : (
            <p className="italic">None selected.</p>
          )}
        </Section>

        {files && (files.blood_report || files.medical_report || files.medication_prescription || (files.body_photos && (files.body_photos as string[]).length > 0)) && (
          <Section title="Uploaded files" icon={FileText}>
            <div className="space-y-2">
              {files.blood_report && (
                <p>
                  <a
                    href={files.blood_report as string}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-primary hover:underline inline-flex items-center gap-1"
                  >
                    Blood report <ExternalLink size={12} />
                  </a>
                </p>
              )}
              {files.medical_report && (
                <p>
                  <a
                    href={files.medical_report as string}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-primary hover:underline inline-flex items-center gap-1"
                  >
                    Medical report <ExternalLink size={12} />
                  </a>
                </p>
              )}
              {files.medication_prescription && (
                <p>
                  <a
                    href={files.medication_prescription as string}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-primary hover:underline inline-flex items-center gap-1"
                  >
                    Medication / prescription <ExternalLink size={12} />
                  </a>
                </p>
              )}
              {files.body_photos && Array.isArray(files.body_photos) && files.body_photos.length > 0 && (
                <p className="text-foreground">
                  <span className="font-medium">Body photos:</span>{" "}
                  {(files.body_photos as string[]).length} file(s)
                </p>
              )}
            </div>
          </Section>
        )}
      </div>
    </div>
  );
}

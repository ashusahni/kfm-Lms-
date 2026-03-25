import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { useForm } from "react-hook-form";
import { useAuth } from "@/context/AuthContext";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import {
  ProgressBar,
  MultiSelect,
  FileUploader,
  SelectDropdown,
  RadioGroupField,
  RangeSlider,
} from "@/components/onboarding";
import { onboardingApi } from "@/services/onboarding";
import { cn } from "@/lib/utils";

const TOTAL_STEPS = 7;

type Step1Data = {
  name: string;
  age: string;
  gender: "male" | "female" | "other" | "";
  height: string;
  weight: string;
  city: string;
  occupation: string;
  lifestyle_type: "sedentary" | "moderately_active" | "very_active" | "";
  language: string;
};

type Step3Data = {
  current_medications: string;
  past_surgeries: string;
  food_allergies: string;
  menstrual_cycle: "regular" | "irregular" | "";
  last_period_date: string;
  pcos_history: "yes" | "no" | "";
};

type Step4Data = {
  diet_type: string;
  meal_pattern: string;
  breakfast: string;
  lunch: string;
  dinner: string;
  food_cravings: string;
  outside_food_frequency: string;
};

type Step5Data = {
  sleep_hours: string;
  stress_level: string;
  water_intake: string;
  physical_activity_level: string;
};

const LIFESTYLE_OPTIONS = [
  { value: "sedentary", label: "Sedentary" },
  { value: "moderately_active", label: "Moderately active" },
  { value: "very_active", label: "Very active" },
];

const AGE_OPTIONS = Array.from({ length: 80 - 18 + 1 }, (_, i) => {
  const age = 18 + i;
  return { value: String(age), label: String(age) };
});

const HEIGHT_MIN = 130;
const HEIGHT_MAX = 210;
const WEIGHT_MIN = 40;
const WEIGHT_MAX = 150;

const OCCUPATION_OPTIONS = [
  "Student",
  "Working professional",
  "Homemaker",
  "Business owner",
  "Retired",
  "Other",
].map((label) => ({ value: label.toLowerCase().replace(/\s+/g, "_"), label }));

const LANGUAGE_OPTIONS = ["English", "Hindi", "Gujarati", "Marathi", "Other"].map((label) => ({
  value: label.toLowerCase(),
  label,
}));

const CITY_OPTIONS = [
  "Mumbai",
  "Delhi",
  "Bengaluru",
  "Hyderabad",
  "Ahmedabad",
  "Chennai",
  "Pune",
  "Kolkata",
  "Other",
].map((label) => ({ value: label.toLowerCase(), label }));

const MEAL_PATTERNS = [
  { value: "north_indian", label: "North Indian" },
  { value: "south_indian", label: "South Indian" },
];

// Fallback lists matching backend seed (same order = same IDs). Used when API fails or returns empty.
const FALLBACK_CONDITIONS: { id: number; name: string }[] = [
  { id: 1, name: "Thyroid" },
  { id: 2, name: "Diabetes" },
  { id: 3, name: "PCOS" },
  { id: 4, name: "Hypertension" },
  { id: 5, name: "Fertility Issues" },
  { id: 6, name: "Hair fall" },
  { id: 7, name: "Skin Issues" },
  { id: 8, name: "PCOD" },
  { id: 9, name: "Constipation" },
  { id: 10, name: "GERD" },
  { id: 11, name: "Acidity" },
  { id: 12, name: "Kidney Health" },
  { id: 13, name: "Liver Health" },
  { id: 14, name: "Fat Loss" },
  { id: 15, name: "Weight Gain" },
  { id: 16, name: "Other" },
];

const FALLBACK_GOALS: { id: number; name: string }[] = [
  { id: 1, name: "Fat loss" },
  { id: 2, name: "Inch loss" },
  { id: 3, name: "PCOS management" },
  { id: 4, name: "Thyroid support" },
  { id: 5, name: "Gut health" },
  { id: 6, name: "Hair fall" },
  { id: 7, name: "Skin health" },
];

export default function Onboarding() {
  const navigate = useNavigate();
  const { isAuthenticated } = useAuth();
  const [step, setStep] = useState(1);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [conditions, setConditions] = useState<{ id: number; name: string }[]>([]);
  const [goals, setGoals] = useState<{ id: number; name: string }[]>([]);
  const [selectedConditions, setSelectedConditions] = useState<number[]>([]);
  const [selectedGoals, setSelectedGoals] = useState<number[]>([]);
  const [files, setFiles] = useState<{
    blood_report: File | null;
    medical_report: File | null;
    body_photos: File[];
    medication_prescription: File | null;
  }>({
    blood_report: null,
    medical_report: null,
    body_photos: [],
    medication_prescription: null,
  });

  useEffect(() => {
    if (!isAuthenticated) {
      navigate("/login", { replace: true });
      return;
    }
    Promise.all([onboardingApi.getHealthConditions(), onboardingApi.getBodyGoals()])
      .then(([c, g]) => {
        setConditions(c?.conditions?.length ? c.conditions : FALLBACK_CONDITIONS);
        setGoals(g?.goals?.length ? g.goals : FALLBACK_GOALS);
      })
      .catch(() => {
        setConditions(FALLBACK_CONDITIONS);
        setGoals(FALLBACK_GOALS);
      });
  }, [isAuthenticated, navigate]);

  const step1Form = useForm<Step1Data>({
    defaultValues: {
      name: "",
      age: "",
      gender: "",
      height: "170",
      weight: "70",
      city: "",
      occupation: "",
      lifestyle_type: "",
      language: "",
    },
  });

  const step3Form = useForm<Step3Data>({
    defaultValues: {
      current_medications: "",
      past_surgeries: "",
      food_allergies: "",
      menstrual_cycle: "",
      last_period_date: "",
      pcos_history: "",
    },
  });

  const step4Form = useForm<Step4Data>({
    defaultValues: {
      diet_type: "",
      meal_pattern: "",
      breakfast: "",
      lunch: "",
      dinner: "",
      food_cravings: "",
      outside_food_frequency: "",
    },
  });

  const step5Form = useForm<Step5Data>({
    defaultValues: {
      sleep_hours: "",
      stress_level: "",
      water_intake: "",
      physical_activity_level: "",
    },
  });

  const saveStep1 = async (data: Step1Data) => {
    setError(null);
    setLoading(true);
    try {
      await onboardingApi.saveHealthProfile({
        name: data.name || undefined,
        age: parseInt(data.age, 10) || 0,
        gender: data.gender || undefined,
        height: parseFloat(data.height) || 0,
        weight: parseFloat(data.weight) || 0,
        city: data.city || undefined,
        occupation: data.occupation || undefined,
        lifestyle_type:
          (data.lifestyle_type as "sedentary" | "moderately_active" | "very_active") || undefined,
        language: data.language || undefined,
      });
      setStep(2);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Failed to save");
    } finally {
      setLoading(false);
    }
  };

  const saveStep2 = async () => {
    setError(null);
    setLoading(true);
    try {
      await onboardingApi.saveHealthConditions(selectedConditions);
      setStep(3);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Failed to save");
    } finally {
      setLoading(false);
    }
  };

  const saveStep3 = async (data: Step3Data) => {
    setError(null);
    setLoading(true);
    try {
      await onboardingApi.saveMedicalData({
        current_medications: data.current_medications || undefined,
        past_surgeries: data.past_surgeries || undefined,
        food_allergies: data.food_allergies || undefined,
        menstrual_history:
          data.menstrual_cycle || data.last_period_date || data.pcos_history
            ? [
                data.menstrual_cycle && `Cycle: ${data.menstrual_cycle === "regular" ? "Regular" : "Irregular"}`,
                data.last_period_date && `Last period: ${data.last_period_date}`,
                data.pcos_history && `PCOS history: ${data.pcos_history === "yes" ? "Yes" : "No"}`,
              ]
                .filter(Boolean)
                .join(" | ")
            : undefined,
      });
      setStep(4);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Failed to save");
    } finally {
      setLoading(false);
    }
  };

  const saveStep4 = async (data: Step4Data) => {
    setError(null);
    setLoading(true);
    try {
      await onboardingApi.saveDietPattern({
        diet_type: (data.diet_type as "veg" | "nonveg" | "eggetarian") || undefined,
        meal_pattern: data.meal_pattern || undefined,
        breakfast: data.breakfast || undefined,
        lunch: data.lunch || undefined,
        dinner: data.dinner || undefined,
        food_cravings: data.food_cravings || undefined,
        outside_food_frequency: data.outside_food_frequency || undefined,
      });
      setStep(5);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Failed to save");
    } finally {
      setLoading(false);
    }
  };

  const saveStep5 = async (data: Step5Data) => {
    setError(null);
    setLoading(true);
    try {
      await onboardingApi.saveLifestyle({
        sleep_hours: data.sleep_hours ? parseFloat(data.sleep_hours) : undefined,
        stress_level: data.stress_level || undefined,
        water_intake: data.water_intake || undefined,
        physical_activity_level: data.physical_activity_level || undefined,
      });
      setStep(6);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Failed to save");
    } finally {
      setLoading(false);
    }
  };

  const saveStep6 = async () => {
    setError(null);
    setLoading(true);
    try {
      await onboardingApi.saveBodyGoals(selectedGoals);
      setStep(7);
    } catch (e) {
      setError(e instanceof Error ? e.message : "Failed to save");
    } finally {
      setLoading(false);
    }
  };

  const saveStep7 = async () => {
    setError(null);
    setLoading(true);
    try {
      const formData = new FormData();
      if (files.blood_report) formData.append("blood_report", files.blood_report);
      if (files.medical_report) formData.append("medical_report", files.medical_report);
      if (files.medication_prescription) formData.append("medication_prescription", files.medication_prescription);
      files.body_photos.forEach((f) => formData.append("body_photos[]", f));
      await onboardingApi.uploadFiles(formData);
      navigate("/panel", { replace: true });
    } catch (e) {
      setError(e instanceof Error ? e.message : "Failed to upload");
    } finally {
      setLoading(false);
    }
  };

  if (!isAuthenticated) return null;

  return (
    <div className="min-h-screen bg-muted/40 py-8 px-4 sm:px-6">
      <div className="mx-auto max-w-2xl">
        <div className="mb-6 space-y-2">
          <ProgressBar currentStep={step} totalSteps={TOTAL_STEPS} />
        </div>
        <Card className="shadow-lg border bg-background/95 backdrop-blur">
          <CardHeader className="pb-4 sm:pb-6">
            <CardTitle className="text-xl sm:text-2xl">
              {step === 1 && "Personal info"}
              {step === 2 && "Health conditions"}
              {step === 3 && "Medical data"}
              {step === 4 && "Diet pattern"}
              {step === 5 && "Lifestyle"}
              {step === 6 && "Body goals"}
              {step === 7 && "File uploads"}
            </CardTitle>
            <CardDescription className="text-sm text-muted-foreground">
              {step === 1 && "Basic details for your health profile"}
              {step === 2 && "Select any conditions that apply"}
              {step === 3 && "Medications, surgeries, allergies"}
              {step === 4 && "Diet and meal habits"}
              {step === 5 && "Sleep, stress, activity"}
              {step === 6 && "What do you want to achieve?"}
              {step === 7 && "Upload reports and photos (optional)"}
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-6 sm:space-y-8">
            {error && (
              <p className="text-sm text-destructive bg-destructive/10 p-3 rounded-md">{error}</p>
            )}

            {/* Step 1 */}
            {step === 1 && (
              <form onSubmit={step1Form.handleSubmit(saveStep1)} className="space-y-6">
                <div className="rounded-lg bg-muted/40 p-4 sm:p-5 space-y-4">
                  <p className="text-xs sm:text-sm text-muted-foreground">
                    This helps your dietician understand your current profile. Most fields are quick
                    selectors, so you can finish in under a minute.
                  </p>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                  <div className="space-y-2">
                    <Label>Name</Label>
                    <Input {...step1Form.register("name")} placeholder="Full name" />
                  </div>
                  <div className="space-y-2">
                    <Label>Age</Label>
                    <SelectDropdown
                      value={step1Form.watch("age")}
                      onChange={(v) => step1Form.setValue("age", v, { shouldValidate: true })}
                      options={AGE_OPTIONS}
                      placeholder="Select age"
                    />
                  </div>
                  </div>
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-7">
                  <div className="space-y-2">
                    <Label>Gender</Label>
                    <RadioGroupField
                      value={step1Form.watch("gender")}
                      onChange={(v) =>
                        step1Form.setValue("gender", v as Step1Data["gender"], { shouldValidate: true })
                      }
                      options={[
                        { value: "male", label: "Male" },
                        { value: "female", label: "Female" },
                        { value: "other", label: "Other" },
                      ]}
                    />
                  </div>
                  <div className="space-y-2">
                    <Label>Height (cm)</Label>
                    <RangeSlider
                      min={HEIGHT_MIN}
                      max={HEIGHT_MAX}
                      step={1}
                      value={step1Form.watch("height")}
                      onChange={(e) => step1Form.setValue("height", e.target.value, { shouldValidate: true })}
                      labelFormatter={(v) => `${v} cm`}
                    />
                  </div>
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-7">
                  <div className="space-y-2">
                    <Label>Weight (kg)</Label>
                    <RangeSlider
                      min={WEIGHT_MIN}
                      max={WEIGHT_MAX}
                      step={1}
                      value={step1Form.watch("weight")}
                      onChange={(e) => step1Form.setValue("weight", e.target.value, { shouldValidate: true })}
                      labelFormatter={(v) => `${v} kg`}
                    />
                  </div>
                  <div className="space-y-2">
                    <Label>City</Label>
                    <SelectDropdown
                      value={step1Form.watch("city")}
                      onChange={(v) => step1Form.setValue("city", v, { shouldValidate: true })}
                      options={CITY_OPTIONS}
                      placeholder="Select city"
                    />
                  </div>
                </div>
                <div className="space-y-2">
                  <Label>Occupation</Label>
                  <SelectDropdown
                    value={step1Form.watch("occupation")}
                    onChange={(v) => step1Form.setValue("occupation", v, { shouldValidate: true })}
                    options={OCCUPATION_OPTIONS}
                    placeholder="Select occupation"
                  />
                </div>
                <div className="space-y-2">
                  <Label>Lifestyle type</Label>
                  <RadioGroupField
                    value={step1Form.watch("lifestyle_type")}
                    onChange={(v) =>
                      step1Form.setValue("lifestyle_type", v as Step1Data["lifestyle_type"], {
                        shouldValidate: true,
                      })
                    }
                    options={LIFESTYLE_OPTIONS.map((o) => ({ value: o.value, label: o.label }))}
                  />
                </div>
                <div className="space-y-2">
                  <Label>Language</Label>
                  <SelectDropdown
                    value={step1Form.watch("language")}
                    onChange={(v) => step1Form.setValue("language", v, { shouldValidate: true })}
                    options={LANGUAGE_OPTIONS}
                    placeholder="Preferred language"
                  />
                </div>
                <div className="flex justify-end gap-2 pt-2 sm:pt-4">
                  <Button type="submit" disabled={loading}>Next</Button>
                </div>
              </form>
            )}

            {/* Step 2 */}
            {step === 2 && (
              <div className="space-y-6">
                <MultiSelect
                  options={conditions}
                  value={selectedConditions}
                  onChange={setSelectedConditions}
                  label="Select all that apply"
                  columns={2}
                />
                <div className="flex justify-between pt-2 sm:pt-4">
                  <Button type="button" variant="outline" onClick={() => setStep(1)}>Back</Button>
                  <Button type="button" onClick={saveStep2} disabled={loading}>Next</Button>
                </div>
              </div>
            )}

            {/* Step 3 */}
            {step === 3 && (
              <form onSubmit={step3Form.handleSubmit(saveStep3)} className="space-y-6">
                <div className="space-y-2">
                  <Label>Current medications</Label>
                  <textarea
                    className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...step3Form.register("current_medications")}
                    placeholder="List any current medications"
                  />
                </div>
                <div className="space-y-2">
                  <Label>Past surgeries</Label>
                  <textarea
                    className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...step3Form.register("past_surgeries")}
                    placeholder="Any past surgeries"
                  />
                </div>
                <div className="space-y-2">
                  <Label>Food allergies</Label>
                  <textarea
                    className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...step3Form.register("food_allergies")}
                    placeholder="Food allergies"
                  />
                </div>
                <div className="space-y-3">
                  <Label>Menstrual data (only if applicable)</Label>
                  {step1Form.watch("gender") === "female" ? (
                    <div className="space-y-4">
                      <div className="space-y-1">
                        <span className="text-xs text-muted-foreground">Cycle regularity</span>
                        <RadioGroupField
                          value={step3Form.watch("menstrual_cycle")}
                          onChange={(v) =>
                            step3Form.setValue("menstrual_cycle", v as Step3Data["menstrual_cycle"], {
                              shouldValidate: true,
                            })
                          }
                          options={[
                            { value: "regular", label: "Regular" },
                            { value: "irregular", label: "Irregular" },
                          ]}
                        />
                      </div>
                      <div className="space-y-1">
                        <span className="text-xs text-muted-foreground">Last period date</span>
                        <Input
                          type="date"
                          value={step3Form.watch("last_period_date")}
                          onChange={(e) =>
                            step3Form.setValue("last_period_date", e.target.value, {
                              shouldValidate: true,
                            })
                          }
                        />
                      </div>
                      <div className="space-y-1">
                        <span className="text-xs text-muted-foreground">PCOS history</span>
                        <RadioGroupField
                          value={step3Form.watch("pcos_history")}
                          onChange={(v) =>
                            step3Form.setValue("pcos_history", v as Step3Data["pcos_history"], {
                              shouldValidate: true,
                            })
                          }
                          options={[
                            { value: "yes", label: "Yes" },
                            { value: "no", label: "No" },
                          ]}
                        />
                      </div>
                    </div>
                  ) : (
                    <p className="text-xs sm:text-sm text-muted-foreground">
                      Menstrual questions are only shown when gender is set to Female.
                    </p>
                  )}
                </div>
                <div className="flex justify-between pt-2 sm:pt-4">
                  <Button type="button" variant="outline" onClick={() => setStep(2)}>Back</Button>
                  <Button type="submit" disabled={loading}>Next</Button>
                </div>
              </form>
            )}

            {/* Step 4 */}
            {step === 4 && (
              <form onSubmit={step4Form.handleSubmit(saveStep4)} className="space-y-6">
                <div className="space-y-2">
                  <Label>Diet type</Label>
                  <select
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...step4Form.register("diet_type")}
                  >
                    <option value="">Select</option>
                    <option value="veg">Veg</option>
                    <option value="nonveg">Non veg</option>
                    <option value="eggetarian">Eggetarian</option>
                  </select>
                </div>
                <div className="space-y-2">
                  <Label>Meal pattern</Label>
                  <select
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    {...step4Form.register("meal_pattern")}
                  >
                    <option value="">Select</option>
                    {MEAL_PATTERNS.map((o) => (
                      <option key={o.value} value={o.value}>{o.label}</option>
                    ))}
                  </select>
                </div>
                <div className="space-y-2">
                  <Label>Breakfast</Label>
                  <Input {...step4Form.register("breakfast")} placeholder="Typical breakfast" />
                </div>
                <div className="space-y-2">
                  <Label>Lunch</Label>
                  <Input {...step4Form.register("lunch")} placeholder="Typical lunch" />
                </div>
                <div className="space-y-2">
                  <Label>Dinner</Label>
                  <Input {...step4Form.register("dinner")} placeholder="Typical dinner" />
                </div>
                <div className="space-y-2">
                  <Label>Food cravings</Label>
                  <Input {...step4Form.register("food_cravings")} placeholder="e.g. sweets, fried" />
                </div>
                <div className="space-y-2">
                  <Label>Outside food frequency</Label>
                  <Input {...step4Form.register("outside_food_frequency")} placeholder="e.g. 2 times/week" />
                </div>
                <div className="flex justify-between pt-2 sm:pt-4">
                  <Button type="button" variant="outline" onClick={() => setStep(3)}>Back</Button>
                  <Button type="submit" disabled={loading}>Next</Button>
                </div>
              </form>
            )}

            {/* Step 5 */}
            {step === 5 && (
              <form onSubmit={step5Form.handleSubmit(saveStep5)} className="space-y-6">
                <div className="space-y-2">
                  <Label>Sleep (hours per night)</Label>
                  <Input type="number" step="0.5" {...step5Form.register("sleep_hours")} placeholder="7" />
                </div>
                <div className="space-y-2">
                  <Label>Stress level</Label>
                  <Input {...step5Form.register("stress_level")} placeholder="e.g. Low / Medium / High" />
                </div>
                <div className="space-y-2">
                  <Label>Water intake</Label>
                  <Input {...step5Form.register("water_intake")} placeholder="e.g. 2-3 L per day" />
                </div>
                <div className="space-y-2">
                  <Label>Physical activity level</Label>
                  <Input {...step5Form.register("physical_activity_level")} placeholder="e.g. Light / Moderate" />
                </div>
                <div className="flex justify-between pt-2 sm:pt-4">
                  <Button type="button" variant="outline" onClick={() => setStep(4)}>Back</Button>
                  <Button type="submit" disabled={loading}>Next</Button>
                </div>
              </form>
            )}

            {/* Step 6 */}
            {step === 6 && (
              <div className="space-y-6">
                <MultiSelect
                  options={goals}
                  value={selectedGoals}
                  onChange={setSelectedGoals}
                  label="Select your body goals"
                  columns={2}
                />
                <div className="flex justify-between pt-2 sm:pt-4">
                  <Button type="button" variant="outline" onClick={() => setStep(5)}>Back</Button>
                  <Button type="button" onClick={saveStep6} disabled={loading}>Next</Button>
                </div>
              </div>
            )}

            {/* Step 7 */}
            {step === 7 && (
              <div className="space-y-6">
                <p className="text-xs sm:text-sm text-muted-foreground">
                  Uploading reports is optional but helps your dietician personalise your plan better.
                  You can also skip now and add them later from your panel.
                </p>
                <div className="space-y-5 sm:space-y-6">
                  <FileUploader
                    type="blood_report"
                    label="Blood report"
                    value={files.blood_report}
                    onChange={(f) => setFiles((prev) => ({ ...prev, blood_report: f as File | null }))}
                  />
                  <FileUploader
                    type="medical_report"
                    label="Medical report"
                    value={files.medical_report}
                    onChange={(f) => setFiles((prev) => ({ ...prev, medical_report: f as File | null }))}
                  />
                  <FileUploader
                    type="body_photos"
                    label="Body photos"
                    multiple
                    value={files.body_photos.length ? files.body_photos : null}
                    onChange={(f) => setFiles((prev) => ({ ...prev, body_photos: f ? (Array.isArray(f) ? f : [f]) : [] }))}
                  />
                  <FileUploader
                    type="medication_prescription"
                    label="Medication / prescription"
                    value={files.medication_prescription}
                    onChange={(f) => setFiles((prev) => ({ ...prev, medication_prescription: f as File | null }))}
                  />
                </div>
                <div className="flex justify-between pt-4 sm:pt-6">
                  <Button type="button" variant="outline" onClick={() => setStep(6)}>Back</Button>
                  <Button type="button" onClick={saveStep7} disabled={loading}>
                    {loading ? "Uploading…" : "Complete"}
                  </Button>
                </div>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}

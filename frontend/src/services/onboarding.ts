/**
 * Onboarding API – health questionnaire after signup.
 */
import { api, apiRaw } from "@/lib/api";
import { paths } from "@/constants/api-paths";

export interface HealthProfilePayload {
  name?: string;
  age: number;
  gender?: string;
  height: number;
  weight: number;
  city?: string;
  occupation?: string;
  lifestyle_type?: "sedentary" | "moderate" | "active";
  language?: string;
}

export interface MedicalDataPayload {
  current_medications?: string;
  past_surgeries?: string;
  food_allergies?: string;
  menstrual_history?: string;
}

export interface DietPatternPayload {
  diet_type?: "veg" | "nonveg" | "eggetarian";
  meal_pattern?: string;
  breakfast?: string;
  lunch?: string;
  dinner?: string;
  food_cravings?: string;
  outside_food_frequency?: string;
}

export interface LifestylePayload {
  sleep_hours?: number;
  stress_level?: string;
  water_intake?: string;
  physical_activity_level?: string;
}

export const onboardingApi = {
  getProfile: () => apiRaw.get(paths.onboarding.profile),

  getProfileData: () =>
    api.get<{
      user?: { id: number; full_name?: string; email?: string };
      health_profile?: Record<string, unknown>;
      health_conditions?: { id: number; name: string }[];
      medical_data?: Record<string, unknown>;
      diet_pattern?: Record<string, unknown>;
      lifestyle_assessment?: Record<string, unknown>;
      body_goals?: { id: number; name: string }[];
      file_uploads?: Record<string, string | string[] | null>;
    }>(paths.onboarding.profile),

  /** Dietician only: get a student's health profile (same shape as getProfileData). Student must be in dietician's course. */
  getStudentProfileForDietician: (userId: number | string) =>
    api.get<{
      user?: { id: number; full_name?: string; email?: string };
      health_profile?: Record<string, unknown>;
      health_conditions?: { id: number; name: string }[];
      medical_data?: Record<string, unknown>;
      diet_pattern?: Record<string, unknown>;
      lifestyle_assessment?: Record<string, unknown>;
      body_goals?: { id: number; name: string }[];
      file_uploads?: Record<string, string | string[] | null>;
    }>(paths.panel.studentHealthProfile(userId)),

  getHealthConditions: () =>
    api.get<{ conditions: { id: number; name: string }[] }>(paths.onboarding.healthConditions),

  getBodyGoals: () =>
    api.get<{ goals: { id: number; name: string }[] }>(paths.onboarding.bodyGoalsList),

  saveHealthProfile: (payload: HealthProfilePayload) =>
    apiRaw.post(paths.onboarding.healthProfile, payload),

  saveHealthConditions: (conditions: number[]) =>
    apiRaw.post(paths.onboarding.healthConditions, { conditions }),

  saveMedicalData: (payload: MedicalDataPayload) =>
    apiRaw.post(paths.onboarding.medicalData, payload),

  saveDietPattern: (payload: DietPatternPayload) =>
    apiRaw.post(paths.onboarding.dietPattern, payload),

  saveLifestyle: (payload: LifestylePayload) =>
    apiRaw.post(paths.onboarding.lifestyle, payload),

  saveBodyGoals: (goals: number[]) =>
    apiRaw.post(paths.onboarding.bodyGoals, { goals }),

  uploadFiles: (formData: FormData) =>
    apiRaw.postFormData<{ files: Record<string, string | string[] | null> }>(paths.onboarding.uploadFiles, formData),
};

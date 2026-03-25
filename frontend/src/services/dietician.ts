/**
 * Dietician panel API: students, meetings, assignments, certificates.
 * All require teacher/dietician role (api.level-access:teacher).
 */
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";

export interface DieticianStudent {
  id: number;
  full_name: string;
  email: string | null;
  avatar: string | null;
  programs: string[];
}

export const dieticianStudentsService = {
  list: () =>
    api
      .get<{ students?: DieticianStudent[] }>(paths.panel.students)
      .then((d) => (d && typeof d === "object" && "students" in d ? (d as { students: DieticianStudent[] }).students : []) ?? []),
};

/** Health Care: course purchases with initial conversation status */
export interface HealthCareStudent {
  sale_id: number;
  user_id: number;
  full_name: string;
  email: string | null;
  avatar: string | null;
  purchased_at: number;
  purchased_at_formatted: string | null;
  intake_submitted?: boolean;
  intake_submitted_at_formatted?: string | null;
  initial_conversation_at: number | null;
  initial_conversation_done: boolean;
  content_unlocked: boolean;
  unlock_after_timestamp: number;
}

export interface HealthCareCourse {
  webinar_id: number;
  title: string;
  slug: string | null;
  students: HealthCareStudent[];
}

export const healthCareService = {
  list: () =>
    api
      .get<{ courses?: HealthCareCourse[] }>(paths.panel.healthCare)
      .then((d) => (d && typeof d === "object" && "courses" in d ? (d as { courses: HealthCareCourse[] }).courses : []) ?? []),
  markInitialConversation: (saleId: number | string) =>
    api.post<{ sale_id?: number; initial_conversation_at?: number }>(
      paths.panel.healthCareMarkConversation(saleId)
    ),
  /** Get full course intake form (questionnaire + file URLs) for a sale. Dietician must manage the course. */
  getIntake: (saleId: number | string) =>
    api.get<{
      sale_id?: number;
      webinar?: { id: number; title: string | null };
      student?: { id: number; full_name: string; email: string | null };
      intake?: Record<string, unknown> & {
        blood_reports_urls?: string[];
        body_measurements_url?: string | null;
        body_photos_urls?: string[];
      };
    }>(paths.panel.healthCareIntake(saleId)),
};

/** Meetings response: reservations (my booked) and requests (students who requested with me). */
export interface MeetingReservation {
  id?: number;
  day?: string;
  time?: string;
  status?: string;
  meeting?: { title?: string };
  user?: { full_name?: string };
  created_at?: number;
}

export interface DieticianMeetingsData {
  reservations?: { count?: number; meetings?: MeetingReservation[] };
  requests?: { count?: number; meetings?: MeetingReservation[] };
}

export const dieticianMeetingsService = {
  get: () =>
    api.get<DieticianMeetingsData>(paths.panel.meetings).then((d) => d ?? { reservations: { count: 0, meetings: [] }, requests: { count: 0, meetings: [] } }),
  finish: (reserveId: number | string) =>
    api.post<unknown>(paths.panel.meetingFinish(reserveId)),
};

/** Assignment with webinar and histories. */
export interface DieticianAssignment {
  id: number;
  title?: string;
  pass_grade?: number;
  webinar?: { id: number; title?: string };
  instructor_assignment_histories?: DieticianAssignmentHistory[];
}

export interface DieticianAssignmentHistory {
  id: number;
  student_id: number;
  status?: string;
  grade?: number;
  student?: { id: number; full_name?: string };
  used_attempts_count?: number;
  last_submission?: string;
}

export const dieticianAssignmentsService = {
  list: () =>
    api.get<{ assignments?: DieticianAssignment[]; pending_reviews_count?: number }>(paths.dieticianApi.assignments).then((d) => ({
      assignments: d?.assignments ?? [],
      pendingCount: d?.pending_reviews_count ?? 0,
    })),
  getStudents: (assignmentId: number | string) =>
    api
      .get<{ assignment_histories?: DieticianAssignmentHistory[] }>(
        paths.dieticianApi.assignmentStudents(assignmentId)
      )
      .then((d) => d?.assignment_histories ?? []),
  setGrade: (historyId: number | string, grade: number) =>
    api.post(paths.dieticianApi.setAssignmentGrade(historyId), { grade }),
};

export const dieticianCertificatesService = {
  created: () =>
    api.get<{ certificates?: unknown[] }>(paths.dieticianApi.certificatesCreated).then((d) => (d as { certificates?: unknown[] })?.certificates ?? []),
  students: () =>
    api.get<unknown[]>(paths.dieticianApi.certificatesStudents).then((d) => (Array.isArray(d) ? d : [])),
};

/** Recipe (dietician recommended meal – not a course) */
export interface Recipe {
  id: number;
  name: string;
  description?: string | null;
  ingredients?: string | null;
  calories?: number | null;
  protein?: number | null;
  carbs?: number | null;
  fats?: number | null;
  meal_type?: string | null;
  preparation_video?: string | null;
  instructions?: string | null;
  image?: string | null;
  status?: string;
}

export interface RecipeAssignment {
  id: number;
  student_id: number;
  recipe_id: number;
  assigned_by: number;
  assigned_for_date?: string | null;
  day_number?: number | null;
  meal_slot?: string | null;
  notes?: string | null;
  recipe?: Recipe;
  assignedByUser?: { id: number; name?: string };
}

export const dieticianRecipesService = {
  list: (params?: { status?: string; meal_type?: string }) =>
    api.get<{ recipes?: Recipe[] }>(paths.dieticianApi.recipes, params ? { params: params as Record<string, string> } : undefined).then((d) => (d && typeof d === "object" && "recipes" in d ? (d as { recipes: Recipe[] }).recipes : []) ?? []),
  get: (id: number | string) =>
    api.get<{ recipe?: Recipe }>(paths.dieticianApi.recipe(id)).then((d) => (d && typeof d === "object" && "recipe" in d ? (d as { recipe: Recipe }).recipe : null)),
  create: (data: Partial<Recipe> & { name: string }) =>
    api.post<{ recipe?: Recipe }>(paths.dieticianApi.recipes, data),
  update: (id: number | string, data: Partial<Recipe>) =>
    api.patch<{ recipe?: Recipe }>(paths.dieticianApi.recipe(id), data),
  delete: (id: number | string) =>
    api.delete(paths.dieticianApi.recipe(id)),
  getAssignmentsForStudent: (studentId: number | string, params?: { from_date?: string; to_date?: string }) =>
    api.get<{ assignments?: RecipeAssignment[] }>(paths.dieticianApi.studentRecipeAssignments(studentId), params ? { params: params as Record<string, string> } : undefined).then((d) => (d && typeof d === "object" && "assignments" in d ? (d as { assignments: RecipeAssignment[] }).assignments : []) ?? []),
  assignToStudent: (studentId: number | string, data: { recipe_id: number; assigned_for_date?: string; day_number?: number; meal_slot?: string; notes?: string }) =>
    api.post<{ assignment?: RecipeAssignment }>(paths.dieticianApi.assignRecipe(studentId), data),
  removeAssignment: (assignmentId: number | string) =>
    api.delete(paths.dieticianApi.removeRecipeAssignment(assignmentId)),
  /** Assign one recipe to multiple students at once */
  assignBulk: (recipeId: number | string, data: { student_ids: number[]; assigned_for_date?: string; day_number?: number; meal_slot?: string; notes?: string }) =>
    api.post<{ assignments?: RecipeAssignment[]; count?: number }>(paths.dieticianApi.assignRecipeBulk(recipeId), data),
};

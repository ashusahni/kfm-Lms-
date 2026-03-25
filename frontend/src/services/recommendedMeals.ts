/**
 * Recommended Meals / Diet Plan – recipes assigned by dietician to the current student.
 * Separate from courses; no drip logic.
 */
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type { RecommendedMealAssignment } from "@/types/api";

export interface RecommendedMealsResponse {
  title?: string;
  assignments?: RecommendedMealAssignment[];
}

export const recommendedMealsService = {
  list: (params?: { from_date?: string; to_date?: string }) =>
    api.get<RecommendedMealsResponse>(paths.panel.recommendedMeals, params ? { params: params as Record<string, string> } : undefined),

  get: (assignmentId: number | string) =>
    api.get<RecommendedMealAssignment & { recipe: RecommendedMealAssignment["recipe"] }>(
      paths.panel.recommendedMeal(assignmentId)
    ),
};

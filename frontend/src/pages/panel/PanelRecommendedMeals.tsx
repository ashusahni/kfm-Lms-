import { Link, useParams } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { Utensils, ChevronRight, Play, Flame, Beef, Wheat, Droplets } from "lucide-react";
import { recommendedMealsService } from "@/services/recommendedMeals";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";
import { format } from "date-fns";
import type { RecommendedMealAssignment } from "@/types/api";

export default function PanelRecommendedMeals() {
  const { assignmentId } = useParams<{ assignmentId?: string }>();
  const listQuery = useQuery({
    queryKey: ["panel-recommended-meals"],
    queryFn: () => recommendedMealsService.list(),
  });
  const detailQuery = useQuery({
    queryKey: ["panel-recommended-meal", assignmentId],
    queryFn: () => recommendedMealsService.get(assignmentId!),
    enabled: !!assignmentId,
  });

  const data = listQuery.data;
  const assignments: RecommendedMealAssignment[] = data?.assignments ?? [];
  const detail = assignmentId ? detailQuery.data : null;
  const isLoadingList = listQuery.isLoading;
  const isLoadingDetail = !!assignmentId && detailQuery.isLoading;

  if (assignmentId && isLoadingDetail && !detail) {
    return (
      <div className="space-y-6">
        <Skeleton className="h-8 w-48" />
        <Skeleton className="h-64 w-full" />
      </div>
    );
  }

  if (assignmentId && detail) {
    const recipe = detail.recipe;
    return (
      <div className="space-y-6">
        <Link to="/panel/recommended-meals">
          <Button variant="ghost" size="sm" className="gap-2">
            <ChevronRight className="rotate-180" size={16} />
            Back to Recommended Meals
          </Button>
        </Link>
        <Card>
          <CardHeader>
            <div className="flex items-center gap-2 text-muted-foreground text-sm">
              {detail.assigned_for_date && (
                <span>{format(new Date(detail.assigned_for_date), "EEE, MMM d, yyyy")}</span>
              )}
              {detail.day_number != null && <span>Day {detail.day_number}</span>}
              {(detail.meal_slot || recipe?.meal_type) && (
                <span className="capitalize">{detail.meal_slot ?? recipe?.meal_type}</span>
              )}
              {detail.assigned_by_name && (
                <span>Recommended by {detail.assigned_by_name}</span>
              )}
            </div>
            <CardTitle className="text-2xl">{recipe?.name ?? "Recipe"}</CardTitle>
            {detail.notes && (
              <p className="text-sm text-muted-foreground">{detail.notes}</p>
            )}
          </CardHeader>
          <CardContent className="space-y-6">
            {recipe?.description && (
              <p className="text-muted-foreground">{recipe.description}</p>
            )}
            <div className="flex flex-wrap gap-4 text-sm">
              {recipe?.calories != null && (
                <span className="flex items-center gap-1.5">
                  <Flame size={16} className="text-amber-500" />
                  {recipe.calories} cal
                </span>
              )}
              {recipe?.protein != null && (
                <span className="flex items-center gap-1.5">
                  <Beef size={16} className="text-red-400" />
                  {recipe.protein}g protein
                </span>
              )}
              {recipe?.carbs != null && (
                <span className="flex items-center gap-1.5">
                  <Wheat size={16} className="text-amber-600" />
                  {recipe.carbs}g carbs
                </span>
              )}
              {recipe?.fats != null && (
                <span className="flex items-center gap-1.5">
                  <Droplets size={16} className="text-blue-400" />
                  {recipe.fats}g fats
                </span>
              )}
            </div>
            {recipe?.ingredients && (
              <div>
                <h3 className="font-semibold mb-2">Ingredients</h3>
                <p className="text-muted-foreground whitespace-pre-wrap">{recipe.ingredients}</p>
              </div>
            )}
            {recipe?.instructions && (
              <div>
                <h3 className="font-semibold mb-2">Instructions</h3>
                <p className="text-muted-foreground whitespace-pre-wrap">{recipe.instructions}</p>
              </div>
            )}
            {recipe?.preparation_video && (
              <div>
                <h3 className="font-semibold mb-2">Video</h3>
                <a
                  href={recipe.preparation_video}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex items-center gap-2 text-primary hover:underline"
                >
                  <Play size={16} />
                  Watch preparation video
                </a>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    );
  }

  if (isLoadingList) {
    return (
      <div className="space-y-6">
        <Skeleton className="h-8 w-64" />
        <div className="grid gap-4 md:grid-cols-2">
          {[1, 2, 3].map((i) => (
            <Skeleton key={i} className="h-32" />
          ))}
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-display font-bold text-foreground">Recommended Meals</h1>
        <p className="text-muted-foreground mt-1">
          Meals and recipes recommended by your dietician. Separate from your course content.
        </p>
      </div>
      {assignments.length === 0 ? (
        <Card>
          <CardContent className="py-12 text-center">
            <Utensils size={48} className="mx-auto text-muted-foreground mb-4" />
            <p className="text-muted-foreground">
              No recommended meals yet. Your dietician can assign recipes for you here.
            </p>
          </CardContent>
        </Card>
      ) : (
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {assignments.map((a) => (
            <Link key={a.assignment_id} to={`/panel/recommended-meals/${a.assignment_id}`}>
              <Card className="h-full transition-colors hover:bg-muted/50">
                <CardHeader className="pb-2">
                  <div className="flex items-center gap-2 text-muted-foreground text-xs">
                    {a.assigned_for_date && (
                      <span>{format(new Date(a.assigned_for_date), "MMM d")}</span>
                    )}
                    {a.day_number != null && <span>Day {a.day_number}</span>}
                    {(a.meal_slot || a.recipe?.meal_type) && (
                      <span className="capitalize">{a.meal_slot ?? a.recipe?.meal_type}</span>
                    )}
                  </div>
                  <CardTitle className="text-lg">{a.recipe?.name ?? "Recipe"}</CardTitle>
                </CardHeader>
                <CardContent className="pt-0">
                  {a.recipe?.calories != null && (
                    <p className="text-sm text-muted-foreground flex items-center gap-1">
                      <Flame size={14} />
                      {a.recipe.calories} cal
                    </p>
                  )}
                  <span className="text-primary text-sm font-medium inline-flex items-center gap-1 mt-2">
                    View details
                    <ChevronRight size={14} />
                  </span>
                </CardContent>
              </Card>
            </Link>
          ))}
        </div>
      )}
    </div>
  );
}

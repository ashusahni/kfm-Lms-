import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import {
  Utensils,
  PlusCircle,
  Pencil,
  Trash2,
  UserPlus,
  Users,
  Flame,
  ChevronDown,
} from "lucide-react";
import { dieticianRecipesService, type Recipe, type RecipeAssignment } from "@/services/dietician";
import { dieticianStudentsService } from "@/services/dietician";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Checkbox } from "@/components/ui/checkbox";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { ScrollArea } from "@/components/ui/scroll-area";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { toast } from "sonner";
import { format } from "date-fns";

const MEAL_TYPES = [
  { value: "breakfast", label: "Breakfast" },
  { value: "lunch", label: "Lunch" },
  { value: "dinner", label: "Dinner" },
  { value: "snack", label: "Snack" },
];

const STATUS_OPTIONS = [
  { value: "active", label: "Active" },
  { value: "inactive", label: "Inactive" },
];

const emptyRecipe: Partial<Recipe> = {
  name: "",
  description: "",
  ingredients: "",
  calories: undefined,
  protein: undefined,
  carbs: undefined,
  fats: undefined,
  meal_type: "",
  preparation_video: "",
  instructions: "",
  image: "",
  status: "active",
};

function RecipeForm({
  recipe,
  onSubmit,
  onCancel,
  isSubmitting,
}: {
  recipe: Partial<Recipe>;
  onSubmit: (data: Partial<Recipe> & { name: string }) => void;
  onCancel: () => void;
  isSubmitting: boolean;
}) {
  const [form, setForm] = useState<Partial<Recipe> & { name: string }>({
    ...emptyRecipe,
    ...recipe,
    name: recipe.name ?? "",
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!form.name?.trim()) {
      toast.error("Recipe name is required.");
      return;
    }
    onSubmit(form);
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <div className="grid gap-4 sm:grid-cols-2">
        <div className="space-y-2 sm:col-span-2">
          <Label htmlFor="name">Recipe name *</Label>
          <Input
            id="name"
            value={form.name}
            onChange={(e) => setForm((p) => ({ ...p, name: e.target.value }))}
            placeholder="e.g. Oats Breakfast"
            required
          />
        </div>
        <div className="space-y-2 sm:col-span-2">
          <Label htmlFor="description">Description</Label>
          <Textarea
            id="description"
            value={form.description ?? ""}
            onChange={(e) => setForm((p) => ({ ...p, description: e.target.value }))}
            placeholder="Short description of the meal"
            rows={2}
          />
        </div>
        <div className="space-y-2">
          <Label htmlFor="meal_type">Meal type</Label>
          <Select
            value={form.meal_type ?? ""}
            onValueChange={(v) => setForm((p) => ({ ...p, meal_type: v || undefined }))}
          >
            <SelectTrigger id="meal_type">
              <SelectValue placeholder="Select" />
            </SelectTrigger>
            <SelectContent>
              {MEAL_TYPES.map((m) => (
                <SelectItem key={m.value} value={m.value}>
                  {m.label}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
        <div className="space-y-2">
          <Label htmlFor="status">Status</Label>
          <Select
            value={form.status ?? "active"}
            onValueChange={(v) => setForm((p) => ({ ...p, status: v as "active" | "inactive" }))}
          >
            <SelectTrigger id="status">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              {STATUS_OPTIONS.map((s) => (
                <SelectItem key={s.value} value={s.value}>
                  {s.label}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
        <div className="space-y-2">
          <Label htmlFor="calories">Calories</Label>
          <Input
            id="calories"
            type="number"
            min={0}
            step={1}
            value={form.calories ?? ""}
            onChange={(e) => setForm((p) => ({ ...p, calories: e.target.value ? Number(e.target.value) : undefined }))}
            placeholder="e.g. 350"
          />
        </div>
        <div className="space-y-2">
          <Label htmlFor="protein">Protein (g)</Label>
          <Input
            id="protein"
            type="number"
            min={0}
            step={0.1}
            value={form.protein ?? ""}
            onChange={(e) => setForm((p) => ({ ...p, protein: e.target.value ? Number(e.target.value) : undefined }))}
            placeholder="e.g. 12"
          />
        </div>
        <div className="space-y-2">
          <Label htmlFor="carbs">Carbs (g)</Label>
          <Input
            id="carbs"
            type="number"
            min={0}
            step={0.1}
            value={form.carbs ?? ""}
            onChange={(e) => setForm((p) => ({ ...p, carbs: e.target.value ? Number(e.target.value) : undefined }))}
            placeholder="e.g. 55"
          />
        </div>
        <div className="space-y-2">
          <Label htmlFor="fats">Fats (g)</Label>
          <Input
            id="fats"
            type="number"
            min={0}
            step={0.1}
            value={form.fats ?? ""}
            onChange={(e) => setForm((p) => ({ ...p, fats: e.target.value ? Number(e.target.value) : undefined }))}
            placeholder="e.g. 8"
          />
        </div>
        <div className="space-y-2 sm:col-span-2">
          <Label htmlFor="ingredients">Ingredients</Label>
          <Textarea
            id="ingredients"
            value={form.ingredients ?? ""}
            onChange={(e) => setForm((p) => ({ ...p, ingredients: e.target.value }))}
            placeholder="One per line or comma-separated"
            rows={3}
          />
        </div>
        <div className="space-y-2 sm:col-span-2">
          <Label htmlFor="instructions">Instructions</Label>
          <Textarea
            id="instructions"
            value={form.instructions ?? ""}
            onChange={(e) => setForm((p) => ({ ...p, instructions: e.target.value }))}
            placeholder="Step-by-step cooking instructions"
            rows={4}
          />
        </div>
        <div className="space-y-2 sm:col-span-2">
          <Label htmlFor="preparation_video">Preparation video URL</Label>
          <Input
            id="preparation_video"
            value={form.preparation_video ?? ""}
            onChange={(e) => setForm((p) => ({ ...p, preparation_video: e.target.value }))}
            placeholder="https://..."
          />
        </div>
        <div className="space-y-2 sm:col-span-2">
          <Label htmlFor="image">Image URL</Label>
          <Input
            id="image"
            value={form.image ?? ""}
            onChange={(e) => setForm((p) => ({ ...p, image: e.target.value }))}
            placeholder="https://..."
          />
        </div>
      </div>
      <DialogFooter>
        <Button type="button" variant="outline" onClick={onCancel}>
          Cancel
        </Button>
        <Button type="submit" disabled={isSubmitting}>
          {isSubmitting ? "Saving…" : recipe.id ? "Update recipe" : "Create recipe"}
        </Button>
      </DialogFooter>
    </form>
  );
}

export default function DieticianRecipes() {
  const queryClient = useQueryClient();
  const [createOpen, setCreateOpen] = useState(false);
  const [editRecipe, setEditRecipe] = useState<Recipe | null>(null);
  const [deleteRecipe, setDeleteRecipe] = useState<Recipe | null>(null);
  const [assignBulkOpen, setAssignBulkOpen] = useState(false);
  const [assignTab, setAssignTab] = useState<"recipes" | "assign">("recipes");

  const [selectedStudentId, setSelectedStudentId] = useState<string>("");
  const [selectedRecipeId, setSelectedRecipeId] = useState<string>("");
  const [assignDate, setAssignDate] = useState(format(new Date(), "yyyy-MM-dd"));
  const [assignDayNumber, setAssignDayNumber] = useState("");
  const [assignMealSlot, setAssignMealSlot] = useState("");
  const [assignNotes, setAssignNotes] = useState("");

  const [bulkRecipeId, setBulkRecipeId] = useState<string>("");
  const [bulkStudentIds, setBulkStudentIds] = useState<number[]>([]);
  const [bulkDate, setBulkDate] = useState(format(new Date(), "yyyy-MM-dd"));
  const [bulkDayNumber, setBulkDayNumber] = useState("");
  const [bulkMealSlot, setBulkMealSlot] = useState("");
  const [bulkNotes, setBulkNotes] = useState("");

  const [assignmentsForStudent, setAssignmentsForStudent] = useState<RecipeAssignment[]>([]);
  const [studentIdForAssignments, setStudentIdForAssignments] = useState<string>("");

  const recipesQuery = useQuery({
    queryKey: ["dietician-recipes"],
    queryFn: () => dieticianRecipesService.list(),
  });
  const studentsQuery = useQuery({
    queryKey: ["dietician-students"],
    queryFn: () => dieticianStudentsService.list(),
  });

  const recipes: Recipe[] = recipesQuery.data ?? [];
  const students = Array.isArray(studentsQuery.data) ? studentsQuery.data : [];

  const createMutation = useMutation({
    mutationFn: (data: Partial<Recipe> & { name: string }) => dieticianRecipesService.create(data),
    onSuccess: () => {
      toast.success("Recipe created.");
      setCreateOpen(false);
      queryClient.invalidateQueries({ queryKey: ["dietician-recipes"] });
    },
    onError: (e) => toast.error(e instanceof Error ? e.message : "Failed to create"),
  });

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: Partial<Recipe> }) =>
      dieticianRecipesService.update(id, data),
    onSuccess: () => {
      toast.success("Recipe updated.");
      setEditRecipe(null);
      queryClient.invalidateQueries({ queryKey: ["dietician-recipes"] });
    },
    onError: (e) => toast.error(e instanceof Error ? e.message : "Failed to update"),
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => dieticianRecipesService.delete(id),
    onSuccess: () => {
      toast.success("Recipe deleted.");
      setDeleteRecipe(null);
      queryClient.invalidateQueries({ queryKey: ["dietician-recipes"] });
    },
    onError: (e) => toast.error(e instanceof Error ? e.message : "Failed to delete"),
  });

  const assignMutation = useMutation({
    mutationFn: (vars: {
      studentId: string;
      recipeId: number;
      assigned_for_date?: string;
      day_number?: number;
      meal_slot?: string;
      notes?: string;
    }) =>
      dieticianRecipesService.assignToStudent(vars.studentId, {
        recipe_id: vars.recipeId,
        assigned_for_date: vars.assigned_for_date,
        day_number: vars.day_number ? Number(vars.day_number) : undefined,
        meal_slot: vars.meal_slot || undefined,
        notes: vars.notes || undefined,
      }),
    onSuccess: (_, vars) => {
      toast.success("Recipe assigned to student.");
      setSelectedStudentId("");
      setSelectedRecipeId("");
      setAssignNotes("");
      queryClient.invalidateQueries({ queryKey: ["dietician-recipes"] });
      if (studentIdForAssignments === vars.studentId) {
        dieticianRecipesService.getAssignmentsForStudent(vars.studentId).then(setAssignmentsForStudent);
      }
    },
    onError: (e) => toast.error(e instanceof Error ? e.message : "Failed to assign"),
  });

  const bulkAssignMutation = useMutation({
    mutationFn: () =>
      dieticianRecipesService.assignBulk(Number(bulkRecipeId), {
        student_ids: bulkStudentIds,
        assigned_for_date: bulkDate || undefined,
        day_number: bulkDayNumber ? Number(bulkDayNumber) : undefined,
        meal_slot: bulkMealSlot || undefined,
        notes: bulkNotes || undefined,
      }),
    onSuccess: (data) => {
      toast.success(`Recipe assigned to ${data?.count ?? bulkStudentIds.length} student(s).`);
      setAssignBulkOpen(false);
      setBulkRecipeId("");
      setBulkStudentIds([]);
      setBulkNotes("");
      queryClient.invalidateQueries({ queryKey: ["dietician-recipes"] });
    },
    onError: (e) => toast.error(e instanceof Error ? e.message : "Failed to assign"),
  });

  const toggleBulkStudent = (id: number) => {
    setBulkStudentIds((prev) =>
      prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]
    );
  };

  const selectAllStudents = () => {
    const ids = students.map((s) => s.id);
    setBulkStudentIds(ids);
  };
  const clearAllStudents = () => setBulkStudentIds([]);

  const loadAssignments = () => {
    if (!studentIdForAssignments) return;
    dieticianRecipesService.getAssignmentsForStudent(studentIdForAssignments).then(setAssignmentsForStudent);
  };

  return (
    <div className="space-y-8">
      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 className="text-2xl font-display font-bold text-foreground">Recommended Meals (Recipes)</h1>
          <p className="text-muted-foreground mt-1">
            Create and manage recipes, then assign them to one or multiple students. Students see these under &quot;Recommended Meals&quot; in their panel.
          </p>
        </div>
        <Button onClick={() => setCreateOpen(true)} className="gap-2 shrink-0">
          <PlusCircle size={18} />
          Create recipe
        </Button>
      </div>

      <Tabs value={assignTab} onValueChange={(v) => setAssignTab(v as "recipes" | "assign")} className="space-y-4">
        <TabsList className="grid w-full max-w-md grid-cols-2">
          <TabsTrigger value="recipes">Recipes</TabsTrigger>
          <TabsTrigger value="assign">Assign to students</TabsTrigger>
        </TabsList>

        <TabsContent value="recipes" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Utensils size={20} />
                All recipes
              </CardTitle>
              <p className="text-sm text-muted-foreground">
                Create, edit, or delete recipes. Use &quot;Assign&quot; to assign a recipe to one or multiple students.
              </p>
            </CardHeader>
            <CardContent>
              {recipesQuery.isLoading ? (
                <Skeleton className="h-32 w-full" />
              ) : recipes.length === 0 ? (
                <div className="rounded-lg border border-dashed border-border py-12 text-center">
                  <Utensils size={48} className="mx-auto text-muted-foreground mb-4" />
                  <p className="text-muted-foreground mb-4">No recipes yet.</p>
                  <Button onClick={() => setCreateOpen(true)} className="gap-2">
                    <PlusCircle size={18} />
                    Create your first recipe
                  </Button>
                </div>
              ) : (
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                  {recipes.map((r) => (
                    <Card key={r.id} className="overflow-hidden">
                      <CardContent className="p-4">
                        <div className="flex items-start justify-between gap-2">
                          <div className="min-w-0 flex-1">
                            <p className="font-semibold truncate">{r.name}</p>
                            {r.meal_type && (
                              <p className="text-xs text-muted-foreground capitalize">{r.meal_type}</p>
                            )}
                            <div className="mt-2 flex items-center gap-2 text-xs text-muted-foreground">
                              {r.calories != null && (
                                <span className="flex items-center gap-1">
                                  <Flame size={12} />
                                  {r.calories} cal
                                </span>
                              )}
                              {(r.protein != null || r.carbs != null || r.fats != null) && (
                                <span>
                                  P {r.protein ?? 0} / C {r.carbs ?? 0} / F {r.fats ?? 0}
                                </span>
                              )}
                            </div>
                          </div>
                          <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                              <Button variant="ghost" size="icon" className="h-8 w-8">
                                <ChevronDown size={16} />
                              </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                              <DropdownMenuItem onClick={() => setEditRecipe(r)}>
                                <Pencil size={14} className="mr-2" />
                                Edit
                              </DropdownMenuItem>
                              <DropdownMenuItem
                                onClick={() => {
                                  setSelectedRecipeId(String(r.id));
                                  setAssignTab("assign");
                                }}
                              >
                                <UserPlus size={14} className="mr-2" />
                                Assign to one student
                              </DropdownMenuItem>
                              <DropdownMenuItem
                                onClick={() => {
                                  setBulkRecipeId(String(r.id));
                                  setBulkStudentIds([]);
                                  setAssignBulkOpen(true);
                                }}
                              >
                                <Users size={14} className="mr-2" />
                                Assign to multiple students
                              </DropdownMenuItem>
                              <DropdownMenuItem
                                className="text-destructive focus:text-destructive"
                                onClick={() => setDeleteRecipe(r)}
                              >
                                <Trash2 size={14} className="mr-2" />
                                Delete
                              </DropdownMenuItem>
                            </DropdownMenuContent>
                          </DropdownMenu>
                        </div>
                      </CardContent>
                    </Card>
                  ))}
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="assign" className="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <UserPlus size={20} />
                Assign to one student
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label>Student</Label>
                  <Select value={selectedStudentId} onValueChange={setSelectedStudentId}>
                    <SelectTrigger>
                      <SelectValue placeholder="Select student" />
                    </SelectTrigger>
                    <SelectContent>
                      {students.map((s) => (
                        <SelectItem key={s.id} value={String(s.id)}>
                          {s.full_name} {s.email ? `(${s.email})` : ""}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
                  <Label>Recipe</Label>
                  <Select value={selectedRecipeId} onValueChange={setSelectedRecipeId}>
                    <SelectTrigger>
                      <SelectValue placeholder="Select recipe" />
                    </SelectTrigger>
                    <SelectContent>
                      {recipes.map((r) => (
                        <SelectItem key={r.id} value={String(r.id)}>
                          {r.name} {r.meal_type ? `(${r.meal_type})` : ""}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              </div>
              <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div className="space-y-2">
                  <Label>Date (optional)</Label>
                  <Input type="date" value={assignDate} onChange={(e) => setAssignDate(e.target.value)} />
                </div>
                <div className="space-y-2">
                  <Label>Day number (optional)</Label>
                  <Input
                    type="number"
                    min={1}
                    placeholder="e.g. 1"
                    value={assignDayNumber}
                    onChange={(e) => setAssignDayNumber(e.target.value)}
                  />
                </div>
                <div className="space-y-2">
                  <Label>Meal slot (optional)</Label>
                  <Select value={assignMealSlot} onValueChange={setAssignMealSlot}>
                    <SelectTrigger>
                      <SelectValue placeholder="Any" />
                    </SelectTrigger>
                    <SelectContent>
                      {MEAL_TYPES.map((s) => (
                        <SelectItem key={s.value} value={s.value}>
                          {s.label}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              </div>
              <div className="space-y-2">
                <Label>Notes (optional)</Label>
                <Input
                  placeholder="e.g. Day 1 breakfast"
                  value={assignNotes}
                  onChange={(e) => setAssignNotes(e.target.value)}
                />
              </div>
              <Button
                onClick={() => {
                  if (!selectedStudentId || !selectedRecipeId) {
                    toast.error("Select a student and a recipe.");
                    return;
                  }
                  assignMutation.mutate({
                    studentId: selectedStudentId,
                    recipeId: Number(selectedRecipeId),
                    assigned_for_date: assignDate || undefined,
                    day_number: assignDayNumber ? Number(assignDayNumber) : undefined,
                    meal_slot: assignMealSlot || undefined,
                    notes: assignNotes || undefined,
                  });
                }}
                disabled={assignMutation.isPending}
              >
                {assignMutation.isPending ? "Assigning…" : "Assign to student"}
              </Button>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Users size={20} />
                Assign to multiple students
              </CardTitle>
              <p className="text-sm text-muted-foreground">
                Select one recipe and one or more students. The same recipe will be assigned to all selected students with the same date/notes.
              </p>
            </CardHeader>
            <CardContent>
              <Button
                onClick={() => {
                  setBulkRecipeId(recipes[0] ? String(recipes[0].id) : "");
                  setBulkStudentIds([]);
                  setAssignBulkOpen(true);
                }}
                disabled={recipes.length === 0 || students.length === 0}
                variant="outline"
                className="gap-2"
              >
                <Users size={18} />
                Assign recipe to multiple students
              </Button>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>View student&apos;s assigned recipes</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex flex-wrap gap-2">
                <Select value={studentIdForAssignments} onValueChange={setStudentIdForAssignments}>
                  <SelectTrigger className="w-64">
                    <SelectValue placeholder="Select student" />
                  </SelectTrigger>
                  <SelectContent>
                    {students.map((s) => (
                      <SelectItem key={s.id} value={String(s.id)}>
                        {s.full_name}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                <Button variant="outline" onClick={loadAssignments} disabled={!studentIdForAssignments}>
                  Load
                </Button>
              </div>
              {assignmentsForStudent.length > 0 && (
                <ul className="space-y-2">
                  {assignmentsForStudent.map((a) => (
                    <li
                      key={a.id}
                      className="flex items-center justify-between rounded-lg border border-border p-3 text-sm"
                    >
                      <span>
                        {a.recipe?.name ?? "Recipe"} –
                        {a.assigned_for_date ? format(new Date(a.assigned_for_date), "MMM d") : ""}
                        {a.meal_slot ? ` (${a.meal_slot})` : ""}
                      </span>
                    </li>
                  ))}
                </ul>
              )}
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      {/* Create recipe dialog */}
      <Dialog open={createOpen} onOpenChange={setCreateOpen}>
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>Create recipe</DialogTitle>
            <DialogDescription>
              Add a new recipe. Students will see it when you assign it to them under Recommended Meals.
            </DialogDescription>
          </DialogHeader>
          <RecipeForm
            recipe={emptyRecipe}
            onSubmit={(data) => createMutation.mutate(data)}
            onCancel={() => setCreateOpen(false)}
            isSubmitting={createMutation.isPending}
          />
        </DialogContent>
      </Dialog>

      {/* Edit recipe dialog */}
      <Dialog open={!!editRecipe} onOpenChange={(open) => !open && setEditRecipe(null)}>
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>Edit recipe</DialogTitle>
            <DialogDescription>Update recipe details.</DialogDescription>
          </DialogHeader>
          {editRecipe && (
            <RecipeForm
              recipe={editRecipe}
              onSubmit={(data) => updateMutation.mutate({ id: editRecipe.id, data })}
              onCancel={() => setEditRecipe(null)}
              isSubmitting={updateMutation.isPending}
            />
          )}
        </DialogContent>
      </Dialog>

      {/* Assign to multiple students dialog */}
      <Dialog open={assignBulkOpen} onOpenChange={setAssignBulkOpen}>
        <DialogContent className="max-w-lg max-h-[90vh] flex flex-col">
          <DialogHeader>
            <DialogTitle>Assign recipe to multiple students</DialogTitle>
            <DialogDescription>
              Select the recipe and the students. The same date and notes will apply to all.
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4 flex-1 overflow-hidden flex flex-col min-h-0">
            <div className="space-y-2">
              <Label>Recipe</Label>
              <Select value={bulkRecipeId} onValueChange={setBulkRecipeId}>
                <SelectTrigger>
                  <SelectValue placeholder="Select recipe" />
                </SelectTrigger>
                <SelectContent>
                  {recipes.map((r) => (
                    <SelectItem key={r.id} value={String(r.id)}>
                      {r.name} {r.meal_type ? `(${r.meal_type})` : ""}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <div className="flex items-center justify-between">
                <Label>Students ({bulkStudentIds.length} selected)</Label>
                <div className="flex gap-1">
                  <Button type="button" variant="ghost" size="sm" onClick={selectAllStudents}>
                    Select all
                  </Button>
                  <Button type="button" variant="ghost" size="sm" onClick={clearAllStudents}>
                    Clear
                  </Button>
                </div>
              </div>
              <ScrollArea className="h-48 rounded-md border border-border p-2">
                <div className="space-y-2">
                  {students.map((s) => (
                    <label
                      key={s.id}
                      className="flex items-center gap-2 cursor-pointer rounded px-2 py-1.5 hover:bg-muted/50"
                    >
                      <Checkbox
                        checked={bulkStudentIds.includes(s.id)}
                        onCheckedChange={() => toggleBulkStudent(s.id)}
                      />
                      <span className="text-sm">
                        {s.full_name}
                        {s.email ? ` (${s.email})` : ""}
                      </span>
                    </label>
                  ))}
                </div>
              </ScrollArea>
            </div>
            <div className="grid gap-4 grid-cols-2">
              <div className="space-y-2">
                <Label>Date (optional)</Label>
                <Input type="date" value={bulkDate} onChange={(e) => setBulkDate(e.target.value)} />
              </div>
              <div className="space-y-2">
                <Label>Day number (optional)</Label>
                <Input
                  type="number"
                  min={1}
                  placeholder="e.g. 1"
                  value={bulkDayNumber}
                  onChange={(e) => setBulkDayNumber(e.target.value)}
                />
              </div>
            </div>
            <div className="space-y-2">
              <Label>Meal slot (optional)</Label>
              <Select value={bulkMealSlot} onValueChange={setBulkMealSlot}>
                <SelectTrigger>
                  <SelectValue placeholder="Any" />
                </SelectTrigger>
                <SelectContent>
                  {MEAL_TYPES.map((s) => (
                    <SelectItem key={s.value} value={s.value}>
                      {s.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <Label>Notes (optional)</Label>
              <Input
                placeholder="e.g. Week 1 plan"
                value={bulkNotes}
                onChange={(e) => setBulkNotes(e.target.value)}
              />
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setAssignBulkOpen(false)}>
              Cancel
            </Button>
            <Button
              onClick={() => {
                if (!bulkRecipeId || bulkStudentIds.length === 0) {
                  toast.error("Select a recipe and at least one student.");
                  return;
                }
                bulkAssignMutation.mutate();
              }}
              disabled={bulkAssignMutation.isPending}
            >
              {bulkAssignMutation.isPending ? "Assigning…" : `Assign to ${bulkStudentIds.length} student(s)`}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Delete confirmation */}
      <AlertDialog open={!!deleteRecipe} onOpenChange={(open) => !open && setDeleteRecipe(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Delete recipe?</AlertDialogTitle>
            <AlertDialogDescription>
              This will permanently delete &quot;{deleteRecipe?.name}&quot;. Existing assignments to students will also be removed. This action cannot be undone.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
              onClick={() => deleteRecipe && deleteMutation.mutate(deleteRecipe.id)}
            >
              {deleteMutation.isPending ? "Deleting…" : "Delete"}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
}

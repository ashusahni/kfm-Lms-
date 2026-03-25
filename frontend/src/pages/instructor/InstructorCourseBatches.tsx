import { useState, useEffect } from "react";
import { Link, useParams, useLocation } from "react-router-dom";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { batchesService, type BatchPayload } from "@/services/batches";
import { instructorCoursesService } from "@/services/batches";
import type { CourseBatch } from "@/types/api";
import {
  ArrowLeft,
  Plus,
  Pencil,
  Trash2,
  Calendar,
  Users,
  Layers,
  Loader2,
} from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { format } from "date-fns";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Sheet,
  SheetContent,
  SheetHeader,
  SheetTitle,
  SheetFooter,
} from "@/components/ui/sheet";
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
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { toast } from "sonner";

const BASE = "/dietician/my-courses";
const BATCH_QUERY_KEY = "instructor-batches";
const BATCH_STATUSES = ["draft", "open", "closed", "completed"] as const;

function formatTs(ts: number | null | undefined): string {
  if (ts == null) return "—";
  try {
    return format(new Date(ts * 1000), "MMM d, yyyy");
  } catch {
    return "—";
  }
}

function BatchForm({
  batch,
  webinarId,
  open,
  onOpenChange,
  onSuccess,
}: {
  batch: CourseBatch | null;
  webinarId: string;
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSuccess: () => void;
}) {
  const queryClient = useQueryClient();
  const isEdit = batch != null;
  const [name, setName] = useState("");
  const [code, setCode] = useState("");
  const [startDate, setStartDate] = useState("");
  const [endDate, setEndDate] = useState("");
  const [capacity, setCapacity] = useState("");
  const [status, setStatus] = useState("draft");
  const [sortOrder, setSortOrder] = useState("0");

  useEffect(() => {
    if (open) {
      setName(batch?.name ?? "");
      setCode(batch?.code ?? "");
      setStartDate(batch?.start_date ? String(batch.start_date) : "");
      setEndDate(batch?.end_date ? String(batch.end_date) : "");
      setCapacity(batch?.capacity != null ? String(batch.capacity) : "");
      setStatus(batch?.status ?? "draft");
      setSortOrder(batch?.sort_order != null ? String(batch.sort_order) : "0");
    }
  }, [open, batch]);

  const createMutation = useMutation({
    mutationFn: (payload: BatchPayload) =>
      batchesService.create(webinarId, payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: [BATCH_QUERY_KEY, webinarId] });
      onSuccess();
      onOpenChange(false);
      toast.success("Batch created.");
    },
    onError: (e) => {
      toast.error(e instanceof Error ? e.message : "Failed to create batch");
    },
  });

  const updateMutation = useMutation({
    mutationFn: (payload: BatchPayload) =>
      batchesService.update(webinarId, batch!.id, payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: [BATCH_QUERY_KEY, webinarId] });
      onSuccess();
      onOpenChange(false);
      toast.success("Batch updated.");
    },
    onError: (e) => {
      toast.error(e instanceof Error ? e.message : "Failed to update batch");
    },
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const payload: BatchPayload = {
      name: name.trim(),
      code: code.trim() || undefined,
      start_date: startDate ? parseInt(startDate, 10) : null,
      end_date: endDate ? parseInt(endDate, 10) : null,
      capacity: capacity ? parseInt(capacity, 10) : null,
      status,
      sort_order: parseInt(sortOrder, 10) || 0,
    };
    if (isEdit) updateMutation.mutate(payload);
    else createMutation.mutate(payload);
  };

  const loading = createMutation.isPending || updateMutation.isPending;

  return (
    <Sheet open={open} onOpenChange={onOpenChange}>
      <SheetContent side="right" className="w-full sm:max-w-md overflow-y-auto">
        <SheetHeader>
          <SheetTitle>{isEdit ? "Edit batch" : "New batch"}</SheetTitle>
        </SheetHeader>
        <form onSubmit={handleSubmit} className="mt-6 space-y-5">
          <div className="space-y-2">
            <Label htmlFor="batch-name">Name *</Label>
            <Input
              id="batch-name"
              value={name}
              onChange={(e) => setName(e.target.value)}
              placeholder="e.g. January 2025"
              required
            />
          </div>
          <div className="space-y-2">
            <Label htmlFor="batch-code">Code</Label>
            <Input
              id="batch-code"
              value={code}
              onChange={(e) => setCode(e.target.value)}
              placeholder="e.g. JAN2025"
            />
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div className="space-y-2">
              <Label htmlFor="batch-start">Start (Unix)</Label>
              <Input
                id="batch-start"
                type="number"
                value={startDate}
                onChange={(e) => setStartDate(e.target.value)}
                placeholder="Optional"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="batch-end">End (Unix)</Label>
              <Input
                id="batch-end"
                type="number"
                value={endDate}
                onChange={(e) => setEndDate(e.target.value)}
                placeholder="Optional"
              />
            </div>
          </div>
          <div className="space-y-2">
            <Label htmlFor="batch-capacity">Capacity</Label>
            <Input
              id="batch-capacity"
              type="number"
              min={0}
              value={capacity}
              onChange={(e) => setCapacity(e.target.value)}
              placeholder="Unlimited if empty"
            />
          </div>
          <div className="space-y-2">
            <Label>Status</Label>
            <Select value={status} onValueChange={setStatus}>
              <SelectTrigger>
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                {BATCH_STATUSES.map((s) => (
                  <SelectItem key={s} value={s}>
                    {s.charAt(0).toUpperCase() + s.slice(1)}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <div className="space-y-2">
            <Label htmlFor="batch-sort">Sort order</Label>
            <Input
              id="batch-sort"
              type="number"
              min={0}
              value={sortOrder}
              onChange={(e) => setSortOrder(e.target.value)}
            />
          </div>
          <SheetFooter className="gap-2 pt-4">
            <Button
              type="button"
              variant="outline"
              onClick={() => onOpenChange(false)}
            >
              Cancel
            </Button>
            <Button type="submit" disabled={loading}>
              {loading && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              {isEdit ? "Save" : "Create"}
            </Button>
          </SheetFooter>
        </form>
      </SheetContent>
    </Sheet>
  );
}

export default function InstructorCourseBatches() {
  const { webinarId } = useParams<{ webinarId: string }>();
  const location = useLocation();
  const queryClient = useQueryClient();
  const courseTitle =
    (location.state as { title?: string } | null)?.title ?? null;

  const { data: courses } = useQuery({
    queryKey: ["instructor-my-courses"],
    queryFn: () => instructorCoursesService.getMyClasses(),
    enabled: !courseTitle && !!webinarId,
  });
  const resolvedTitle =
    courseTitle ??
    (webinarId && Array.isArray(courses)
      ? courses.find((c) => String(c.id) === webinarId)?.title
      : null) ??
    "Program";

  const {
    data: batches,
    isLoading,
    error,
  } = useQuery({
    queryKey: [BATCH_QUERY_KEY, webinarId],
    queryFn: () => batchesService.list(webinarId!),
    enabled: !!webinarId,
  });

  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingBatch, setEditingBatch] = useState<CourseBatch | null>(null);
  const [deletingBatch, setDeletingBatch] = useState<CourseBatch | null>(null);

  const deleteMutation = useMutation({
    mutationFn: () =>
      batchesService.delete(webinarId!, deletingBatch!.id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: [BATCH_QUERY_KEY, webinarId] });
      setDeletingBatch(null);
      toast.success("Batch deleted.");
    },
    onError: (e) => {
      toast.error(e instanceof Error ? e.message : "Failed to delete batch");
    },
  });

  const list = Array.isArray(batches) ? batches : [];

  if (!webinarId) {
    return (
      <div className="space-y-6">
        <p className="text-muted-foreground">Missing program.</p>
        <Link to={BASE}>
          <Button variant="outline">Back to My programs</Button>
        </Link>
      </div>
    );
  }

  return (
    <div className="space-y-10">
      <motion.div
        initial={{ opacity: 0, y: 8 }}
        animate={{ opacity: 1, y: 0 }}
        className="relative rounded-2xl bg-gradient-to-br from-primary/15 via-primary/5 to-transparent dark:from-primary/20 dark:via-primary/10 p-6 sm:p-8 border border-primary/10"
      >
          <Link
          to={BASE}
          className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground mb-4"
        >
          <ArrowLeft className="h-4 w-4" />
          My programs
        </Link>
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div className="flex items-center gap-4">
            <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
              <Layers className="h-6 w-6" />
            </div>
            <div>
              <h1 className="text-2xl font-display font-bold tracking-tight text-foreground">
                {resolvedTitle}
              </h1>
              <p className="text-muted-foreground">Manage batches (cohorts)</p>
            </div>
          </div>
          <Button
            onClick={() => {
              setEditingBatch(null);
              setSheetOpen(true);
            }}
            className="shrink-0"
          >
            <Plus className="mr-2 h-4 w-4" />
            New batch
          </Button>
        </div>
      </motion.div>

      {error && (
        <Card className="border-destructive/50">
          <CardContent className="py-8 text-center text-muted-foreground">
            {(error as Error).message}
            <Link to={BASE} className="block mt-4">
              <Button variant="outline">Back to My programs</Button>
            </Link>
          </CardContent>
        </Card>
      )}

      {isLoading && (
        <div className="space-y-4">
          <Skeleton className="h-24 rounded-xl" />
          <Skeleton className="h-24 rounded-xl" />
          <Skeleton className="h-24 rounded-xl" />
        </div>
      )}

      {!isLoading && !error && list.length === 0 && (
        <Card className="border border-border overflow-hidden">
          <div className="px-6 py-16 text-center">
            <Layers className="mx-auto h-14 w-14 text-muted-foreground/60" />
            <p className="mt-4 font-medium text-foreground">No batches yet</p>
            <p className="mt-1 text-sm text-muted-foreground">
              Create a batch to offer this program in cohorts (e.g. by start date or capacity).
            </p>
            <Button
              className="mt-6"
              onClick={() => {
                setEditingBatch(null);
                setSheetOpen(true);
              }}
            >
              <Plus className="mr-2 h-4 w-4" />
              Create first batch
            </Button>
          </div>
        </Card>
      )}

      {!isLoading && !error && list.length > 0 && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          className="space-y-4"
        >
          <AnimatePresence mode="popLayout">
            {list.map((batch, i) => (
              <motion.div
                key={batch.id}
                layout
                initial={{ opacity: 0, y: 8 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0 }}
                transition={{ delay: i * 0.02 }}
              >
                <Card className="border border-border overflow-hidden transition-shadow hover:shadow-md">
                  <CardContent className="p-0">
                    <div className="flex flex-col sm:flex-row sm:items-stretch">
                      <div className="flex flex-1 flex-col p-5 sm:p-6">
                        <div className="flex flex-wrap items-center gap-2">
                          <h3 className="font-semibold text-foreground">
                            {batch.name}
                          </h3>
                          {batch.code && (
                            <Badge variant="secondary" className="font-mono text-xs">
                              {batch.code}
                            </Badge>
                          )}
                          <Badge
                            variant={
                              batch.status === "open"
                                ? "default"
                                : batch.status === "draft"
                                  ? "secondary"
                                  : "outline"
                            }
                          >
                            {batch.status}
                          </Badge>
                          {batch.is_open && (
                            <Badge variant="default" className="bg-emerald-600">
                              Open
                            </Badge>
                          )}
                        </div>
                        <div className="mt-3 flex flex-wrap gap-6 text-sm text-muted-foreground">
                          <span className="flex items-center gap-1.5">
                            <Calendar className="h-4 w-4" />
                            {formatTs(batch.start_date)} – {formatTs(batch.end_date)}
                          </span>
                          <span className="flex items-center gap-1.5">
                            <Users className="h-4 w-4" />
                            {batch.enrolled_count ?? 0}
                            {batch.capacity != null ? ` / ${batch.capacity}` : ""} enrolled
                          </span>
                        </div>
                      </div>
                      <div className="flex items-center gap-2 border-t sm:border-t-0 sm:border-l bg-muted/30 px-5 py-4 sm:px-4">
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => {
                            setEditingBatch(batch);
                            setSheetOpen(true);
                          }}
                        >
                          <Pencil className="h-4 w-4 sm:mr-1" />
                          <span className="hidden sm:inline">Edit</span>
                        </Button>
                        <Button
                          variant="outline"
                          size="sm"
                          className="text-destructive hover:text-destructive"
                          onClick={() => setDeletingBatch(batch)}
                          disabled={(batch.enrolled_count ?? 0) > 0}
                        >
                          <Trash2 className="h-4 w-4 sm:mr-1" />
                          <span className="hidden sm:inline">Delete</span>
                        </Button>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </motion.div>
            ))}
          </AnimatePresence>
        </motion.div>
      )}

      <BatchForm
        webinarId={webinarId}
        batch={editingBatch}
        open={sheetOpen}
        onOpenChange={setSheetOpen}
        onSuccess={() => setEditingBatch(null)}
      />

      <AlertDialog
        open={!!deletingBatch}
        onOpenChange={(open) => !open && setDeletingBatch(null)}
      >
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Delete batch</AlertDialogTitle>
            <AlertDialogDescription>
              Delete “{deletingBatch?.name}”? This cannot be undone. Batches with enrollments cannot be deleted.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={() => deletingBatch && deleteMutation.mutate()}
              className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
            >
              {deleteMutation.isPending ? (
                <Loader2 className="h-4 w-4 animate-spin" />
              ) : (
                "Delete"
              )}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
}

import { useState, useCallback, useEffect, useRef } from "react";
import { useParams, Link } from "react-router-dom";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import {
  CheckCircle,
  Circle,
  Video,
  FileText,
  Radio,
  ChevronDown,
  ChevronRight,
  Play,
  Trophy,
  ClipboardList,
  Lock,
} from "lucide-react";
import { useConfig } from "@/context/ConfigContext";
import { programsService } from "@/services/programs";
import { getApiBase } from "@/lib/api";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { toast } from "sonner";

interface ContentItem {
  type: string;
  id?: number;
  title?: string;
  auth_has_read?: boolean;
  can?: { view?: boolean };
  items?: ContentItem[];
  play_url?: string;
  file?: string;
  is_video?: boolean;
  description?: string;
  file_type?: string;
  /** Backend unlock state: false = locked (day/date/sequential/manual rule) */
  unlocked?: boolean;
  /** Message when locked: e.g. "Unlocks in 2 days", "Complete previous lesson to unlock" */
  unlock_message?: string | null;
  /** Unix timestamp when content unlocks (optional) */
  unlock_at?: number | null;
  /** If false, item is hidden from list (backend omits it) */
  visible?: boolean;
}

function ensurePlayUrl(item: ContentItem, slug?: string): string | null {
  if (item.play_url) return item.play_url;
  if (item.type === "file" && item.id && slug && item.is_video) {
    const base = getApiBase() || "";
    return `${base}/course/${slug}/file/${item.id}/play`;
  }
  return item.file || null;
}

export default function PanelCourseLearn() {
  const { id } = useParams<{ id: string }>();
  const { t } = useConfig();
  const queryClient = useQueryClient();
  const [selectedItem, setSelectedItem] = useState<{
    type: string;
    id: number;
    title?: string;
    item: ContentItem;
  } | null>(null);
  const [expandedChapters, setExpandedChapters] = useState<Set<number>>(new Set());
  const [showCongratsModal, setShowCongratsModal] = useState(false);
  const pendingCongratsRef = useRef(false);

  const { data: contentData, isLoading, error } = useQuery({
    queryKey: ["course-content", id],
    queryFn: () => programsService.getContent(id!),
    enabled: !!id,
  });

  const webinar = (contentData as { webinar?: { id: number; slug: string; title: string } })?.webinar;
  const contentLocked = (contentData as { content_locked?: boolean })?.content_locked === true;
  const sequentialModuleUnlock = (contentData as { sequential_module_unlock?: boolean })?.sequential_module_unlock === true;
  const intakeSubmitted = (contentData as { intake_submitted?: boolean })?.intake_submitted === true;
  const lockedMessage =
    (contentData as { locked_message?: string })?.locked_message ||
    "Course content does not open immediately. Submit the course intake form first; then content unlocks 48 hours after submission so your dietician can review your case and prepare your diet plan.";
  const contentList = Array.isArray(contentData)
    ? contentData
    : (contentData as { content?: ContentItem[] })?.content ?? [];

  const learningStatusMutation = useMutation({
    mutationFn: ({
      item,
      itemId,
      status,
    }: {
      item: "file_id" | "session_id" | "text_lesson_id";
      itemId: number;
      status: boolean;
    }) => programsService.setLearningStatus(id!, item, itemId, status),
    onSuccess: (_, variables) => {
      if (variables.status) pendingCongratsRef.current = true;
      queryClient.invalidateQueries({ queryKey: ["course-content", id] });
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : "Failed to update"),
  });

  const itemTypeToApi = (type: string): "file_id" | "session_id" | "text_lesson_id" => {
    if (type === "file") return "file_id";
    if (type === "session") return "session_id";
    return "text_lesson_id";
  };

  const toggleComplete = useCallback(
    (item: ContentItem) => {
      if (item.unlocked === false) return;
      const iid = item.id ?? (item as { file_id?: number }).file_id ?? (item as { session_id?: number }).session_id ?? (item as { text_lesson_id?: number }).text_lesson_id;
      if (iid == null) return;
      const type = item.type === "chapter" ? null : itemTypeToApi(item.type);
      if (!type) return;
      const isRead = !!item.auth_has_read;
      learningStatusMutation.mutate({ item: type, itemId: Number(iid), status: !isRead });
    },
    [learningStatusMutation]
  );

  const selectItem = (item: ContentItem) => {
    const iid = item.id ?? (item as { file_id?: number }).file_id ?? (item as { session_id?: number }).session_id ?? (item as { text_lesson_id?: number }).text_lesson_id;
    if (iid == null || item.type === "chapter") return;
    if (item.unlocked === false) {
      toast.error(item.unlock_message || "This content is locked. Complete the previous lesson or wait until it unlocks.");
      return;
    }
    setSelectedItem({
      type: item.type,
      id: Number(iid),
      title: item.title,
      item,
    });
  };

  const toggleChapter = (chapterId: number) => {
    setExpandedChapters((prev) => {
      const next = new Set(prev);
      if (next.has(chapterId)) next.delete(chapterId);
      else next.add(chapterId);
      return next;
    });
  };

  const extractItems = (items: ContentItem[]): ContentItem[] => {
    const out: ContentItem[] = [];
    for (const c of items) {
      if (c.type === "chapter") {
        out.push(c);
        if (c.items) out.push(...extractItems(c.items));
      } else {
        out.push(c);
      }
    }
    return out;
  };

  const allItems = extractItems(contentList);
  const completedCount = allItems.filter((i) => i.type !== "chapter" && i.auth_has_read).length;
  const totalCount = allItems.filter((i) => i.type !== "chapter").length;
  const progressPercent = totalCount > 0 ? Math.round((completedCount / totalCount) * 100) : 0;
  const isCourseComplete = totalCount > 0 && completedCount === totalCount;

  useEffect(() => {
    if (isCourseComplete && pendingCongratsRef.current) {
      setShowCongratsModal(true);
      pendingCongratsRef.current = false;
    }
  }, [isCourseComplete, completedCount, totalCount]);

  if (!id) return null;
  if (isLoading) {
    return (
      <div className="space-y-6">
        <Skeleton className="h-8 w-48" />
        <div className="grid lg:grid-cols-3 gap-6">
          <Skeleton className="h-96" />
          <Skeleton className="h-96 lg:col-span-2" />
        </div>
      </div>
    );
  }
  if (error || (!contentLocked && !contentList.length)) {
    return (
      <div className="text-center py-12">
        <p className="text-destructive">{(error as Error)?.message ?? "Course content not found."}</p>
        <Link to="/panel/programs">
          <Button variant="outline" className="mt-4">
            Back to My {t("courses")}
          </Button>
        </Link>
      </div>
    );
  }

  if (contentLocked) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-display font-bold text-foreground">
            {webinar?.title ?? `Course #${id}`}
          </h1>
        </div>
        <div className="bg-amber-500/10 border border-amber-500/30 rounded-xl p-6 text-center max-w-2xl mx-auto">
          <div className="text-amber-600 dark:text-amber-400 font-semibold mb-2">Content temporarily locked</div>
          <p className="text-muted-foreground text-sm mb-4">{lockedMessage}</p>
          <p className="text-muted-foreground text-xs mb-4">
            Content unlock delay is 48 hours after course form submission so your dietician can review your case and prepare your diet plan. Content also unlocks when your dietician marks the initial conversation complete.
          </p>
          {!intakeSubmitted && id && (
            <Link to={`/panel/learn/${id}/intake`}>
              <Button className="gap-2 mt-2">
                <ClipboardList size={18} />
                Submit course intake form
              </Button>
            </Link>
          )}
        </div>
        <div className="flex justify-center gap-2 flex-wrap">
          {id && (
            <Link to={`/panel/learn/${id}/intake`}>
              <Button variant="outline" className="gap-2">
                <ClipboardList size={18} />
                Course intake form
              </Button>
            </Link>
          )}
          <Link to="/panel/programs">
            <Button variant="outline">Back to My {t("courses")}</Button>
          </Link>
        </div>
      </div>
    );
  }

  const renderSidebarItem = (item: ContentItem, depth = 0) => {
    if (item.type === "chapter") {
      const chId = item.id ?? 0;
      const isExpanded = expandedChapters.has(chId) || expandedChapters.size === 0;
      const isLocked = sequentialModuleUnlock && item.unlocked === false;
      return (
        <div key={`ch-${chId}`} className="mb-1">
          <button
            type="button"
            onClick={() => toggleChapter(chId)}
            className={`flex items-center gap-2 w-full text-left py-2 px-3 rounded-lg hover:bg-muted font-medium ${isLocked ? "text-muted-foreground" : ""}`}
          >
            {isExpanded ? <ChevronDown size={16} /> : <ChevronRight size={16} />}
            {isLocked && <Lock size={14} className="shrink-0" />}
            {item.title ?? "Chapter"}
            {isLocked && (
              <span className="text-xs text-muted-foreground ml-1">(Complete previous module to unlock)</span>
            )}
          </button>
          {isExpanded && item.items && (
            <div className="ml-4 pl-2 border-l border-border">
              {item.items.map((sub) => renderSidebarItem(sub, depth + 1))}
            </div>
          )}
        </div>
      );
    }

    const iid = item.id ?? (item as { file_id?: number }).file_id ?? (item as { session_id?: number }).session_id ?? (item as { text_lesson_id?: number }).text_lesson_id;
    const isSelected = selectedItem?.id === iid && selectedItem?.type === item.type;
    const isLocked = item.unlocked === false;
    const Icon =
      item.type === "file"
        ? item.is_video
          ? Video
          : FileText
        : item.type === "session"
          ? Radio
          : FileText;

    return (
      <button
        key={`${item.type}-${iid}`}
        type="button"
        onClick={() => selectItem(item)}
        className={`flex items-center gap-2 w-full text-left py-2 px-3 rounded-lg text-sm transition-colors ${
          isLocked ? "opacity-60 cursor-not-allowed text-muted-foreground hover:bg-muted/50" : ""
        } ${isSelected ? "bg-primary/10 text-primary" : "hover:bg-muted"}`}
      >
        {isLocked ? (
          <Lock size={18} className="shrink-0 text-muted-foreground" />
        ) : item.auth_has_read ? (
          <CheckCircle size={18} className="text-primary shrink-0" />
        ) : (
          <Circle size={18} className="shrink-0 text-muted-foreground" />
        )}
        <Icon size={16} className="shrink-0" />
        <span className="truncate flex-1">{item.title ?? "Untitled"}</span>
        {isLocked && item.unlock_message && (
          <span className="text-xs text-muted-foreground shrink-0 ml-1" title={item.unlock_message}>
            (locked)
          </span>
        )}
      </button>
    );
  };

  const videoUrl = selectedItem
    ? ensurePlayUrl(selectedItem.item, webinar?.slug)
    : null;
  const isVideo = selectedItem?.item?.is_video && videoUrl;
  const isEmbed = selectedItem?.item?.file && (selectedItem.item.file.includes("iframe") || selectedItem.item.file.includes("youtube") || selectedItem.item.file.includes("vimeo"));

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-display font-bold text-foreground">
            {webinar?.title ?? `Course #${id}`}
          </h1>
          <div className="flex items-center gap-4 mt-2">
            <div className="flex items-center gap-2 text-sm text-muted-foreground">
              <span>{completedCount} / {totalCount} completed</span>
              <span className="font-medium text-primary">{progressPercent}%</span>
            </div>
            <div className="h-2 flex-1 max-w-xs rounded-full bg-muted overflow-hidden">
              <div
                className="h-full bg-primary transition-all"
                style={{ width: `${progressPercent}%` }}
              />
            </div>
          </div>
        </div>
        <div className="flex flex-wrap items-center gap-2">
          <Link to="/panel/programs">
            <Button variant="outline">Back to My {t("courses")}</Button>
          </Link>
          <Link to={`/panel/learn/${id}/intake`}>
            <Button variant="secondary" className="gap-2">
              <ClipboardList size={18} />
              Course intake form
            </Button>
          </Link>
        </div>
      </div>

      <div className="grid lg:grid-cols-3 gap-6">
        <aside className="lg:col-span-1 bg-card rounded-xl border border-border p-4 h-fit lg:sticky lg:top-4">
          <h2 className="font-semibold text-foreground mb-4">Curriculum</h2>
          <nav className="space-y-0">
            {contentList.map((c) => renderSidebarItem(c))}
          </nav>
        </aside>

        <main className="lg:col-span-2 space-y-6">
          {selectedItem ? (
            <>
              <div className="bg-card rounded-xl border border-border overflow-hidden">
                <div className="p-4 border-b border-border flex items-center justify-between">
                  <h2 className="font-semibold text-foreground">{selectedItem.title}</h2>
                  <Button
                    variant={selectedItem.item.auth_has_read ? "outline" : "default"}
                    size="sm"
                    onClick={() => toggleComplete(selectedItem.item)}
                    disabled={learningStatusMutation.isPending || selectedItem.item.unlocked === false}
                  >
                    {selectedItem.item.auth_has_read ? (
                      <>
                        <CheckCircle size={16} className="mr-1" />
                        Completed
                      </>
                    ) : (
                      <>
                        <Circle size={16} className="mr-1" />
                        Mark complete
                      </>
                    )}
                  </Button>
                </div>

                <div className="p-4">
                  {isVideo && (
                    <div className="aspect-video bg-black rounded-lg overflow-hidden">
                      <video
                        controls
                        className="w-full h-full"
                        src={videoUrl!}
                        onEnded={() => {
                          if (!selectedItem.item.auth_has_read) {
                            toggleComplete(selectedItem.item);
                          }
                        }}
                      >
                        Your browser does not support video.
                      </video>
                    </div>
                  )}
                  {isEmbed && selectedItem.item.file && (
                    <div className="space-y-2">
                      <div
                        className="aspect-video bg-black rounded-lg overflow-hidden [&>iframe]:w-full [&>iframe]:h-full"
                        dangerouslySetInnerHTML={{ __html: selectedItem.item.file }}
                      />
                      {!selectedItem.item.auth_has_read && (
                        <p className="text-sm text-muted-foreground">
                          When you&apos;ve finished watching, click &quot;Mark complete&quot; above.
                        </p>
                      )}
                    </div>
                  )}
                  {!isVideo && !isEmbed && selectedItem.item.description && (
                    <div className="prose prose-sm max-w-none dark:prose-invert">
                      <p className="whitespace-pre-wrap">{selectedItem.item.description}</p>
                    </div>
                  )}
                  {!isVideo && !isEmbed && !selectedItem.item.description && (
                    <p className="text-muted-foreground">
                      No content to display. Open this item from the curriculum.
                    </p>
                  )}
                </div>
              </div>
            </>
          ) : (
            <div className="bg-card rounded-xl border border-border p-12 text-center">
              <Play size={48} className="mx-auto text-muted-foreground mb-4" />
              <p className="text-muted-foreground">
                Select a lesson from the curriculum to start learning.
              </p>
            </div>
          )}
        </main>
      </div>

      <Dialog open={showCongratsModal} onOpenChange={setShowCongratsModal}>
        <DialogContent className="sm:max-w-md text-center">
          <DialogHeader>
            <div className="mx-auto mb-2 flex h-14 w-14 items-center justify-center rounded-full bg-primary/10">
              <Trophy className="h-8 w-8 text-primary" />
            </div>
            <DialogTitle className="text-xl">Course completed!</DialogTitle>
            <DialogDescription>
              Congratulations! You&apos;ve watched and completed all lessons in this program. Great work.
            </DialogDescription>
          </DialogHeader>
          <DialogFooter className="sm:justify-center">
            <Button onClick={() => setShowCongratsModal(false)}>
              Done
            </Button>
            <Link to="/panel/programs">
              <Button variant="outline">Back to My {t("courses")}</Button>
            </Link>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}

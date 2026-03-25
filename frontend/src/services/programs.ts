/**
 * Programs (courses) API – guest listing, details, panel purchases, cart.
 */
import { api, apiRaw } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type {
  WebinarBrief,
  CourseDetails,
  ChapterBrief,
  ContentItemBrief,
  PaginatedData,
  AssignmentBrief,
  CategoryItem,
} from "@/types/api";

export const programsService = {
  list: (params?: { cat?: number; search?: string; sort?: string; free?: number }) =>
    api.get<WebinarBrief[]>(
      paths.guest.courses,
      params ? { params: params as Record<string, string> } : undefined
    ),

  get: (id: number | string) =>
    api.get<CourseDetails>(paths.guest.course(id)),

  getContent: (id: number | string) =>
    api.get<{ webinar?: { id: number; slug: string; title: string }; content?: unknown[] } | unknown[]>(
      paths.guest.courseContent(id)
    ),

  /** Mark course content item as watched/read (status: true) or not (status: false). */
  setLearningStatus: (
    webinarId: number | string,
    item: "file_id" | "session_id" | "text_lesson_id",
    itemId: number,
    status: boolean
  ) =>
    api.post<unknown>(paths.guest.learningStatusToggle(webinarId), {
      item,
      item_id: itemId,
      status,
    }),

  getFeatured: () =>
    api.get<WebinarBrief[]>(paths.guest.featuredCourses),

  getCategories: () =>
    api.get<CategoryItem[]>(paths.guest.categories),

  getCategoryCourses: (categoryId: number | string) =>
    api.get<WebinarBrief[]>(paths.guest.categoryWebinars(categoryId)),

  search: (params?: { q?: string; cat?: number }) =>
    api.get<WebinarBrief[]>(paths.guest.search, params ? { params: params as Record<string, string> } : undefined),

  getMyPrograms: () =>
    api.get<WebinarBrief[] | PaginatedData<WebinarBrief>>(
      paths.panel.webinars.purchases
    ),

  getMyProgram: (id: number | string) =>
    api.get<WebinarBrief>(paths.panel.webinar(id)),

  /** Enroll in a free program (panel). */
  enrollFree: (id: number | string) =>
    api.post<unknown>(paths.panel.webinars.free(id)),

  getChapters: (id: number | string) =>
    api.get<ChapterBrief[]>(paths.panel.webinarChapters(id)),

  getNoticeboards: (id: number | string) =>
    api.get<unknown[]>(paths.panel.webinarNoticeboards(id)),

  getMyAssignments: () =>
    api.get<AssignmentBrief[]>(paths.panel.assignments),

  getAssignment: (id: number | string) =>
    api.get<AssignmentBrief>(paths.panel.assignment(id)),

  /** Course intake form (unlocked after purchase). */
  getIntake: (webinarId: number | string) =>
    api.get<{ intake?: Record<string, unknown>; webinar?: { id: number; title: string } }>(
      paths.panel.webinarIntake(webinarId)
    ),

  saveIntake: (webinarId: number | string, data: Record<string, unknown>) =>
    api.post<{ intake: Record<string, unknown> }>(paths.panel.webinarIntake(webinarId), data),

  uploadIntakeFiles: (webinarId: number | string, formData: FormData) =>
    apiRaw.postFormData<{ files: Record<string, string | string[]> }>(
      paths.panel.webinarIntakeUpload(webinarId),
      formData
    ),
};

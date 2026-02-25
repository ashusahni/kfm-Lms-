/**
 * Programs (courses) API – guest listing, details, panel purchases, cart.
 */
import { api } from "@/lib/api";
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
    api.get<{ chapters?: ChapterBrief[]; items?: ContentItemBrief[] } | unknown[]>(
      paths.guest.courseContent(id)
    ),

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
};

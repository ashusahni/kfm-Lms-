/**
 * Dietician course batches API (panel).
 * Backend role is teacher; UI is rebranded as Dietician.
 */
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type { CourseBatch, WebinarBrief } from "@/types/api";

/** GET panel/classes returns { my_classes } (unwrapped from apiResponse2 data). */
export const dieticianCoursesService = {
  getMyClasses: () =>
    api
      .get<{ my_classes?: WebinarBrief[] }>(paths.panel.classes)
      .then((res) => res?.my_classes ?? []),
};

/** @deprecated Use dieticianCoursesService */
export const instructorCoursesService = dieticianCoursesService;

export const batchesService = {
  list: (webinarId: number | string) =>
    api
      .get<{ batches?: CourseBatch[] }>(paths.panel.batches(webinarId))
      .then((res) => res?.batches ?? []),

  create: (webinarId: number | string, body: BatchPayload) =>
    api
      .post<{ batch?: CourseBatch }>(paths.panel.batches(webinarId), body)
      .then((res) => res?.batch),

  update: (webinarId: number | string, batchId: number | string, body: BatchPayload) =>
    api
      .put<{ batch?: CourseBatch }>(paths.panel.batch(webinarId, batchId), body)
      .then((res) => res?.batch),

  delete: (webinarId: number | string, batchId: number | string) =>
    api.delete(paths.panel.batch(webinarId, batchId)),
};

export interface BatchPayload {
  name: string;
  code?: string;
  start_date?: number | null;
  end_date?: number | null;
  capacity?: number | null;
  status: string;
  sort_order?: number;
}

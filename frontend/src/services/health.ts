/**
 * Daily health logging API – list, get, create/update.
 */
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type {
  HealthLog,
  HealthLogCreatePayload,
  PaginatedData,
  CourseHealthLogSetting,
} from "@/types/api";

function buildParams(params?: {
  from_date?: string;
  to_date?: string;
  webinar_id?: number;
  per_page?: number;
  page?: number;
}): Record<string, string> | undefined {
  if (!params) return undefined;
  const p: Record<string, string> = {};
  if (params.from_date) p.from_date = params.from_date;
  if (params.to_date) p.to_date = params.to_date;
  if (params.webinar_id != null) p.webinar_id = String(params.webinar_id);
  if (params.per_page != null) p.per_page = String(params.per_page);
  if (params.page != null) p.page = String(params.page);
  return Object.keys(p).length ? p : undefined;
}

export const healthService = {
  list: (params?: {
    from_date?: string;
    to_date?: string;
    webinar_id?: number;
    per_page?: number;
    page?: number;
  }) =>
    api.get<PaginatedData<HealthLog>>(paths.panel.healthLogs, {
      params: buildParams(params),
    }),

  get: (id: number | string) =>
    api.get<HealthLog>(paths.panel.healthLog(id)),

  save: (payload: HealthLogCreatePayload) =>
    api.post<HealthLog>(paths.panel.healthLogs, payload),

  getCourseSetting: (webinarId: number | string) =>
    api.get<CourseHealthLogSetting>(paths.panel.courseHealthLogSetting(webinarId)),
};

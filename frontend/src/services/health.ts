/**
 * Daily health logging API – list, get, create/update.
 * Contract: HEALTH_LOG_SPEC.md (repo root). Any new field/endpoint must be added in both backend and frontend and the spec updated.
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
  user_id?: number;
  per_page?: number;
  page?: number;
}): Record<string, string> | undefined {
  if (!params) return undefined;
  const p: Record<string, string> = {};
  if (params.from_date) p.from_date = params.from_date;
  if (params.to_date) p.to_date = params.to_date;
  if (params.webinar_id != null) p.webinar_id = String(params.webinar_id);
  if (params.user_id != null) p.user_id = String(params.user_id);
  if (params.per_page != null) p.per_page = String(params.per_page);
  if (params.page != null) p.page = String(params.page);
  return Object.keys(p).length ? p : undefined;
}

export interface HealthLogSummaryData {
  total_entries: number;
  unique_days: number;
  avg_adherence_score: number | null;
  avg_water_ml: number | null;
  avg_calories: number | null;
}

export const healthService = {
  list: (params?: {
    from_date?: string;
    to_date?: string;
    webinar_id?: number;
    user_id?: number;
    per_page?: number;
    page?: number;
  }) =>
    api.get<PaginatedData<HealthLog>>(paths.panel.healthLogs, {
      params: buildParams(params),
    }),

  summary: (params?: {
    from_date?: string;
    to_date?: string;
    webinar_id?: number;
    user_id?: number;
  }) =>
    api.get<HealthLogSummaryData>(paths.panel.healthLogsSummary, {
      params: buildParams(params),
    }),

  get: (id: number | string) =>
    api.get<HealthLog>(paths.panel.healthLog(id)),

  save: (payload: HealthLogCreatePayload) =>
    api.post<HealthLog>(paths.panel.healthLogs, payload),

  getCourseSetting: (webinarId: number | string) =>
    api.get<CourseHealthLogSetting>(paths.panel.courseHealthLogSetting(webinarId)),
};

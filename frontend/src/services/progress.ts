/**
 * Progress & dashboard summary API – quick-info.
 */
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type { DashboardQuickInfo } from "@/types/api";

export const progressService = {
  getQuickInfo: () =>
    api.get<DashboardQuickInfo>(paths.panel.quickInfo),

  getMeetings: () =>
    api.get<unknown[]>(paths.panel.meetings),
};

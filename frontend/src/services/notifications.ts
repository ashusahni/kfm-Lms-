/**
 * Notifications API – list, mark as read.
 * Backend returns { count, notifications }.
 */
import { api, apiRaw } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type { NotificationItem } from "@/types/api";

export interface NotificationsListResponse {
  count: number;
  notifications: (NotificationItem & { status?: "read" | "unread" })[];
}

export const notificationsService = {
  list: () =>
    api.get<NotificationsListResponse>(paths.panel.notifications),

  markSeen: (id: number | string) =>
    apiRaw.post<unknown>(paths.panel.notificationSeen(id)),
};

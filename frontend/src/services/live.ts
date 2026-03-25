/**
 * Live sessions API – get session, join URL.
 */
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type { SessionBrief } from "@/types/api";

export const liveService = {
  getSession: (id: number | string) =>
    api.get<SessionBrief>(paths.panel.session(id)),
};

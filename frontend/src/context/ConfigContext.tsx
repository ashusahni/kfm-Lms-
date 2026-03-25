import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { api, apiRaw } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type { FitKarnatakaConfig, AppConfig } from "@/types/api";

interface ConfigState {
  fitKarnataka: FitKarnatakaConfig | null;
  appConfig: AppConfig | null;
  loading: boolean;
  error: string | null;
}

interface ConfigContextValue extends ConfigState {
  refetch: () => Promise<void>;
  /** Terminology from Fit Karnataka (e.g. "Programs" instead of "Courses") */
  t: (key: string) => string;
  /** Whether a feature is disabled by Fit Karnataka */
  isDisabled: (feature: string) => boolean;
  /** Whether Fit Karnataka mode is enabled */
  isFitKarnataka: boolean;
}

const defaultTerminology: Record<string, string> = {
  courses: "Programs",
  course: "Program",
  classes: "Classes",
  class: "Class",
  assignments: "Daily Challenges",
  assignment: "Daily Challenge",
  reviews: "Reviews",
  review: "Review",
  progress: "Adherence Progress",
  course_progress: "Program & Adherence Progress",
};

const ConfigContext = createContext<ConfigContextValue | null>(null);

export function ConfigProvider({ children }: { children: ReactNode }) {
  const [state, setState] = useState<ConfigState>({
    fitKarnataka: null,
    appConfig: null,
    loading: true,
    error: null,
  });

  const fetchConfig = useCallback(async () => {
    setState((s) => ({ ...s, loading: true, error: null }));
    try {
      // Both endpoints return raw JSON (no ApiResponse2 wrapper)
      const [fkRes, appRes] = await Promise.all([
        apiRaw.get<FitKarnatakaConfig>(paths.fitKarnatakaConfig).catch(() => ({
          enabled: false,
          disable: {},
          terminology: {},
        })),
        apiRaw.get<AppConfig | { success?: boolean; data?: AppConfig }>(paths.config).catch(() => null),
      ]);
      const fk: FitKarnatakaConfig =
        fkRes && typeof fkRes === "object" && "enabled" in fkRes
          ? (fkRes as FitKarnatakaConfig)
          : { enabled: false, disable: {}, terminology: {} };
      // Backend may return raw config or wrapped { success, data }
      const rawApp = appRes && typeof appRes === "object" ? appRes : null;
      const app: AppConfig | null = rawApp
        ? "data" in rawApp && rawApp.data && typeof rawApp.data === "object"
          ? (rawApp.data as AppConfig)
          : (rawApp as AppConfig)
        : null;
      setState({
        fitKarnataka: fk,
        appConfig: app,
        loading: false,
        error: null,
      });
    } catch (e) {
      setState((s) => ({
        ...s,
        loading: false,
        error: e instanceof Error ? e.message : "Failed to load config",
      }));
    }
  }, []);

  useEffect(() => {
    fetchConfig();
  }, [fetchConfig]);

  const t = useCallback(
    (key: string) => {
      const term = state.fitKarnataka?.terminology?.[key];
      return term ?? defaultTerminology[key] ?? key;
    },
    [state.fitKarnataka]
  );

  const isDisabled = useCallback(
    (feature: string) => {
      return state.fitKarnataka?.disable?.[feature] === true;
    },
    [state.fitKarnataka]
  );

  const value = useMemo<ConfigContextValue>(
    () => ({
      ...state,
      refetch: fetchConfig,
      t,
      isDisabled,
      isFitKarnataka: state.fitKarnataka?.enabled === true,
    }),
    [state, fetchConfig, t, isDisabled]
  );

  return (
    <ConfigContext.Provider value={value}>{children}</ConfigContext.Provider>
  );
}

export function useConfig(): ConfigContextValue {
  const ctx = useContext(ConfigContext);
  if (!ctx)
    throw new Error("useConfig must be used within ConfigProvider");
  return ctx;
}

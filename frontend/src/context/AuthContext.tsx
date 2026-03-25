import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { apiRaw, setAuthToken, getAuthToken, setAuthRole, getAuthRole, setAuthUserName, getAuthUserName, api } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type { LoginRequest, LoginData } from "@/types/api";

/** Role names from backend: user = student/client, teacher = dietician, organization = dietician */
export const ROLE_STUDENT = "user";
export const ROLE_INSTRUCTOR = "teacher";
export const ROLE_ORGANIZATION = "organization";
export const ROLE_ADMIN = "admin";

function isInstructorRole(role: string | null): boolean {
  return role === ROLE_INSTRUCTOR || role === ROLE_ORGANIZATION;
}

interface AuthState {
  token: string | null;
  userId: number | null;
  roleName: string | null;
  fullName: string | null;
  loading: boolean;
  initialized: boolean;
}

interface AuthContextValue extends AuthState {
  isAuthenticated: boolean;
  isInstructor: boolean;
  isStudent: boolean;
  fullName: string | null;
  login: (payload: LoginRequest) => Promise<{ ok: boolean; message?: string }>;
  logout: () => Promise<void>;
  registerStep: (step: number, body: Record<string, unknown>) => Promise<{ ok: boolean; message?: string; debugCode?: string }>;
}

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [state, setState] = useState<AuthState>({
    token: getAuthToken(),
    userId: null,
    roleName: getAuthRole(),
    fullName: getAuthUserName(),
    loading: false,
    initialized: false,
  });

  useEffect(() => {
    const token = getAuthToken();
    const role = getAuthRole();
    const fullName = getAuthUserName();
    if (token) {
      setState((s) => ({ ...s, token, roleName: role, fullName: fullName || s.fullName, initialized: true }));
    } else {
      setAuthRole(null);
      setAuthUserName(null);
      setState((s) => ({ ...s, roleName: null, fullName: null, initialized: true }));
    }
  }, []);

  // When user has token but no stored name (e.g. after refresh), fetch from quick-info
  useEffect(() => {
    if (!state.initialized || !state.token || state.fullName) return;
    let cancelled = false;
    api.get<{ full_name?: string }>(paths.panel.quickInfo).then((data) => {
      if (cancelled || !data?.full_name) return;
      setAuthUserName(data.full_name);
      setState((s) => ({ ...s, fullName: data.full_name }));
    }).catch(() => {});
    return () => { cancelled = true; };
  }, [state.initialized, state.token, state.fullName]);

  const login = useCallback(
    async (payload: LoginRequest): Promise<{ ok: boolean; message?: string }> => {
      setState((s) => ({ ...s, loading: true }));
      try {
        const res = await apiRaw.post<LoginData>(paths.auth.login, payload);
        if (res?.success && res?.data?.token) {
          const data = res.data as LoginData;
          setAuthToken(data.token);
          const role = data.role_name ?? ROLE_STUDENT;
          setAuthRole(role);
          const fullName = data.full_name ?? null;
          if (fullName) setAuthUserName(fullName);
          setState({
            token: data.token,
            userId: data.user_id ?? null,
            roleName: role,
            fullName: fullName ?? getAuthUserName(),
            loading: false,
            initialized: true,
          });
          return { ok: true };
        }
        return {
          ok: false,
          message: res?.message ?? "Login failed",
        };
      } catch (e) {
        const message =
          e instanceof Error ? e.message : "Login failed";
        setState((s) => ({ ...s, loading: false }));
        return { ok: false, message };
      }
    },
    []
  );

  const logout = useCallback(async () => {
    setState((s) => ({ ...s, loading: true }));
    try {
      await apiRaw.post(paths.auth.logout);
    } catch {
      // ignore
    } finally {
      setAuthToken(null);
      setAuthRole(null);
      setAuthUserName(null);
      setState({
        token: null,
        userId: null,
        roleName: null,
        fullName: null,
        loading: false,
        initialized: true,
      });
    }
  }, []);

  const registerStep = useCallback(
    async (
      step: number,
      body: Record<string, unknown>
    ): Promise<{ ok: boolean; message?: string; debugCode?: string }> => {
      setState((s) => ({ ...s, loading: true }));
      try {
        const res = await apiRaw.post<LoginData>(
          paths.auth.registerStep(step),
          body
        );
        if (res?.success && res?.data) {
          const data = res.data as LoginData & { user_id?: number };
          if (data.token) {
            setAuthToken(data.token);
            const role = (data as { role_name?: string }).role_name ?? ROLE_STUDENT;
            setAuthRole(role);
            setState({
              token: data.token,
              userId: data.user_id ?? null,
              roleName: role,
              loading: false,
              initialized: true,
            });
            return { ok: true };
          }
          // Step 1 returns user_id only; token comes after verification + step 3
          setState((s) => ({ ...s, loading: false }));
          const debugCode = (res.data as { debug_code?: string }).debug_code;
          return {
            ok: true,
            message: "Please verify your account (check email/SMS), then sign in.",
            debugCode: debugCode ?? undefined,
          };
        }
        return {
          ok: false,
          message: res?.message ?? "Registration failed",
        };
      } catch (e) {
        const message =
          e instanceof Error ? e.message : "Registration failed";
        setState((s) => ({ ...s, loading: false }));
        return { ok: false, message };
      }
    },
    []
  );

  const value = useMemo<AuthContextValue>(
    () => ({
      ...state,
      isAuthenticated: !!state.token,
      isInstructor: isInstructorRole(state.roleName),
      isStudent: state.roleName === ROLE_STUDENT || !state.roleName,
      fullName: state.fullName,
      login,
      logout,
      registerStep,
    }),
    [state, login, logout, registerStep]
  );

  return (
    <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
  );
}

export function useAuth(): AuthContextValue {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error("useAuth must be used within AuthProvider");
  return ctx;
}

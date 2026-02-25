import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import { apiRaw, setAuthToken, getAuthToken } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type { LoginRequest, LoginData } from "@/types/api";

interface AuthState {
  token: string | null;
  userId: number | null;
  loading: boolean;
  initialized: boolean;
}

interface AuthContextValue extends AuthState {
  isAuthenticated: boolean;
  login: (payload: LoginRequest) => Promise<{ ok: boolean; message?: string }>;
  logout: () => Promise<void>;
  registerStep: (step: number, body: Record<string, unknown>) => Promise<{ ok: boolean; message?: string }>;
}

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [state, setState] = useState<AuthState>({
    token: getAuthToken(),
    userId: null,
    loading: false,
    initialized: false,
  });

  useEffect(() => {
    const token = getAuthToken();
    if (token) {
      setState((s) => ({ ...s, token, initialized: true }));
    } else {
      setState((s) => ({ ...s, initialized: true }));
    }
  }, []);

  const login = useCallback(
    async (payload: LoginRequest): Promise<{ ok: boolean; message?: string }> => {
      setState((s) => ({ ...s, loading: true }));
      try {
        const res = await apiRaw.post<LoginData>(paths.auth.login, payload);
        if (res?.success && res?.data?.token) {
          const data = res.data as LoginData;
          setAuthToken(data.token);
          setState({
            token: data.token,
            userId: data.user_id ?? null,
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
      setState({
        token: null,
        userId: null,
        loading: false,
        initialized: true,
      });
    }
  }, []);

  const registerStep = useCallback(
    async (
      step: number,
      body: Record<string, unknown>
    ): Promise<{ ok: boolean; message?: string }> => {
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
            setState({
              token: data.token,
              userId: data.user_id ?? null,
              loading: false,
              initialized: true,
            });
            return { ok: true };
          }
          // Step 1 returns user_id only; token comes after verification + step 3
          setState((s) => ({ ...s, loading: false }));
          return {
            ok: true,
            message: "Please verify your account (check email/SMS), then sign in.",
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

/**
 * Auth API service – login, logout, forgot password, reset password, register.
 * JWT is stored and sent via lib/api (setAuthToken / getAuthToken).
 */
import { apiRaw } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type { LoginRequest, LoginData } from "@/types/api";

export const authService = {
  login: (payload: LoginRequest) =>
    apiRaw.post<LoginData>(paths.auth.login, payload),

  logout: () => apiRaw.post(paths.auth.logout),

  forgotPassword: (payload: { email?: string; mobile?: string; country_code?: string }) =>
    apiRaw.post<{ message?: string }>(paths.auth.forgetPassword, payload),

  resetPassword: (
    token: string,
    payload: { email: string; password: string; password_confirmation: string }
  ) =>
    apiRaw.post<unknown>(paths.auth.resetPassword(token), payload),

  registerStep: (step: number, body: Record<string, unknown>) =>
    apiRaw.post<LoginData>(paths.auth.registerStep(step), body),
};

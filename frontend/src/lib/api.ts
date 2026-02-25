/**
 * API client for the Rocket LMS backend.
 * Handles backend response format: { success, status, message, data? }.
 * JWT: set token via setAuthToken(); it is sent as Authorization: Bearer <token>.
 */
import type { ApiResponse2 } from "@/types/api";

const API_BASE =
  import.meta.env.VITE_API_URL !== undefined && import.meta.env.VITE_API_URL !== ""
    ? import.meta.env.VITE_API_URL.replace(/\/$/, "")
    : "";

const AUTH_TOKEN_KEY = "rocket_lms_token";
const AUTH_ROLE_KEY = "rocket_lms_role";

export const getApiBase = () => API_BASE;

export function getAuthToken(): string | null {
  return localStorage.getItem(AUTH_TOKEN_KEY);
}

export function setAuthToken(token: string | null): void {
  if (token === null) localStorage.removeItem(AUTH_TOKEN_KEY);
  else localStorage.setItem(AUTH_TOKEN_KEY, token);
}

export function getAuthRole(): string | null {
  return localStorage.getItem(AUTH_ROLE_KEY);
}

export function setAuthRole(role: string | null): void {
  if (role === null) localStorage.removeItem(AUTH_ROLE_KEY);
  else localStorage.setItem(AUTH_ROLE_KEY, role);
}

/**
 * Full URL for an API path. Path should start with /api.
 */
export const apiUrl = (path: string) => {
  const p = path.startsWith("/") ? path : `/${path}`;
  return `${API_BASE}${p}`;
};

export type RequestOptions = RequestInit & {
  params?: Record<string, string>;
};

/**
 * Build headers with optional Bearer token and API key.
 */
function buildHeaders(init?: RequestInit, token?: string | null): HeadersInit {
  const apiKey = import.meta.env.VITE_API_KEY;
  const headers: Record<string, string> = {
    "Content-Type": "application/json",
    Accept: "application/json",
    ...(apiKey ? { "x-api-key": apiKey } : {}),
    ...(init?.headers as Record<string, string>),
  };
  const t = token ?? getAuthToken();
  if (t) headers["Authorization"] = `Bearer ${t}`;
  return headers;
}

/**
 * Raw request – returns full JSON (e.g. ApiResponse2). Does not unwrap data.
 */
export async function requestRaw<T = unknown>(
  path: string,
  options: RequestOptions = {}
): Promise<T> {
  const { params, ...init } = options;
  let url = apiUrl(path);
  if (params && Object.keys(params).length > 0) {
    const search = new URLSearchParams(params).toString();
    url += (url.includes("?") ? "&" : "?") + search;
  }
  const res = await fetch(url, {
    ...init,
    headers: buildHeaders(init),
  });
  const text = await res.text();
  if (!res.ok) {
    let msg = res.statusText;
    try {
      const body = text ? JSON.parse(text) : {};
      if (typeof body.message === "string") msg = body.message;
    } catch {
      if (text) msg = text;
    }
    throw new Error(msg);
  }
  if (!text) return undefined as T;
  return JSON.parse(text) as T;
}

/**
 * Request that expects backend ApiResponse2. Returns data or throws with message.
 */
async function request<T = unknown>(
  path: string,
  options: RequestOptions = {}
): Promise<T> {
  const raw = await requestRaw<ApiResponse2<T>>(path, options);
  if (!raw) return undefined as T;
  if (raw.success && (raw as ApiResponse2<T>).data !== undefined)
    return (raw as ApiResponse2<T>).data as T;
  if (!raw.success)
    throw new Error(
      typeof (raw as ApiResponse2).message === "string"
        ? (raw as ApiResponse2).message
        : "Request failed"
    );
  return undefined as T;
}

/**
 * API methods. Use paths from @/constants/api-paths.
 * For authenticated calls, set token first with setAuthToken(token); these methods send it automatically.
 */
export const api = {
  get: <T = unknown>(path: string, options?: RequestOptions) =>
    request<T>(path, { ...options, method: "GET" }),
  post: <T = unknown>(path: string, body?: unknown, options?: RequestOptions) =>
    request<T>(path, {
      ...options,
      method: "POST",
      body: body !== undefined ? JSON.stringify(body) : undefined,
    }),
  put: <T = unknown>(path: string, body?: unknown, options?: RequestOptions) =>
    request<T>(path, {
      ...options,
      method: "PUT",
      body: body !== undefined ? JSON.stringify(body) : undefined,
    }),
  patch: <T = unknown>(path: string, body?: unknown, options?: RequestOptions) =>
    request<T>(path, {
      ...options,
      method: "PATCH",
      body: body !== undefined ? JSON.stringify(body) : undefined,
    }),
  delete: <T = unknown>(path: string, options?: RequestOptions) =>
    request<T>(path, { ...options, method: "DELETE" }),
};

/**
 * Raw API (returns full ApiResponse2). Use when you need success/status/message.
 */
export const apiRaw = {
  get: <T = unknown>(path: string, options?: RequestOptions) =>
    requestRaw<ApiResponse2<T>>(path, { ...options, method: "GET" }),
  post: <T = unknown>(path: string, body?: unknown, options?: RequestOptions) =>
    requestRaw<ApiResponse2<T>>(path, {
      ...options,
      method: "POST",
      body: body !== undefined ? JSON.stringify(body) : undefined,
    }),
};

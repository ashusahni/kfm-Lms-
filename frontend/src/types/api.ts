/**
 * Backend API response format (apiResponse2).
 */
export interface ApiResponse2<T = unknown> {
  success: boolean;
  status: string;
  message: string;
  data?: T;
  title?: string;
}

/**
 * Fit Karnataka config from GET /api/fit-karnataka-config
 */
export interface FitKarnatakaConfig {
  enabled: boolean;
  disable?: Record<string, boolean>;
  terminology?: Record<string, string>;
}

/**
 * App config from GET /api/development/config
 */
export interface AppConfig {
  register_method?: string;
  offline_bank_account?: string | null;
  user_language?: { id: number; title: string }[];
  payment_channels?: Record<string, unknown[]>;
  minimum_payout_amount?: number;
  currency?: { sign: string; name: string };
  price_display?: string;
  currency_position?: string;
}

/**
 * User brief (teacher/instructor)
 */
export interface UserBrief {
  id: number;
  full_name?: string;
  avatar?: string;
  headline?: string;
  rate?: number;
  students_count?: number;
}

/**
 * Webinar/course brief from API (list & details base)
 */
export interface WebinarBrief {
  id: number;
  title: string;
  image: string;
  link?: string;
  price?: number;
  price_string?: string | null;
  best_ticket_string?: string | null;
  best_ticket_price?: number;
  discount_percent?: number;
  duration?: number;
  teacher?: UserBrief;
  students_count?: number;
  rate?: number;
  reviews_count?: number;
  created_at?: string | number;
  start_date?: number | null;
  is_favorite?: boolean;
  auth_has_bought?: boolean | null;
  label?: string;
  type?: string;
  category?: string;
  auth?: boolean;
  can?: { view?: boolean };
  capacity?: number;
  progress?: number | string;
  progress_percent?: number;
}

/** Ticket / pricing option for a course */
export interface CourseTicket {
  id: number;
  title?: string;
  sub_title?: string;
  discount?: number;
  is_valid?: boolean;
}

/** Prerequisite course ref */
export interface CoursePrerequisite {
  required?: boolean;
  webinar?: WebinarBrief | null;
}

/** FAQ item */
export interface CourseFaq {
  id?: number;
  title?: string;
  answer?: string;
  order?: number;
}

/** Review on a course */
export interface CourseReview {
  user?: { full_name?: string; avatar?: string };
  create_at?: number;
  description?: string;
  rates?: number;
  replies?: unknown[];
}

/** Comment on a course */
export interface CourseComment {
  user?: { full_name?: string; avatar?: string };
  create_at?: number;
  comment?: string;
  replies?: unknown[];
}

/** Session/live class in curriculum */
export interface CourseSessionDetail {
  id: number;
  title?: string;
  description?: string;
  date?: number;
  duration?: number;
}

/** File/video in curriculum */
export interface CourseFileDetail {
  id: number;
  title?: string;
  duration?: number;
  order?: number;
}

/** Text lesson in curriculum */
export interface CourseTextLessonDetail {
  id: number;
  title?: string;
  order?: number;
}

/** Chapter with items in curriculum */
export interface CourseChapterDetail {
  id: number;
  title?: string;
  topics_count?: number;
  items?: unknown[];
}

/** Full course details from GET /courses/{id} */
export interface CourseDetails extends WebinarBrief {
  description?: string | null;
  video_demo?: string | null;
  video_demo_source?: string;
  image_cover?: string | null;
  support?: boolean;
  subscribe?: boolean;
  isDownloadable?: boolean;
  certificate?: unknown[];
  quizzes_count?: number;
  sessions_count?: number;
  files_count?: number;
  text_lessons_count?: number;
  sessions_without_chapter?: CourseSessionDetail[];
  session_chapters?: CourseChapterDetail[];
  files_without_chapter?: CourseFileDetail[];
  files_chapters?: CourseChapterDetail[];
  text_lessons_without_chapter?: CourseTextLessonDetail[];
  text_lesson_chapters?: CourseChapterDetail[];
  tickets?: CourseTicket[];
  prerequisites?: CoursePrerequisite[];
  faqs?: CourseFaq[];
  reviews?: CourseReview[];
  comments?: CourseComment[];
  tags?: { id: number; title: string }[];
  can_add_to_cart?: string | null;
  can_buy_with_points?: boolean;
  auth_has_subscription?: boolean | null;
  rate_type?: {
    content_quality?: number;
    instructor_skills?: number;
    purchase_worth?: number;
    support_quality?: number;
  };
}

/**
 * Category
 */
export interface CategoryItem {
  id: number;
  title: string;
  icon?: string;
  webinars_count?: number;
}

/**
 * Blog post brief
 */
export interface BlogBrief {
  id: number;
  title: string;
  image?: string;
  author?: UserBrief;
  category?: string;
  comment_count?: number;
  created_at?: string;
}

/**
 * Login request
 */
export interface LoginRequest {
  username: string;
  password: string;
}

/**
 * Login response data
 */
export interface LoginData {
  token: string;
  user_id: number;
  profile_completion?: string[];
}

/** Health log meal entry */
export interface HealthLogMeal {
  type?: string;
  name?: string;
  time?: string;
  calories?: number;
  notes?: string;
}

/** Daily health log (Fit Karnataka) */
export interface HealthLog {
  id: number;
  user_id: number;
  webinar_id: number | null;
  log_date: string;
  water_ml: number | null;
  meals: HealthLogMeal[] | null;
  calories: number | null;
  protein: number | null;
  carbs: number | null;
  fat: number | null;
  medicines: string | null;
  activity_minutes: number | null;
  activity_notes: string | null;
  adherence_score: number | null;
  locked_at: number | null;
  custom_data?: Record<string, string | number | null> | null;
  created_at?: number;
  updated_at?: number;
  user?: UserBrief;
  webinar?: { id: number; title: string };
}

export interface HealthLogCreatePayload {
  log_date: string;
  webinar_id?: number | null;
  water_ml?: number | null;
  meals?: HealthLogMeal[] | null;
  calories?: number | null;
  protein?: number | null;
  carbs?: number | null;
  fat?: number | null;
  medicines?: string | null;
  activity_minutes?: number | null;
  activity_notes?: string | null;
  adherence_score?: number | null;
  custom_data?: Record<string, string | number | null> | null;
}

/** Paginated list response from backend */
export interface PaginatedData<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

/** Panel quick-info / dashboard summary */
export interface DashboardQuickInfo {
  full_name?: string;
  role_name?: string;
  webinarsCount?: number;
  unread_notifications?: { count: number; notifications?: NotificationItem[] };
  unread_noticeboards?: unknown[];
  reserveMeetingsCount?: number;
  supportsCount?: number;
  commentsCount?: number;
  balance?: number;
  badges?: { next_badge?: string; percent?: number; earned?: string };
  [key: string]: unknown;
}

export interface NotificationItem {
  id: number;
  title?: string;
  message?: string;
  type?: string;
  created_at?: number;
  read_at?: number | null;
}

/** Assignment / Daily Challenge */
export interface AssignmentBrief {
  id: number;
  title?: string;
  description?: string;
  deadline?: number;
  status?: string;
  webinar?: { id: number; title: string };
}

/** Session / Live class */
export interface SessionBrief {
  id: number;
  title?: string;
  start_at?: number;
  end_at?: number;
  webinar_id?: number;
  join_url?: string;
  status?: string;
  moderator_secret?: string;
}

/** Chapter / Module */
export interface ChapterBrief {
  id: number;
  title: string;
  order?: number;
  status?: string;
}

/** Content item (lesson): video, text, live, etc. */
export interface ContentItemBrief {
  id: number;
  title: string;
  type?: string;
  order?: number;
  duration?: number;
  session_id?: number;
  file_id?: number;
  text_lesson_id?: number;
}

/** Course health log setting – what to track for a course (from description / admin) */
export interface CourseHealthLogSetting {
  webinar_id: number;
  enable_health_log: boolean;
  tracking_notes: string | null;
  custom_fields: { key: string; label: string; type: "number" | "text" }[];
}

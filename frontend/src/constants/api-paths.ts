/**
 * Backend API path constants (all under /api; development routes under /api/development).
 */
const API = "/api";
const DEV = `${API}/development`;

export const paths = {
  home: `${API}/home`,
  fitKarnatakaConfig: `${API}/fit-karnataka-config`,
  config: `${DEV}/config`,

  auth: {
    login: `${DEV}/login`,
    logout: `${DEV}/logout`,
    registerStep: (step: number) => `${DEV}/register/step/${step}`,
    forgetPassword: `${DEV}/forget-password`,
    resetPassword: (token: string) => `${DEV}/reset-password/${token}`,
    verification: `${DEV}/verification`,
  },

  guest: {
    courses: `${DEV}/courses`,
    course: (id: number | string) => `${DEV}/courses/${id}`,
    courseContent: (id: number | string) => `${DEV}/courses/${id}/content`,
    featuredCourses: `${DEV}/featured-courses`,
    categories: `${DEV}/categories`,
    categoryWebinars: (id: number | string) => `${DEV}/categories/${id}/webinars`,
    trendCategories: `${DEV}/trend-categories`,
    search: `${DEV}/search`,
    certificateValidation: `${DEV}/certificate_validation`,
    instructors: `${DEV}/providers/instructors`,
    organizations: `${DEV}/providers/organizations`,
    consultations: `${DEV}/providers/consultations`,
    userProfile: (id: number | string) => `${DEV}/users/${id}/profile`,
    blogs: `${DEV}/blogs`,
    blogCategories: `${DEV}/blogs/categories`,
    blog: (id: number | string) => `${DEV}/blogs/${id}`,
    newsletter: `${DEV}/newsletter`,
    contact: `${DEV}/contact`,
    regions: {
      countries: `${DEV}/regions/countries`,
      provinces: (id?: string) => `${DEV}/regions/provinces/${id ?? ""}`,
      cities: (id?: string) => `${DEV}/regions/cities/${id ?? ""}`,
      districts: (id?: string) => `${DEV}/regions/districts/${id ?? ""}`,
    },
    timezones: `${DEV}/timezones`,
  },

  panel: {
    base: `${DEV}/panel`,
    quickInfo: `${DEV}/panel/quick-info`,
    comments: `${DEV}/panel/comments`,
    webinars: {
      purchases: `${DEV}/panel/webinars/purchases`,
      free: (id: number | string) => `${DEV}/panel/webinars/${id}/free`,
    },
    profile: `${DEV}/panel/profile-setting`,
    certificates: `${DEV}/panel/webinars/certificates`,
    meetings: `${DEV}/panel/meetings`,
    healthLogs: `${DEV}/panel/health-logs`,
    healthLog: (id: number | string) => `${DEV}/panel/health-logs/${id}`,
    courseHealthLogSetting: (webinarId: number | string) => `${DEV}/panel/course-health-log-settings/${webinarId}`,
    support: `${DEV}/panel/support`,
    notifications: `${DEV}/panel/notifications`,
    notificationSeen: (id: number | string) => `${DEV}/panel/notifications/${id}/seen`,
    assignments: `${DEV}/panel/my_assignments`,
    assignment: (id: number | string) => `${DEV}/panel/my_assignments/${id}`,
    assignmentMessages: (id: number | string) => `${DEV}/panel/assignments/${id}/messages`,
    webinar: (id: number | string) => `${DEV}/panel/webinars/${id}`,
    webinarChapters: (id: number | string) => `${DEV}/panel/webinars/${id}/chapters`,
    webinarChapter: (webinarId: number | string, chapterId: number | string) =>
      `${DEV}/panel/webinars/${webinarId}/chapters/${chapterId}`,
    webinarNoticeboards: (id: number | string) => `${DEV}/panel/webinars/${id}/noticeboards`,
    session: (id: number | string) => `${DEV}/panel/sessions/${id}`,
    file: (id: number | string) => `${DEV}/panel/files/${id}`,
    textLesson: (id: number | string) => `${DEV}/panel/text-lessons/${id}`,
    quiz: (id: number | string) => `${DEV}/panel/quizzes/${id}`,
    cart: {
      list: `${DEV}/panel/cart/list`,
      store: `${DEV}/panel/cart/store`,
      delete: (id: number | string) => `${DEV}/panel/cart/${id}`,
      checkout: `${DEV}/panel/cart/checkout`,
      webCheckout: `${DEV}/panel/cart/web_checkout`,
      validateCoupon: `${DEV}/panel/cart/coupon/validate`,
    },
    subscribe: {
      list: `${DEV}/panel/subscribe`,
      apply: `${DEV}/panel/subscribe/apply`,
      webPay: `${DEV}/panel/subscribe/web_pay`,
    },
  },
} as const;

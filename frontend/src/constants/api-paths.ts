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
    learningStatusToggle: (webinarId: number | string) => `${DEV}/courses/${webinarId}/toggle`,
    featuredCourses: `${DEV}/featured-courses`,
    categories: `${DEV}/categories`,
    categoryWebinars: (id: number | string) => `${DEV}/categories/${id}/webinars`,
    trendCategories: `${DEV}/trend-categories`,
    search: `${DEV}/search`,
    certificateValidation: `${DEV}/certificate_validation`,
    instructors: `${DEV}/providers/instructors`,
    dieticians: `${DEV}/providers/dieticians`,
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

  onboarding: {
    profile: `${DEV}/onboarding/profile`,
    healthProfile: `${DEV}/onboarding/health-profile`,
    medicalData: `${DEV}/onboarding/medical-data`,
    dietPattern: `${DEV}/onboarding/diet-pattern`,
    lifestyle: `${DEV}/onboarding/lifestyle`,
    bodyGoals: `${DEV}/onboarding/body-goals`,
    uploadFiles: `${DEV}/onboarding/upload-files`,
    healthConditions: `${DEV}/onboarding/health-conditions`,
    bodyGoalsList: `${DEV}/onboarding/body-goals`,
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
    meetingFinish: (id: number | string) => `${DEV}/panel/meetings/${id}/finish`,
    healthLogs: `${DEV}/panel/health-logs`,
    healthLogsSummary: `${DEV}/panel/health-logs/summary`,
    healthLog: (id: number | string) => `${DEV}/panel/health-logs/${id}`,
    /** Dietician: get a student's full health profile (onboarding data). Student must be enrolled in dietician's course. */
    studentHealthProfile: (userId: number | string) => `${DEV}/panel/students/${userId}/health-profile`,
    /** Dietician: list my students (unique buyers in my courses) */
    students: `${DEV}/panel/students`,
    /** Dietician: Health Care – students by course, initial conversation status */
    healthCare: `${DEV}/panel/health-care`,
    healthCareMarkConversation: (saleId: number | string) =>
      `${DEV}/panel/health-care/sales/${saleId}/initial-conversation`,
    healthCareIntake: (saleId: number | string) =>
      `${DEV}/panel/health-care/sales/${saleId}/intake`,
    courseHealthLogSetting: (webinarId: number | string) => `${DEV}/panel/course-health-log-settings/${webinarId}`,
    support: `${DEV}/panel/support`,
    notifications: `${DEV}/panel/notifications`,
    notificationSeen: (id: number | string) => `${DEV}/panel/notifications/${id}/seen`,
    assignments: `${DEV}/panel/my_assignments`,
    assignment: (id: number | string) => `${DEV}/panel/my_assignments/${id}`,
    assignmentMessages: (id: number | string) => `${DEV}/panel/assignments/${id}/messages`,
    webinar: (id: number | string) => `${DEV}/panel/webinars/${id}`,
    webinarIntake: (webinarId: number | string) => `${DEV}/panel/webinars/${webinarId}/intake`,
    webinarIntakeUpload: (webinarId: number | string) => `${DEV}/panel/webinars/${webinarId}/intake/upload`,
    batches: (webinarId: number | string) => `${DEV}/panel/webinars/${webinarId}/batches`,
    batch: (webinarId: number | string, batchId: number | string) =>
      `${DEV}/panel/webinars/${webinarId}/batches/${batchId}`,
    classes: `${DEV}/panel/classes`,
    myBatches: `${DEV}/panel/my-batches`,
    webinarChapters: (id: number | string) => `${DEV}/panel/webinars/${id}/chapters`,
    webinarChapter: (webinarId: number | string, chapterId: number | string) =>
      `${DEV}/panel/webinars/${webinarId}/chapters/${chapterId}`,
    webinarNoticeboards: (id: number | string) => `${DEV}/panel/webinars/${id}/noticeboards`,
    /** Recommended Meals / Diet Plan – recipes assigned by dietician (not courses) */
    recommendedMeals: `${DEV}/panel/recommended-meals`,
    recommendedMeal: (assignmentId: number | string) => `${DEV}/panel/recommended-meals/${assignmentId}`,
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
    payments: {
      credit: `${DEV}/panel/payments/credit`,
      razorpayOrder: `${DEV}/panel/payments/razorpay-order`,
      verify: (gateway: string) => `${DEV}/panel/payments/verify/${gateway}`,
    },
    subscribe: {
      list: `${DEV}/panel/subscribe`,
      apply: `${DEV}/panel/subscribe/apply`,
      webPay: `${DEV}/panel/subscribe/web_pay`,
    },
  },

  /** Dietician panel: routes under /api/development/dietician (teacher-only) */
  dieticianApi: {
    recipes: `${DEV}/dietician/recipes`,
    recipe: (id: number | string) => `${DEV}/dietician/recipes/${id}`,
    studentRecipeAssignments: (studentId: number | string) => `${DEV}/dietician/students/${studentId}/recipe-assignments`,
    assignRecipe: (studentId: number | string) => `${DEV}/dietician/students/${studentId}/recipe-assignments`,
    assignRecipeBulk: (recipeId: number | string) => `${DEV}/dietician/recipes/${recipeId}/assign-bulk`,
    removeRecipeAssignment: (assignmentId: number | string) => `${DEV}/dietician/recipe-assignments/${assignmentId}`,
    assignments: `${DEV}/dietician/assignments`,
    assignmentStudents: (assignmentId: number | string) =>
      `${DEV}/dietician/assignments/${assignmentId}/students`,
    setAssignmentGrade: (historyId: number | string) =>
      `${DEV}/dietician/assignments/histories/${historyId}/rate`,
    certificatesCreated: `${DEV}/panel/certificates/created`,
    certificatesStudents: `${DEV}/panel/certificates/students`,
  },
} as const;

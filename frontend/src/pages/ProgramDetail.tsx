import { useParams, Link, useNavigate } from "react-router-dom";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import {
  Clock,
  Users,
  Star,
  Video,
  FileText,
  Radio,
  CheckCircle,
  ChevronRight,
  Play,
  BookOpen,
  MessageCircle,
  HelpCircle,
  ListChecks,
  GraduationCap,
  Award,
} from "lucide-react";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { useConfig } from "@/context/ConfigContext";
import { useAuth } from "@/context/AuthContext";
import { programsService } from "@/services/programs";
import { cartService } from "@/services/cart";
import type {
  CourseDetails,
  CourseSessionDetail,
  CourseFileDetail,
  CourseTextLessonDetail,
  CourseChapterDetail,
} from "@/types/api";
import { Button } from "@/components/ui/button";
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion";

const ProgramDetail = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { t } = useConfig();
  const { isAuthenticated } = useAuth();

  const { data: course, isLoading, error } = useQuery({
    queryKey: ["course", id],
    queryFn: () => programsService.get(id!),
    enabled: !!id,
  });
  const { data: content } = useQuery({
    queryKey: ["course-content", id],
    queryFn: () => programsService.getContent(id!),
    enabled: !!id && !!course?.auth_has_bought,
  });

  const addToCartMutation = useMutation({
    mutationFn: () => cartService.add(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["course", id] });
      queryClient.invalidateQueries({ queryKey: ["panel-cart"] });
      navigate("/panel/cart");
    },
  });

  const freeEnrollMutation = useMutation({
    mutationFn: () => programsService.enrollFree(id!),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["course", id] });
      queryClient.invalidateQueries({ queryKey: ["panel-programmes"] });
      navigate("/panel/programs");
    },
  });

  const contentList = Array.isArray(content) ? content : (content as { data?: unknown[] })?.data ?? [];
  const hasContentFromApi = contentList.length > 0;

  if (!id) return null;

  if (isLoading) {
    return (
      <div className="page-bg">
        <Navbar />
        <main className="pt-24 pb-16">
          <div className="section-container max-w-4xl">
            <div className="h-8 w-48 bg-muted rounded-xl animate-pulse mb-8" />
            <div className="aspect-video bg-muted rounded-2xl animate-pulse mb-8" />
            <div className="grid lg:grid-cols-3 gap-8">
              <div className="lg:col-span-2 space-y-6">
                <div className="h-10 bg-muted rounded-xl animate-pulse w-3/4" />
                <div className="h-32 bg-muted rounded-xl animate-pulse" />
              </div>
              <div className="h-80 bg-muted rounded-2xl animate-pulse" />
            </div>
          </div>
        </main>
        <Footer />
      </div>
    );
  }

  if (error || !course) {
    return (
      <div className="page-bg">
        <Navbar />
        <main className="pt-24 section-container py-16 text-center">
          <p className="text-destructive font-medium">{(error as Error)?.message ?? "Program not found."}</p>
          <Link to="/programs">
            <Button className="mt-6 bg-primary hover:bg-primary/90 text-primary-foreground rounded-xl">Back to {t("courses")}</Button>
          </Link>
        </main>
        <Footer />
      </div>
    );
  }

  const detail = course as CourseDetails;
  const sessionsWithout = detail.sessions_without_chapter ?? [];
  const filesWithout = detail.files_without_chapter ?? [];
  const textLessonsWithout = detail.text_lessons_without_chapter ?? [];
  const sessionChapters = detail.session_chapters ?? [];
  const filesChapters = detail.files_chapters ?? [];
  const textChapters = detail.text_lesson_chapters ?? [];
  const validTickets = (detail.tickets ?? []).filter((tk) => tk.is_valid);
  const canEnrollFree = detail.can_add_to_cart === "free";
  const canAddToCart = detail.can_add_to_cart === "ok" && !detail.auth_has_bought;

  const hasCurriculum =
    sessionChapters.length > 0 ||
    filesChapters.length > 0 ||
    textChapters.length > 0 ||
    sessionsWithout.length > 0 ||
    filesWithout.length > 0 ||
    textLessonsWithout.length > 0 ||
    hasContentFromApi;

  const totalItems =
    (detail.sessions_count ?? 0) +
    (detail.files_count ?? 0) +
    (detail.text_lessons_count ?? 0);

  return (
    <div className="page-bg">
      <Navbar />
      <main className="pt-20 pb-16">
        {/* Breadcrumb */}
        <div className="border-b border-border bg-card/60 backdrop-blur-sm">
          <div className="section-container max-w-4xl py-4">
            <nav className="flex items-center gap-2 text-sm text-muted-foreground">
              <Link to="/programs" className="hover:text-primary transition-colors">
                {t("courses")}
              </Link>
              <ChevronRight size={16} className="text-muted-foreground/70 shrink-0" />
              <span className="text-foreground font-medium truncate max-w-[200px] md:max-w-md" title={detail.title}>
                {detail.title}
              </span>
            </nav>
          </div>
        </div>

        <div className="section-container max-w-4xl">
          {/* Hero: image/video + title row */}
          <section className="mt-8">
            <div className="rounded-2xl overflow-hidden bg-muted shadow-card border border-border">
              <div className="relative aspect-video">
                {detail.video_demo ? (
                  <video
                    src={detail.video_demo}
                    controls
                    className="w-full h-full object-cover"
                    poster={detail.image}
                  />
                ) : (
                  <img
                    src={detail.image}
                    alt={detail.title}
                    className="w-full h-full object-cover"
                  />
                )}
                <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent pointer-events-none" />
                {detail.discount_percent ? (
                  <span className="absolute top-4 right-4 px-3 py-1.5 rounded-lg bg-red-500 text-white text-sm font-semibold">
                    -{detail.discount_percent}% off
                  </span>
                ) : null}
                {detail.category && (
                  <span className="absolute bottom-4 left-4 px-3 py-1.5 rounded-lg bg-white/90 text-gray-900 text-sm font-medium">
                    {detail.category}
                  </span>
                )}
              </div>
            </div>

            <div className="mt-6 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
              <div className="flex-1 min-w-0">
                <h1 className="text-2xl md:text-3xl font-bold text-gray-900 leading-tight">
                  {detail.title}
                </h1>
                <p className="mt-2 text-gray-600">
                  {detail.teacher?.headline ?? "Learn with expert instructors."}
                </p>
                <div className="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-600">
                  {detail.rate != null && (
                    <span className="flex items-center gap-1.5 font-medium text-gray-900">
                      <Star size={18} className="fill-amber-400 text-amber-400 shrink-0" />
                      {Number(detail.rate).toFixed(1)}
                      <span className="text-gray-500 font-normal">
                        ({detail.reviews_count ?? 0} {t("reviews")})
                      </span>
                    </span>
                  )}
                  {detail.students_count != null && detail.students_count > 0 && (
                    <span className="flex items-center gap-1.5">
                      <Users size={16} className="shrink-0" />
                      {detail.students_count} students
                    </span>
                  )}
                  {detail.duration != null && detail.duration > 0 && (
                    <span className="flex items-center gap-1.5">
                      <Clock size={16} className="shrink-0" />
                      {detail.duration} min total
                    </span>
                  )}
                  {detail.sessions_count != null && detail.sessions_count > 0 && (
                    <span className="flex items-center gap-1.5">
                      <Radio size={16} className="shrink-0" />
                      {detail.sessions_count} live sessions
                    </span>
                  )}
                  {detail.files_count != null && detail.files_count > 0 && (
                    <span className="flex items-center gap-1.5">
                      <Video size={16} className="shrink-0" />
                      {detail.files_count} videos
                    </span>
                  )}
                  {detail.text_lessons_count != null && detail.text_lessons_count > 0 && (
                    <span className="flex items-center gap-1.5">
                      <FileText size={16} className="shrink-0" />
                      {detail.text_lessons_count} lessons
                    </span>
                  )}
                </div>
              </div>
            </div>
          </section>

          {/* Two-column: main content + sticky sidebar */}
          <div className="mt-10 grid lg:grid-cols-3 gap-8 lg:gap-12">
            {/* Left column: About, Curriculum, Reviews, FAQ, Prerequisites, Pricing */}
            <div className="lg:col-span-2 space-y-10">
              {/* About */}
              {detail.description && (
                <section className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                  <div className="px-6 py-5 border-b border-gray-100">
                    <h2 className="text-xl font-semibold text-gray-900 flex items-center gap-2">
                      <BookOpen size={22} className="text-primary" />
                      About this {t("course").toLowerCase()}
                    </h2>
                  </div>
                  <div className="p-6">
                    <div
                      className="prose prose-gray max-w-none prose-headings:font-semibold prose-a:text-primary prose-a:no-underline hover:prose-a:underline"
                      dangerouslySetInnerHTML={{ __html: detail.description }}
                    />
                  </div>
                </section>
              )}

              {/* Curriculum */}
              <section className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div className="px-6 py-5 border-b border-gray-100">
                  <h2 className="text-xl font-semibold text-gray-900 flex items-center gap-2">
                    <ListChecks size={22} className="text-primary" />
                    Curriculum
                    {totalItems > 0 && (
                      <span className="text-sm font-normal text-gray-500">
                        · {totalItems} {totalItems === 1 ? "item" : "items"}
                      </span>
                    )}
                  </h2>
                </div>
                <div className="p-6">
                  {hasCurriculum ? (
                    <Accordion type="single" collapsible className="w-full">
                      {sessionChapters.map((ch: CourseChapterDetail) => (
                        <AccordionItem key={`s-${ch.id}`} value={`s-${ch.id}`} className="border-gray-100">
                          <AccordionTrigger className="hover:no-underline py-4">
                            <span className="flex items-center gap-2 text-left">
                              <Radio size={18} className="text-primary shrink-0" />
                              {ch.title}
                              <span className="text-gray-500 text-sm font-normal">
                                ({(ch.items as CourseSessionDetail[] ?? []).length} sessions)
                              </span>
                            </span>
                          </AccordionTrigger>
                          <AccordionContent>
                            <ul className="space-y-3 pl-1">
                              {(ch.items as CourseSessionDetail[] ?? []).map((s) => (
                                <li key={s.id} className="flex items-center gap-3 text-sm">
                                  <Play size={14} className="text-gray-400 shrink-0" />
                                  <span className="text-gray-700">{s.title}</span>
                                  {s.duration != null && (
                                    <span className="text-gray-400 ml-auto">{s.duration} min</span>
                                  )}
                                </li>
                              ))}
                            </ul>
                          </AccordionContent>
                        </AccordionItem>
                      ))}
                      {sessionsWithout.map((s) => (
                        <AccordionItem key={`s-${s.id}`} value={`s-${s.id}`} className="border-gray-100">
                          <AccordionTrigger className="hover:no-underline py-4">
                            <span className="flex items-center gap-2 text-left">
                              <Radio size={18} className="text-primary shrink-0" />
                              {s.title}
                            </span>
                          </AccordionTrigger>
                          <AccordionContent>
                            {s.description && (
                              <p className="text-sm text-gray-600 pl-6">{s.description}</p>
                            )}
                          </AccordionContent>
                        </AccordionItem>
                      ))}
                      {filesChapters.map((ch: CourseChapterDetail) => (
                        <AccordionItem key={`f-${ch.id}`} value={`f-${ch.id}`} className="border-gray-100">
                          <AccordionTrigger className="hover:no-underline py-4">
                            <span className="flex items-center gap-2 text-left">
                              <Video size={18} className="text-primary shrink-0" />
                              {ch.title}
                              <span className="text-gray-500 text-sm font-normal">
                                ({(ch.items as CourseFileDetail[] ?? []).length} videos)
                              </span>
                            </span>
                          </AccordionTrigger>
                          <AccordionContent>
                            <ul className="space-y-3 pl-1">
                              {(ch.items as CourseFileDetail[] ?? []).map((f) => (
                                <li key={f.id} className="flex items-center gap-3 text-sm">
                                  <Play size={14} className="text-gray-400 shrink-0" />
                                  <span className="text-gray-700">{f.title}</span>
                                  {f.duration != null && (
                                    <span className="text-gray-400 ml-auto">{f.duration} min</span>
                                  )}
                                </li>
                              ))}
                            </ul>
                          </AccordionContent>
                        </AccordionItem>
                      ))}
                      {filesWithout.map((f) => (
                        <AccordionItem key={`f-${f.id}`} value={`f-${f.id}`} className="border-gray-100">
                          <AccordionTrigger className="hover:no-underline py-4">
                            <span className="flex items-center gap-2 text-left">
                              <Video size={18} className="text-primary shrink-0" />
                              {f.title}
                            </span>
                          </AccordionTrigger>
                          <AccordionContent>
                            {f.duration != null && (
                              <p className="text-sm text-gray-600 pl-6">{f.duration} min</p>
                            )}
                          </AccordionContent>
                        </AccordionItem>
                      ))}
                      {textChapters.map((ch: CourseChapterDetail) => (
                        <AccordionItem key={`t-${ch.id}`} value={`t-${ch.id}`} className="border-gray-100">
                          <AccordionTrigger className="hover:no-underline py-4">
                            <span className="flex items-center gap-2 text-left">
                              <FileText size={18} className="text-primary shrink-0" />
                              {ch.title}
                              <span className="text-gray-500 text-sm font-normal">
                                ({(ch.items as CourseTextLessonDetail[] ?? []).length} lessons)
                              </span>
                            </span>
                          </AccordionTrigger>
                          <AccordionContent>
                            <ul className="space-y-3 pl-1">
                              {(ch.items as CourseTextLessonDetail[] ?? []).map((tl) => (
                                <li key={tl.id} className="flex items-center gap-3 text-sm">
                                  <FileText size={14} className="text-gray-400 shrink-0" />
                                  <span className="text-gray-700">{tl.title}</span>
                                </li>
                              ))}
                            </ul>
                          </AccordionContent>
                        </AccordionItem>
                      ))}
                      {textLessonsWithout.map((tl) => (
                        <AccordionItem key={`t-${tl.id}`} value={`t-${tl.id}`} className="border-gray-100">
                          <AccordionTrigger className="hover:no-underline py-4">
                            <span className="flex items-center gap-2 text-left">
                              <FileText size={18} className="text-primary shrink-0" />
                              {tl.title}
                            </span>
                          </AccordionTrigger>
                          <AccordionContent />
                        </AccordionItem>
                      ))}
                      {hasContentFromApi &&
                        contentList.filter((c: { type?: string }) => c.type === "chapter").length > 0 &&
                        contentList
                          .filter((c: { type?: string }) => c.type === "chapter")
                          .map((ch: { id: number; title?: string; items?: unknown[] }) => (
                            <AccordionItem key={`c-${ch.id}`} value={`c-${ch.id}`} className="border-gray-100">
                              <AccordionTrigger className="hover:no-underline py-4">
                                {ch.title}
                              </AccordionTrigger>
                              <AccordionContent>
                                <ul className="space-y-2 pl-2">
                                  {((ch.items as { id: number; title?: string; type?: string }[]) ?? []).map(
                                    (item) => (
                                      <li key={item.id} className="flex items-center gap-2 text-sm text-gray-700">
                                        {item.type === "session" ? (
                                          <Radio size={14} className="shrink-0" />
                                        ) : item.type === "text_lesson" ? (
                                          <FileText size={14} className="shrink-0" />
                                        ) : (
                                          <Video size={14} className="shrink-0" />
                                        )}
                                        {item.title}
                                      </li>
                                    )
                                  )}
                                </ul>
                              </AccordionContent>
                            </AccordionItem>
                          ))}
                    </Accordion>
                  ) : (
                    <p className="text-gray-500 text-sm py-4">
                      Curriculum will be available after enrollment.
                    </p>
                  )}
                </div>
              </section>

              {/* Reviews */}
              <section className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div className="px-6 py-5 border-b border-gray-100">
                  <h2 className="text-xl font-semibold text-gray-900 flex items-center gap-2">
                    <MessageCircle size={22} className="text-primary" />
                    Student reviews
                    {detail.reviews_count != null && detail.reviews_count > 0 && (
                      <span className="text-sm font-normal text-gray-500">
                        · {detail.reviews_count} {t("reviews")}
                      </span>
                    )}
                  </h2>
                </div>
                <div className="p-6">
                  {detail.reviews && detail.reviews.length > 0 ? (
                    <ul className="space-y-6">
                      {detail.reviews.map((rev, i) => (
                        <li key={i} className="flex gap-4">
                          {rev.user?.avatar ? (
                            <img
                              src={rev.user.avatar}
                              alt=""
                              className="w-11 h-11 rounded-full object-cover shrink-0"
                            />
                          ) : (
                            <div className="w-11 h-11 rounded-full bg-gray-200 shrink-0 flex items-center justify-center text-gray-500 font-medium">
                              {(rev.user?.full_name ?? "?")[0]}
                            </div>
                          )}
                          <div className="min-w-0 flex-1">
                            <div className="flex items-center gap-2 flex-wrap">
                              <span className="font-medium text-gray-900">{rev.user?.full_name ?? "Anonymous"}</span>
                              {rev.rates != null && (
                                <span className="flex items-center gap-1 text-amber-500">
                                  <Star size={14} className="fill-current" />
                                  {rev.rates}
                                </span>
                              )}
                            </div>
                            {rev.description && (
                              <p className="text-gray-600 text-sm mt-1 leading-relaxed">{rev.description}</p>
                            )}
                          </div>
                        </li>
                      ))}
                    </ul>
                  ) : (
                    <p className="text-gray-500 text-sm py-4">No reviews yet. Be the first to leave one after completing the course.</p>
                  )}
                </div>
              </section>

              {/* FAQ */}
              {detail.faqs && detail.faqs.length > 0 && (
                <section className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                  <div className="px-6 py-5 border-b border-gray-100">
                    <h2 className="text-xl font-semibold text-gray-900 flex items-center gap-2">
                      <HelpCircle size={22} className="text-primary" />
                      Frequently asked questions
                    </h2>
                  </div>
                  <div className="p-6">
                    <Accordion type="single" collapsible className="w-full">
                      {detail.faqs.map((faq, i) => (
                        <AccordionItem key={faq.id ?? i} value={`faq-${i}`} className="border-gray-100">
                          <AccordionTrigger className="hover:no-underline py-4 text-left font-medium text-gray-900">
                            {faq.title ?? "Question"}
                          </AccordionTrigger>
                          <AccordionContent>
                            <p className="text-gray-600 text-sm leading-relaxed pb-2">{faq.answer}</p>
                          </AccordionContent>
                        </AccordionItem>
                      ))}
                    </Accordion>
                  </div>
                </section>
              )}

              {/* Prerequisites */}
              {detail.prerequisites && detail.prerequisites.length > 0 && (
                <section className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                  <div className="px-6 py-5 border-b border-gray-100">
                    <h2 className="text-xl font-semibold text-gray-900 flex items-center gap-2">
                      <GraduationCap size={22} className="text-primary" />
                      Prerequisites
                    </h2>
                  </div>
                  <div className="p-6">
                    <ul className="space-y-2">
                      {detail.prerequisites.map((pre, i) => (
                        <li key={i} className="flex items-center gap-2 text-sm">
                          {pre.required && (
                            <span className="text-red-600 font-medium shrink-0">Required:</span>
                          )}
                          {pre.webinar ? (
                            <Link
                              to={`/programs/${pre.webinar.id}`}
                              className="text-primary hover:underline font-medium"
                            >
                              {pre.webinar.title}
                            </Link>
                          ) : null}
                        </li>
                      ))}
                    </ul>
                  </div>
                </section>
              )}

              {/* Pricing options (multiple tickets) */}
              {validTickets.length > 1 && (
                <section className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                  <div className="px-6 py-5 border-b border-gray-100">
                    <h2 className="text-xl font-semibold text-gray-900">Pricing options</h2>
                  </div>
                  <div className="p-6">
                    <ul className="space-y-4">
                      {validTickets.map((tk) => (
                        <li
                          key={tk.id}
                          className="flex items-center justify-between py-3 border-b border-gray-100 last:border-0"
                        >
                          <div>
                            <span className="font-medium text-gray-900">{tk.title}</span>
                            {tk.sub_title && (
                              <p className="text-sm text-gray-500 mt-0.5">{tk.sub_title}</p>
                            )}
                          </div>
                          {tk.discount != null && tk.discount > 0 && (
                            <span className="text-primary font-semibold">{tk.discount}% off</span>
                          )}
                        </li>
                      ))}
                    </ul>
                  </div>
                </section>
              )}
            </div>

            {/* Right column: sticky sidebar */}
            <div className="lg:col-span-1">
              <div className="lg:sticky lg:top-24 space-y-6">
                {/* Price & CTA card */}
                <div className="bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">
                  <div className="p-6">
                    <div className="flex items-baseline gap-2 flex-wrap">
                      <span className="text-3xl font-bold text-gray-900">
                        {detail.best_ticket_string ?? detail.price_string ?? (detail.price != null ? `₹${detail.price}` : "Free")}
                      </span>
                      {detail.discount_percent && detail.discount_percent > 0 && (
                        <span className="text-gray-500 line-through text-lg">
                          {detail.price_string ?? (detail.price != null ? `₹${detail.price}` : "")}
                        </span>
                      )}
                    </div>

                    <div className="mt-6 space-y-3">
                      {detail.auth_has_bought ? (
                        <Link to="/panel/programs" className="block">
                          <Button className="w-full bg-primary hover:bg-primary/90 text-white h-12 font-semibold rounded-xl gap-2">
                            <CheckCircle size={20} /> Go to {t("course")}
                          </Button>
                        </Link>
                      ) : canEnrollFree ? (
                        isAuthenticated ? (
                          <Button
                            className="w-full bg-primary hover:bg-primary/90 text-white h-12 font-semibold rounded-xl"
                            disabled={freeEnrollMutation.isPending}
                            onClick={() => freeEnrollMutation.mutate()}
                          >
                            {freeEnrollMutation.isPending ? "Enrolling…" : "Enroll for free"}
                          </Button>
                        ) : (
                          <Link to="/login" className="block">
                            <Button className="w-full bg-primary hover:bg-primary/90 text-white h-12 font-semibold rounded-xl">
                              Enroll for free
                            </Button>
                          </Link>
                        )
                      ) : canAddToCart && isAuthenticated ? (
                        <>
                          <Button
                            className="w-full bg-primary hover:bg-primary/90 text-white h-12 font-semibold rounded-xl"
                            disabled={addToCartMutation.isPending}
                            onClick={() => addToCartMutation.mutate()}
                          >
                            {addToCartMutation.isPending ? "Adding…" : "Add to cart"}
                          </Button>
                          <Link to="/panel/cart" className="block">
                            <Button variant="outline" className="w-full h-11 rounded-xl border-2 border-gray-200">
                              View cart
                            </Button>
                          </Link>
                        </>
                      ) : !isAuthenticated ? (
                        <Link to="/login" className="block">
                          <Button className="w-full bg-primary hover:bg-primary/90 text-white h-12 font-semibold rounded-xl">
                            Sign in to enroll
                          </Button>
                        </Link>
                      ) : null}
                    </div>

                    {detail.subscribe && (
                      <p className="text-sm text-gray-500 mt-4">
                        This {t("course").toLowerCase()} is also available with a subscription.
                      </p>
                    )}
                  </div>
                </div>

                {/* This course includes */}
                <div className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                  <div className="px-6 py-4 border-b border-gray-100">
                    <h3 className="font-semibold text-gray-900">This {t("course").toLowerCase()} includes</h3>
                  </div>
                  <ul className="p-6 space-y-3 text-sm text-gray-700">
                    {detail.duration != null && detail.duration > 0 && (
                      <li className="flex items-center gap-3">
                        <Clock size={18} className="text-primary shrink-0" />
                        {detail.duration} min total length
                      </li>
                    )}
                    {detail.files_count != null && detail.files_count > 0 && (
                      <li className="flex items-center gap-3">
                        <Video size={18} className="text-primary shrink-0" />
                        {detail.files_count} video {detail.files_count === 1 ? "lesson" : "lessons"}
                      </li>
                    )}
                    {detail.text_lessons_count != null && detail.text_lessons_count > 0 && (
                      <li className="flex items-center gap-3">
                        <FileText size={18} className="text-primary shrink-0" />
                        {detail.text_lessons_count} reading {detail.text_lessons_count === 1 ? "lesson" : "lessons"}
                      </li>
                    )}
                    {detail.sessions_count != null && detail.sessions_count > 0 && (
                      <li className="flex items-center gap-3">
                        <Radio size={18} className="text-primary shrink-0" />
                        {detail.sessions_count} live {detail.sessions_count === 1 ? "session" : "sessions"}
                      </li>
                    )}
                    {detail.certificate && (detail.certificate as unknown[]).length > 0 && (
                      <li className="flex items-center gap-3">
                        <Award size={18} className="text-primary shrink-0" />
                        Certificate of completion
                      </li>
                    )}
                  </ul>
                </div>

                {/* Instructor */}
                {detail.teacher && (
                  <div className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div className="px-6 py-4 border-b border-gray-100">
                      <h3 className="font-semibold text-gray-900">Instructor</h3>
                    </div>
                    <div className="p-6 flex gap-4">
                      <img
                        src={detail.teacher.avatar ?? ""}
                        alt={detail.teacher.full_name ?? "Instructor"}
                        className="w-14 h-14 rounded-full object-cover shrink-0 border-2 border-gray-100"
                      />
                      <div className="min-w-0">
                        <p className="font-semibold text-gray-900">{detail.teacher.full_name}</p>
                        {detail.teacher.headline && (
                          <p className="text-sm text-gray-600 mt-0.5 line-clamp-2">{detail.teacher.headline}</p>
                        )}
                      </div>
                    </div>
                  </div>
                )}

                {/* Quick links (when enrolled) */}
                {isAuthenticated && (
                  <div className="flex flex-col gap-2">
                    <Link to="/panel/meetings">
                      <Button variant="outline" className="w-full justify-center rounded-xl border-2">
                        Join live session
                      </Button>
                    </Link>
                    <Link to="/panel/health-log">
                      <Button variant="outline" className="w-full justify-center rounded-xl border-2 gap-2">
                        <ListChecks size={16} /> Daily log
                      </Button>
                    </Link>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default ProgramDetail;

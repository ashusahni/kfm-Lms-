import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Link } from "react-router-dom";
import { Clock, Users, Star, Search, Filter, ChevronRight, Dumbbell } from "lucide-react";
import { motion } from "framer-motion";
import Navbar from "@/components/Navbar";
import Footer from "@/components/Footer";
import { useConfig } from "@/context/ConfigContext";
import { programsService } from "@/services/programs";
import type { WebinarBrief } from "@/types/api";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

function ensureList(data: unknown): WebinarBrief[] {
  if (Array.isArray(data)) return data;
  if (data && typeof data === "object" && "data" in data && Array.isArray((data as { data: WebinarBrief[] }).data))
    return (data as { data: WebinarBrief[] }).data;
  if (data && typeof data === "object" && "webinars" in data && Array.isArray((data as { webinars: WebinarBrief[] }).webinars))
    return (data as { webinars: WebinarBrief[] }).webinars;
  return [];
}

const Programs = () => {
  const { t } = useConfig();
  const [categoryId, setCategoryId] = useState<string>("all");
  const [searchQ, setSearchQ] = useState("");
  const [sort, setSort] = useState<string>("default");

  const { data: categories } = useQuery({
    queryKey: ["categories"],
    queryFn: () => programsService.getCategories(),
  });
  const categoryList = Array.isArray(categories) ? categories : [];

  const { data: coursesRaw, isLoading, error } = useQuery({
    queryKey: ["courses", categoryId, searchQ, sort],
    queryFn: () => {
      const params: Record<string, string> = {};
      if (categoryId && categoryId !== "all") params.cat = categoryId;
      if (searchQ.trim()) params.q = searchQ.trim();
      if (sort && sort !== "default") params.sort = sort;
      return searchQ.trim()
        ? programsService.search({ q: searchQ.trim(), cat: categoryId && categoryId !== "all" ? Number(categoryId) : undefined })
        : programsService.list(params);
    },
  });

  const courses = ensureList(coursesRaw);

  return (
    <div className="page-bg">
      <Navbar />
      <main className="pt-20 pb-16">
        {/* Hero banner */}
        <section className="relative overflow-hidden bg-gradient-fitness-hero text-white">
          <div className="absolute inset-0 bg-gradient-to-b from-[hsl(152,50%,28%)]/90 to-[hsl(165,45%,18%)]/95 z-0" />
          <div className="section-container relative z-10 py-16 md:py-20">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5 }}
              className="max-w-2xl"
            >
              <span className="inline-block px-4 py-1.5 rounded-full bg-white/15 text-white/95 text-sm font-semibold mb-4">
                Fitness Programs
              </span>
              <h1 className="text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-4">
                Find Your Program
              </h1>
              <p className="text-lg text-white/90 leading-relaxed">
                Browse fitness programs, nutrition plans, and coaching. Filter by category, search by goal, and start your transformation.
              </p>
            </motion.div>
          </div>
        </section>

        <div className="section-container -mt-6 relative z-20">
          {/* Filters card */}
          <motion.div
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.1 }}
            className="bg-card/90 backdrop-blur-sm rounded-2xl border border-border shadow-card p-4 md:p-5 mb-10"
          >
            <div className="flex flex-col lg:flex-row gap-4">
              <div className="relative flex-1">
                <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-muted-foreground" />
                <Input
                  placeholder="Search programs—yoga, strength, nutrition..."
                  value={searchQ}
                  onChange={(e) => setSearchQ(e.target.value)}
                  className="pl-11 h-12 rounded-xl border-2 focus-visible:ring-primary"
                />
              </div>
              <Select value={categoryId} onValueChange={setCategoryId}>
                <SelectTrigger className="w-full lg:w-52 h-12 rounded-xl border-2">
                  <Filter className="h-4 w-4 mr-2 text-muted-foreground" />
                  <SelectValue placeholder="Category" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All categories</SelectItem>
                  {categoryList.map((c) => (
                    <SelectItem key={c.id} value={String(c.id)}>
                      {c.title}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <Select value={sort} onValueChange={setSort}>
                <SelectTrigger className="w-full lg:w-48 h-12 rounded-xl border-2">
                  <SelectValue placeholder="Sort by" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="default">Default</SelectItem>
                  <SelectItem value="newest">Newest</SelectItem>
                  <SelectItem value="expensive">Price: High to Low</SelectItem>
                  <SelectItem value="inexpensive">Price: Low to High</SelectItem>
                  <SelectItem value="bestsellers">Most popular</SelectItem>
                  <SelectItem value="best_rates">Top rated</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </motion.div>

          {/* Results header */}
          <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <h2 className="text-xl font-semibold text-foreground">
              {courses.length > 0 ? (
                <>
                  <span className="text-primary">{courses.length}</span> {t("courses").toLowerCase()} found
                </>
              ) : !isLoading && !error ? (
                "No programs match your filters"
              ) : (
                "Programs"
              )}
            </h2>
          </div>

          {/* Loading */}
          {isLoading && (
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
              {[1, 2, 3, 4, 5, 6].map((i) => (
                <div key={i} className="bg-card rounded-2xl border border-border overflow-hidden shadow-card">
                  <Skeleton className="h-52 w-full rounded-none" />
                  <div className="p-6 space-y-3">
                    <Skeleton className="h-5 w-20 rounded-full" />
                    <Skeleton className="h-6 w-3/4" />
                    <Skeleton className="h-4 w-full" />
                    <Skeleton className="h-4 w-2/3" />
                    <div className="flex justify-between pt-4">
                      <Skeleton className="h-8 w-24" />
                      <Skeleton className="h-10 w-28 rounded-xl" />
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* Error */}
          {error && (
            <div className="text-center py-16 bg-card rounded-2xl border border-border">
              <p className="text-destructive font-medium">Failed to load programs. Please try again.</p>
              <Button variant="outline" className="mt-4 rounded-xl" onClick={() => window.location.reload()}>
                Retry
              </Button>
            </div>
          )}

          {/* Empty */}
          {!isLoading && !error && courses.length === 0 && (
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="text-center py-20 bg-card rounded-2xl border border-border"
            >
              <Dumbbell className="h-14 w-14 text-muted-foreground mx-auto mb-4 opacity-60" />
              <p className="text-muted-foreground text-lg">No {t("courses").toLowerCase()} found.</p>
              <p className="text-muted-foreground text-sm mt-1">Try changing filters or search terms.</p>
              <Button
                variant="outline"
                className="mt-6 rounded-xl border-2"
                onClick={() => {
                  setCategoryId("all");
                  setSearchQ("");
                  setSort("default");
                }}
              >
                Clear filters
              </Button>
            </motion.div>
          )}

          {/* Program cards */}
          {!isLoading && courses.length > 0 && (
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
              {courses.map((prog, i) => (
                <motion.article
                  key={prog.id}
                  initial={{ opacity: 0, y: 16 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: Math.min(i * 0.05, 0.25) }}
                  className="group"
                >
                  <Link
                    to={`/programs/${prog.id}`}
                    className="flex flex-col h-full bg-card rounded-2xl border border-border overflow-hidden shadow-card hover:shadow-card-hover hover:border-primary/30 transition-all duration-300"
                  >
                    <div className="relative overflow-hidden">
                      <img
                        src={prog.image}
                        alt={prog.title}
                        className="w-full h-56 object-cover group-hover:scale-105 transition-transform duration-500"
                        loading="lazy"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity" />
                      {prog.discount_percent ? (
                        <Badge className="absolute top-4 right-4 bg-accent text-accent-foreground border-0 shadow-md">
                          -{prog.discount_percent}%
                        </Badge>
                      ) : null}
                      {prog.category ? (
                        <Badge className="absolute bottom-4 left-4 bg-primary/90 text-primary-foreground border-0 shadow-md">
                          {prog.category}
                        </Badge>
                      ) : null}
                    </div>
                    <div className="p-6 flex-1 flex flex-col">
                      <h3 className="font-display text-xl font-bold text-foreground mb-3 line-clamp-2 group-hover:text-primary transition-colors">
                        {prog.title}
                      </h3>
                      <div className="flex items-center gap-4 text-sm text-muted-foreground mb-4 flex-wrap">
                        {prog.duration ? (
                          <span className="flex items-center gap-1.5">
                            <Clock size={15} className="text-primary/70" /> {prog.duration} min
                          </span>
                        ) : null}
                        {prog.students_count != null && (
                          <span className="flex items-center gap-1.5">
                            <Users size={15} className="text-primary/70" /> {prog.students_count} joined
                          </span>
                        )}
                        {prog.rate != null && (
                          <span className="flex items-center gap-1.5 font-medium text-foreground">
                            <Star size={15} className="fill-amber-400 text-amber-400" /> {Number(prog.rate).toFixed(1)}
                          </span>
                        )}
                      </div>
                      <div className="flex items-center justify-between mt-auto pt-4 border-t border-border">
                        <span className="text-xl font-bold text-foreground">
                          {prog.price_string ?? (prog.price != null ? `₹${prog.price}` : "Free")}
                        </span>
                        <span className="inline-flex items-center gap-1.5 text-primary font-semibold text-sm group-hover:gap-2 transition-all">
                          View program <ChevronRight size={18} />
                        </span>
                      </div>
                    </div>
                  </Link>
                </motion.article>
              ))}
            </div>
          )}
        </div>
      </main>
      <Footer />
    </div>
  );
};

export default Programs;

import { useQuery } from "@tanstack/react-query";
import { Link } from "react-router-dom";
import { motion } from "framer-motion";
import { Clock, Target, ArrowRight } from "lucide-react";
import { useConfig } from "@/context/ConfigContext";
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type { WebinarBrief } from "@/types/api";
import { Button } from "@/components/ui/button";
import { CardWithBorderBeam } from "@/components/aceternity/BorderBeam";
import { Skeleton } from "@/components/ui/skeleton";
import { Badge } from "@/components/ui/badge";

const FeaturedPrograms = () => {
  const { t } = useConfig();
  const { data: programs, isLoading, error } = useQuery({
    queryKey: ["featured-courses"],
    queryFn: () => api.get<WebinarBrief[]>(paths.guest.featuredCourses),
  });

  const list = programs ?? [];

  return (
    <section id="programs" className="py-20 md:py-24">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="flex flex-wrap items-end justify-between gap-6 mb-12"
        >
          <div>
            <span className="text-sm font-semibold text-primary uppercase tracking-wider">Featured</span>
            <h2 className="text-2xl md:text-3xl font-bold text-foreground tracking-tight mt-2">
              Featured fitness programs
            </h2>
            <p className="text-muted-foreground mt-1">Popular programs to transform your health and fitness.</p>
          </div>
          <Link to="/programs" className="text-primary font-semibold hover:underline">
            View all →
          </Link>
        </motion.div>

        {isLoading && (
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {[1, 2, 3].map((i) => (
              <CardWithBorderBeam key={i} className="overflow-hidden">
                <Skeleton className="h-52 w-full rounded-none" />
                <div className="p-6 space-y-3">
                  <Skeleton className="h-6 w-3/4" />
                  <Skeleton className="h-4 w-full" />
                  <Skeleton className="h-8 w-24" />
                </div>
              </CardWithBorderBeam>
            ))}
          </div>
        )}
        {error && (
          <p className="text-center text-muted-foreground">
            Could not load featured {t("courses").toLowerCase()}.
          </p>
        )}
        {!isLoading && !error && list.length === 0 && (
          <p className="text-center text-muted-foreground">
            No featured {t("courses").toLowerCase()} yet.
          </p>
        )}
        {!isLoading && list.length > 0 && (
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {list.slice(0, 6).map((prog, i) => (
              <motion.div
                key={prog.id}
                initial={{ opacity: 0, y: 24 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.1 }}
                className="group"
              >
                <CardWithBorderBeam className="overflow-hidden h-full transition-shadow hover:shadow-card-hover">
                  <Link to={`/programs/${prog.id}`} className="block h-full">
                    <div className="relative overflow-hidden">
                      <img
                        src={prog.image}
                        alt={prog.title}
                        className="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-500"
                        loading="lazy"
                      />
                      {prog.discount_percent ? (
                        <Badge className="absolute top-3 right-3 bg-accent/95 text-accent-foreground backdrop-blur-sm">
                          -{prog.discount_percent}%
                        </Badge>
                      ) : null}
                    </div>
                    <div className="p-6">
                      <h3 className="font-display text-xl font-bold text-foreground mb-3 line-clamp-2">
                        {prog.title}
                      </h3>
                      <div className="flex items-center gap-4 text-sm text-muted-foreground mb-4">
                        {prog.duration != null && (
                          <span className="flex items-center gap-1">
                            <Clock size={14} /> {prog.duration} min
                          </span>
                        )}
                        {prog.students_count != null && (
                          <span className="flex items-center gap-1">
                            <Target size={14} /> {prog.students_count} joined
                          </span>
                        )}
                      </div>
                      <div className="flex items-center justify-between mt-2">
                        <span className="text-xl font-bold text-foreground">
                          {prog.price_string ?? (prog.price != null ? `₹${prog.price}` : "Free")}
                        </span>
                        <span className="text-primary font-semibold text-sm hover:underline inline-flex items-center gap-1">
                          View program <ArrowRight size={14} />
                        </span>
                      </div>
                    </div>
                  </Link>
                </CardWithBorderBeam>
              </motion.div>
            ))}
          </div>
        )}
        {list.length > 0 && (
          <div className="text-center mt-10">
            <Link to="/programs">
              <Button variant="outline" size="lg" className="border-2 border-primary text-primary hover:bg-primary hover:text-primary-foreground rounded-xl font-semibold">
                View all programs
              </Button>
            </Link>
          </div>
        )}
      </div>
    </section>
  );
};

export default FeaturedPrograms;

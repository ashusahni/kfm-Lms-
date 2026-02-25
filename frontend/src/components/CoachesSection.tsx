import { useQuery } from "@tanstack/react-query";
import { motion } from "framer-motion";
import { Award, ArrowRight } from "lucide-react";
import { Link } from "react-router-dom";
import { useConfig } from "@/context/ConfigContext";
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type { UserBrief } from "@/types/api";

const CoachesSection = () => {
  const { isDisabled } = useConfig();
  const { data: instructors, isLoading, error } = useQuery({
    queryKey: ["instructors"],
    queryFn: () => api.get<UserBrief[]>(paths.guest.instructors),
    enabled: !isDisabled("instructor_finder"),
  });

  if (isDisabled("instructor_finder")) return null;

  const list = instructors ?? [];

  return (
    <section id="coaches" className="py-20 md:py-24">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-center mb-16"
        >
          <span className="text-sm font-semibold text-primary uppercase tracking-wider">Our Team</span>
          <h2 className="text-3xl md:text-4xl font-display font-bold text-foreground mt-3">
            Meet Our Expert Coaches
          </h2>
        </motion.div>

        {isLoading && (
          <div className="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            {[1, 2, 3].map((i) => (
              <div key={i} className="bg-card rounded-2xl h-72 border border-border animate-pulse" />
            ))}
          </div>
        )}
        {error && (
          <p className="text-center text-muted-foreground">Could not load coaches.</p>
        )}
        {!isLoading && !error && list.length === 0 && (
          <p className="text-center text-muted-foreground">No coaches listed yet.</p>
        )}
        {!isLoading && list.length > 0 && (
          <div className="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            {list.slice(0, 6).map((coach, i) => (
              <motion.div
                key={coach.id}
                initial={{ opacity: 0, y: 24 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.1 }}
                className="bg-card rounded-2xl overflow-hidden shadow-card border border-border text-center hover:shadow-card-hover transition-all duration-300"
              >
                <div className="pt-8 px-6">
                  <img
                    src={coach.avatar ?? ""}
                    alt={coach.full_name ?? "Coach"}
                    className="w-28 h-28 mx-auto rounded-full object-cover border-4 border-primary/20 bg-muted"
                    loading="lazy"
                  />
                </div>
                <div className="p-6">
                  <h3 className="font-sans text-xl font-bold text-foreground">{coach.full_name ?? "Instructor"}</h3>
                  {coach.headline && (
                    <p className="text-sm text-primary font-medium mt-1">{coach.headline}</p>
                  )}
                  {(coach.students_count != null || coach.rate != null) && (
                    <div className="flex items-center justify-center gap-2 mt-3 text-sm text-muted-foreground">
                      {coach.students_count != null && <span>{coach.students_count} students</span>}
                      {coach.rate != null && <span>· Rating {Number(coach.rate).toFixed(1)}</span>}
                    </div>
                  )}
                  <Link
                    to={`/instructors/${coach.id}`}
                    className="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:text-foreground transition-colors"
                  >
                    View Profile <ArrowRight size={14} />
                  </Link>
                </div>
              </motion.div>
            ))}
          </div>
        )}
      </div>
    </section>
  );
};

export default CoachesSection;

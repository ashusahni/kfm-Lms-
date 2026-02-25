import { useQuery } from "@tanstack/react-query";
import { Link } from "react-router-dom";
import { motion } from "framer-motion";
import { ArrowRight } from "lucide-react";
import { useConfig } from "@/context/ConfigContext";
import { programsService } from "@/services/programs";
import type { WebinarBrief } from "@/types/api";
import { Button } from "@/components/ui/button";
import { CardWithBorderBeam } from "@/components/aceternity/BorderBeam";
import { Skeleton } from "@/components/ui/skeleton";

function isPaginated<T>(d: T[] | { data: T[] }): d is { data: T[] } {
  return typeof d === "object" && d !== null && "data" in d;
}

const LatestPrograms = () => {
  const { t } = useConfig();
  const { data, isLoading, error } = useQuery({
    queryKey: ["latest-courses"],
    queryFn: () => programsService.list(),
  });

  const list = !data
    ? []
    : isPaginated(data)
      ? data.data
      : Array.isArray(data)
        ? data
        : [];
  const show = list.slice(0, 6);

  return (
    <section id="latest" className="py-20 md:py-24">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-center mb-14"
        >
          <span className="text-sm font-semibold text-primary uppercase tracking-wider">
            New programs
          </span>
          <h2 className="text-3xl md:text-4xl font-display font-bold text-foreground mt-2">
            Latest fitness programs
          </h2>
        </motion.div>
        {isLoading && (
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {[1, 2, 3].map((i) => (
              <CardWithBorderBeam key={i} className="overflow-hidden">
                <Skeleton className="h-52 w-full rounded-none" />
                <div className="p-6 space-y-3">
                  <Skeleton className="h-6 w-3/4" />
                  <Skeleton className="h-4 w-full" />
                  <Skeleton className="h-4 w-1/2" />
                </div>
              </CardWithBorderBeam>
            ))}
          </div>
        )}
        {error && (
          <p className="text-center text-muted-foreground">
            Could not load {t("courses").toLowerCase()}.
          </p>
        )}
        {!isLoading && show.length > 0 && (
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {show.map((prog: WebinarBrief, i: number) => (
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
                    </div>
                    <div className="p-6">
                      <h3 className="font-sans text-xl font-bold text-foreground mb-3 line-clamp-2">
                        {prog.title}
                      </h3>
                      <span className="inline-flex items-center gap-1.5 text-primary font-semibold">
                        View program <ArrowRight size={14} />
                      </span>
                    </div>
                  </Link>
                </CardWithBorderBeam>
              </motion.div>
            ))}
          </div>
        )}
        {show.length > 0 && (
          <div className="text-center mt-10">
            <Link to="/programs">
              <Button variant="outline" size="lg">
                View all {t("courses")}
              </Button>
            </Link>
          </div>
        )}
      </div>
    </section>
  );
};

export default LatestPrograms;

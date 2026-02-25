import { useQuery } from "@tanstack/react-query";
import { motion } from "framer-motion";
import { ArrowRight } from "lucide-react";
import { Link } from "react-router-dom";
import { useConfig } from "@/context/ConfigContext";
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";
import type { BlogBrief } from "@/types/api";

const BlogSection = () => {
  const { isDisabled } = useConfig();
  const { data: blogs, isLoading, error } = useQuery({
    queryKey: ["blogs"],
    queryFn: () => api.get<BlogBrief[]>(paths.guest.blogs),
    enabled: !isDisabled("instructor_blog"),
  });

  if (isDisabled("instructor_blog")) return null;

  const list = blogs ?? [];

  return (
    <section id="blog" className="py-20 md:py-24 bg-card/40">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-center mb-16"
        >
          <span className="text-sm font-semibold text-primary uppercase tracking-wider">Learn</span>
          <h2 className="text-3xl md:text-4xl font-display font-bold text-foreground mt-3">
            Health Insights & Articles
          </h2>
        </motion.div>

        {isLoading && (
          <div className="grid md:grid-cols-3 gap-8">
            {[1, 2, 3].map((i) => (
              <div key={i} className="bg-background rounded-2xl h-64 border border-border animate-pulse" />
            ))}
          </div>
        )}
        {error && (
          <p className="text-center text-muted-foreground">Could not load articles.</p>
        )}
        {!isLoading && !error && list.length === 0 && (
          <p className="text-center text-muted-foreground">No articles yet.</p>
        )}
        {!isLoading && list.length > 0 && (
          <div className="grid md:grid-cols-3 gap-8">
            {list.slice(0, 3).map((article, i) => (
              <motion.article
                key={article.id}
                initial={{ opacity: 0, y: 24 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.1 }}
                className="bg-background rounded-2xl overflow-hidden shadow-card border border-border group hover:shadow-card-hover transition-all duration-300"
              >
                <Link to={`/blog/${article.id}`} className="block">
                  <div className="overflow-hidden">
                    <img
                      src={article.image ?? ""}
                      alt={article.title}
                      className="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500 bg-muted"
                      loading="lazy"
                    />
                  </div>
                  <div className="p-6">
                    <h3 className="font-sans text-lg font-bold text-foreground mb-2 leading-snug line-clamp-2">
                      {article.title}
                    </h3>
                    {article.category && (
                      <p className="text-sm text-muted-foreground mb-2">{article.category}</p>
                    )}
                    <span className="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:text-foreground transition-colors">
                      Read More <ArrowRight size={14} />
                    </span>
                  </div>
                </Link>
              </motion.article>
            ))}
          </div>
        )}
      </div>
    </section>
  );
};

export default BlogSection;

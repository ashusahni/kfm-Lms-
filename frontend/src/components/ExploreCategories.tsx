import { useQuery } from "@tanstack/react-query";
import { Link } from "react-router-dom";
import { motion } from "framer-motion";
import { api } from "@/lib/api";
import { paths } from "@/constants/api-paths";

const DEFAULT_CATEGORIES = [
  { title: "Yoga & Flexibility", slug: "yoga" },
  { title: "Strength Training", slug: "strength" },
  { title: "Cardio & HIIT", slug: "cardio" },
  { title: "Nutrition & Diet", slug: "nutrition" },
  { title: "Wellness & Recovery", slug: "wellness" },
  { title: "Weight Management", slug: "weight-management" },
];

type CategoryItem = { id: number; title: string; slug?: string };

const ExploreCategories = () => {
  const { data: categories, isLoading } = useQuery({
    queryKey: ["trend-categories"],
    queryFn: () => api.get<CategoryItem[]>(paths.guest.trendCategories),
  });

  const list = Array.isArray(categories) ? categories.slice(0, 12) : [];
  const display = list.length > 0 ? list : DEFAULT_CATEGORIES.map((c, i) => ({ id: i, title: c.title, slug: c.slug }));

  return (
    <section className="py-20 md:py-24 bg-gradient-hero">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="mb-12"
        >
          <span className="text-sm font-semibold text-primary uppercase tracking-wider">Categories</span>
          <h2 className="text-2xl md:text-3xl font-bold text-foreground tracking-tight mt-2 mb-2">
            Explore by focus
          </h2>
          <p className="text-base text-muted-foreground max-w-xl">
            Find the right fitness program for your goals—yoga, strength, nutrition, and more.
          </p>
        </motion.div>
        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
          {isLoading
            ? DEFAULT_CATEGORIES.map((c, i) => (
                <div
                  key={i}
                  className="h-28 rounded-2xl bg-card border border-border animate-pulse shadow-card"
                />
              ))
            : display.map((cat, i) => (
                <motion.div
                  key={cat.id ?? i}
                  initial={{ opacity: 0, y: 16 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ delay: i * 0.03 }}
                >
                  <Link
                    to={cat.slug ? `/programs?category=${cat.slug}` : "/programs"}
                    className="group block p-5 rounded-2xl bg-card border border-border text-center font-semibold text-foreground shadow-card hover:border-primary/50 hover:shadow-card-hover hover:text-primary transition-all duration-300 hover:bg-primary/[0.03]"
                  >
                    <span className="group-hover:underline decoration-2 underline-offset-2">{cat.title}</span>
                  </Link>
                </motion.div>
              ))}
        </div>
      </div>
    </section>
  );
};

export default ExploreCategories;

import { motion } from "framer-motion";
import { Link } from "react-router-dom";
import { useConfig } from "@/context/ConfigContext";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Sparkles } from "lucide-react";

const HeroSection = () => {
  const { t } = useConfig();
  const programsLabel = t("courses");

  return (
    <section className="relative min-h-[90vh] flex items-center overflow-hidden bg-gradient-fitness-hero pt-24 pb-16">
      {/* Gradient orbs */}
      <div className="absolute inset-0 z-0 overflow-hidden">
        <div className="absolute top-1/4 -left-20 w-[480px] h-[480px] rounded-full bg-white/10 blur-[100px] animate-pulse-soft" />
        <div className="absolute bottom-1/4 -right-20 w-[400px] h-[400px] rounded-full bg-primary/20 blur-[80px] animate-pulse-soft" style={{ animationDelay: "1s" }} />
        <div className="absolute inset-0 bg-gradient-to-b from-[hsl(152,50%,28%)]/95 to-[hsl(165,45%,18%)]/95" aria-hidden />
      </div>

      <div className="section-container relative z-10">
        <div className="grid lg:grid-cols-2 gap-14 lg:gap-16 items-center">
          <motion.div
            initial={{ opacity: 0, y: 28 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, ease: [0.22, 1, 0.36, 1] }}
            className="max-w-xl"
          >
            <motion.span
              initial={{ opacity: 0, x: -12 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: 0.2, duration: 0.4 }}
              className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/15 backdrop-blur-sm text-white/95 text-sm font-semibold mb-6 border border-white/10"
            >
              <Sparkles className="w-4 h-4 text-amber-200" />
              Fitness Programs & Coaching
            </motion.span>
            <h1 className="text-4xl sm:text-5xl lg:text-[3.25rem] font-bold text-white leading-[1.1] mb-5 tracking-tight">
              Transform Your Fitness. Live Healthier.
            </h1>
            <p className="text-xl text-white/95 font-medium mb-3">
              Move more. Eat better. Build lasting habits.
            </p>
            <p className="text-lg text-white/85 leading-relaxed mb-8 max-w-[520px]">
              Join structured fitness programs with daily coaching, nutrition guidance, and live sessions. Designed for real results—whether you're starting out or leveling up.
            </p>
            <div className="flex flex-wrap gap-3 mb-8">
              <Link to="/register">
                <Button
                  size="lg"
                  className="bg-white text-[hsl(152,55%,28%)] hover:bg-white/95 font-semibold rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-[1.02]"
                >
                  Join a Program
                </Button>
              </Link>
              <Link to="/programs">
                <Button
                  size="lg"
                  variant="outline"
                  className="border-2 border-white/70 text-white bg-white/5 backdrop-blur-sm hover:bg-white/15 font-semibold rounded-xl transition-all duration-300"
                >
                  Explore {programsLabel}
                </Button>
              </Link>
            </div>
            <motion.form
              initial={{ opacity: 0, y: 12 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.4, duration: 0.4 }}
              action="/programs"
              method="get"
              className="max-w-[440px]"
            >
              <div className="flex rounded-xl bg-white/95 shadow-xl overflow-hidden ring-2 ring-white/30">
                <Input
                  name="search"
                  placeholder="Find your program—yoga, strength, nutrition..."
                  className="border-0 flex-1 rounded-none py-6 text-base focus-visible:ring-0 bg-transparent"
                />
                <Button type="submit" className="bg-fitness-primary hover:bg-fitness-primary-dark text-white rounded-none px-6 font-semibold shrink-0">
                  Search
                </Button>
              </div>
            </motion.form>
          </motion.div>
          <motion.div
            initial={{ opacity: 0, x: 32 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.6, delay: 0.25, ease: [0.22, 1, 0.36, 1] }}
            className="hidden lg:flex justify-center items-center"
          >
            <div className="relative">
              <div className="absolute -inset-4 rounded-3xl bg-white/10 blur-2xl" />
              <img
                src="https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=600&q=80"
                alt="Fitness training"
                className="relative rounded-2xl shadow-2xl max-h-[400px] object-cover ring-2 ring-white/30"
              />
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};

export default HeroSection;

import { motion } from "framer-motion";
import { Link } from "react-router-dom";
import { Calendar, Dumbbell, ClipboardCheck, Video } from "lucide-react";
import { useConfig } from "@/context/ConfigContext";
import { Button } from "@/components/ui/button";

const steps = [
  {
    icon: Calendar,
    title: "Pick a program",
    description: "Choose a fitness program that matches your goals—yoga, strength, nutrition, or wellness.",
  },
  {
    icon: Dumbbell,
    title: "Follow your plan",
    description: "Get daily workouts, meal guidance, and challenges designed by expert coaches.",
  },
  {
    icon: ClipboardCheck,
    title: "Track progress",
    description: "Log your activity, adherence, and results so you stay accountable and see change.",
  },
  {
    icon: Video,
    title: "Join live sessions",
    description: "Attend live coaching and Q&A sessions to stay motivated and get personalized support.",
  },
];

const HowItWorks = () => {
  const { t } = useConfig();
  return (
    <section className="py-20 md:py-24 bg-card/50 border-y border-border/60">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-center mb-14"
        >
          <span className="text-sm font-semibold text-primary uppercase tracking-wider">Process</span>
          <h2 className="text-3xl md:text-4xl font-bold text-foreground tracking-tight mt-2">
            How it works
          </h2>
          <p className="text-muted-foreground mt-3 max-w-xl mx-auto">
            Join a program, follow your plan, and transform your fitness with structure and support.
          </p>
        </motion.div>
        <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 relative">
          {/* Connector line (desktop) */}
          <div className="hidden lg:block absolute top-16 left-[12.5%] right-[12.5%] h-0.5 bg-gradient-to-r from-transparent via-primary/30 to-transparent" aria-hidden />

          {steps.map((step, i) => (
            <motion.div
              key={step.title}
              initial={{ opacity: 0, y: 24 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: i * 0.08 }}
              className="relative"
            >
              <div className="h-full rounded-2xl border border-border bg-card p-6 shadow-card hover:shadow-card-hover hover:border-primary/20 transition-all duration-300 text-center">
                <div className="relative inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-primary/10 mb-4">
                  <step.icon className="w-7 h-7 text-primary" />
                  <span className="absolute -top-1.5 -right-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-primary text-primary-foreground text-xs font-bold">
                    {i + 1}
                  </span>
                </div>
                <h3 className="text-lg font-semibold text-foreground mb-2">{step.title}</h3>
                <p className="text-sm text-muted-foreground leading-relaxed">{step.description}</p>
              </div>
            </motion.div>
          ))}
        </div>
        <div className="text-center mt-12">
          <Link to="/register">
            <Button size="lg" className="bg-gradient-cta text-primary-foreground font-semibold rounded-xl px-8 shadow-lg shadow-primary/20 hover:shadow-primary/30 transition-all">
              Join a program
            </Button>
          </Link>
        </div>
      </div>
    </section>
  );
};

export default HowItWorks;

import { motion } from "framer-motion";
import { Calendar, Users, Timer } from "lucide-react";

const batches = [
  { program: "Clean Eating Reset", startDate: "Mar 15, 2026", seats: 8, totalSeats: 30, daysLeft: 19 },
  { program: "Strength & Sculpt", startDate: "Apr 1, 2026", seats: 15, totalSeats: 25, daysLeft: 36 },
  { program: "Holistic PCOS Care", startDate: "Mar 20, 2026", seats: 4, totalSeats: 20, daysLeft: 24 },
];

const UpcomingBatches = () => {
  return (
    <section id="cohorts" className="py-20 md:py-24 bg-card/50">
      <div className="section-container">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-center mb-16"
        >
          <span className="text-sm font-semibold text-accent uppercase tracking-wider">Limited Spots</span>
          <h2 className="text-3xl md:text-4xl font-display font-bold text-foreground mt-3">
            Upcoming Cohorts
          </h2>
        </motion.div>

        <div className="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
          {batches.map((batch, i) => {
            const seatsPercent = ((batch.totalSeats - batch.seats) / batch.totalSeats) * 100;
            const isUrgent = batch.seats <= 8;
            return (
              <motion.div
                key={batch.program}
                initial={{ opacity: 0, y: 24 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.1 }}
                className={`bg-background rounded-2xl p-6 shadow-card border transition-all duration-300 hover:shadow-card-hover ${isUrgent ? "border-accent/40" : "border-border"}`}
              >
                {isUrgent && (
                  <span className="inline-block px-3 py-1 rounded-full bg-accent/10 text-accent text-xs font-bold mb-4">
                    🔥 Filling Fast
                  </span>
                )}
                <h3 className="font-sans text-lg font-bold text-foreground mb-4">{batch.program}</h3>
                <div className="space-y-3 text-sm text-muted-foreground">
                  <div className="flex items-center gap-2">
                    <Calendar size={16} className="text-primary" />
                    Starts: <span className="font-semibold text-foreground">{batch.startDate}</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <Users size={16} className="text-primary" />
                    <span className="font-semibold text-foreground">{batch.seats} seats</span> remaining
                  </div>
                  <div className="flex items-center gap-2">
                    <Timer size={16} className="text-accent" />
                    <span className="font-semibold text-foreground">{batch.daysLeft} days</span> until start
                  </div>
                </div>
                {/* Seats bar */}
                <div className="mt-4 w-full h-2 rounded-full bg-muted">
                  <div
                    className="h-full rounded-full bg-gradient-accent transition-all"
                    style={{ width: `${seatsPercent}%` }}
                  />
                </div>
                <button className="mt-5 w-full py-2.5 rounded-lg bg-gradient-cta text-primary-foreground text-sm font-semibold hover:opacity-90 transition-opacity">
                  Reserve Spot
                </button>
              </motion.div>
            );
          })}
        </div>
      </div>
    </section>
  );
};

export default UpcomingBatches;

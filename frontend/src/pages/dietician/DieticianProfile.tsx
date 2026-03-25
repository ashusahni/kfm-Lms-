import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { progressService } from "@/services/progress";
import { User, Award, TrendingUp } from "lucide-react";
import { motion } from "framer-motion";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import { Progress } from "@/components/ui/progress";

export default function DieticianProfile() {
  const { data: quickInfo, isLoading } = useQuery({
    queryKey: ["panel-quick-info"],
    queryFn: () => progressService.getQuickInfo(),
  });

  const badges = quickInfo?.badges;
  const nextBadge = typeof badges?.next_badge === "string" ? badges.next_badge : null;
  const percent = typeof badges?.percent === "number" ? badges.percent : 0;
  const earned = typeof badges?.earned === "string" ? badges.earned : null;

  return (
    <div className="space-y-8">
      <motion.div
        initial={{ opacity: 0, y: 8 }}
        animate={{ opacity: 1, y: 0 }}
        className="relative rounded-2xl bg-gradient-to-br from-primary/15 via-primary/5 to-transparent dark:from-primary/20 dark:via-primary/10 p-8 border border-primary/10"
      >
        <div className="flex items-center gap-4">
          <div className="flex h-14 w-14 items-center justify-center rounded-xl bg-primary/10 text-primary">
            <User className="h-7 w-7" />
          </div>
          <div>
            <h1 className="text-3xl font-display font-bold tracking-tight text-foreground">
              Profile & badges
            </h1>
            <p className="mt-1 text-muted-foreground">
              Your progress and badges. Update your profile from the student panel.
            </p>
          </div>
        </div>
      </motion.div>

      {isLoading && (
        <div className="grid gap-6 md:grid-cols-2">
          <Skeleton className="h-40 rounded-xl" />
          <Skeleton className="h-40 rounded-xl" />
        </div>
      )}

      {!isLoading && (
        <div className="grid gap-8 md:grid-cols-2">
          <Card className="border border-border overflow-hidden">
            <CardContent className="pt-6">
              <h2 className="text-lg font-semibold text-foreground flex items-center gap-2 mb-4">
                <Award className="h-5 w-5 text-primary" />
                Badges
              </h2>
              {earned && (
                <p className="text-sm text-muted-foreground mb-2">
                  Current: <span className="font-medium text-foreground">{earned}</span>
                </p>
              )}
              {nextBadge && (
                <>
                  <p className="text-sm text-muted-foreground mb-2">
                    Next: <span className="font-medium text-foreground">{nextBadge}</span>
                  </p>
                  <Progress value={percent} className="h-2" />
                  <p className="text-xs text-muted-foreground mt-1">{percent}% progress</p>
                </>
              )}
              {!nextBadge && !earned && (
                <p className="text-sm text-muted-foreground">No badge data yet.</p>
              )}
            </CardContent>
          </Card>

          <Card className="border border-border overflow-hidden">
            <CardContent className="pt-6">
              <h2 className="text-lg font-semibold text-foreground flex items-center gap-2 mb-4">
                <TrendingUp className="h-5 w-5 text-primary" />
                Quick stats
              </h2>
              <ul className="space-y-2 text-sm text-muted-foreground">
                {quickInfo?.pendingAppointments != null && (
                  <li>Pending appointments: <span className="font-medium text-foreground">{quickInfo.pendingAppointments}</span></li>
                )}
                {quickInfo?.balance != null && (
                  <li>Balance: <span className="font-medium text-foreground">{quickInfo.balance}</span></li>
                )}
              </ul>
              <Link to="/panel" className="inline-block mt-4">
                <Button variant="outline" size="sm">
                  View as student
                </Button>
              </Link>
            </CardContent>
          </Card>
        </div>
      )}
    </div>
  );
}

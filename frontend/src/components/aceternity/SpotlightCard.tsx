import * as React from "react";
import { cn } from "@/lib/utils";

interface SpotlightCardProps extends React.HTMLAttributes<HTMLDivElement> {
  gradient?: "primary" | "accent" | "neutral";
}

export function SpotlightCard({
  className,
  children,
  gradient = "primary",
  ...props
}: SpotlightCardProps) {
  const gradientClass =
    gradient === "primary"
      ? "from-primary/20 via-primary/5 to-transparent"
      : gradient === "accent"
        ? "from-accent/20 via-accent/5 to-transparent"
        : "from-muted/50 via-muted/20 to-transparent";

  return (
    <div
      className={cn(
        "group relative overflow-hidden rounded-2xl border border-border bg-card/80 p-6 shadow-card backdrop-blur-sm transition-all duration-300 hover:shadow-card-hover",
        className
      )}
      {...props}
    >
      <div
        className={cn(
          "pointer-events-none absolute -inset-px opacity-0 transition-opacity duration-500 group-hover:opacity-100",
          "rounded-2xl bg-gradient-to-br",
          gradientClass
        )}
        style={{
          mask: "linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0)",
          maskComposite: "exclude",
          WebkitMaskComposite: "xor",
          padding: "1px",
        }}
      />
      <div className="relative z-10">{children}</div>
    </div>
  );
}

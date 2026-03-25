import * as React from "react";
import { cn } from "@/lib/utils";

export function BorderBeam({ className, size = 200, duration = 8 }: { className?: string; size?: number; duration?: number }) {
  return (
    <div
      className={cn("pointer-events-none absolute inset-0 overflow-hidden rounded-[inherit]", className)}
      aria-hidden
    >
      <div
        className="absolute left-1/2 top-1/2 h-[200%] w-[200%] -translate-x-1/2 -translate-y-1/2 animate-border-beam"
        style={{
          background: `conic-gradient(from 0deg, transparent 0deg 180deg, hsl(var(--primary) / 0.5) 180deg 270deg, transparent 270deg)`,
          animationDuration: `${duration}s`,
        }}
      />
    </div>
  );
}

interface CardWithBorderBeamProps extends React.HTMLAttributes<HTMLDivElement> {
  beamColor?: string;
}

export function CardWithBorderBeam({ className, children, ...props }: CardWithBorderBeamProps) {
  return (
    <div className={cn("relative rounded-2xl border border-border bg-card overflow-hidden", className)} {...props}>
      <BorderBeam size={260} duration={8} />
      <div className="relative z-10 flex min-h-full flex-col rounded-2xl bg-card m-[1px] overflow-hidden">
        {children}
      </div>
    </div>
  );
}

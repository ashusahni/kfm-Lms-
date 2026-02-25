import { cn } from "@/lib/utils";

type Variant = "grid" | "dots" | "gradient";

interface AnimatedBackgroundProps {
  variant?: Variant;
  className?: string;
  children?: React.ReactNode;
}

export function AnimatedBackground({
  variant = "grid",
  className,
  children,
}: AnimatedBackgroundProps) {
  return (
    <div className={cn("relative min-h-screen w-full overflow-hidden", className)}>
      {variant === "grid" && <GridBackground />}
      {variant === "dots" && <DotsBackground />}
      {variant === "gradient" && <GradientBackground />}
      {children && (
        <div className="relative z-10 flex min-h-screen w-full items-center justify-center">
          {children}
        </div>
      )}
    </div>
  );
}

function GridBackground() {
  return (
    <div className="absolute inset-0 bg-background">
      <div
        className="absolute inset-0 opacity-[0.4]"
        style={{
          backgroundImage: `
            linear-gradient(to right, hsl(var(--border)) 1px, transparent 1px),
            linear-gradient(to bottom, hsl(var(--border)) 1px, transparent 1px)
          `,
          backgroundSize: "64px 64px",
          animation: "grid-move 20s linear infinite",
        }}
      />
      <div className="absolute inset-0 bg-gradient-to-br from-primary/[0.03] via-transparent to-accent/[0.03]" />
    </div>
  );
}

function DotsBackground() {
  return (
    <div className="absolute inset-0 bg-background">
      <div
        className="absolute inset-0 opacity-60"
        style={{
          backgroundImage: `radial-gradient(circle at 1px 1px, hsl(var(--border)) 1px, transparent 0)`,
          backgroundSize: "40px 40px",
        }}
      />
      <div className="absolute inset-0 bg-gradient-to-br from-primary/[0.04] via-transparent to-accent/[0.04]" />
    </div>
  );
}

function GradientBackground() {
  return (
    <div className="absolute inset-0 bg-background">
      <div className="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-primary/10 blur-3xl animate-pulse-soft" />
      <div className="absolute bottom-20 -left-20 h-72 w-72 rounded-full bg-accent/10 blur-3xl animate-pulse-soft" style={{ animationDelay: "1s" }} />
      <div className="absolute top-1/2 left-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full bg-primary/5 blur-3xl" />
    </div>
  );
}

import { cn } from "@/lib/utils";

interface ProgressBarProps {
  currentStep: number;
  totalSteps: number;
  className?: string;
}

export function ProgressBar({ currentStep, totalSteps, className }: ProgressBarProps) {
  const percent = totalSteps > 0 ? Math.round((currentStep / totalSteps) * 100) : 0;
  return (
    <div className={cn("w-full", className)}>
      <div className="flex justify-between text-sm text-muted-foreground mb-1">
        <span>Step {currentStep} of {totalSteps}</span>
        <span>{percent}%</span>
      </div>
      <div className="h-2 w-full rounded-full bg-muted overflow-hidden">
        <div
          className="h-full bg-primary transition-all duration-300 ease-out rounded-full"
          style={{ width: `${percent}%` }}
        />
      </div>
    </div>
  );
}

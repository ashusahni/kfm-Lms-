import { ReactNode } from "react";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { cn } from "@/lib/utils";

export interface RadioOption {
  value: string;
  label: string;
  description?: ReactNode;
}

interface RadioGroupFieldProps {
  value?: string;
  onChange?: (value: string) => void;
  options: RadioOption[];
  className?: string;
}

export function RadioGroupField({ value, onChange, options, className }: RadioGroupFieldProps) {
  return (
    <RadioGroup
      value={value}
      onValueChange={onChange}
      className={cn("grid grid-cols-1 sm:grid-cols-3 gap-3", className)}
    >
      {options.map((opt) => (
        <label
          key={opt.value}
          className={cn(
            "flex items-center gap-3 rounded-lg border p-3 cursor-pointer transition-colors",
            "hover:bg-accent/50",
            value === opt.value && "border-primary bg-primary/10",
          )}
        >
          <RadioGroupItem value={opt.value} />
          <div className="flex flex-col">
            <span className="text-sm font-medium">{opt.label}</span>
            {opt.description && (
              <span className="text-xs text-muted-foreground">{opt.description}</span>
            )}
          </div>
        </label>
      ))}
    </RadioGroup>
  );
}


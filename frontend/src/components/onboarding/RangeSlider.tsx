import * as React from "react";
import { cn } from "@/lib/utils";

interface RangeSliderProps extends React.InputHTMLAttributes<HTMLInputElement> {
  labelFormatter?: (value: number) => string;
}

export function RangeSlider({
  className,
  labelFormatter,
  min = 0,
  max = 100,
  step = 1,
  value,
  onChange,
  ...rest
}: RangeSliderProps) {
  const numericValue = typeof value === "string" ? Number(value) : (value as number | undefined);

  return (
    <div className={cn("space-y-1", className)}>
      <div className="flex items-center justify-between text-xs text-muted-foreground">
        <span>{typeof min === "number" ? min : Number(min)}</span>
        <span>
          {numericValue != null && !Number.isNaN(numericValue)
            ? labelFormatter
              ? labelFormatter(numericValue)
              : numericValue
            : "-"}
        </span>
        <span>{typeof max === "number" ? max : Number(max)}</span>
      </div>
      <input
        type="range"
        min={min}
        max={max}
        step={step}
        value={value}
        onChange={onChange}
        className={cn(
          "w-full cursor-pointer appearance-none bg-transparent",
          "[&::-webkit-slider-runnable-track]:h-1 [&::-webkit-slider-runnable-track]:rounded-full [&::-webkit-slider-runnable-track]:bg-muted",
          "[&::-webkit-slider-thumb]:-mt-1 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-primary",
          "[&::-moz-range-track]:h-1 [&::-moz-range-track]:rounded-full [&::-moz-range-track]:bg-muted",
          "[&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:appearance-none [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:bg-primary",
        )}
        {...rest}
      />
    </div>
  );
}


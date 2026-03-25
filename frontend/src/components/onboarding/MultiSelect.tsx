import { cn } from "@/lib/utils";

export interface MultiSelectOption {
  id: number;
  name: string;
}

interface MultiSelectProps {
  options: MultiSelectOption[];
  value: number[];
  onChange: (ids: number[]) => void;
  label?: string;
  className?: string;
  columns?: 2 | 3 | 4;
}

export function MultiSelect({
  options,
  value,
  onChange,
  label,
  className,
  columns = 2,
}: MultiSelectProps) {
  const toggle = (id: number) => {
    if (value.includes(id)) {
      onChange(value.filter((x) => x !== id));
    } else {
      onChange([...value, id]);
    }
  };

  return (
    <div className={cn("space-y-3", className)}>
      {label && (
        <p className="text-sm font-medium text-foreground">{label}</p>
      )}
      <div
        className={cn(
          "grid gap-2",
          columns === 2 && "grid-cols-1 sm:grid-cols-2",
          columns === 3 && "grid-cols-1 sm:grid-cols-2 md:grid-cols-3",
          columns === 4 && "grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4",
        )}
      >
        {options.map((opt) => (
          <label
            key={opt.id}
            className={cn(
              "flex items-center gap-3 rounded-lg border p-3 cursor-pointer transition-colors",
              "hover:bg-accent/50",
              value.includes(opt.id) && "border-primary bg-primary/10",
            )}
          >
            <input
              type="checkbox"
              checked={value.includes(opt.id)}
              onChange={() => toggle(opt.id)}
              className="h-4 w-4 rounded border-input"
            />
            <span className="text-sm font-medium">{opt.name}</span>
          </label>
        ))}
      </div>
    </div>
  );
}

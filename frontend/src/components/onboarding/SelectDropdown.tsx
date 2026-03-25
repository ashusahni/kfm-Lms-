import { ReactNode } from "react";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { cn } from "@/lib/utils";

export interface SelectOption {
  value: string;
  label: string;
}

interface SelectDropdownProps {
  value?: string;
  onChange?: (value: string) => void;
  options: SelectOption[];
  placeholder?: string;
  className?: string;
  disabled?: boolean;
  children?: ReactNode;
}

export function SelectDropdown({
  value,
  onChange,
  options,
  placeholder,
  className,
  disabled,
}: SelectDropdownProps) {
  return (
    <Select value={value} onValueChange={onChange} disabled={disabled}>
      <SelectTrigger className={cn(className)}>
        <SelectValue placeholder={placeholder} />
      </SelectTrigger>
      <SelectContent>
        {options.map((opt) => (
          <SelectItem key={opt.value} value={opt.value}>
            {opt.label}
          </SelectItem>
        ))}
      </SelectContent>
    </Select>
  );
}


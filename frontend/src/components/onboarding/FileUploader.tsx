import { useCallback } from "react";
import { cn } from "@/lib/utils";

export type FileUploadType = "blood_report" | "medical_report" | "body_photos" | "medication_prescription";

interface FileUploaderProps {
  type: FileUploadType;
  label: string;
  accept?: string;
  multiple?: boolean;
  value: File | File[] | null;
  onChange: (files: File | File[] | null) => void;
  className?: string;
}

export function FileUploader({
  type,
  label,
  accept = "image/*,.pdf",
  multiple = false,
  value,
  onChange,
  className,
}: FileUploaderProps) {
  const handleChange = useCallback(
    (e: React.ChangeEvent<HTMLInputElement>) => {
      const files = e.target.files;
      if (!files?.length) {
        onChange(null);
        return;
      }
      if (multiple) {
        onChange(Array.from(files));
      } else {
        onChange(files[0]);
      }
    },
    [multiple, onChange],
  );

  const displayValue = value
    ? Array.isArray(value)
      ? `${value.length} file(s) selected`
      : value.name
    : "No file chosen";

  return (
    <div className={cn("space-y-2", className)}>
      <label className="text-sm font-medium text-foreground">{label}</label>
      <div className="flex flex-col sm:flex-row gap-2 items-start">
        <label className="flex-1 w-full min-w-0 flex items-center gap-2 rounded-md border border-input bg-background px-3 py-2 text-sm cursor-pointer hover:bg-accent/50 transition-colors">
          <input
            type="file"
            accept={accept}
            multiple={multiple}
            onChange={handleChange}
            className="sr-only"
          />
          <span className="truncate text-muted-foreground">{displayValue}</span>
        </label>
      </div>
    </div>
  );
}

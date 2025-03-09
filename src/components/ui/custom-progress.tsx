
import * as React from "react";
import { cn } from "@/lib/utils";

interface CustomProgressProps {
  value: number;
  max?: number;
  className?: string;
  bgClassName?: string;
  fillClassName?: string;
  showValue?: boolean;
  size?: "sm" | "md" | "lg";
  label?: string;
  animated?: boolean;
}

const CustomProgress = React.forwardRef<HTMLDivElement, CustomProgressProps>(
  (
    {
      value,
      max = 100,
      className,
      bgClassName,
      fillClassName,
      showValue = false,
      size = "md",
      label,
      animated = false,
      ...props
    },
    ref
  ) => {
    const percentage = Math.min(100, Math.max(0, (value / max) * 100));
    
    const sizeClasses = {
      sm: "h-1.5",
      md: "h-2.5",
      lg: "h-4",
    };

    return (
      <div className={cn("w-full space-y-1.5", className)} ref={ref} {...props}>
        {(label || showValue) && (
          <div className="flex items-center justify-between text-xs">
            {label && <span className="text-muted-foreground">{label}</span>}
            {showValue && <span className="font-medium">{percentage.toFixed(0)}%</span>}
          </div>
        )}
        <div
          className={cn(
            "overflow-hidden rounded-full bg-secondary",
            sizeClasses[size],
            bgClassName
          )}
        >
          <div
            className={cn(
              "h-full rounded-full bg-primary transition-all",
              animated && "animate-pulse",
              fillClassName
            )}
            style={{ width: `${percentage}%` }}
          />
        </div>
      </div>
    );
  }
);

CustomProgress.displayName = "CustomProgress";

export { CustomProgress };

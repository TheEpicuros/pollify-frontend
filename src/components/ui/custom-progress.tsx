
import * as React from "react";
import { cn } from "@/lib/utils";

interface CustomProgressProps {
  value: number;
  max?: number;
  className?: string;
  bgClassName?: string;
  fillClassName?: string;
  showValue?: boolean;
  size?: "xs" | "sm" | "md" | "lg";
  label?: string;
  animated?: boolean;
  gradient?: boolean;
  labelPosition?: "top" | "side";
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
      gradient = false,
      labelPosition = "top",
      ...props
    },
    ref
  ) => {
    const percentage = Math.min(100, Math.max(0, (value / max) * 100));
    
    const sizeClasses = {
      xs: "h-1",
      sm: "h-1.5",
      md: "h-2.5",
      lg: "h-4",
    };

    return (
      <div className={cn("w-full", className)} ref={ref} {...props}>
        {(label || showValue) && (
          <div className={cn(
            "flex text-xs mb-1.5",
            labelPosition === "top" ? "items-center justify-between" : "flex-col gap-1"
          )}>
            {label && <span className="text-muted-foreground font-medium">{label}</span>}
            {showValue && <span className={cn(
              "font-semibold",
              labelPosition === "side" && "text-right"
            )}>{percentage.toFixed(0)}%</span>}
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
              "h-full rounded-full transition-all duration-700 ease-in-out",
              animated && "animate-pulse",
              gradient 
                ? "bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"
                : "bg-primary",
              fillClassName
            )}
            style={{ 
              width: `${percentage}%`,
              transition: "width 1s cubic-bezier(0.4, 0, 0.2, 1)" 
            }}
          />
        </div>
      </div>
    );
  }
);

CustomProgress.displayName = "CustomProgress";

export { CustomProgress };


import React from "react";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Slider } from "@/components/ui/slider";
import { HelpCircle } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";

interface RatingScalePollFormProps {
  ratingScale: [number, number];
  handleRatingScaleChange: (values: number[]) => void;
}

const RatingScalePollForm: React.FC<RatingScalePollFormProps> = ({
  ratingScale,
  handleRatingScaleChange,
}) => {
  return (
    <div className="space-y-4 mt-4">
      <div className="flex flex-col space-y-3">
        <Label className="flex items-center">
          Rating Scale Range
          <TooltipProvider>
            <Tooltip>
              <TooltipTrigger asChild>
                <span className="ml-2 cursor-help">
                  <HelpCircle size={14} className="text-muted-foreground" />
                </span>
              </TooltipTrigger>
              <TooltipContent>
                <p className="w-[200px]">Set the minimum and maximum values for your rating scale</p>
              </TooltipContent>
            </Tooltip>
          </TooltipProvider>
        </Label>
        <div className="pt-4 pb-2 px-4">
          <Slider
            value={[ratingScale[0], ratingScale[1]]}
            min={1}
            max={10}
            step={1}
            onValueChange={handleRatingScaleChange}
          />
        </div>
        <div className="flex justify-between text-sm text-muted-foreground px-2">
          <span>Min: {ratingScale[0]}</span>
          <span>Max: {ratingScale[1]}</span>
        </div>
      </div>
      <div className="flex items-center space-y-0 space-x-2 mt-4">
        <Label htmlFor="low-label">Low Label (Optional)</Label>
        <Input id="low-label" placeholder="Poor" className="max-w-[150px]" />
      </div>
      <div className="flex items-center space-y-0 space-x-2">
        <Label htmlFor="high-label">High Label (Optional)</Label>
        <Input id="high-label" placeholder="Excellent" className="max-w-[150px]" />
      </div>
    </div>
  );
};

export default RatingScalePollForm;

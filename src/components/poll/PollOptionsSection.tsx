
import { Plus } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import PollOptionItem from "./PollOptionItem";

interface PollOptionsSectionProps {
  options: string[];
  handleAddOption: () => void;
  handleRemoveOption: (index: number) => void;
  handleOptionChange: (index: number, value: string) => void;
}

const PollOptionsSection = ({
  options,
  handleAddOption,
  handleRemoveOption,
  handleOptionChange,
}: PollOptionsSectionProps) => {
  return (
    <div className="space-y-3">
      <div className="flex items-center justify-between">
        <label className="block text-sm font-medium">
          Poll Options <span className="text-destructive">*</span>
        </label>
        <span className="text-xs text-muted-foreground">
          {options.length}/10 options
        </span>
      </div>

      <div className="space-y-3">
        <AnimatePresence mode="popLayout">
          {options.map((option, index) => (
            <PollOptionItem
              key={index}
              option={option}
              index={index}
              handleOptionChange={handleOptionChange}
              handleRemoveOption={handleRemoveOption}
              canRemove={options.length > 2}
            />
          ))}
        </AnimatePresence>
      </div>

      <button
        type="button"
        onClick={handleAddOption}
        disabled={options.length >= 10}
        className="w-full py-2 px-4 border border-dashed rounded-lg text-sm font-medium flex items-center justify-center gap-2 text-muted-foreground hover:text-foreground hover:border-foreground/30 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <Plus size={16} />
        Add Option
      </button>
    </div>
  );
};

export default PollOptionsSection;

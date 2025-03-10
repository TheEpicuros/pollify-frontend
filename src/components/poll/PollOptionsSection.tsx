
import { motion, AnimatePresence } from "framer-motion";
import PollOptionItem from "./PollOptionItem";
import PollOptionsHeader from "./PollOptionsHeader";
import AddOptionButton from "./AddOptionButton";

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
      <PollOptionsHeader optionsCount={options.length} />

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

      <AddOptionButton
        handleAddOption={handleAddOption}
        disabled={options.length >= 10}
      />
    </div>
  );
};

export default PollOptionsSection;

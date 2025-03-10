
import { motion, AnimatePresence } from "framer-motion";
import PollOptionItem from "./PollOptionItem";
import PollOptionsHeader from "./PollOptionsHeader";
import AddOptionButton from "./AddOptionButton";

interface PollOptionsSectionProps {
  options: string[];
  handleAddOption: () => void;
  handleRemoveOption: (index: number) => void;
  handleOptionChange: (index: number, value: string) => void;
  canRemoveOption?: boolean;
  showImages?: boolean;
  optionImages?: string[];
  handleImageUpload?: (index: number, file: File) => void;
  showCorrectAnswers?: boolean;
  correctAnswers?: string[];
  onToggleCorrectAnswer?: (index: string) => void;
}

const PollOptionsSection = ({
  options,
  handleAddOption,
  handleRemoveOption,
  handleOptionChange,
  canRemoveOption = true,
  showImages = false,
  optionImages = [],
  handleImageUpload,
  showCorrectAnswers = false,
  correctAnswers = [],
  onToggleCorrectAnswer,
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
              canRemove={canRemoveOption && options.length > 2}
              imageUrl={showImages ? optionImages[index] : undefined}
              onImageUpload={showImages ? handleImageUpload : undefined}
              isCorrectAnswer={showCorrectAnswers ? correctAnswers?.includes(index.toString()) : undefined}
              onToggleCorrectAnswer={showCorrectAnswers ? onToggleCorrectAnswer : undefined}
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

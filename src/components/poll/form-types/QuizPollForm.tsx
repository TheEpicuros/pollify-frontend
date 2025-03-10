
import React from "react";
import { Switch } from "@/components/ui/switch";
import { Label } from "@/components/ui/label";
import { Info } from "lucide-react";
import PollOptionsSection from "../PollOptionsSection";

interface QuizPollFormProps {
  options: string[];
  showCorrectAnswers: boolean;
  setShowCorrectAnswers: (value: boolean) => void;
  correctAnswers?: string[];
  handleAddOption: () => void;
  handleRemoveOption: (index: number) => void;
  handleOptionChange: (index: number, value: string) => void;
  toggleCorrectAnswer: (index: string) => void;
  canRemoveOption: boolean;
}

const QuizPollForm: React.FC<QuizPollFormProps> = ({
  options,
  showCorrectAnswers,
  setShowCorrectAnswers,
  correctAnswers,
  handleAddOption,
  handleRemoveOption,
  handleOptionChange,
  toggleCorrectAnswer,
  canRemoveOption,
}) => {
  return (
    <div className="space-y-4 mt-4">
      <div className="flex items-center space-x-2">
        <Switch
          id="show-correct-answers"
          checked={showCorrectAnswers}
          onCheckedChange={setShowCorrectAnswers}
        />
        <Label htmlFor="show-correct-answers" className="cursor-pointer">
          Mark correct answers
        </Label>
      </div>

      {showCorrectAnswers && (
        <div className="p-4 bg-muted/50 rounded-lg mb-4">
          <div className="flex items-start gap-3">
            <Info size={18} className="text-primary mt-0.5" />
            <p className="text-sm">
              Select which options are correct answers for your quiz. You can select multiple
              correct answers.
            </p>
          </div>
        </div>
      )}

      <PollOptionsSection
        options={options}
        handleAddOption={handleAddOption}
        handleRemoveOption={handleRemoveOption}
        handleOptionChange={handleOptionChange}
        canRemoveOption={canRemoveOption}
        showCorrectAnswers={showCorrectAnswers}
        correctAnswers={correctAnswers}
        onToggleCorrectAnswer={toggleCorrectAnswer}
      />
    </div>
  );
};

export default QuizPollForm;

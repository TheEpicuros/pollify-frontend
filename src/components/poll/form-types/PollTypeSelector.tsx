
import React from "react";
import { PollType } from "@/lib/types";
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from "@/components/ui/select";

interface PollTypeSelectorProps {
  pollType: PollType;
  onPollTypeChange: (value: string) => void;
}

const PollTypeSelector: React.FC<PollTypeSelectorProps> = ({
  pollType,
  onPollTypeChange,
}) => {
  return (
    <div className="space-y-2">
      <label htmlFor="pollType" className="block text-sm font-medium">
        Poll Type
      </label>
      <Select value={pollType || "multiple-choice"} onValueChange={onPollTypeChange}>
        <SelectTrigger id="pollType">
          <SelectValue placeholder="Select poll type" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="multiple-choice">Multiple Choice (Select One)</SelectItem>
          <SelectItem value="check-all">Multiple Answers (Select Many)</SelectItem>
          <SelectItem value="binary">Yes/No Question</SelectItem>
          <SelectItem value="rating-scale">Rating Scale</SelectItem>
          <SelectItem value="image-based">Image Based Poll</SelectItem>
          <SelectItem value="quiz">Quiz</SelectItem>
          <SelectItem value="open-ended">Open-Ended Response</SelectItem>
          <SelectItem value="ranked-choice">Ranked Choice</SelectItem>
        </SelectContent>
      </Select>

      <div className="mt-1 text-sm text-muted-foreground">
        {pollType === "multiple-choice" && "Voters can select only one option."}
        {pollType === "check-all" && "Voters can select multiple options that apply."}
        {pollType === "binary" && "Simple yes/no or either/or questions."}
        {pollType === "rating-scale" && "Ask voters to rate on a numeric scale."}
        {pollType === "image-based" && "Use images as answer options."}
        {pollType === "quiz" && "Test knowledge with right/wrong answers."}
        {pollType === "open-ended" && "Allow voters to provide text responses."}
        {pollType === "ranked-choice" && "Voters rank options in order of preference."}
      </div>
    </div>
  );
};

export default PollTypeSelector;

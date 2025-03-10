
import React from "react";
import { PollFormData } from "@/lib/types";
import PollOptionsSection from "../PollOptionsSection";
import BinaryPollForm from "../form-types/BinaryPollForm";
import RatingScalePollForm from "../form-types/RatingScalePollForm";
import OpenEndedPollForm from "../form-types/OpenEndedPollForm";
import QuizPollForm from "../form-types/QuizPollForm";

interface PollTypeContentProps {
  formData: PollFormData;
  ratingScale: [number, number];
  setRatingScale: React.Dispatch<React.SetStateAction<[number, number]>>;
  showCorrectAnswers: boolean;
  setShowCorrectAnswers: React.Dispatch<React.SetStateAction<boolean>>;
  handleAddOption: () => void;
  handleRemoveOption: (index: number) => void;
  handleOptionChange: (index: number, value: string) => void;
  toggleCorrectAnswer: (index: string) => void;
  handleRatingScaleChange: (values: number[]) => void;
  handleImageUpload?: (index: number, file: File) => void;
}

const PollTypeContent: React.FC<PollTypeContentProps> = ({
  formData,
  ratingScale,
  setRatingScale,
  showCorrectAnswers,
  setShowCorrectAnswers,
  handleAddOption,
  handleRemoveOption,
  handleOptionChange,
  toggleCorrectAnswer,
  handleRatingScaleChange,
  handleImageUpload,
}) => {
  const minOptionCount = formData.type === "binary" ? 2 : 
                        formData.type === "open-ended" ? 1 : 2;
  
  const canRemoveOption = formData.options.length > minOptionCount;

  switch (formData.type) {
    case "binary":
      return (
        <BinaryPollForm
          options={formData.options}
          handleOptionChange={handleOptionChange}
        />
      );
    
    case "rating-scale":
      return (
        <RatingScalePollForm
          ratingScale={ratingScale}
          handleRatingScaleChange={handleRatingScaleChange}
        />
      );
    
    case "open-ended":
      return <OpenEndedPollForm />;
    
    case "quiz":
      return (
        <QuizPollForm
          options={formData.options}
          showCorrectAnswers={showCorrectAnswers}
          setShowCorrectAnswers={setShowCorrectAnswers}
          correctAnswers={formData.correctAnswers}
          handleAddOption={handleAddOption}
          handleRemoveOption={handleRemoveOption}
          handleOptionChange={handleOptionChange}
          toggleCorrectAnswer={toggleCorrectAnswer}
          canRemoveOption={canRemoveOption}
        />
      );
    
    case "image-based":
      return (
        <PollOptionsSection
          options={formData.options}
          handleAddOption={handleAddOption}
          handleRemoveOption={handleRemoveOption}
          handleOptionChange={handleOptionChange}
          canRemoveOption={canRemoveOption}
          showImages={true}
          optionImages={formData.optionImages}
          handleImageUpload={handleImageUpload}
        />
      );
    
    default:
      return (
        <PollOptionsSection
          options={formData.options}
          handleAddOption={handleAddOption}
          handleRemoveOption={handleRemoveOption}
          handleOptionChange={handleOptionChange}
          canRemoveOption={canRemoveOption}
        />
      );
  }
};

export default PollTypeContent;

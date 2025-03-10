
import React, { useEffect, useState } from "react";
import { Button } from "@/components/ui/button";
import { usePollForm } from "./context";
import { toast } from "sonner";

// Import refactored components
import BasicPollFields from "./form-types/BasicPollFields";
import PollTypeSelector from "./form-types/PollTypeSelector";
import PollTypeContent from "./form-utils/PollTypeContent";
import { PollFormHandlers } from "./form-utils/PollFormHandlers";

const BasicInfoForm: React.FC = () => {
  const { formData, setFormData, setCurrentTab, getDefaultOptionsForType } = usePollForm();
  const [ratingScale, setRatingScale] = useState<[number, number]>([1, 5]);
  const [showCorrectAnswers, setShowCorrectAnswers] = useState(false);

  // Get all handler functions from our utility component
  const {
    handleMoveToNextTab,
    handleAddOptionWrapper,
    handleRemoveOptionWrapper,
    handleOptionChangeWrapper,
    handleImageUpload,
    handlePollTypeChange,
    toggleCorrectAnswer,
    handleRatingScaleChange
  } = PollFormHandlers({
    formData,
    setFormData,
    setCurrentTab,
    ratingScale,
    setRatingScale,
    getDefaultOptionsForType
  });

  useEffect(() => {
    // Initialize optionImages array if it doesn't exist for image-based polls
    if (formData.type === "image-based" && !formData.optionImages) {
      setFormData(prev => ({
        ...prev,
        optionImages: Array(prev.options.length).fill("")
      }));
    }

    // Initialize correctAnswers array for quiz polls
    if (formData.type === "quiz" && !formData.correctAnswers) {
      setFormData(prev => ({
        ...prev,
        correctAnswers: []
      }));
    }
  }, [formData.type, formData.optionImages, formData.correctAnswers, setFormData]);

  return (
    <div className="space-y-6">
      <BasicPollFields
        title={formData.title}
        description={formData.description || ""}
        onTitleChange={(value) => setFormData({ ...formData, title: value })}
        onDescriptionChange={(value) => setFormData({ ...formData, description: value })}
      />
      
      <PollTypeSelector 
        pollType={formData.type || "multiple-choice"}
        onPollTypeChange={handlePollTypeChange}
      />

      <PollTypeContent
        formData={formData}
        ratingScale={ratingScale}
        setRatingScale={setRatingScale}
        showCorrectAnswers={showCorrectAnswers}
        setShowCorrectAnswers={setShowCorrectAnswers}
        handleAddOption={handleAddOptionWrapper}
        handleRemoveOption={handleRemoveOptionWrapper}
        handleOptionChange={handleOptionChangeWrapper}
        toggleCorrectAnswer={toggleCorrectAnswer}
        handleRatingScaleChange={handleRatingScaleChange}
        handleImageUpload={handleImageUpload}
      />
      
      <div className="flex justify-end pt-4">
        <Button type="button" onClick={handleMoveToNextTab}>
          Next: Poll Settings →
        </Button>
      </div>
    </div>
  );
};

export default BasicInfoForm;

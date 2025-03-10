
import React, { useEffect, useState } from "react";
import { Button } from "@/components/ui/button";
import { usePollForm } from "./PollFormContext";
import { handleAddOption, handleRemoveOption, handleOptionChange } from "./PollFormUtils";
import { toast } from "sonner";
import { PollType } from "@/lib/types";

// Import refactored components
import BasicPollFields from "./form-types/BasicPollFields";
import PollTypeSelector from "./form-types/PollTypeSelector";
import PollTypeContent from "./form-utils/PollTypeContent";

const BasicInfoForm: React.FC = () => {
  const { formData, setFormData, setCurrentTab, getDefaultOptionsForType } = usePollForm();
  const [ratingScale, setRatingScale] = useState<[number, number]>([1, 5]);
  const [showCorrectAnswers, setShowCorrectAnswers] = useState(false);

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

  const moveToNextTab = () => {
    if (!formData.title.trim()) {
      toast.error("Please enter a poll title");
      return;
    }
    
    if (formData.options.some(option => !option.trim())) {
      toast.error("All options must have content");
      return;
    }

    // Quiz type validation - ensure at least one correct answer
    if (formData.type === "quiz" && (!formData.correctAnswers || formData.correctAnswers.length === 0)) {
      toast.error("Please select at least one correct answer for your quiz");
      return;
    }
    
    setCurrentTab("settings");
  };

  const handleAddOptionWrapper = () => {
    handleAddOption(formData, setFormData);
    
    // Add an empty image URL if we're in image-based poll mode
    if (formData.type === "image-based" && formData.optionImages) {
      setFormData(prev => ({
        ...prev,
        optionImages: [...prev.optionImages!, ""]
      }));
    }
  };

  const handleRemoveOptionWrapper = (index: number) => {
    handleRemoveOption(index, formData, setFormData);
    
    // Remove the image URL for this option if we're in image-based poll mode
    if (formData.type === "image-based" && formData.optionImages) {
      const newOptionImages = [...formData.optionImages];
      newOptionImages.splice(index, 1);
      setFormData(prev => ({
        ...prev,
        optionImages: newOptionImages
      }));
    }

    // Remove from correct answers if in quiz mode
    if (formData.type === "quiz" && formData.correctAnswers) {
      setFormData(prev => ({
        ...prev,
        correctAnswers: prev.correctAnswers?.filter(i => i !== index.toString())
      }));
    }
  };

  const handleOptionChangeWrapper = (index: number, value: string) => {
    handleOptionChange(index, value, formData, setFormData);
  };

  const handleImageUpload = (index: number, file: File) => {
    if (!file.type.startsWith("image/")) {
      toast.error("Please upload an image file");
      return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
      if (e.target?.result && formData.optionImages) {
        const newOptionImages = [...formData.optionImages];
        newOptionImages[index] = e.target.result as string;
        setFormData(prev => ({
          ...prev,
          optionImages: newOptionImages
        }));
      }
    };
    reader.readAsDataURL(file);
  };

  const handlePollTypeChange = (value: string) => {
    // Cast the string value to PollType to ensure type safety
    const newType = value as PollType;
    
    // Get default options for the new poll type
    const defaultOptions = getDefaultOptionsForType(newType);
    
    setFormData(prev => {
      const newFormData = { 
        ...prev,
        type: newType,
        options: defaultOptions
      };
      
      // Initialize type-specific properties
      if (newType === "image-based") {
        newFormData.optionImages = Array(defaultOptions.length).fill("");
      } else if (newType === "quiz") {
        newFormData.correctAnswers = [];
      } else if (newType === "rating-scale") {
        // Default 1-5 scale
        setRatingScale([1, 5]);
      }
      
      return newFormData;
    });
  };

  const toggleCorrectAnswer = (index: string) => {
    if (!formData.correctAnswers) {
      setFormData(prev => ({
        ...prev,
        correctAnswers: [index]
      }));
      return;
    }

    const newCorrectAnswers = [...formData.correctAnswers];
    const existingIndex = newCorrectAnswers.indexOf(index);
    
    if (existingIndex > -1) {
      newCorrectAnswers.splice(existingIndex, 1);
    } else {
      newCorrectAnswers.push(index);
    }
    
    setFormData(prev => ({
      ...prev,
      correctAnswers: newCorrectAnswers
    }));
  };

  const handleRatingScaleChange = (values: number[]) => {
    setRatingScale([values[0], values[1]]);
    
    // Update options based on scale
    const min = values[0];
    const max = values[1];
    const newOptions = [];
    
    for (let i = min; i <= max; i++) {
      newOptions.push(i.toString());
    }
    
    setFormData(prev => ({
      ...prev,
      options: newOptions
    }));
  };

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
        <Button type="button" onClick={moveToNextTab}>
          Next: Poll Settings →
        </Button>
      </div>
    </div>
  );
};

export default BasicInfoForm;

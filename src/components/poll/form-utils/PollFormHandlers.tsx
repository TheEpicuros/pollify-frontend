
import React from "react";
import { toast } from "sonner";
import { PollFormData, PollType } from "@/lib/types";

interface PollFormHandlersProps {
  formData: PollFormData;
  setFormData: React.Dispatch<React.SetStateAction<PollFormData>>;
  setCurrentTab: (tab: string) => void;
  ratingScale: [number, number];
  setRatingScale: React.Dispatch<React.SetStateAction<[number, number]>>;
  getDefaultOptionsForType: (type: PollType) => string[];
}

// Custom hook that returns poll form handler functions
export const usePollFormHandlers = ({
  formData,
  setFormData,
  setCurrentTab,
  ratingScale,
  setRatingScale,
  getDefaultOptionsForType,
}: PollFormHandlersProps) => {
  const handleMoveToNextTab = () => {
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

  const handleAddOption = () => {
    if (formData.options.length < 10) {
      setFormData(prev => ({
        ...prev,
        options: [...prev.options, ""],
      }));
      
      // Add an empty image URL if we're in image-based poll mode
      if (formData.type === "image-based" && formData.optionImages) {
        setFormData(prev => ({
          ...prev,
          optionImages: [...prev.optionImages!, ""]
        }));
      }
    } else {
      toast.error("Maximum 10 options allowed");
    }
  };

  const handleRemoveOption = (index: number) => {
    if (formData.options.length > 2) {
      const newOptions = [...formData.options];
      newOptions.splice(index, 1);
      setFormData(prev => ({
        ...prev,
        options: newOptions
      }));
      
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
    } else {
      toast.error("Minimum 2 options required");
    }
  };

  const handleOptionChange = (index: number, value: string) => {
    const newOptions = [...formData.options];
    newOptions[index] = value;
    setFormData({ ...formData, options: newOptions });
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

  return {
    handleMoveToNextTab,
    handleAddOption,
    handleRemoveOption,
    handleOptionChange,
    handleImageUpload,
    handlePollTypeChange,
    toggleCorrectAnswer,
    handleRatingScaleChange
  };
};

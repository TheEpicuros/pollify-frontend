
import React, { useEffect, useState } from "react";
import { Button } from "@/components/ui/button";
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from "@/components/ui/select";
import PollOptionsSection from "./PollOptionsSection";
import { usePollForm } from "./PollFormContext";
import { handleAddOption, handleRemoveOption, handleOptionChange } from "./PollFormUtils";
import { toast } from "sonner";
import { PollType } from "@/lib/types";
import { Input } from "@/components/ui/input";
import { Slider } from "@/components/ui/slider";
import { Switch } from "@/components/ui/switch";
import { Label } from "@/components/ui/label";
import { HelpCircle, Info } from "lucide-react";
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip";

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

  const renderPollTypeOptions = () => {
    const minOptionCount = formData.type === "binary" ? 2 : 
                           formData.type === "open-ended" ? 1 : 2;
    
    const canRemoveOption = formData.options.length > minOptionCount;

    switch (formData.type) {
      case "binary":
        return (
          <div className="space-y-4 mt-4">
            <div className="flex flex-col space-y-3">
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="yes-option">Yes/Positive Option</Label>
                  <Input 
                    id="yes-option"
                    value={formData.options[0]} 
                    onChange={(e) => handleOptionChangeWrapper(0, e.target.value)}
                    placeholder="Yes" 
                    className="border-green-500 focus:ring-green-500"
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="no-option">No/Negative Option</Label>
                  <Input 
                    id="no-option"
                    value={formData.options[1]} 
                    onChange={(e) => handleOptionChangeWrapper(1, e.target.value)}
                    placeholder="No" 
                    className="border-red-500 focus:ring-red-500"
                  />
                </div>
              </div>
            </div>
          </div>
        );
      
      case "rating-scale":
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
              <Input 
                id="low-label"
                placeholder="Poor" 
                className="max-w-[150px]"
              />
            </div>
            <div className="flex items-center space-y-0 space-x-2">
              <Label htmlFor="high-label">High Label (Optional)</Label>
              <Input 
                id="high-label"
                placeholder="Excellent" 
                className="max-w-[150px]"
              />
            </div>
          </div>
        );
      
      case "open-ended":
        return (
          <div className="space-y-4 mt-4">
            <div className="p-4 bg-muted/50 rounded-lg">
              <div className="flex items-start gap-3">
                <Info size={18} className="text-primary mt-0.5" />
                <div className="text-sm">
                  <p className="font-medium mb-1">Open-ended polls allow responders to provide their own text answers.</p>
                  <p className="text-muted-foreground">You don't need to provide options for this poll type. Responders will see a text area to submit their answers.</p>
                </div>
              </div>
            </div>
          </div>
        );
      
      case "quiz":
        return (
          <div className="space-y-4 mt-4">
            <div className="flex items-center space-x-2">
              <Switch 
                id="show-correct-answers"
                checked={showCorrectAnswers}
                onCheckedChange={setShowCorrectAnswers}
              />
              <Label htmlFor="show-correct-answers" className="cursor-pointer">Mark correct answers</Label>
            </div>
            
            {showCorrectAnswers && (
              <div className="p-4 bg-muted/50 rounded-lg mb-4">
                <div className="flex items-start gap-3">
                  <Info size={18} className="text-primary mt-0.5" />
                  <p className="text-sm">Select which options are correct answers for your quiz. You can select multiple correct answers.</p>
                </div>
              </div>
            )}
            
            <PollOptionsSection
              options={formData.options}
              handleAddOption={handleAddOptionWrapper}
              handleRemoveOption={handleRemoveOptionWrapper}
              handleOptionChange={handleOptionChangeWrapper}
              canRemoveOption={canRemoveOption}
              showCorrectAnswers={showCorrectAnswers}
              correctAnswers={formData.correctAnswers}
              onToggleCorrectAnswer={toggleCorrectAnswer}
            />
          </div>
        );
      
      case "image-based":
        return (
          <PollOptionsSection
            options={formData.options}
            handleAddOption={handleAddOptionWrapper}
            handleRemoveOption={handleRemoveOptionWrapper}
            handleOptionChange={handleOptionChangeWrapper}
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
            handleAddOption={handleAddOptionWrapper}
            handleRemoveOption={handleRemoveOptionWrapper}
            handleOptionChange={handleOptionChangeWrapper}
            canRemoveOption={canRemoveOption}
          />
        );
    }
  };

  return (
    <div className="space-y-6">
      <div className="space-y-2">
        <label htmlFor="title" className="block text-sm font-medium">
          Poll Title <span className="text-destructive">*</span>
        </label>
        <input
          id="title"
          type="text"
          className="w-full px-4 py-2 rounded-lg border bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition"
          placeholder="Ask a question..."
          value={formData.title}
          onChange={(e) => setFormData({ ...formData, title: e.target.value })}
          required
        />
      </div>

      <div className="space-y-2">
        <label htmlFor="description" className="block text-sm font-medium">
          Description <span className="text-muted-foreground">(optional)</span>
        </label>
        <textarea
          id="description"
          rows={3}
          className="w-full px-4 py-2 rounded-lg border bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition resize-none"
          placeholder="Add more context to your question..."
          value={formData.description}
          onChange={(e) =>
            setFormData({ ...formData, description: e.target.value })
          }
        />
      </div>
      
      <div className="space-y-2">
        <label htmlFor="pollType" className="block text-sm font-medium">
          Poll Type
        </label>
        <Select 
          value={formData.type || "multiple-choice"} 
          onValueChange={handlePollTypeChange}
        >
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
          {formData.type === "multiple-choice" && "Voters can select only one option."}
          {formData.type === "check-all" && "Voters can select multiple options that apply."}
          {formData.type === "binary" && "Simple yes/no or either/or questions."}
          {formData.type === "rating-scale" && "Ask voters to rate on a numeric scale."}
          {formData.type === "image-based" && "Use images as answer options."}
          {formData.type === "quiz" && "Test knowledge with right/wrong answers."}
          {formData.type === "open-ended" && "Allow voters to provide text responses."}
          {formData.type === "ranked-choice" && "Voters rank options in order of preference."}
        </div>
      </div>

      {renderPollTypeOptions()}
      
      <div className="flex justify-end pt-4">
        <Button type="button" onClick={moveToNextTab}>
          Next: Poll Settings →
        </Button>
      </div>
    </div>
  );
};

export default BasicInfoForm;


import React, { useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from "@/components/ui/select";
import PollOptionsSection from "./PollOptionsSection";
import { usePollForm } from "./PollFormContext";
import { handleAddOption, handleRemoveOption, handleOptionChange } from "./PollFormUtils";
import { toast } from "sonner";
import { PollType } from "@/lib/types";

const BasicInfoForm: React.FC = () => {
  const { formData, setFormData, setCurrentTab } = usePollForm();

  useEffect(() => {
    // Initialize optionImages array if it doesn't exist
    if (formData.type === "image-based" && !formData.optionImages) {
      setFormData(prev => ({
        ...prev,
        optionImages: Array(prev.options.length).fill("")
      }));
    }
  }, [formData.type, formData.optionImages, setFormData]);

  const moveToNextTab = () => {
    if (!formData.title.trim()) {
      toast.error("Please enter a poll title");
      return;
    }
    
    if (formData.options.some(option => !option.trim())) {
      toast.error("All options must have content");
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
    setFormData({ ...formData, type: value as PollType });
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
          </SelectContent>
        </Select>
      </div>

      <PollOptionsSection
        options={formData.options}
        handleAddOption={handleAddOptionWrapper}
        handleRemoveOption={handleRemoveOptionWrapper}
        handleOptionChange={handleOptionChangeWrapper}
        showImages={formData.type === "image-based"}
        optionImages={formData.optionImages}
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

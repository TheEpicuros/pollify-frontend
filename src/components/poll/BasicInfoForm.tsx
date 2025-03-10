
import React from "react";
import { Button } from "@/components/ui/button";
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from "@/components/ui/select";
import PollOptionsSection from "./PollOptionsSection";
import { usePollForm } from "./PollFormContext";
import { handleAddOption, handleRemoveOption, handleOptionChange } from "./PollFormUtils";

const BasicInfoForm: React.FC = () => {
  const { formData, setFormData, setCurrentTab } = usePollForm();

  const moveToNextTab = () => {
    if (!formData.title.trim()) {
      return;
    }
    
    if (formData.options.some(option => !option.trim())) {
      return;
    }
    
    setCurrentTab("settings");
  };

  const handleAddOptionWrapper = () => {
    handleAddOption(formData, setFormData);
  };

  const handleRemoveOptionWrapper = (index: number) => {
    handleRemoveOption(index, formData, setFormData);
  };

  const handleOptionChangeWrapper = (index: number, value: string) => {
    handleOptionChange(index, value, formData, setFormData);
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
          value={formData.type} 
          onValueChange={(value) => setFormData({ ...formData, type: value })}
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

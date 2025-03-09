
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { motion } from "framer-motion";
import { PollFormData } from "@/lib/types";
import { createPoll } from "@/lib/data";
import { toast } from "sonner";
import PollOptionsSection from "./poll/PollOptionsSection";
import PollSubmitButton from "./poll/PollSubmitButton";

const PollForm = () => {
  const navigate = useNavigate();
  const [formData, setFormData] = useState<PollFormData>({
    title: "",
    description: "",
    options: ["", ""],
  });
  const [isSubmitting, setIsSubmitting] = useState(false);

  const handleAddOption = () => {
    if (formData.options.length < 10) {
      setFormData({
        ...formData,
        options: [...formData.options, ""],
      });
    } else {
      toast.error("Maximum 10 options allowed");
    }
  };

  const handleRemoveOption = (index: number) => {
    if (formData.options.length > 2) {
      const newOptions = [...formData.options];
      newOptions.splice(index, 1);
      setFormData({ ...formData, options: newOptions });
    } else {
      toast.error("Minimum 2 options required");
    }
  };

  const handleOptionChange = (index: number, value: string) => {
    const newOptions = [...formData.options];
    newOptions[index] = value;
    setFormData({ ...formData, options: newOptions });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    // Validation
    if (!formData.title.trim()) {
      toast.error("Please enter a poll title");
      return;
    }
    
    if (formData.options.some(option => !option.trim())) {
      toast.error("All options must have content");
      return;
    }
    
    setIsSubmitting(true);
    
    // Simulate API call
    setTimeout(() => {
      try {
        const newPoll = createPoll(
          formData.title,
          formData.description || "",
          formData.options
        );
        
        toast.success("Poll created successfully");
        navigate(`/poll/${newPoll.id}`);
      } catch (error) {
        console.error("Error creating poll:", error);
        toast.error("Failed to create poll. Please try again.");
      } finally {
        setIsSubmitting(false);
      }
    }, 800);
  };

  return (
    <motion.form
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      className="space-y-6 max-w-2xl mx-auto"
      onSubmit={handleSubmit}
    >
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

      <PollOptionsSection
        options={formData.options}
        handleAddOption={handleAddOption}
        handleRemoveOption={handleRemoveOption}
        handleOptionChange={handleOptionChange}
      />

      <div className="pt-4">
        <PollSubmitButton isSubmitting={isSubmitting} />
      </div>
    </motion.form>
  );
};

export default PollForm;

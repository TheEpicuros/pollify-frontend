
import { toast } from "sonner";
import { PollFormData } from "@/lib/types";

export const validatePollForm = (formData: PollFormData, currentTab: string): boolean => {
  if (currentTab === "basic" || currentTab === "all") {
    if (!formData.title.trim()) {
      toast.error("Please enter a poll title");
      return false;
    }
    
    if (formData.options.some(option => !option.trim())) {
      toast.error("All options must have content");
      return false;
    }
  }
  
  return true;
};

export const handleOptionChange = (
  index: number, 
  value: string, 
  formData: PollFormData, 
  setFormData: React.Dispatch<React.SetStateAction<PollFormData>>
) => {
  const newOptions = [...formData.options];
  newOptions[index] = value;
  setFormData({ ...formData, options: newOptions });
};

export const handleAddOption = (
  formData: PollFormData, 
  setFormData: React.Dispatch<React.SetStateAction<PollFormData>>
) => {
  if (formData.options.length < 10) {
    setFormData({
      ...formData,
      options: [...formData.options, ""],
    });
  } else {
    toast.error("Maximum 10 options allowed");
  }
};

export const handleRemoveOption = (
  index: number, 
  formData: PollFormData, 
  setFormData: React.Dispatch<React.SetStateAction<PollFormData>>
) => {
  if (formData.options.length > 2) {
    const newOptions = [...formData.options];
    newOptions.splice(index, 1);
    setFormData({ ...formData, options: newOptions });
  } else {
    toast.error("Minimum 2 options required");
  }
};

export const handleDateSelect = (
  selectedDate: Date | undefined, 
  setDate: React.Dispatch<React.SetStateAction<Date | undefined>>, 
  formData: PollFormData, 
  setFormData: React.Dispatch<React.SetStateAction<PollFormData>>
) => {
  setDate(selectedDate);
  setFormData({
    ...formData,
    endDate: selectedDate,
  });
};


import { PollFormData, PollType } from "@/lib/types";

export interface PollFormContextType {
  formData: PollFormData;
  setFormData: React.Dispatch<React.SetStateAction<PollFormData>>;
  isSubmitting: boolean;
  setIsSubmitting: React.Dispatch<React.SetStateAction<boolean>>;
  date: Date | undefined;
  setDate: React.Dispatch<React.SetStateAction<Date | undefined>>;
  currentTab: string;
  setCurrentTab: React.Dispatch<React.SetStateAction<string>>;
  getDefaultOptionsForType: (type: PollType) => string[];
}

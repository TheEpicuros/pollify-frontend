
import React, { createContext, useContext, useState } from "react";
import { PollFormData, PollType } from "@/lib/types";

interface PollFormContextType {
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

const PollFormContext = createContext<PollFormContextType | undefined>(undefined);

export const PollFormProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [formData, setFormData] = useState<PollFormData>({
    title: "",
    description: "",
    options: ["", ""],
    optionImages: ["", ""],
    type: "multiple-choice",
    endDate: undefined,
    settings: {
      showResults: false,
      resultsDisplay: "bar",
      allowComments: true,
    }
  });
  
  const [date, setDate] = useState<Date | undefined>(undefined);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [currentTab, setCurrentTab] = useState("basic");

  // Helper function to get default options based on poll type
  const getDefaultOptionsForType = (type: PollType): string[] => {
    switch (type) {
      case "binary":
        return ["Yes", "No"];
      case "rating-scale":
        return ["1", "2", "3", "4", "5"];
      case "open-ended":
        return ["Open Response"];
      case "check-all":
        return ["Option 1", "Option 2", "Option 3"];
      case "ranked-choice":
        return ["Option 1", "Option 2", "Option 3"];
      case "image-based":
        return ["Image 1", "Image 2"];
      case "quiz":
        return ["Answer 1", "Answer 2", "Answer 3", "Answer 4"];
      default:
        return ["", ""];
    }
  };

  return (
    <PollFormContext.Provider value={{
      formData,
      setFormData,
      isSubmitting,
      setIsSubmitting,
      date,
      setDate,
      currentTab,
      setCurrentTab,
      getDefaultOptionsForType
    }}>
      {children}
    </PollFormContext.Provider>
  );
};

export const usePollForm = () => {
  const context = useContext(PollFormContext);
  if (context === undefined) {
    throw new Error("usePollForm must be used within a PollFormProvider");
  }
  return context;
};

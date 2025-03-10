
import React, { createContext, useState } from "react";
import { PollFormData } from "@/lib/types";
import { PollFormContextType } from "./types";
import { getDefaultOptionsForType } from "./pollTypeUtils";

// Create the context with undefined as default value
export const PollFormContext = createContext<PollFormContextType | undefined>(undefined);

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

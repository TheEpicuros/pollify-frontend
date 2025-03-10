
import React, { createContext, useContext, useState } from "react";
import { PollFormData } from "@/lib/types";

interface PollFormContextType {
  formData: PollFormData;
  setFormData: React.Dispatch<React.SetStateAction<PollFormData>>;
  isSubmitting: boolean;
  setIsSubmitting: React.Dispatch<React.SetStateAction<boolean>>;
  date: Date | undefined;
  setDate: React.Dispatch<React.SetStateAction<Date | undefined>>;
  currentTab: string;
  setCurrentTab: React.Dispatch<React.SetStateAction<string>>;
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

  return (
    <PollFormContext.Provider value={{
      formData,
      setFormData,
      isSubmitting,
      setIsSubmitting,
      date,
      setDate,
      currentTab,
      setCurrentTab
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

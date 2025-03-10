
import { useContext } from "react";
import { PollFormContext } from "./PollFormProvider";

export const usePollForm = () => {
  const context = useContext(PollFormContext);
  if (context === undefined) {
    throw new Error("usePollForm must be used within a PollFormProvider");
  }
  return context;
};

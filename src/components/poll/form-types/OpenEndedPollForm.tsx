
import React from "react";
import { Info } from "lucide-react";

const OpenEndedPollForm: React.FC = () => {
  return (
    <div className="space-y-4 mt-4">
      <div className="p-4 bg-muted/50 rounded-lg">
        <div className="flex items-start gap-3">
          <Info size={18} className="text-primary mt-0.5" />
          <div className="text-sm">
            <p className="font-medium mb-1">Open-ended polls allow responders to provide their own text answers.</p>
            <p className="text-muted-foreground">
              You don't need to provide options for this poll type. Responders will see a text area to submit their answers.
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default OpenEndedPollForm;

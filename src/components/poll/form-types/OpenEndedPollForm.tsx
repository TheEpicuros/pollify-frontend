
import React from "react";
import { Info } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";

interface OpenEndedPollFormProps {
  placeholder?: string;
  onPlaceholderChange?: (value: string) => void;
}

const OpenEndedPollForm: React.FC<OpenEndedPollFormProps> = ({
  placeholder = "Share your thoughts...",
  onPlaceholderChange,
}) => {
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
      
      {onPlaceholderChange && (
        <Card>
          <CardContent className="pt-6">
            <div className="space-y-3">
              <Label htmlFor="response-placeholder">Response Placeholder (Optional)</Label>
              <Textarea
                id="response-placeholder"
                placeholder="Enter a placeholder text for the response field..."
                value={placeholder}
                onChange={(e) => onPlaceholderChange(e.target.value)}
                className="resize-none"
              />
              <p className="text-xs text-muted-foreground">
                This text will be shown as a placeholder in the response field.
              </p>
            </div>
          </CardContent>
        </Card>
      )}
    </div>
  );
};

export default OpenEndedPollForm;

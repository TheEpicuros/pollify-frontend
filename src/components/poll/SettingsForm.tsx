
import React from "react";
import { format } from "date-fns";
import { Calendar } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Switch } from "@/components/ui/switch";
import { Label } from "@/components/ui/label";
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from "@/components/ui/select";
import { Popover, PopoverTrigger, PopoverContent } from "@/components/ui/popover";
import { Calendar as CalendarUI } from "@/components/ui/calendar";
import PollSubmitButton from "./PollSubmitButton";
import { usePollForm } from "./PollFormContext";
import { handleDateSelect } from "./PollFormUtils";

const SettingsForm: React.FC = () => {
  const { 
    formData, 
    setFormData, 
    date, 
    setDate, 
    isSubmitting, 
    setCurrentTab 
  } = usePollForm();

  const handleDateSelectWrapper = (selectedDate: Date | undefined) => {
    handleDateSelect(selectedDate, setDate, formData, setFormData);
  };

  const moveToPrevTab = () => {
    setCurrentTab("basic");
  };

  return (
    <div className="space-y-6">
      <div className="space-y-6 bg-muted/30 p-4 rounded-lg">
        <div className="space-y-2">
          <h3 className="text-base font-medium">Poll Duration</h3>
          
          <div className="flex flex-col space-y-2">
            <label htmlFor="endDate" className="block text-sm text-muted-foreground">
              End Date (Optional)
            </label>
            <div className="flex space-x-2">
              <Popover>
                <PopoverTrigger asChild>
                  <Button
                    variant="outline"
                    className="w-full justify-start text-left"
                  >
                    <Calendar className="mr-2 h-4 w-4" />
                    {date ? format(date, "PPP") : "Select date"}
                  </Button>
                </PopoverTrigger>
                <PopoverContent className="w-auto p-0">
                  <CalendarUI
                    mode="single"
                    selected={date}
                    onSelect={handleDateSelectWrapper}
                    initialFocus
                    disabled={(date) => date < new Date()}
                  />
                </PopoverContent>
              </Popover>
              
              {date && (
                <Button 
                  variant="ghost" 
                  size="icon" 
                  onClick={() => handleDateSelectWrapper(undefined)}
                  aria-label="Clear date"
                >
                  ×
                </Button>
              )}
            </div>
            <p className="text-xs text-muted-foreground">
              Leave blank for a poll without an end date
            </p>
          </div>
        </div>
        
        <div className="space-y-6 pt-2">
          <h3 className="text-base font-medium">Display Settings</h3>
          
          <div className="flex items-center justify-between">
            <div className="space-y-0.5">
              <Label htmlFor="show-results">Show Results Before Voting</Label>
              <p className="text-xs text-muted-foreground">
                Allow users to see results before casting their vote
              </p>
            </div>
            <Switch
              id="show-results"
              checked={formData.settings.showResults}
              onCheckedChange={(checked) => 
                setFormData({
                  ...formData,
                  settings: {
                    ...formData.settings,
                    showResults: checked
                  }
                })
              }
            />
          </div>
          
          <div className="space-y-2">
            <Label htmlFor="results-display">Results Display Format</Label>
            <Select 
              value={formData.settings.resultsDisplay} 
              onValueChange={(value: "bar" | "pie" | "donut" | "text") => 
                setFormData({
                  ...formData,
                  settings: {
                    ...formData.settings,
                    resultsDisplay: value
                  }
                })
              }
            >
              <SelectTrigger id="results-display">
                <SelectValue placeholder="Select display format" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="bar">Bar Chart</SelectItem>
                <SelectItem value="pie">Pie Chart</SelectItem>
                <SelectItem value="donut">Donut Chart</SelectItem>
                <SelectItem value="text">Text Only</SelectItem>
              </SelectContent>
            </Select>
          </div>
          
          <div className="flex items-center justify-between">
            <div className="space-y-0.5">
              <Label htmlFor="allow-comments">Allow Comments</Label>
              <p className="text-xs text-muted-foreground">
                Let users leave comments on your poll
              </p>
            </div>
            <Switch
              id="allow-comments"
              checked={formData.settings.allowComments}
              onCheckedChange={(checked) => 
                setFormData({
                  ...formData,
                  settings: {
                    ...formData.settings,
                    allowComments: checked
                  }
                })
              }
            />
          </div>
        </div>
      </div>
      
      <div className="flex justify-between pt-4">
        <Button type="button" variant="outline" onClick={moveToPrevTab}>
          ← Back
        </Button>
        
        <PollSubmitButton isSubmitting={isSubmitting} />
      </div>
    </div>
  );
};

export default SettingsForm;

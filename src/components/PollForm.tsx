import { useState, useCallback } from "react";
import { useNavigate } from "react-router-dom";
import { motion } from "framer-motion";
import { PollFormData } from "@/lib/types";
import { createPoll } from "@/lib/data";
import { toast } from "sonner";
import PollOptionsSection from "./poll/PollOptionsSection";
import PollSubmitButton from "./poll/PollSubmitButton";
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs";
import { Calendar } from "lucide-react";
import { Switch } from "@/components/ui/switch";
import { Label } from "@/components/ui/label";
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from "@/components/ui/select";
import { Popover, PopoverTrigger, PopoverContent } from "@/components/ui/popover";
import { Button } from "@/components/ui/button";
import { Calendar as CalendarUI } from "@/components/ui/calendar";
import { format } from "date-fns";

const PollForm = () => {
  const navigate = useNavigate();
  const [formData, setFormData] = useState<PollFormData>({
    title: "",
    description: "",
    options: ["", ""],
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

  const handleAddOption = useCallback(() => {
    if (formData.options.length < 10) {
      setFormData({
        ...formData,
        options: [...formData.options, ""],
      });
    } else {
      toast.error("Maximum 10 options allowed");
    }
  }, [formData]);

  const handleRemoveOption = useCallback((index: number) => {
    if (formData.options.length > 2) {
      const newOptions = [...formData.options];
      newOptions.splice(index, 1);
      setFormData({ ...formData, options: newOptions });
    } else {
      toast.error("Minimum 2 options required");
    }
  }, [formData]);

  const handleOptionChange = useCallback((index: number, value: string) => {
    const newOptions = [...formData.options];
    newOptions[index] = value;
    setFormData({ ...formData, options: newOptions });
  }, [formData]);

  const handleDateSelect = (selectedDate: Date | undefined) => {
    setDate(selectedDate);
    setFormData({
      ...formData,
      endDate: selectedDate,
    });
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
        const newPoll = createPoll(formData);
        
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

  const moveToPrevTab = () => {
    if (currentTab === "settings") {
      setCurrentTab("basic");
    }
  };

  const moveToNextTab = () => {
    if (currentTab === "basic") {
      if (!formData.title.trim()) {
        toast.error("Please enter a poll title");
        return;
      }
      
      if (formData.options.some(option => !option.trim())) {
        toast.error("All options must have content");
        return;
      }
      
      setCurrentTab("settings");
    }
  };

  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      className="max-w-2xl mx-auto"
    >
      <Tabs value={currentTab} onValueChange={setCurrentTab} className="w-full">
        <TabsList className="grid w-full grid-cols-2 mb-8">
          <TabsTrigger value="basic">Basic Info</TabsTrigger>
          <TabsTrigger value="settings">Settings</TabsTrigger>
        </TabsList>
        
        <form onSubmit={handleSubmit} className="space-y-6">
          <TabsContent value="basic" className="space-y-6">
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
            
            <div className="space-y-2">
              <label htmlFor="pollType" className="block text-sm font-medium">
                Poll Type
              </label>
              <Select 
                value={formData.type} 
                onValueChange={(value) => setFormData({ ...formData, type: value })}
              >
                <SelectTrigger id="pollType">
                  <SelectValue placeholder="Select poll type" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="multiple-choice">Multiple Choice (Select One)</SelectItem>
                  <SelectItem value="check-all">Multiple Answers (Select Many)</SelectItem>
                  <SelectItem value="binary">Yes/No Question</SelectItem>
                  <SelectItem value="rating-scale">Rating Scale</SelectItem>
                  <SelectItem value="image-based">Image Based Poll</SelectItem>
                </SelectContent>
              </Select>
            </div>

            <PollOptionsSection
              options={formData.options}
              handleAddOption={handleAddOption}
              handleRemoveOption={handleRemoveOption}
              handleOptionChange={handleOptionChange}
            />
            
            <div className="flex justify-end pt-4">
              <Button type="button" onClick={moveToNextTab}>
                Next: Poll Settings →
              </Button>
            </div>
          </TabsContent>
          
          <TabsContent value="settings" className="space-y-6">
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
                          onSelect={handleDateSelect}
                          initialFocus
                          disabled={(date) => date < new Date()}
                        />
                      </PopoverContent>
                    </Popover>
                    
                    {date && (
                      <Button 
                        variant="ghost" 
                        size="icon" 
                        onClick={() => handleDateSelect(undefined)}
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
          </TabsContent>
        </form>
      </Tabs>
    </motion.div>
  );
};

export default PollForm;

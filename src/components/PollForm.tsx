
import { useCallback } from "react";
import { useNavigate } from "react-router-dom";
import { motion } from "framer-motion";
import { toast } from "sonner";
import { Tabs, TabsList, TabsTrigger, TabsContent } from "@/components/ui/tabs";
import { PollFormProvider, usePollForm } from "./poll/PollFormContext";
import BasicInfoForm from "./poll/BasicInfoForm";
import SettingsForm from "./poll/SettingsForm";
import { validatePollForm } from "./poll/PollFormUtils";
import { useCreatePoll } from "@/hooks/use-polls";

const PollFormContent = () => {
  const navigate = useNavigate();
  const { formData, setIsSubmitting, currentTab, setCurrentTab } = usePollForm();
  const createPollMutation = useCreatePoll();

  const handleSubmit = useCallback((e: React.FormEvent) => {
    e.preventDefault();
    
    // Validation
    if (!validatePollForm(formData, "all")) {
      return;
    }
    
    setIsSubmitting(true);
    
    // Use the React Query mutation to create the poll
    createPollMutation.mutate(formData, {
      onSuccess: (newPoll) => {
        navigate(`/poll/${newPoll.id}`);
      },
      onSettled: () => {
        setIsSubmitting(false);
      }
    });
  }, [formData, navigate, setIsSubmitting, createPollMutation]);

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
          <TabsContent value="basic">
            <BasicInfoForm />
          </TabsContent>
          
          <TabsContent value="settings">
            <SettingsForm />
          </TabsContent>
        </form>
      </Tabs>
    </motion.div>
  );
};

// Main component that provides the context
const PollForm = () => {
  return (
    <PollFormProvider>
      <PollFormContent />
    </PollFormProvider>
  );
};

export default PollForm;

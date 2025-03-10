
import React, { useState } from "react";
import { Trash } from "lucide-react";
import { motion } from "framer-motion";
import { Button } from "@/components/ui/button";
import { Check } from "lucide-react";

interface PollOptionItemProps {
  option: string;
  index: number;
  handleOptionChange: (index: number, value: string) => void;
  handleRemoveOption: (index: number) => void;
  canRemove: boolean;
  imageUrl?: string;
  onImageUpload?: (index: number, file: File) => void;
  isCorrectAnswer?: boolean;
  onToggleCorrectAnswer?: (index: string) => void;
}

const PollOptionItem: React.FC<PollOptionItemProps> = ({
  option,
  index,
  handleOptionChange,
  handleRemoveOption,
  canRemove,
  imageUrl,
  onImageUpload,
  isCorrectAnswer = false,
  onToggleCorrectAnswer,
}) => {
  const [isDragging, setIsDragging] = useState(false);

  const handleFilePicker = () => {
    const input = document.createElement("input");
    input.type = "file";
    input.accept = "image/*";
    input.onchange = (e) => {
      const file = (e.target as HTMLInputElement).files?.[0];
      if (file && onImageUpload) {
        onImageUpload(index, file);
      }
    };
    input.click();
  };

  return (
    <motion.div
      layout
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, height: 0, marginBottom: 0 }}
      className="relative group"
    >
      <div className="flex space-x-2">
        <div className="flex-grow relative">
          <div className="flex">
            <span className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
              {index + 1}.
            </span>
            <input
              type="text"
              value={option}
              onChange={(e) => handleOptionChange(index, e.target.value)}
              className={`pl-8 pr-10 py-2 w-full rounded-lg border bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition ${
                onToggleCorrectAnswer && isCorrectAnswer
                  ? "border-green-500 bg-green-50 dark:bg-green-950/20"
                  : ""
              }`}
              placeholder={`Option ${index + 1}`}
            />
            {onToggleCorrectAnswer && (
              <button
                type="button"
                onClick={() => onToggleCorrectAnswer(index.toString())}
                className={`absolute right-10 top-1/2 -translate-y-1/2 flex items-center justify-center w-5 h-5 rounded-full transition-colors ${
                  isCorrectAnswer
                    ? "bg-green-500 text-white"
                    : "bg-muted hover:bg-muted-foreground/20"
                }`}
                title={isCorrectAnswer ? "Correct answer" : "Mark as correct"}
              >
                <Check size={12} />
              </button>
            )}
          </div>
          {imageUrl && (
            <div className="mt-2 relative">
              <div
                className="w-full h-20 bg-cover bg-center rounded-md overflow-hidden cursor-pointer hover:opacity-90 transition-opacity"
                style={{ backgroundImage: `url(${imageUrl})` }}
                onClick={handleFilePicker}
              >
                {!imageUrl && (
                  <div className="absolute inset-0 flex items-center justify-center text-muted-foreground">
                    Click to add image
                  </div>
                )}
              </div>
              <button
                type="button"
                onClick={handleFilePicker}
                className="mt-1 text-xs text-muted-foreground hover:text-foreground transition-colors"
              >
                {imageUrl ? "Change image" : "Add image"}
              </button>
            </div>
          )}
        </div>
        {canRemove && (
          <Button
            variant="ghost"
            size="icon"
            onClick={() => handleRemoveOption(index)}
            className="opacity-0 group-hover:opacity-100 transition-opacity hover:bg-destructive/10 hover:text-destructive"
            aria-label="Remove option"
          >
            <Trash size={16} />
          </Button>
        )}
      </div>
    </motion.div>
  );
};

export default PollOptionItem;
